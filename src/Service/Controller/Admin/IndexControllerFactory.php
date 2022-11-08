<?php
namespace SruSrwMaker\Service\Controller\Admin;

use Interop\Container\ContainerInterface;
use SruSrwMaker\Controller\Admin\IndexController;
use Laminas\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $config = $serviceLocator->get('Config');
        return new IndexController($config);
    }


}