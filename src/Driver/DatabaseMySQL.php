<?php

namespace Driver;

use Exception;
use PDO;
use PDOException;
use PDOStatement;
use App\Utilities;

class DatabaseMySQL
{
    /**
     * @var PDO|null
     */
    private ?PDO $pdo = null;

    /** @var bool */
    private bool $activeTransaction = false;

    private ?PDOException $lastException = null;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->connect();
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    /**
     * @param $sql
     * @param array $array
     * @param bool $debug
     * @param int $retryNo
     * @param $started_at
     * @return bool|PDOStatement
     */
    public function prepare(
        $sql,
        array $array = array(),
        bool $debug = false,
        int $retryNo = 0,
        $started_at = null
    ): bool|PDOStatement {
        if (is_null($started_at)) {
            $startTime = Utilities::getCurrentUnixTimeWithMilliseconds();
        } else {
            $startTime = $started_at;
        }

        # used to keep count of number of max retries
        $noRetries = 2;

        if (!$this->pdo) {
            if ($this->lastException) {
                throw new PDOException('Exception encountered during prepare', 0, $this->lastException);
            } else {
                throw new PDOException('DB not available');
            }
        }

        try {
            $stmt = $this->pdo->prepare($sql);

            if (!$stmt->execute($array)) {
                throw new PDOException('Error encountered during execution of prepared statement');
            }
            return $stmt;
        } catch (Exception $exception) {
            $stmt?->closeCursor();

            # used to mark if the operation is retryable
            $isRetryable = false;

            # don't log for duplicate entries
            if ($exception->getCode() != '23000') {
                # check if the error can be marked as retryable
                if ($retryNo <= $noRetries) {
                    $isRetryable = true;
                    $retryNo++;
                }
            }

            # if is not retryable then throw exception else retry
            if (!$isRetryable) {
                throw new PDOException(
                    $exception->getMessage() . ' Query: ' . print_r($this->interpolateQuery($sql, $array), true),
                    (int)$exception->getCode(),
                    $exception
                );
            } else {
                return $this->prepare($sql, $array, $debug, $retryNo);
            }
        }
    }

    /**
     * @param string $query
     * @param array $params
     * @return string
     */
    public function interpolateQuery(string $query, array $params): string
    {
        # build a regular expression for each parameter
        foreach ($params as $key => $value) {
            $query = str_replace(':' . ($key), is_numeric($value) ? $value : "'$value'", $query);
        }

        return $query;
    }

    /**
     * @return void
     */
    public function beginTransaction(): void
    {
        $this->checkConnection();
        if ($this->pdo && !$this->activeTransaction) {
            $this->activeTransaction = true;
            $this->pdo->beginTransaction();
        }
    }

    /**
     * @return void
     */
    public function commit(): void
    {
        $this->checkConnection();
        if ($this->pdo && $this->activeTransaction) {
            $this->pdo->commit();
            $this->activeTransaction = false;
        }
    }

    /**
     * @return void
     */
    public function rollback(): void
    {
        $this->checkConnection();
        if ($this->pdo && $this->activeTransaction) {
            $this->pdo->rollback();
            $this->activeTransaction = false;
        }
    }

    /**
     * @return int
     */
    public function lastInsertId(): int
    {
        $this->checkConnection();
        return intval($this->pdo->lastInsertId());
    }

    /**
     * @return void
     * @throws Exception
     */
    private function connect(): void
    {
        try {
            $dsn = "mysql:host=" . getenv('DB_HOST') . ";port=" . getenv('DB_PORT') . ";charset=utf8mb4";

            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            );

            # limit the execution/connection time to 10 seconds
            if (extension_loaded('pdo_mysql')) {
                $options[PDO::ATTR_TIMEOUT] = 10;
            }

            try {
                $this->pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), $options);
            } catch (PDOException $e) {
                if (is_null($this->pdo)) {
                    throw new PDOException($e->getMessage(), (int)$e->getCode());
                }
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * Checks if the connection is still active.
     * If not, attempts to reconnect.
     */
    private function checkConnection(): void
    {
        try {
            $this->pdo->getAttribute(PDO::ATTR_SERVER_INFO);
        } catch (PDOException $e) {

            error_log(
                json_encode(array(
                    'error' => "DB connection dropped! Attempt to re-connect...",
                    'request' => array(
                        'request' => array(
                            '$_POST' => $_POST,
                            '$_GET' => $_GET,
                            'raw' => file_get_contents("php://input"),
                        ),
                        'error' => "{$e->getCode()}:{$e->getMessage()} [{$e->getFile()}:{$e->getLine()}]"
                    ),
                )),
                0
            );

            $this->connect();
        }
    }
}
