<?php

namespace JsonRpc\Exceptions;

class ExceptionApplication extends Exception
{
    /**
     * @param string $message
     * Short description of the error that occurred. This message SHOULD
     * be limited to a single, concise sentence.
     *
     * @param int $code
     * Integer identifying the type of error that occurred. As the author of
     * a server-side application, you are free to define any error codes
     * that you find useful for your application--with one exception:
     *
     * Please be aware that the error codes in the range from -32768 to -32000,
     * inclusive, have special meanings under the JSON-RPC 2.0 specification.
     * These error codes have already been taken, so they cannot be redefined
     * as application-defined error codes! However, you can safely use any
     * integer from outside this reserved range.
     *
     * @param null|boolean|integer|float|string|array $data
     * An optional primitive value that contains additional information about
     * the error. You're free to define the format of this data (e.g. you could
     * supply an array with detailed error information). Alternatively, you may
     * omit this field by providing a null value.
     */
    public function __construct($message, $code, $data = null)
    {
        if (!self::isValidMessage($message)) {
            $message = '';
        }

        if (!self::isValidCode($code)) {
            $code = 1;
        }

        if (!self::isValidData($data)) {
            $data = null;
        }

        parent::__construct($message, $code, $data);
    }

    /**
     * Determines whether a value can be used as an error message.
     *
     * @param string $input
     * Short description of the error that occurred. This message SHOULD
     * be limited to a single, concise sentence.
     *
     * @return bool
     * Returns true iff the value can be used as an error message.
     */
    private static function isValidMessage($input)
    {
        return is_string($input);
    }

    /**
     * Determines whether a value can be used as an application-defined error
     * code.
     *
     * @param int $code
     * Integer identifying the type of error that occurred. As the author of
     * a server-side application, you are free to define any error codes
     * that you find useful for your application.
     *
     * Please be aware that the error codes in the range from -32768 to -32000,
     * inclusive, have special meanings under the JSON-RPC 2.0 specification:
     * These error codes have already been taken, so they cannot be redefined
     * as application-defined error codes! However, you can safely use any
     * integer from outside this reserved range.
     *
     * @return bool
     * Returns true iff the value can be used as an application-defined
     * error code.
     */
    private static function isValidCode($code)
    {
        return is_int($code) && (($code < -32768) || (-32000 < $code));
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
     * Returns true if the value can be used as the data value in an error
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
