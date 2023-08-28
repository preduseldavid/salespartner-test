<?php

namespace JsonRpc;

interface Core
{
    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function execute($method, $arguments);
}
