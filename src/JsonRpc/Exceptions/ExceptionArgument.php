<?php

namespace JsonRpc\Exceptions;

use JsonRpc\Responses\ResponseError;

class ExceptionArgument extends Exception
{
    public function __construct($data = null)
    {
        parent::__construct(
            'Invalid params',
            ResponseError::INVALID_ARGUMENTS,
            $data
        );
    }
}
