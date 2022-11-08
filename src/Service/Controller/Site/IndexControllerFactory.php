<?php
namespace SruSrwMaker\Service\Controller\Site;

use Interop\Container\ContainerInterface;
use SruSrwMaker\Controller\Site\IndexController;
use Laminas\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $omekaModules = $serviceLocator->get('Omeka\ModuleManager');
        return new IndexController($omekaModules);
    }


}