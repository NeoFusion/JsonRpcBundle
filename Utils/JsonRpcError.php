<?php

namespace NeoFusion\JsonRpcBundle\Utils;

/**
 * @link http://www.jsonrpc.org/specification
 */
class JsonRpcError implements JsonRpcInterface
{
    /**
     * Invalid JSON was received by the server.
     * An error occurred on the server while parsing the JSON text.
     */
    const CODE_PARSE_ERROR      = -32700;
    /**
     * The JSON sent is not a valid Request object.
     */
    const CODE_INVALID_REQUEST  = -32600;
    /**
     * The method does not exist / is not available.
     */
    const CODE_METHOD_NOT_FOUND = -32601;
    /**
     * Invalid method parameter(s).
     */
    const CODE_INVALID_PARAMS   = -32602;
    /**
     * Internal JSON-RPC error.
     */
    const CODE_INTERNAL_ERROR   = -32603;
    /**
     * Reserved for implementation-defined server-errors.
     */
    const CODE_SERVER_ERROR     = -32000;

    public static $errorMessages = array(
        self::CODE_PARSE_ERROR      => 'Parse error',
        self::CODE_INVALID_REQUEST  => 'Invalid Request',
        self::CODE_METHOD_NOT_FOUND => 'Method not found',
        self::CODE_INVALID_PARAMS   => 'Invalid params',
        self::CODE_INTERNAL_ERROR   => 'Internal error',
        self::CODE_SERVER_ERROR     => 'Server error'
    );

    /**
     * @var int A Number that indicates the error type that occurred.
     *
     * This MUST be an integer.
     */
    private $code;

    /**
     * @var string A String providing a short description of the error.
     *
     * The message SHOULD be limited to a concise single sentence.
     */
    private $message;

    /**
     * @var mixed A Primitive or Structured value that contains additional information about the error.
     *
     * This may be omitted.
     * The value of this member is defined by the Server (e.g. detailed error information, nested errors etc.).
     */
    private $data;

    /**
     * @param int    $code    A Number that indicates the error type that occurred.
     * @param string $message A String providing a short description of the error.
     * @param mixed  $data    A Primitive or Structured value that contains additional information about the error.
     */
    public function __construct($code, $message = null, $data = null)
    {
        $this->code = $code;
        if ($message === null) {
            $this->message = array_key_exists($code, self::$errorMessages) ? self::$errorMessages[$code] : '';
        } else {
            $this->message = $message;
        }
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = array(
            'code'    => $this->code,
            'message' => $this->message
        );
        if ($this->data !== null) {
            $result['data'] = $this->data;
        }

        return $result;
    }
}
