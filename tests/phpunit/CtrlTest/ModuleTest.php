<?php

namespace CtrlTest;

use Ctrl\View\Helper\Navigation\Navigation;
use Ctrl\Mvc\Controller\Plugin\Redirect;

class ModuleTest extends ApplicationTestCase
{
    public function testCanRetrieveDomainServiceLoader()
    {
        $loader = $this->getApplicationServiceManager()->get('DomainServiceLoader');
        $this->assertInstanceOf('\Zend\ServiceManager\ServiceManager', $loader);
    }

    public function testHasOverridenDefaultRedirectControllerPlugin()
    {
        $loader = $this->getApplicationServiceManager()->get('ControllerPluginManager');
        $redirectPlugin = $loader->get('Redirect');
        $this->assertInstanceOf(Redirect::class, $redirectPlugin);
    }

    public function testHasOverridenDefaultNavigationViewHelper()
    {
        $loader = $this->getApplicationServiceManager()->get('Navigation');
        $navHelper = $loader->get('Navigation');
        $this->assertInstanceOf(Navigation::class, $navHelper);
    }
}
