<?php

namespace NeoFusion\JsonRpcBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    protected static function getKernelClass()
    {
        require_once __DIR__.'/app/AppKernel.php';

        return 'NeoFusion\JsonRpcBundle\Tests\Functional\app\AppKernel';
    }
}
