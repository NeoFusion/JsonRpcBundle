<?php

namespace NeoFusion\JsonRpcBundle\Utils;

class JsonRpcException extends \Exception
{
    /** @var mixed */
    private $data;

    /**
     * @param int         $code    Error code from JsonRpcError
     * @param string|null $message Message for `data` field in JsonRpcException
     * @param mixed|null  $data    Additional error information
     */
    public function __construct($code = 0, $message = null, $data = null)
    {
        parent::__construct($message === null ? '' : $message, $code);
        $this->data = $data;
    }

    /**
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }
}
