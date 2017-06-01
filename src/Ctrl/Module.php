<?php

namespace Ctrl;

use Zend\ServiceManager\ServiceLocatorInterface;
use Ctrl\Mvc\View\Http\InjectTemplateListener;
use Zend\Mvc\MvcEvent;
use Ctrl\EntityManager\PostLoadSubscriber;

class Module
{
    public function onBootstrap($e)
    {
        $application = $e->getApplication();
        /** @var $serviceManager \Zend\ServiceManager\ServiceManager */
        $serviceManager = $application->getServiceManager();

        $this->initModules($serviceManager);
        $this->setPhpSettings($serviceManager);
        $this->initDoctrine($serviceManager);

        $serviceManager->setAlias('EntityManager', 'doctrine.entitymanager.orm_default');
    }

    protected function initDoctrine(ServiceLocatorInterface $serviceManager)
    {
        /** @var $entityManager \Doctrine\ORM\EntityManager */
        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');
        $entityManager->getEventManager()->addEventListener(
            array(\Doctrine\ORM\Events::postLoad),
            new PostLoadSubscriber($serviceManager)
        );
    }

    protected function initModules(ServiceLocatorInterface $serviceManager)
    {
        $injectTemplateListener = new InjectTemplateListener();

        $eventManager = $serviceManager->get('Application')->getEventManager();
        $sharedEvents = $eventManager->getSharedManager();
        $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($injectTemplateListener, 'injectTemplate'), -81);
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
        return array(
            'phpSettings' => array(
                'date.timezone' => 'UTC',
            ),
            'controller_plugins' => array(
                'invokables' => array(
                    'CtrlRedirect' => Mvc\Controller\Plugin\Redirect::class,
                ),
                'aliases' => array(
                    'Redirect' => 'CtrlRedirect'
                )
            ),
            'view_helpers' => array(
                'invokables' => array(
                    'CtrlNavigation' => View\Helper\Navigation\Navigation::class,
                    'FormatDate' => View\Helper\FormatDate::class,
                    'CtrlJsLoader' => CtrlJs\ViewHelper\CtrlJsLoader::class,
                    'PageTitle' => View\Helper\TwitterBootstrap\PageTitle::class,
                    'CtrlFormInput' => View\Helper\TwitterBootstrap\Form\CtrlFormInput::class,
                    'CtrlForm' => View\Helper\TwitterBootstrap\Form\CtrlForm::class,
                    'CtrlFormErrors' => View\Helper\TwitterBootstrap\Form\CtrlFormErrors::class,
                    'CtrlButton' => View\Helper\TwitterBootstrap\Form\CtrlButton::class,
                    'CtrlFormActions' => View\Helper\TwitterBootstrap\Form\CtrlFormActions::class,
                    'OrderControls' => View\Helper\TwitterBootstrap\OrderControls::class,
                    'ButtonBar' => View\Helper\TwitterBootstrap\ButtonBar::class,
                    'ButtonGroup' => View\Helper\TwitterBootstrap\ButtonGroup::class,
                ),
                'aliases' => array(
                    'Navigation' => 'CtrlNavigation'
                )
            ),
            'app_log' => array(
                'class' => Log\Logger::class,
                'writers' => array(
                    array (
                        'writer' => 'stream',
                        'options' => array('stream' => 'php://stderr'),
                    ),
                ),
                'registerErrorHandler' => false,
                'registerExceptionHandler' => false,
            ),
        );
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

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'DomainServiceLoader'       => Service\DomainServiceLoaderFactory::class,
                'CtrlAcl'                   => Permissions\AclFactory::class,
                'Log'                       => Log\LogFactory::class,
            ),
            'aliases' => array(
                'Acl' => 'CtrlAcl'
            )
        );
    }
}
