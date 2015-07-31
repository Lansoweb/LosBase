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
namespace LosBase\Controller\ORM;

use Zend\Stdlib\ResponseInterface as Response;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Paginator\Paginator;
use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity as DoctrineHydrator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\QueryBuilder;
use LosBase\ORM\Tools\Pagination\Paginator as LosPaginator;
use LosBase\Entity\EntityManagerAwareTrait;
use LosBase\Validator\NoEntityExists;
use LosBase\Validator\NoOtherEntityExists;
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
    use EntityManagerAwareTrait;

    /**
     * Entity Service
     *
     * @var mixed
     */
    private $entityService;

    protected $uniqueEntityMessage = null;

    /**
     * Retorna o serviço da entidade
     *
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function getEntityService()
    {
        if (null === $this->entityService) {
            $entityServiceClass = $this->getEntityServiceClass();
            if (! class_exists($entityServiceClass)) {
                throw new \RuntimeException("Classe $entityServiceClass inexistente!");
            }
            $this->entityService = new $entityServiceClass();
            $this->entityService->setServiceLocator($this->getServiceLocator());
        }

        return $this->entityService;
    }

    public function getEntityClass()
    {
        $module = $this->getModuleName();

        return "$module\Entity\\$module";
    }

    public function getEntityServiceClass()
    {
        $module = $this->getModuleName();

        return "$module\Service\\$module";
    }

    protected function getAddForm()
    {
        $form = $this->getForm();

        if ($this->uniqueField !== null) {
            $validator = new NoEntityExists([
                'object_repository' => $this->getEntityManager()->getRepository($this->getEntityClass()),
                'fields' => $this->uniqueField,
            ]);
            if ($this->uniqueEntityMessage !== null) {
                $validator->setMessage($this->uniqueEntityMessage, 'objectFound');
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
                'object_repository' => $this->getEntityManager()->getRepository($this->getEntityClass()),
                'fields' => $this->uniqueField,
                'id' => $this->getEvent()
                    ->getRouteMatch()
                    ->getParam('id', 0),
            ]);
            if ($this->uniqueEntityMessage !== null) {
                $validator->setMessage($this->uniqueEntityMessage, 'objectFound');
            }
            $form->getInputFilter()
                ->get($this->uniqueField)
                ->getValidatorChain()
                ->attach($validator);
        }

        return $form;
    }

    /**
     * Retorna a form para o cadastro da entidade
     */
    public function getForm($entityClass = null)
    {
        if (null === $entityClass) {
            $entityClass = $this->getEntityClass();
        }

        $builder = new AnnotationBuilder();
        $form = $builder->createForm($entityClass);

        $hasEntity = false;
        foreach ($form->getElements() as $element) {
            if (method_exists($element, 'setObjectManager')) {
                $element->setObjectManager($this->getEntityManager());
                $hasEntity = true;
            } elseif (method_exists($element, 'getProxy')) {
                $proxy = $element->getProxy();
                if (method_exists($proxy, 'setObjectManager')) {
                    $proxy->setObjectManager($this->getEntityManager());
                    $hasEntity = true;
                }
            }
        }

        if ($hasEntity) {
            $hydrator = new DoctrineHydrator($this->getEntityManager(), $entityClass);
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

    public function handleSearch(QueryBuilder $qb)
    {
    }

    /**
     * Lista as entidades, suporte a paginação, ordenação e busca
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

        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->add('select', 'e')
            ->add('from', $this->getEntityClass().' e')
            ->orderBy('e.'.$sort, $order)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $this->handleSearch($qb);

        $paginator = new Paginator(new DoctrinePaginator(new LosPaginator($qb, false)));
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

        $em = $this->getEntityManager();
        $objRepository = $em->getRepository($this->getEntityClass());
        $entity = $objRepository->find($id);

        return [
            'entity' => $entity
        ];
    }

    public function addAction()
    {
        if (method_exists($this, 'getAddForm')) {
            $form = $this->getAddForm();
        } else {
            $form = $this->getForm();
        }

        $classe = $this->getEntityClass();
        $entity = new $classe();

        $this->getEventManager()->trigger('getForm', $this, [
            'form' => $form,
            'entityClass' => $this->getEntityClass(),
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
                'entityClass' => $this->getEntityClass(),
                'entity' => $entity,
            ]);

            return [
                'entityForm' => $form,
                'entity' => $entity,
            ];
        }

        $this->getEventManager()->trigger('add', $this, [
            'form' => $form,
            'entityClass' => $this->getEntityClass(),
            'entity' => $entity,
        ]);

        $savedEntity = $this->getEntityService()->save($form, $entity);

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

        $em = $this->getEntityManager();
        $objRepository = $em->getRepository($this->getEntityClass());
        $entity = $objRepository->find($id);

        $this->getEventManager()->trigger('getForm', $this, [
            'form' => $form,
            'entityClass' => $this->getEntityClass(),
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
                'entityClass' => $this->getEntityClass(),
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
            'entityClass' => $this->getEntityClass(),
            'id' => $id,
            'entity' => $entity,
        ]);

        $savedEntity = $this->getEntityService()->save($form, $entity);

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
            $em = $this->getEntityManager();
            $objRepository = $em->getRepository($this->getEntityClass());
            $entity = $objRepository->find($id);

            return [
                'entity' => $entity,
            ];
        }

        $post = $prg;

        $em = $this->getEntityManager();
        $objRepository = $em->getRepository($this->getEntityClass());
        $entity = $objRepository->find($id);

        if ($this->validateDelete($post)) {
            if ($this->getEntityService()->delete($entity)) {
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
