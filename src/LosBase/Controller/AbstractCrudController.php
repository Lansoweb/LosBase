<?php

/**
 * Abstract CRUD Controller
 *
 * @package   LosBase\Controller
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright Copyright (c) 2011-2013 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase\Controller;

use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Stdlib\Hydrator\ClassMethods;
use Doctrine\ORM\EntityManager;
use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity as DoctrineHydrator;

/**
 * Abstract CRUD Controller
 *
 * @package   LosBase\Controller
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright Copyright (c) 2011-2013 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
abstract class AbstractCrudController extends AbstractActionController
{

    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Entity Service
     *
     * @var mixed
     */
    protected $entityService;

    /**
     * Sets the EntityManager
     *
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Retorna o EntityManager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()
                ->get('doctrine.entitymanager.orm_default');
        }

        return $this->em;
    }

    /**
     * Retorna o serviço da entidade
     *
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function getEntityService()
    {
        if (null == $this->entityService) {
            $entityServiceClass = $this->getEntityServiceClass();
            if (!class_exists($entityServiceClass)) {
                throw new \RuntimeException("Classe $entityServiceClass inexistente!");
            }
            $this->entityService = new $entityServiceClass();
            $this->entityService->setServiceLocator($this->getServiceLocator());
        }

        return $this->entityService;
    }

    /**
     * Nome da rota raiz do controlador
     */
    abstract public function getRouteName();

    abstract public function getEntityClass();

    abstract public function getEntityServiceClass();

    /**
     * Campo default para fazer o sort das entidades
     */
    public function defaultSortBy()
    {
        return 'nome';
    }

    /**
     * Retorna a form para o cadastro da entidade
     */
    public function getForm($entityClass = null)
    {
        if (null === $entityClass) {
            $entityClass = $this->getEntityClass();
        }

        /*
         * @var $cache \Zend\Cache\Storage\Adapter\Filesystem
         * $cache = $this->getServiceLocator()->get('app_cache');
         * $key = 'form_' .str_replace('\\', '_', $entityClass);
         * if ($cache->hasItem($key)) { //return $cache->getItem($key); }
         */

        $builder = new \Zend\Form\Annotation\AnnotationBuilder();
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
        // $cache->addItem($key, $form);
        return $form;
    }

    /**
     * Lista as entidades, suporte a paginação, ordenação e busca
     */
    public function listaAction()
    {
        $pm = $this->getPluginManager();

        $page = $this->getRequest()->getQuery('page', '-1');
        $limit = $this->getRequest()->getQuery('limit', '0');
        $sort = $this->getRequest()->getQuery('sort', $this->defaultSortBy());
        $order = $this->getRequest()->getQuery('order', 'ASC');
        $search = $this->getRequest()->getQuery('search', 'false');

        if (empty($sort)) {
            $sort = $this->defaultSortBy();
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->add('select', 'e')
            ->add('from', $this->getEntityClass() . ' e')
            ->orderBy('e.' . $sort, $order);

        if ('true' == $search) {
            $field = $this->getRequest()->getQuery('searchField', $this->defaultSortBy());
            $oper = $this->getRequest()->getQuery('searchOper', 'eq');
            $por = $this->getRequest()->getQuery('searchString', '');
            if ($oper == 'cn') {
                // contém
                $qb->add('where', $qb->expr()
                    ->like("e." . $field, $qb->expr()
                    ->literal('%' . $por . '%')));
            } else {
                // igual
                $qb->add('where', 'e.' . $field . ' = ?1')->setParameter(1, $por);
            }
        }

        $q = $qb->getQuery();
        $q->useResultCache(true, 120, 'list_' . $this->getRouteName());
        $entities = $q->getResult();

        $total_entities = count($entities);

        if ($page >= 0 && $limit > 0) {

            $total = ceil($total_entities / $limit);
            if ($page > $total)
                $page = $total;
            $start = $limit * $page - $limit;
            if ($start < 0) {
                $start = 0;
            }

            $qb->setFirstResult($start);
            $qb->setMaxResults($limit);
            $q = $qb->getQuery();
            $q->useResultCache(true, 120);
            $entities = $q->getResult();
        } else {
            $page = 1;
            $total = 1;
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            return new JsonModel($this->createJsonResponse($entities, $page, $total, $total_entities));
            // exit;
        }

        return new ViewModel(array(
            'entities' => $entities,
            'flashMessages' => $this->flashMessenger()->getMessages()
        ));
    }

    /**
     * Cria uma resposta JSON da lista das entidades
     *
     * @param  array  $entities
     * @param  int    $page
     * @param  int    $total
     * @param  int    $total_entities
     * @return string
     */
    public function createJsonResponse($entities, $page, $total, $total_entities)
    {
        $ret = array();
        $ret['page'] = "$page";
        $ret['total'] = "$total";
        $ret['records'] = $total_entities;
        $i = 0;
        foreach ($entities as $entity) {
            $ret['rows'][$i]['cell'] = array(
                $entity->getId(),
                $entity->getNome()
            );
            $i ++;
        }

        return $ret;
        // return json_encode($ret);
    }

    /**
     * Retorna uma rota para a ação especificada ou a atual
     *
     * @param string $action
     */
    public function getActionRoute($action = null)
    {
        if (null == $action) {
            $action = $this->getEvent()
                ->getRouteMatch()
                ->getParam('action');
        }

        return $this->getRouteName() . '/' . $action;
    }

    /**
     * Insere ou Altera uma entidade
     */
    public function editAction()
    {
        $request = $this->getRequest();
        if ($request->getQuery()->get('redirect')) {
            $redirect = $request->getQuery()->get('redirect');
        } else {
            $redirect = false;
        }

        if (method_exists($this, 'getEditForm')) {
            $form = $this->getEditForm();
        } else {
            $form = $this->getForm();

            $uploaded = new \Zend\Form\Element\Hidden('uploaded');
            // $uploaded->setValue('');
            $form->add($uploaded, array(
                'priority' => - 100
            ));

            $submitElement = new \Zend\Form\Element\Button('submit');
            $submitElement->setAttributes(array(
                'type' => 'submit',
                'class' => 'btn btn-primary'
            ));
            $submitElement->setLabel('Salvar');
            $form->add($submitElement, array(
                'priority' => - 100
            ));

            $cancelarElement = new \Zend\Form\Element\Button('cancelar');
            $cancelarElement->setAttributes(array(
                'type' => 'button',
                'class' => 'btn',
                'onclick' => 'top.location="' . $this->url()
                    ->fromRoute($this->getActionRoute('lista')) . '"'
            ));
            $cancelarElement->setLabel('Cancelar');
            $form->add($cancelarElement, array(
                'priority' => - 100
            ));
        }

        $redirectUrl = $this->url()->fromRoute($this->getActionRoute()) . ($redirect ? '?redirect=' . $redirect : '');
        $prg = $this->prg($redirectUrl, true);

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            $id = $this->getEvent()
                ->getRouteMatch()
                ->getParam('id', 0);

            if ($id > 0) {
                $em = $this->getEntityManager();
                $objRepository = $em->getRepository($this->getEntityClass());
                $entity = $objRepository->find($id);
                if ($entity->getInputFilter() !== null) {
                    $form->setInputFilter($entity->getInputFilter());
                }
                else {
                    $entity->setInputFilter($form->getInputFilter());
                }
                $form->bind($entity);

                $idForm = new \Zend\Form\Element\Hidden('id');
                $idForm->setValue($id);
                $form->add($idForm, array(
                    'priority' => - 100
                ));
            } else {
                $classe = $this->getEntityClass();
                $entity = new $classe();
                $form->get('id')->setValue(0);
            }

            $this->getEventManager()->trigger('getForm', $this,
                array(
                    'form' => $form,
                    'entityClass' => $this->getEntityClass(),
                    'id' => $id,
                    'entity' => $entity
                ));

            return array(
                'entityForm' => $form,
                'redirect' => $redirect,
                'entity' => $entity,
            );
        }

        $post = $prg;

        $id = $post['id'];
        if ($id > 0) {
            $em = $this->getEntityManager();
            $objRepository = $em->getRepository($this->getEntityClass());

            $entity = $objRepository->find($id);
        } else {
            $classe = $this->getEntityClass();
            $entity = new $classe();
        }

        $this->getEventManager()->trigger('getForm', $this,
            array(
                'form' => $form,
                'entityClass' => $this->getEntityClass(),
                'id' => $id,
                'entity' => $entity,
                'post' => $post
            ));

        $savedEntity = $this->getEntityService()->save($form, $post, $entity);

        if (! $savedEntity) {
            return array(
                'entityForm' => $form,
                'redirect' => $redirect,
                'entity' => $entity
            );
        }

        $cacheDriver = $this->getEntityManager()
            ->getConfiguration()
            ->getQueryCacheImpl();
        $cacheDriver->delete('lista_' . $this->getRouteName());

        $entity = $savedEntity;

        $this->flashMessenger()->addMessage($this->getServiceLocator()
            ->get('translator')
            ->translate('Operação realizada com sucesso!'));

        return $this->redirect()->toRoute($this->getActionRoute('lista'));
    }

    public function indexAction()
    {
        return new ViewModel();
    }
}
