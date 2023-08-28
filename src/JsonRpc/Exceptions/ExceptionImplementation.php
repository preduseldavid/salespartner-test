<?php

namespace JsonRpc\Exceptions;

class ExceptionImplementation extends Exception
{
    /**
     * @param int $code
     * Integer identifying the type of error that occurred. As the author of a
     * JSON-RPC 2.0 implementation, you are free to define any custom error code
     * that you find useful for your implementation, as long as your error code
     * falls within the range from -32099 to -32000 inclusive.
     *
     * @param null|boolean|integer|float|string|array $data
     * An optional primitive value that contains additional information about
     * the error. You're free to define the format of this data (e.g. you could
     * supply an array with detailed error information). Alternatively, you may
     * omit this field by providing a null value.
     */
    public function __construct($code, $data = null)
    {
        if (!self::isValidCode($code)) {
            $code = -32099;
        }

        if (!self::isValidData($data)) {
            $data = null;
        }

        parent::__construct('Server error', $code, $data);
    }

    /**
     * @param int $code
     *
     * @return bool
     * Returns true iff the value can be used as an implementation-defined
     * error code.
     */
    private static function isValidCode($code)
    {
        return is_int($code) && (-32099 <= $code) && ($code <= -32000);
    }

    /**
     * Determines whether a value can be used as the data value in an error
     * object.
     *
     * @param null|boolean|integer|float|string|array $input
     * An optional primitive value that contains additional information about
     * the error. You're free to define the format of this data (e.g. you could
     * supply an array with detailed error information). Alternatively, you may
     * omit this field by supplying a null value.
     *
     * @return bool
     * Returns true iff the value can be used as the data value in an error
     * object.
     */
    private static function isValidData($input)
    {
        $type = gettype($input);

        return ($type === 'array')
            || ($type === 'string')
            || ($type === 'double')
            || ($type === 'integer')
            || ($type === 'boolean')
            || ($type === 'NULL');
    }
}
