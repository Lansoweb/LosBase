<?php

/**
 * Abstract CRUD Controller
 *
 * @package   LosBase\Controller
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase\Controller\ODM;

use Zend\Stdlib\ResponseInterface as Response;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Paginator\Paginator;
use LosBase\Validator\NoEntityExists;
use LosBase\Validator\NoOtherEntityExists;
use LosBase\Document\DocumentManagerAwareTrait;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineObjectHydrator;
use Doctrine\ODM\MongoDB\Query\Builder;
use DoctrineMongoODMModule\Paginator\Adapter\DoctrinePaginator;
use LosBase\Controller\AbstractBaseController;

/**
 * Abstract CRUD Controller
 *
 * @package   LosBase\Controller
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
abstract class AbstractCrudController extends AbstractBaseController
{
    use DocumentManagerAwareTrait;

    /**
     * Document Service
     *
     * @var mixed
     */
    private $documentService;

    protected $uniqueDocumentMessage = null;

    /**
     * Retorna o serviço do documento
     *
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function getDocumentService()
    {
        if (null === $this->documentService) {
            $documentServiceClass = $this->getDocumentServiceClass();
            if (! class_exists($documentServiceClass)) {
                throw new \RuntimeException("Classe $documentServiceClass inexistente!");
            }
            $this->documentService = new $documentServiceClass();
            $this->documentService->setServiceLocator($this->getServiceLocator());
        }

        return $this->documentService;
    }

    public function getDocumentClass()
    {
        $module = $this->getModuleName();

        return "$module\Document\\$module";
    }

    public function getDocumentServiceClass()
    {
        $module = $this->getModuleName();

        return "$module\Service\\$module";
    }

    protected function getAddForm()
    {
        $form = $this->getForm();

        if ($this->uniqueField !== null) {
            $validator = new NoEntityExists([
                'object_repository' => $this->getDocumentManager()->getRepository($this->getDocumentClass()),
                'fields' => $this->uniqueField,
            ]);
            if ($this->uniqueDocumentMessage !== null) {
                $validator->setMessage($this->uniqueDocumentMessage, 'objectFound');
            }
            $form->getInputFilter()
                ->get($this->uniqueField)
                ->getValidatorChain()
                ->attach($validator);
        }

        return $form;
    }

    protected function getEditForm()
    {
        $form = $this->getForm();

        if ($this->uniqueField !== null) {
            $validator = new NoOtherEntityExists([
                'object_repository' => $this->getDocumentManager()->getRepository($this->getDocumentClass()),
                'fields' => $this->uniqueField,
                'id' => $this->getEvent()
                    ->getRouteMatch()
                    ->getParam('id', 0),
            ]);
            if ($this->uniqueDocumentMessage !== null) {
                $validator->setMessage($this->uniqueDocumentMessage, 'objectFound');
            }
            $form->getInputFilter()
                ->get($this->uniqueField)
                ->getValidatorChain()
                ->attach($validator);
        }

        return $form;
    }

    /**
     * Retorna a form para o cadastro do documento
     */
    public function getForm($documentClass = null)
    {
        if (null === $documentClass) {
            $documentClass = $this->getDocumentClass();
        }

        $builder = new AnnotationBuilder();
        $form = $builder->createForm($documentClass);

        $hasDocument = false;
        foreach ($form->getElements() as $element) {
            if (method_exists($element, 'setObjectManager')) {
                $element->setObjectManager($this->getDocumentManager());
                $hasDocument = true;
            } elseif (method_exists($element, 'getProxy')) {
                $proxy = $element->getProxy();
                if (method_exists($proxy, 'setObjectManager')) {
                    $proxy->setObjectManager($this->getDocumentManager());
                    $hasDocument = true;
                }
            }
        }

        if ($hasDocument) {
            $hydrator = new DoctrineObjectHydrator($this->getDocumentManager(), $documentClass);
            $form->setHydrator($hydrator);
        } else {
            $form->setHydrator(new ClassMethods());
        }

        $form->add([
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf',
            'attributes' => [
                'id' => 'csrf',
            ],
        ]);

        $submitElement = new \Zend\Form\Element\Button('submit');
        $submitElement->setAttributes([
            'type' => 'submit',
            'class' => 'btn btn-primary',
        ]);
        $submitElement->setLabel('Salvar');
        $form->add($submitElement, [
            'priority' => - 100,
        ]);

        $cancelarElement = new \Zend\Form\Element\Button('cancelar');
        $cancelarElement->setAttributes([
            'type' => 'button',
            'class' => 'btn btn-default',
            'onclick' => 'top.location=\''.$this->url()
                ->fromRoute($this->getActionRoute('list')).'\'',
        ]);
        $cancelarElement->setLabel('Cancelar');
        $form->add($cancelarElement, [
            'priority' => - 100,
        ]);

        return $form;
    }

    public function handleSearch(Builder $qb)
    {
    }

    /**
     * Lista os documentos, suporte a paginação, ordenação e busca
     */
    public function listAction()
    {
        $page = $this->getRequest()->getQuery('page', 0);
        $limit = $this->getRequest()->getQuery('limit', $this->defaultPageSize);
        $sort = $this->getRequest()->getQuery('sort', $this->defaultSort);
        $order = $this->getRequest()->getQuery('order', $this->defaultOrder);

        if (empty($sort)) {
            $sort = $this->defaultSort;
        }

        $offset = $limit * $page - $limit;
        if ($offset < 0) {
            $offset = 0;
        }

        /* @var $qb \Doctrine\ODM\MongoDB\Query\Builder */
        $qb = $this->getDocumentManager()->createQueryBuilder($this->getDocumentClass());
        $qb->select()
        ->sort($sort, $order)
        ->limit($limit)
        ->skip($offset);

        $this->handleSearch($qb);

        $paginator = new Paginator(new DoctrinePaginator($qb->getQuery()->execute()));
        $paginator->setDefaultItemCountPerPage($limit);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange($this->paginatorRange);

        return [
            'paginator' => $paginator,
            'sort' => $sort,
            'order' => $order,
            'page' => $page,
            'query' => $this->params()->fromQuery(),
        ];
    }

    public function viewAction()
    {
        $id = $this->getEvent()
            ->getRouteMatch()
            ->getParam('id', 0);

        $em = $this->getDocumentManager();
        $objRepository = $em->getRepository($this->getDocumentClass());
        $document = $objRepository->find($id);

        return [
            'document' => $document
        ];
    }

    public function addAction()
    {
        if (method_exists($this, 'getAddForm')) {
            $form = $this->getAddForm();
        } else {
            $form = $this->getForm();
        }

        $classe = $this->getDocumentClass();
        $entity = new $classe();

        $this->getEventManager()->trigger('getForm', $this, [
            'form' => $form,
            'documentClass' => $this->getDocumentClass(),
            'id' => 0,
            'entity' => $entity,
        ]);

        $form->bind($entity);

        $redirectUrl = $this->url()->fromRoute($this->getActionRoute(), [], true);
        $prg = $this->fileprg($form, $redirectUrl, true);

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            $this->getEventManager()->trigger('getForm', $this, [
                'form' => $form,
                'documentClass' => $this->getDocumentClass(),
                'entity' => $entity,
            ]);

            return [
                'entityForm' => $form,
                'entity' => $entity,
            ];
        }

        $this->getEventManager()->trigger('add', $this, [
            'form' => $form,
            'entityClass' => $this->getDocumentClass(),
            'entity' => $entity,
        ]);

        $savedEntity = $this->getDocumentService()->save($form, $entity);

        if (! $savedEntity) {
            return [
                'entityForm' => $form,
                'entity' => $entity,
            ];
        }

        $this->flashMessenger()->addSuccessMessage($this->getServiceLocator()
            ->get('translator')
            ->translate($this->successAddMessage));

        if ($this->needAddOther($form)) {
            $action = 'add';
        } else {
            $action = 'list';
        }

        return $this->redirect()->toRoute($this->getActionRoute($action), [], true);
    }

    /**
     * Altera uma entidade
     */
    public function editAction()
    {
        if (method_exists($this, 'getEditForm')) {
            $form = $this->getEditForm();
        } else {
            $form = $this->getForm();
        }

        $id = $this->getEvent()
            ->getRouteMatch()
            ->getParam('id', 0);

        $form->add([
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'id',
            'attributes' => [
                'id' => 'id',
                'value' => $id,
            ],
            'filters' => [
                [
                    'name' => 'Int',
                ],
            ],
            'validators' => [
                [
                    'name' => 'Digits',
                ],
            ],
        ]);

        $em = $this->getDocumentManager();
        $objRepository = $em->getRepository($this->getDocumentClass());
        $entity = $objRepository->find($id);

        $this->getEventManager()->trigger('getForm', $this, [
            'form' => $form,
            'documentClass' => $this->getDocumentClass(),
            'id' => $id,
            'entity' => $entity,
        ]);

        $form->bind($entity);

        $redirectUrl = $this->url()->fromRoute($this->getActionRoute(), [], true);
        $prg = $this->fileprg($form, $redirectUrl, true);

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            $this->getEventManager()->trigger('getForm', $this, [
                'form' => $form,
                'documentClass' => $this->getDocumentClass(),
                'id' => $id,
                'entity' => $entity,
            ]);

            return [
                'entityForm' => $form,
                'entity' => $entity,
            ];
        }

        $this->getEventManager()->trigger('edit', $this, [
            'form' => $form,
            'entityClass' => $this->getDocumentClass(),
            'id' => $id,
            'entity' => $entity,
        ]);

        $savedEntity = $this->getDocumentService()->save($form, $entity);

        if (! $savedEntity) {
            return [
                'entityForm' => $form,
                'entity' => $entity,
            ];
        }

        $this->flashMessenger()->addSuccessMessage($this->getServiceLocator()
            ->get('translator')
            ->translate($this->successEditMessage));

        return $this->redirect()->toRoute($this->getActionRoute('list'), [], true);
    }

    public function deleteAction()
    {
        $id = $this->getEvent()
            ->getRouteMatch()
            ->getParam('id', 0);

        $redirectUrl = $this->url()->fromRoute($this->getActionRoute(), [], true);
        $prg = $this->prg($redirectUrl, true);

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            $em = $this->getDocumentManager();
            $objRepository = $em->getRepository($this->getDocumentClass());
            $entity = $objRepository->find($id);

            return [
                'entity' => $entity,
            ];
        }

        $post = $prg;

        $em = $this->getDocumentManager();
        $objRepository = $em->getRepository($this->getDocumentClass());
        $entity = $objRepository->find($id);

        if ($this->validateDelete($post)) {
            if ($this->getDocumentService()->delete($entity)) {
                $this->flashMessenger()->addSuccessMessage($this->getServiceLocator()
                    ->get('translator')
                    ->translate($this->successDeleteMessage));

                return $this->redirect()->toRoute($this->getActionRoute('list'), [], true);
            }
        }

        $this->flashMessenger()->addErrorMessage($this->getServiceLocator()
            ->get('translator')
            ->translate($this->errorDeleteMessage));

        return [
            'entity' => $entity,
        ];
    }

}
