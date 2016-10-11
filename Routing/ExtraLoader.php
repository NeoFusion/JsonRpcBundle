<?php

namespace NeoFusion\JsonRpcBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ExtraLoader extends Loader
{
    private $loaded = false;
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "extra" loader twice');
        }

        $config = $this->container->getParameter('neofusion_jsonrpc');

        $routes = new RouteCollection();
        foreach ($config['routing'] as $name => $params) {
            $route = new Route($params['path'], array(
                '_controller' => 'NeoFusionJsonRpcBundle:Server:process',
                'route'       => $name
            ), array(), array(), '', array(), array('POST'));
            $routes->add($name, $route);
        }

        $this->loaded = true;

        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'extra' === $type;
    }
}
