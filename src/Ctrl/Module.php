<?php

namespace Ctrl;

class Module
{
    public function onBootstrap($e)
    {
        $application = $e->getApplication();
        /** @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $application->getServiceManager();

        // Add initializer to Controller Service Manager that check if controllers needs entity manager injection
        $serviceManager->addInitializer(function ($instance) use ($serviceManager) {
            if (method_exists($instance, 'setEntityManager')) {
                $instance->setEntityManager($serviceManager->get('doctrine.entitymanager.orm_default'));
            }
        });

        $this->setPhpSettings($serviceManager);
        $this->setViewHelpers($serviceManager);
    }

    protected function setViewHelpers($serviceManager)
    {
        /** @var $viewManager \Zend\Mvc\View\Http\ViewManager */
        $viewManager = $serviceManager->get('ViewManager');
        if (method_exists($viewManager, 'getHelperManager')) {
            $viewManager->getHelperManager()
                ->setInvokableClass('CtrlJsLoader', 'Ctrl\CtrlJs\ViewHelper\CtrlJsLoader')
                ->setInvokableClass('CtrlFormInput', 'Ctrl\View\Helper\TwitterBootstrap\Form\CtrlFormInput')
                ->setInvokableClass('CtrlForm', 'Ctrl\View\Helper\TwitterBootstrap\Form\CtrlForm')
                ->setInvokableClass('CtrlButton', 'Ctrl\View\Helper\TwitterBootstrap\Form\CtrlButton')
                ->setInvokableClass('CtrlFormActions', 'Ctrl\View\Helper\TwitterBootstrap\Form\CtrlFormActions')
                ->setInvokableClass('PageTitle', 'Ctrl\View\Helper\TwitterBootstrap\PageTitle')
                ->setInvokableClass('FormatDate', 'Ctrl\View\Helper\FormatDate')
                ->setInvokableClass('OrderControls', 'Ctrl\View\Helper\TwitterBootstrap\OrderControls');
        }
    }

    protected function setPhpSettings($serviceManager)
    {
        $config      = $serviceManager->get('Configuration');
        $phpSettings = $config['phpSettings'];
        if($phpSettings) {
            foreach($phpSettings as $key => $value) {
                ini_set($key, $value);
            }
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../',
                ),
            ),
        );
    }
}
