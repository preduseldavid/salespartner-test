<?php

namespace App\Services;

use App\App;
use App\Models\Car;
use App\Models\Lead;
use Exception;
use JsonRpc\Responses\ResponseError;
use PDO;

class CarService
{
    public function __constructor()
    {
    }

    /**
     * @throws Exception
     */
    public function getAll($lastChanged, $page, $limit): array|Car
    {
        $db = App::getDB();
        $dbName = getenv('DB_DATABASE');
        $offset = ($page - 1) * $limit;

        $sql = "SELECT *
                FROM `$dbName`.`" . Car::TABLE_NAME . "`
                " . ($lastChanged ? "WHERE updated_at > :updated_at" : "") . "
                ORDER BY `".Lead::PRIMARY_KEY_NAME."` DESC
                LIMIT " . $limit . " OFFSET $offset;";

        $params = [];
        if ($lastChanged) {
            $params = [
                'updated_at' => $lastChanged
            ];
        }
        $stmt = $db->prepare($sql, $params);

        $stmt->setFetchMode(PDO::FETCH_CLASS, 'App\Models\Car');
        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        $cars = [];
        /** @var Car $car */
        foreach ($result as $car) {
            $cars[] = $car->asArray();
        }

        return $cars;
    }
}