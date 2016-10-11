<?php

namespace NeoFusion\JsonRpcBundle\Utils;

/**
 * @link http://www.jsonrpc.org/specification
 */
class JsonRpcBatchResponse implements JsonRpcInterface
{
    private $responses = array();

    public function addResponse(JsonRpcSingleResponse $response)
    {
        $this->responses[] = $response;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = array();
        /** @var JsonRpcSingleResponse $response */
        foreach ($this->responses as $response) {
            $result[] = $response->toArray();
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->responses) === 0;
    }
}
