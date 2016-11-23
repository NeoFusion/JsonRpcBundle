<?php

namespace NeoFusion\JsonRpcBundle\Utils;

/**
 * @link http://www.jsonrpc.org/specification
 */
class JsonRpcSingleRequest
{
    /**
     * @var string A String specifying the version of the JSON-RPC protocol.
     *
     * MUST be exactly "2.0".
     */
    private $jsonrpc;

    /**
     * @var string A String containing the name of the method to be invoked.
     */
    private $method;

    /**
     * @var mixed A Structured value that holds the parameter values to be used during the invocation of the method.
     *
     * This member MAY be omitted.
     */
    private $params;

    /**
     * @var mixed An identifier established by the Client that MUST contain a String, Number, or NULL value if included.
     *
     * If it is not included it is assumed to be a notification.
     * The value SHOULD normally not be Null and Numbers SHOULD NOT contain fractional parts.
     */
    private $id;

    /**
     * @param string $jsonrpc
     * @param string $method
     * @param mixed  $params
     * @param mixed  $id
     */
    public function __construct($jsonrpc, $method, $params, $id)
    {
        $this->jsonrpc = $jsonrpc;
        $this->method  = $method;
        $this->params  = $params;
        $this->id      = $id;
    }

    /**
     * @return string
     */
    public function getJsonrpc()
    {
        return $this->jsonrpc;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Field validation
     *
     * @return bool
     */
    public function isValid()
    {
        if ($this->jsonrpc !== '2.0') {
            return false;
        }
        if (!is_string($this->method) || empty($this->method)) {
            return false;
        }
        if (($this->params !== null) && !is_array($this->params)) {
            return false;
        }
        if (($this->id !== null) && !(is_string($this->id) || is_int($this->id))) {
            return false;
        }
        return true;
    }

    /**
     * Check whether Request is Notification
     *
     * @return bool
     */
    public function isNotification()
    {
        return $this->id === null;
    }
}
