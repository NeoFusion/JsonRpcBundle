<?php

namespace NeoFusion\JsonRpcBundle\Utils;

/**
 * @link http://www.jsonrpc.org/specification
 */
class JsonRpcSingleResponse implements JsonRpcInterface
{
    /**
     * @var string A String specifying the version of the JSON-RPC protocol.
     *             MUST be exactly "2.0".
     */
    private $jsonrpc = '2.0';

    /**
     * @var mixed
     * 
     * This member is REQUIRED on success.
     * This member MUST NOT exist if there was an error invoking the method.
     * The value of this member is determined by the method invoked on the Server.
     */
    private $result;

    /**
     * @var JsonRpcError
     * 
     * This member is REQUIRED on error.
     * This member MUST NOT exist if there was no error triggered during invocation.
     * The value for this member MUST be an Object as defined in section 5.1.
     */
    private $error;

    /**
     * @var int
     *
     * This member is REQUIRED.
     * It MUST be the same as the value of the id member in the Request Object.
     * If there was an error in detecting the id in the Request object (e.g. Parse error/Invalid Request), it MUST be Null.
     */
    private $id;

    /**
     * @param mixed|null        $result
     * @param JsonRpcError|null $error
     * @param int|null          $id
     */
    public function __construct($result = null, JsonRpcError $error = null, $id = null)
    {
        $this->result = $result;
        $this->error  = $error;
        $this->id     = $id;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = array();
        $result['jsonrpc'] = $this->jsonrpc;
        if ($this->error === null) {
            $result['result'] = $this->result;
        } else {
            $result['error'] = $this->error->toArray();
            $result['id'] = null;
        }
        if ($this->id !== null) {
            $result['id'] = $this->id;
        }

        return $result;
    }
}
