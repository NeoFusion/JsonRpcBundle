<?php

namespace NeoFusion\JsonRpcBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ExtraLoader extends Loader
{
    private $loaded = false;
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "extra" loader twice');
        }

        $routes = new RouteCollection();
        foreach ($this->config['routing'] as $name => $params) {
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
