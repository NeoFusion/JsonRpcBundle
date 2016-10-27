<?php

namespace NeoFusion\JsonRpcBundle\Tests\Functional\app;

use NeoFusion\JsonRpcBundle\NeoFusionJsonRpcBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new FrameworkBundle(),
            new NeoFusionJsonRpcBundle()
        );

        return $bundles;
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir().'/neofusion_jsonrpc_test/cache';
    }

    public function getLogDir()
    {
        return sys_get_temp_dir() . '/neofusion_jsonrpc_test/cache/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yml');
    }
}
