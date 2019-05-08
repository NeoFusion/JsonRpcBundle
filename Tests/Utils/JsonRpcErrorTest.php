<?php

namespace NeoFusion\JsonRpcBundle\Tests\Utils;

use NeoFusion\JsonRpcBundle\Utils\JsonRpcError;
use PHPUnit\Framework\TestCase;

class JsonRpcErrorTest extends TestCase
{
    public function testCode()
    {
        $jsonRpcError = new JsonRpcError(JsonRpcError::CODE_SERVER_ERROR);
        $this->assertEquals(array(
            'code'    => -32000,
            'message' => 'Server error'
        ), $jsonRpcError->toArray());
    }

    public function testMessage()
    {
        $jsonRpcError = new JsonRpcError(JsonRpcError::CODE_SERVER_ERROR, 'Custom message');
        $this->assertEquals(array(
            'code'    => -32000,
            'message' => 'Custom message'
        ), $jsonRpcError->toArray());
    }

    public function testData()
    {
        $jsonRpcError = new JsonRpcError(JsonRpcError::CODE_SERVER_ERROR, null, 'Custom data');
        $this->assertEquals(array(
            'code'    => -32000,
            'message' => 'Server error',
            'data'    => 'Custom data'
        ), $jsonRpcError->toArray());
    }
}
