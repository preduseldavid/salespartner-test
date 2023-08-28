<?php

namespace App\Services;

use App\App;
use App\Models\Lead;
use App\Models\Seller;
use Exception;
use JsonRpc\Responses\ResponseError;
use PDO;

class LeadService
{
    public function __constructor()
    {
    }

    public function refreshSellers($waitMins): array
    {
        $db = App::getDB();
        $dbName = getenv('DB_DATABASE');

        $sql = "SELECT *
                FROM `".$dbName."`.`".Lead::TABLE_NAME."`
                WHERE DATE_ADD(seller_start_timestamp, interval :wait_mins minute) < CURRENT_TIMESTAMP
                ORDER BY `".Lead::PRIMARY_KEY_NAME."` DESC";

        $stmt = $db->prepare($sql, array(
            'wait_mins' => $waitMins
        ));

        $stmt->setFetchMode(PDO::FETCH_CLASS, 'App\Models\Lead');
        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        $count = 0;
        $sellersIds = $this->getSellersIds();

        /** @var Lead $lead */
        foreach ($result as $lead) {
            $currSellerId = $lead->getSellerId();
            $nextSellerId = null;
            // identify the next seller id
            foreach ($sellersIds as $key => $sellerId) {
                if ($currSellerId == $sellerId && $key < count($sellersIds) - 1) {
                    $nextSellerId = $sellersIds[$key + 1];
                }
            }
            echo $nextSellerId;
            // set next seller id
            if ($nextSellerId) {
                $lead->setSellerId($nextSellerId)
                    ->setSellerStartTimestamp(date('Y-m-d H:i:s'))
                    ->save();
                ++$count;
            }
        }

        // return all leads with their sellers
        return $this->getAll();
    }

    public function getAll(): array
    {
        $db = App::getDB();
        $dbName = getenv('DB_DATABASE');
        $sql = "SELECT id, seller_id
                FROM `".$dbName."`.`".Lead::TABLE_NAME."`
                ORDER BY `".Lead::PRIMARY_KEY_NAME."` DESC";

        $stmt = $db->prepare($sql);

        $stmt->setFetchMode(PDO::FETCH_CLASS, 'App\Models\Lead');
        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        if (empty($result)) {
            return [];
        }

        $all = [];
        /** @var Lead $lead */
        foreach ($result as $lead)
            $all[] = $lead->asArray();

        return $all;
    }

    /**
     * @throws Exception
     */
    public function allocate(array $data): array|Lead
    {
        if ($this->existsByEmailOrPhone($data['email'], $data['phone'])) {
            throw new Exception('Email or Phone already exists in database.', ResponseError::BAD_REQUEST_ERROR);
        }

        $sellerId = 1;

        $lead = new Lead();
        $lead->setFirstName($data['first_name'])
            ->setLastName($data['last_name'])
            ->setEmail($data['email'])
            ->setPhone($data['phone'])
            ->setMessage($data['message'])
            ->setSellerId($sellerId);
        $lead->create();

        return $lead;
    }

    private function existsByEmailOrPhone(string $email, string $phone): bool
    {
        $db = App::getDB();
        $dbName = getenv('DB_DATABASE');
        $sql = "SELECT *
                FROM `".$dbName."`.`".Lead::TABLE_NAME."`
                WHERE email = :email OR phone = :phone
                ORDER BY `".Lead::PRIMARY_KEY_NAME."` DESC
                LIMIT 1;";

        $stmt = $db->prepare($sql, array(
            'email' => $email,
            'phone' => $phone
        ));

        $stmt->setFetchMode(PDO::FETCH_CLASS, 'App\Models\Lead');
        $result = $stmt->fetch();
        $stmt->closeCursor();

        if (empty($result)) {
            return false;
        }

        return true;
    }

    private function getSellersIds(): array
    {
        $db = App::getDB();
        $dbName = getenv('DB_DATABASE');
        $sql = "SELECT id
                FROM `".$dbName."`.`".Seller::TABLE_NAME."`
                ORDER BY `".Seller::PRIMARY_KEY_NAME."` ASC";

        $stmt = $db->prepare($sql);

        $stmt->setFetchMode(PDO::FETCH_CLASS, 'App\Models\Seller');
        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        if (empty($result)) {
            return [];
        }

        $ids = [];
        /** @var Seller $seller */
        foreach ($result as $seller) {
            $ids[] = $seller->getId();
        }

        return $ids;
    }
}