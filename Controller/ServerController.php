<?php

namespace NeoFusion\JsonRpcBundle\Controller;

use NeoFusion\JsonRpcBundle\Utils\JsonRpcBatchResponse;
use NeoFusion\JsonRpcBundle\Utils\JsonRpcError;
use NeoFusion\JsonRpcBundle\Utils\JsonRpcInterface;
use NeoFusion\JsonRpcBundle\Utils\JsonRpcSingleRequest;
use NeoFusion\JsonRpcBundle\Utils\JsonRpcSingleResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ServerController extends Controller
{
    /**
     * Entry point for handler of API requests
     *
     * @param Request $request
     * @param string  $route   Route name
     *
     * @return Response|JsonResponse
     */
    public function processAction(Request $request, $route)
    {
        $content = $request->getContent();
        $jsonArray = json_decode($content, true);
        // Checking for JSON parsing errors
        if ($jsonArray === null) {
            $jsonRpcSingleResponse = new JsonRpcSingleResponse(null, new JsonRpcError(JsonRpcError::CODE_PARSE_ERROR));
            return new JsonResponse($jsonRpcSingleResponse->toArray());
        }
        // Checking for array is not empty
        if (!(is_array($jsonArray) && !empty($jsonArray))) {
            $jsonRpcSingleResponse = new JsonRpcSingleResponse(null, new JsonRpcError(JsonRpcError::CODE_INVALID_REQUEST));
            return new JsonResponse($jsonRpcSingleResponse->toArray());
        }
        // Getting array type
        $isAssoc = $this->isAssocArray($jsonArray);
        // Making requests depending on array type
        if ($isAssoc) {
            $singleResult = $this->processSingleJsonArray($jsonArray, $route);
            $answer = ($singleResult instanceof JsonRpcInterface) ? $singleResult->toArray() : null;
        } else {
            $batchResult = new JsonRpcBatchResponse();
            foreach ($jsonArray as $singleJsonArray) {
                // Checking for array is not empty
                if (!(is_array($singleJsonArray) && !empty($singleJsonArray))) {
                    $jsonRpcSingleResponse = new JsonRpcSingleResponse(null, new JsonRpcError(JsonRpcError::CODE_INVALID_REQUEST));
                    $batchResult->addResponse($jsonRpcSingleResponse);
                } else {
                    $singleResult = $this->processSingleJsonArray($singleJsonArray, $route);
                    if ($singleResult instanceof JsonRpcInterface) {
                        $batchResult->addResponse($singleResult);
                    }
                }
            }
            $answer = $batchResult->isEmpty() ? null : $batchResult->toArray();
        }

        return ($answer === null) ? new Response() : new JsonResponse($answer);
    }

    /**
     * Convert array to JsonRpcSingleRequest
     *
     * @param array $singleJsonArray
     *
     * @return JsonRpcSingleRequest
     */
    private function prepareSingleRequest(array $singleJsonArray)
    {
        return new JsonRpcSingleRequest(
            array_key_exists('jsonrpc', $singleJsonArray) ? $singleJsonArray['jsonrpc'] : null,
            array_key_exists('method',  $singleJsonArray) ? $singleJsonArray['method']  : null,
            array_key_exists('params',  $singleJsonArray) ? $singleJsonArray['params']  : null,
            array_key_exists('id',      $singleJsonArray) ? $singleJsonArray['id']      : null
        );
    }

    /**
     * Processing array of JSON data
     *
     * @param array  $singleJsonArray
     * @param string $route           Route name
     *
     * @return JsonRpcSingleResponse|null NULL, if JsonRpcSingleRequest is Notification
     */
    private function processSingleJsonArray(array $singleJsonArray, $route)
    {
        $jsonRpcSingleRequest = $this->prepareSingleRequest($singleJsonArray);
        if ($jsonRpcSingleRequest->isValid()) {
            if ($jsonRpcSingleRequest->isNotification()) {
                return null;
            } else {
                return $this->processSingleRequest($jsonRpcSingleRequest, $route);
            }
        } else {
            return new JsonRpcSingleResponse(null, new JsonRpcError(JsonRpcError::CODE_INVALID_REQUEST));
        }
    }

    /**
     * Making a single request
     *
     * @param JsonRpcSingleRequest $request
     * @param string               $route   Route name
     *
     * @return JsonRpcSingleResponse
     */
    private function processSingleRequest(JsonRpcSingleRequest $request, $route)
    {
        $jsonRpcParams = $this->getParameter('neofusion_jsonrpc');
        $methods = $jsonRpcParams['routing'][$route]['methods'];
        // Checking for method presence in the list
        if (!array_key_exists($request->getMethod(), $methods)) {
            return new JsonRpcSingleResponse(null, new JsonRpcError(JsonRpcError::CODE_METHOD_NOT_FOUND), $request->getId());
        }
        $method = $methods[$request->getMethod()];
        $action = $method['action'];
        // Checking for service presence
        try {
            $service = $this->get($method['service']);
        } catch (ServiceNotFoundException $e) {
            return new JsonRpcSingleResponse(null, new JsonRpcError(JsonRpcError::CODE_SERVER_ERROR), $request->getId());
        }
        // Checking for method presence
        if (!is_callable(array($service, $action))) {
            return new JsonRpcSingleResponse(null, new JsonRpcError(JsonRpcError::CODE_METHOD_NOT_FOUND), $request->getId());
        }
        // Calling the method
        try {
            $result = $service->$action($request->getParams());
        } catch (\Exception $e) {
            // If error code exists in JsonRpcError list, that use it. Otherwise using standard CODE_SERVER_ERROR
            if (array_key_exists($e->getCode(), JsonRpcError::$errorMessages)) {
                $code = $e->getCode();
            } else {
                $code = JsonRpcError::CODE_SERVER_ERROR;
            }
            // If an exception has `getData` method and it's not return null, then pass `data` as an array
            if (is_callable(array($e, 'getData')) && $e->getData() !== null) {
                $data = array(
                    'code'    => $e->getCode(),
                    'message' => $e->getMessage(),
                    'data'    => $e->getData()
                );
            } else {
                if (empty($e->getMessage())) {
                    $data = null;
                } else {
                    $data = $e->getMessage();
                }
            }
            return new JsonRpcSingleResponse(null, new JsonRpcError($code, null, $data), $request->getId());
        }

        return new JsonRpcSingleResponse($result, null, $request->getId());
    }

    /**
     * Getting array type (associative / sequential)
     *
     * @param mixed $arr
     *
     * @return bool True, if associative
     */
    private function isAssocArray($arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
