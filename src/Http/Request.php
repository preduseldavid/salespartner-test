<?php

namespace Http;

/**
 * HTTP LAYER
 *
 * This class is responsible only for the reading/input of the request. It takes
 * care of the client's input in a raw format and that data gets passed
 * to the server that is parsing and handling it.
 */
class Request
{
    const HEADER_CONTENT_JSON = 'Content-Type: application/json';
    const HEADER_ACCEPT_JSON = 'Accept: application/json';

    /** @var array */
    private $headers = [];

    /** @var string */
    private $data = '';

    /**
     * Get the request's data when the client makes a request.
     *
     * @return self
     * Returns the object handler
     */
    public static function capture(): self
    {
        $request = new self();
        $request->data = file_get_contents("php://input");
        $request->headers = getallheaders();
        return $request;
    }

    /**
     * Make a JSON HTTP request to a host.
     *
     * @param string $url
     * The url where we have to send this request
     *
     * @param string $data
     * Raw data that is already encoded and needs to be sent to the host
     *
     * @return string
     * Returns raw data (body)
     */
    public static function sendJson($url, $data): string
    {
        $options = array(
          'http' => array(
            'method'  => 'POST',
            'content' => $data,
            'header'=>  [self::HEADER_CONTENT_JSON, self::HEADER_ACCEPT_JSON]
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

    /**
     * Check if the clients is expecting a JSON response
     *
     * @return bool
     * Returns true when response MUST have JSON format, false otherwise.
     */
    public function expectsJson(): bool
    {
        return $this->headers['Accept'] === "application/json";
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getData(): string
    {
        return $this->data;
    }
}
