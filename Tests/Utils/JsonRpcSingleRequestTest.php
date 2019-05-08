<?php

namespace NeoFusion\JsonRpcBundle\Tests\Utils;

use NeoFusion\JsonRpcBundle\Utils\JsonRpcSingleRequest;
use PHPUnit\Framework\TestCase;

class JsonRpcSingleRequestTest extends TestCase
{
    public function testValidRequest()
    {
        $jsonRpcSingleRequest = new JsonRpcSingleRequest('2.0', 'create', array(42), 1);

        $this->assertEquals('2.0', $jsonRpcSingleRequest->getJsonrpc());
        $this->assertEquals('create', $jsonRpcSingleRequest->getMethod());
        $this->assertEquals(array(42), $jsonRpcSingleRequest->getParams());
        $this->assertEquals(1, $jsonRpcSingleRequest->getId());

        $this->assertTrue($jsonRpcSingleRequest->isValid());
        $this->assertFalse($jsonRpcSingleRequest->isNotification());
    }

    public function testNotification()
    {
        $jsonRpcSingleRequest = new JsonRpcSingleRequest('2.0', 'create', array(42), null);
        $this->assertTrue($jsonRpcSingleRequest->isValid());
        $this->assertTrue($jsonRpcSingleRequest->isNotification());
    }

    public function testInvalidParams()
    {
        $jsonRpcSingleRequest = new JsonRpcSingleRequest('2.0', 'create', 42, 1);
        $this->assertFalse($jsonRpcSingleRequest->isValid());
    }

    public function testInvalidId()
    {
        $jsonRpcSingleRequest = new JsonRpcSingleRequest('2.0', 'create', array(42), array(1));
        $this->assertFalse($jsonRpcSingleRequest->isValid());
    }
}
