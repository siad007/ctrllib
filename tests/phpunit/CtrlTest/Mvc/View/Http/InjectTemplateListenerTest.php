<?php

namespace CtrlTest\Mvc\View\Http;

use CtrlTest\ApplicationTestCase;
use Ctrl\Mvc\View\Http\InjectTemplateListener;
use Zend\Router\RouteMatch;
use Zend\Router\RouterFactory;

class InjectTemplateListenerTest extends ApplicationTestCase
{
    /**
     * @var InjectTemplateListener
     */
    protected $listener;

    protected function setup()
    {
        $this->listener = new InjectTemplateListener();
    }

    protected function breakdown()
    {
        $this->listener = null;
    }

    public function testDoesInjectTemplateWhenInModuleNamespace()
    {
        $e = new \Zend\Mvc\MvcEvent();
        $routerFactory = new RouterFactory();

        $routeMatch = new RouteMatch(array(
            '__NAMESPACE__' => 'Ctrl\Module\Test\Controller',
            'controller' => 'Index',
            'action' => 'index'
        ));
        $routeMatch->setMatchedRouteName('test');
        $e->setRouteMatch($routeMatch)
            ->setController('Index')
            ->setControllerClass('Ctrl\Module\Test\Controller\Index')
            ->setTarget('Ctrl\Module\Test\Controller\Index');
        $e->setResult(new \Zend\View\Model\ViewModel());

        $this->listener->injectTemplate($e);
        $this->assertEquals('ctrl/test/index/index', $e->getResult()->getTemplate());
    }

    public function testDoesNotInjectTemplateWhenInModuleNamespace()
    {
        $e = new \Zend\Mvc\MvcEvent();
        $routerFactory = new RouterFactory();

        $routeMatch = new \Zend\Router\Http\RouteMatch(array(
            '__NAMESPACE__' => 'App\Controller',
            'controller' => 'Index',
            'action' => 'index'
        ));
        $routeMatch->setMatchedRouteName('test');
        $e->setRouteMatch($routeMatch)
            ->setController('Index')
            ->setControllerClass('App\Controller\Index')
            ->setTarget('App\Controller\Index');
        $e->setResult(new \Zend\View\Model\ViewModel());

        $this->listener->injectTemplate($e);
        $this->assertEquals('', $e->getResult()->getTemplate());
    }
}
