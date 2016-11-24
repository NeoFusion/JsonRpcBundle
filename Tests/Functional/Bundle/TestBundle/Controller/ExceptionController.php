<?php

namespace NeoFusion\JsonRpcBundle\Tests\Functional\Bundle\TestBundle\Controller;

use NeoFusion\JsonRpcBundle\Utils\JsonRpcError;
use NeoFusion\JsonRpcBundle\Utils\JsonRpcException;

class ExceptionController
{
    public function getException()
    {
        throw new JsonRpcException();
    }

    public function getExceptionWithMessage()
    {
        throw new JsonRpcException(0, 'Unknown error');
    }

    public function getExtendedException()
    {
        throw new JsonRpcException(JsonRpcError::CODE_INVALID_PARAMS, 'Value out of range', 'Additional data');
    }
}
