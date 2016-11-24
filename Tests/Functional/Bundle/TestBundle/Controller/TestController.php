<?php

namespace NeoFusion\JsonRpcBundle\Tests\Functional\Bundle\TestBundle\Controller;

class TestController
{
    /**
     * @param array $params
     *
     * @return int
     */
    public function sum($params)
    {
        $result = 0;
        foreach ($params as $value) {
            $result += $value;
        }

        return $result;
    }

    /**
     * @param mixed $params
     *
     * @return mixed
     */
    public function subtract($params)
    {
        if (array_key_exists('subtrahend', $params) && array_key_exists('minuend', $params)) {
            return $params['minuend'] - $params['subtrahend'];
        } else {
            return $params[0] - $params[1];
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return array('hello', 5);
    }

    public function update()
    {
    }
}
