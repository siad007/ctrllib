<?php

namespace Ctrl\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Config;
use Zend\EventManager\EventManagerAwareInterface;
use Ctrl\ServiceManager\EntityManagerAwareInterface;

class DomainServiceLoaderFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface|ServiceManager $serviceLocator
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName = '', array $options = null)
    {
        $config = $container->get('Configuration');
        $serviceConfig = new Config(
            isset($config['domain_services']) ? $config['domain_services'] : array()
        );

        $domainServiceFactory = new ServiceManager($serviceConfig->toArray());
        // $container->addPeeringServiceManager($domainServiceFactory);

        $domainServiceFactory->addInitializer(function ($instance) use ($container) {
            /*
            if ($instance instanceof ServiceLocatorAwareInterface)
                $instance->setServiceLocator($serviceLocator->get('Zend\ServiceManager\ServiceLocatorInterface'));
            */
            if ($instance instanceof EventManagerAwareInterface)
                $instance->setEventManager($container->get('EventManager'));

            if ($instance instanceof EntityManagerAwareInterface) {
                try {
                    $instance->setEntityManager($container->get('EntityManager'));
                } catch (\Zend\ServiceManager\Exception\ServiceNotFoundException $e) {
                    // no entitymanager set
                    // TODO: log
                }
            }
        });

        return $domainServiceFactory;
    }
}
