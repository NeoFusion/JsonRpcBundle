<?php

namespace NeoFusion\JsonRpcBundle\Tests\Functional\Controller;

use NeoFusion\JsonRpcBundle\Tests\Functional\WebTestCase;

class ServerControllerTest extends WebTestCase
{
    /**
     * @dataProvider requestsProvider
     *
     * @param string $jsonRequest
     * @param string $jsonResponse
     */
    public function testRequest($jsonRequest, $jsonResponse)
    {
        $client = static::createClient();
        $client->request('POST', '/api/test', array(), array(), array(), $jsonRequest);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($jsonResponse, $client->getResponse()->getContent());
    }

    public function requestsProvider()
    {
        return array(
            // Rpc call with positional parameters
            array(
                '{"jsonrpc": "2.0", "method": "subtract", "params": [42, 23], "id": 1}',
                '{"jsonrpc":"2.0","result":19,"id":1}'
            ),
            array(
                '{"jsonrpc": "2.0", "method": "subtract", "params": [23, 42], "id": 2}',
                '{"jsonrpc":"2.0","result":-19,"id":2}'
            ),
            // Rpc call with named parameters
            array(
                '{"jsonrpc": "2.0", "method": "subtract", "params": {"subtrahend": 23, "minuend": 42}, "id": 3}',
                '{"jsonrpc":"2.0","result":19,"id":3}'
            ),
            array(
                '{"jsonrpc": "2.0", "method": "subtract", "params": {"minuend": 42, "subtrahend": 23}, "id": 4}',
                '{"jsonrpc":"2.0","result":19,"id":4}'
            ),
            // A Notification
            array(
                '{"jsonrpc": "2.0", "method": "update", "params": [1,2,3,4,5]}',
                ''
            ),
            array(
                '{"jsonrpc":"2.0","method":"foobar"}',
                ''
            ),
            // Rpc call of non-existent method
            array(
                '{"jsonrpc":"2.0","method":"foobar","id":"1"}',
                '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method not found"},"id":"1"}'
            ),
            // Rpc call with invalid JSON
            array(
                '{"jsonrpc":"2.0","method":"foobar,"params":"bar","baz]',
                '{"jsonrpc":"2.0","error":{"code":-32700,"message":"Parse error"},"id":null}'
            ),
            // Rpc call with invalid Request object
            array(
                '{"jsonrpc":"2.0","method":1,"params":"bar"}',
                '{"jsonrpc":"2.0","error":{"code":-32600,"message":"Invalid Request"},"id":null}'
            ),
            // Rpc call Batch, invalid JSON
            array(
                '['
                . '{"jsonrpc": "2.0", "method": "sum", "params": [1,2,4], "id": "1"},'
                . '{"jsonrpc": "2.0", "method"'
                . ']',
                '{"jsonrpc":"2.0","error":{"code":-32700,"message":"Parse error"},"id":null}'
            ),
            // Rpc call with an empty Array
            array(
                '[]',
                '{"jsonrpc":"2.0","error":{"code":-32600,"message":"Invalid Request"},"id":null}'
            ),
            // Rpc call with an invalid Batch (but not empty)
            array(
                '[1]',
                '['
                . '{"jsonrpc":"2.0","error":{"code":-32600,"message":"Invalid Request"},"id":null}'
                . ']'
            ),
            // Rpc call with invalid Batch
            array(
                '[1,2,3]',
                '['
                . '{"jsonrpc":"2.0","error":{"code":-32600,"message":"Invalid Request"},"id":null},'
                . '{"jsonrpc":"2.0","error":{"code":-32600,"message":"Invalid Request"},"id":null},'
                . '{"jsonrpc":"2.0","error":{"code":-32600,"message":"Invalid Request"},"id":null}'
                . ']'
            ),
            // Rpc call Batch
            array(
                '['
                . '{"jsonrpc": "2.0", "method": "sum", "params": [1,2,4], "id": "1"},'
                . '{"jsonrpc": "2.0", "method": "notify_hello", "params": [7]},'
                . '{"jsonrpc": "2.0", "method": "subtract", "params": [42,23], "id": "2"},'
                . '{"foo": "boo"},'
                . '{"jsonrpc": "2.0", "method": "foo.get", "params": {"name": "myself"}, "id": "5"},'
                . '{"jsonrpc": "2.0", "method": "getData", "id": "9"}'
                . ']',
                '['
                . '{"jsonrpc":"2.0","result":7,"id":"1"},'
                . '{"jsonrpc":"2.0","result":19,"id":"2"},'
                . '{"jsonrpc":"2.0","error":{"code":-32600,"message":"Invalid Request"},"id":null},'
                . '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method not found"},"id":"5"},'
                . '{"jsonrpc":"2.0","result":["hello",5],"id":"9"}'
                . ']'
            ),
            // Rpc call Batch (all notifications)
            array(
                '['
                . '{"jsonrpc": "2.0", "method": "notify_sum", "params": [1,2,4]},'
                . '{"jsonrpc": "2.0", "method": "notify_hello", "params": [7]}'
                . ']',
                ''
            )
        );
    }

    /**
     * @dataProvider exceptionRequestsProvider
     *
     * @param string $jsonRequest
     * @param string $jsonResponse
     */
    public function testException($jsonRequest, $jsonResponse)
    {
        $client = static::createClient();
        $client->request('POST', '/api/exception', array(), array(), array(), $jsonRequest);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($jsonResponse, $client->getResponse()->getContent());
    }

    public function exceptionRequestsProvider()
    {
        return array(
            // Service not found
            array(
                '{"jsonrpc": "2.0", "method": "serviceNotFound", "id": 1}',
                '{"jsonrpc":"2.0","error":{"code":-32000,"message":"Server error"},"id":1}'
            ),
            // Method not found
            array(
                '{"jsonrpc": "2.0", "method": "methodNotFound", "id": 1}',
                '{"jsonrpc":"2.0","error":{"code":-32601,"message":"Method not found"},"id":1}'
            ),
            // Server error
            array(
                '{"jsonrpc": "2.0", "method": "getException", "id": 1}',
                '{"jsonrpc":"2.0","error":{"code":-32000,"message":"Server error"},"id":1}'
            ),
            // Server error with data
            array(
                '{"jsonrpc": "2.0", "method": "getExceptionWithMessage", "id": 1}',
                '{"jsonrpc":"2.0","error":{"code":-32000,"message":"Server error","data":"Unknown error"},"id":1}'
            ),
            // Invalid params
            array(
                '{"jsonrpc": "2.0", "method": "getExtendedException", "id": 1}',
                '{"jsonrpc":"2.0","error":{"code":-32602,"message":"Invalid params","data":{"code":-32602,"message":"Value out of range","data":"Additional data"}},"id":1}'
            ),
        );
    }
}
