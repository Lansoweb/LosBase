<?php
namespace LosBaseTest\Controller;

use LosBaseTest\Assets\Controller\CrudController;
use Zend\Http\PhpEnvironment\Response;
use LosBaseTest\Assets\Entity\TestEntity;
use LosBaseTest\TestCase;
use LosBaseTest\ServiceManagerTestCase;

class AbstractCrudControllerTest extends TestCase
{
    private $controller;

    private $pluginManager;

    private $pluginManagerPlugins = [];

    protected function setUp()
    {
        parent::setUp();
        $this->controller = new CrudController();

        $sm = new ServiceManagerTestCase();
        $this->controller->setServiceLocator($sm->getServiceManager());

        $pluginManager = $this->getMock('Zend\Mvc\Controller\PluginManager', array(
            'get',
        ));

        $pluginManager->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(array(
            $this,
            'helperMockCallbackPluginManagerGet',
        )));

        $this->pluginManager = $pluginManager;
        $this->controller->setPluginManager($pluginManager);
    }

    protected function tearDown()
    {
        $this->controller = null;

        parent::tearDown();
    }

    /**
     * Tests AbstractCrudController->getEntityService()
     */
    public function testGetEntityService()
    {
        $this->assertInstanceOf('LosBaseTest\Assets\Service\TestService', $this->controller->getEntityService());
    }

    /**
     * Tests AbstractCrudController->getRouteName()
     */
    public function testGetRouteName()
    {
        $this->assertSame('losbasetest', $this->controller->getRouteName());
    }

    /**
     * Tests AbstractCrudController->getEntityClass()
     */
    public function testGetEntityClass()
    {
        $this->assertSame('LosBaseTest\Assets\Entity\TestEntity', $this->controller->getEntityClass());
    }

    /**
     * Tests AbstractCrudController->getEntityServiceClass()
     */
    public function testGetEntityServiceClass()
    {
        $this->assertSame('LosBaseTest\Assets\Service\TestService', $this->controller->getEntityServiceClass());
    }

    /**
     * Tests AbstractCrudController->getForm()
     */
    public function testGetForm()
    {
        $this->helperAddUrlPlugin();

        $form = $this->controller->getForm();
        $this->assertSame('formTest', $form->getName());
        $this->assertTrue($form->has('name'));
        $this->assertTrue($form->has('csrf'));
        $this->assertTrue($form->has('submit'));
        $this->assertTrue($form->has('cancelar'));
    }

    /**
     * Tests AbstractCrudController->handleSearch()
     */
    public function testHandleSearch()
    {
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->controller->handleSearch($qb);
    }

    /**
     * Tests AbstractCrudController->listAction()
     */
    public function testListAction()
    {
        $this->helperAddUrlPlugin();
        $this->helperAddParamsPlugin();
        $this->addEntityManager();

        $ret = $this->controller->listAction();
    }

    /**
     * Tests AbstractCrudController->getActionRoute()
     */
    public function testGetActionRoute()
    {
        $this->assertSame('losbasetest/list', $this->controller->getActionRoute('list'));
    }

    /**
     * Tests AbstractCrudController->addAction()
     */
    public function testAddAction()
    {
        $this->helperAddUrlPlugin();
        $this->helperAddParamsPlugin();

        $event = $this->getMockBuilder('Zend\Mvc\MvcEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->any())
            ->method('getRouteMatch')
            ->will($this->returnValue($event));
        $event->expects($this->any())
            ->method('getParam')
            ->will($this->returnValue('add'));
        $this->controller->setEvent($event);

        $ret = $this->controller->addAction();
        $this->assertArrayHasKey('entity', $ret);
        $this->assertArrayHasKey('entityForm', $ret);
        $this->assertInstanceOf('LosBaseTest\Assets\Entity\TestEntity', $ret['entity']);
        $this->assertInstanceOf('LosBase\Form\AbstractForm', $ret['entityForm']);
        $this->assertSame('name', $ret['entityForm']->get('name')
            ->getName());
    }

    /**
     * Tests AbstractCrudController->editAction()
     */
    public function testEditAction()
    {
        $this->helperAddUrlPlugin();
        $this->helperAddParamsPlugin();
        $this->addEntityManager();

        $event = $this->getMockBuilder('Zend\Mvc\MvcEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->any())
            ->method('getRouteMatch')
            ->will($this->returnValue($event));
        $event->expects($this->any())
            ->method('getParam')
            ->will($this->returnValue('add'));
        $this->controller->setEvent($event);

        $ret = $this->controller->editAction();
        $this->assertArrayHasKey('entity', $ret);
        $this->assertArrayHasKey('entityForm', $ret);
        $this->assertInstanceOf('LosBaseTest\Assets\Entity\TestEntity', $ret['entity']);
        $this->assertInstanceOf('LosBase\Form\AbstractForm', $ret['entityForm']);
        $this->assertSame('name', $ret['entityForm']->get('name')
            ->getName());
    }

    /**
     * Tests AbstractCrudController->deleteAction()
     */
    public function testDeleteAction()
    {
        $this->helperAddUrlPlugin();
        $this->helperAddParamsPlugin();
        $this->helperAddFlashMessengerPlugin();
        $this->addEntityManager();

        $event = $this->getMockBuilder('Zend\Mvc\MvcEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->any())
            ->method('getRouteMatch')
            ->will($this->returnValue($event));
        $event->expects($this->any())
            ->method('getParam')
            ->will($this->returnValue('add'));
        $this->controller->setEvent($event);

        $translator = $this->getMockBuilder('Zend\I18n\Translator\Translator')
            ->disableOriginalConstructor()
            ->getMock();
        $translator->expects($this->any())
            ->method('translate')
            ->will($this->returnValue(true));
        $this->controller->getServiceLocator()->setService('translator', $translator);

        $ret = $this->controller->deleteAction();
        $this->assertArrayHasKey('entity', $ret);
        $this->assertInstanceOf('LosBaseTest\Assets\Entity\TestEntity', $ret['entity']);
    }

    /**
     * Tests AbstractCrudController->indexAction()
     */
    public function testIndexAction()
    {
        $this->assertEmpty($this->controller->indexAction());
    }

    public function helperMockCallbackPluginManagerGet($key)
    {
        return (array_key_exists($key, $this->pluginManagerPlugins)) ? $this->pluginManagerPlugins[$key] : null;
    }

    public function helperAddUrlPlugin($return = '')
    {
        $response = new Response();
        $url = $this->getMock('Zend\Mvc\Controller\Plugin\Url', array(
            'fromRoute',
        ));
        $url->expects($this->any())
            ->method('fromRoute')
            ->will($this->returnValue($return));

        $this->pluginManagerPlugins['url'] = $url;
    }

    public function helperAddParamsPlugin($post = [], $query = [])
    {
        $params = $this->getMock('Zend\Mvc\Controller\Plugin\Params');
        $params->expects($this->any())
            ->method('__invoke')
            ->will($this->returnSelf());
        $params->expects($this->any())
            ->method('fromPost')
            ->will($this->returnCallback(function ($key, $default) use ($post) {
            return $post ?: $default;
        }));
        $params->expects($this->any())
            ->method('fromQuery')
            ->will($this->returnCallback(function ($key, $default) use ($query) {
            return $query ?: $default;
        }));
        $this->pluginManagerPlugins['params'] = $params;
    }

    public function helperAddFlashMessengerPlugin()
    {
        $flash = $this->getMockBuilder('Zend\Mvc\Controller\Plugin\FlashMessenger')
            ->disableOriginalConstructor()
            ->getMock();
        $flash->expects($this->any())
            ->method('addErrorMessage')
            ->will($this->returnValue($flash));

        $this->pluginManagerPlugins['flashMessenger'] = $flash;
    }

    public function addEntityManager()
    {
        $entity = new TestEntity();
        $rep = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $rep->expects($this->any())
            ->method('find')
            ->will($this->returnValue($entity));

        $emConf = $this->getMockBuilder('Doctrine\ORM\Configuration')
            ->disableOriginalConstructor()->getMock();
        $emConf->expects($this->any())
            ->method('getDefaultQueryHints')
            ->will($this->returnValue([]));

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($rep));
        $em->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($emConf));
        $em->expects($this->any())
            ->method('createQuery')
            ->will($this->returnValue(new \Doctrine\ORM\Query($em)));

        $qb = $this->getMock('Doctrine\ORM\QueryBuilder', ['add', 'orderBy', 'select', 'setFirstResult', 'setMaxResults'], [$em]);
        $qb->expects($this->any())
        ->method('add')
        ->will($this->returnValue($qb));
        $qb->expects($this->any())
        ->method('orderBy')
        ->will($this->returnValue($qb));
        $qb->expects($this->any())
        ->method('select')
        ->will($this->returnValue($qb));
        $qb->expects($this->any())
        ->method('setFirstResult')
        ->will($this->returnValue($qb));
        $qb->expects($this->any())
        ->method('setMaxResults')
        ->will($this->returnValue($qb));

        $em->expects($this->any())
        ->method('createQueryBuilder')
        ->will($this->returnValue($qb));

        $this->controller->setEntityManager($em);
    }
}
