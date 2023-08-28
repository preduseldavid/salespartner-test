<?php

namespace App;

use Driver\DatabaseMySQL;
use Http\Request;
use Http\Response;
use JsonRpc\Server;

class App
{
    /** @var Routes */
    private Routes $routes;

    /** @var Server */
    private Server $server;

    /** @var DatabaseMySQL|null */
    private static ?DatabaseMySQL $db = null;

    public function __construct()
    {
        $this->routes = new Routes();
        $this->server = new Server($this->routes);
    }

    /**
     * Handle the incoming request through JsonRPC Server.
     *
     * @param  Request  $request
     * Instance of the Request class.
     *
     * @return Response|null Returns a response instance with the output of the handled request
     */
    public function handle(Request &$request): ?Response
    {
        $requestRawData = $request->getData();
        $responseRawData = $this->server->handle($requestRawData);

        if ($responseRawData) {
            $responseHeaders = [];
            if ($request->expectsJson()) {
                $responseHeaders[] = Request::HEADER_CONTENT_JSON;
            }
            return new Response($responseRawData, $responseHeaders);
        } else {
            /* No output required */
            return null;
        }
    }

    /**
     * @return DatabaseMySQL|null
     */
    public static function getDB(): ?DatabaseMySQL
    {
        if (is_null(self::$db)) {
            self::$db = new DatabaseMySQL();
        }
        return self::$db;
    }
}
