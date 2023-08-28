<?php

namespace App;

use App\Models\Car;
use App\Models\Lead;
use App\Services\CarService;
use App\Services\LeadService;
use App\Services\ValidatorService;
use Exception;
use JsonRpc\Core;
use JsonRpc\Exceptions\ExceptionApplication;
use JsonRpc\Exceptions\ExceptionArgument;
use JsonRpc\Exceptions\ExceptionMethod;

class Routes implements Core
{
    /**
     * @throws ExceptionArgument
     * @throws ExceptionApplication
     * @throws ExceptionMethod
     */
    public function execute($method, $arguments)
    {
        return match ($method) {
            'allocLead' => self::AllocateLead($arguments),
            'getCars' => self::GetCars($arguments),
            'refreshLeads' => self::RefreshLeadSeller($arguments),

            default => throw new ExceptionMethod(),
        };
    }

    /**
     * @throws ExceptionArgument
     * @throws ExceptionApplication
     */
    private static function AllocateLead($arguments): int
    {
        ValidatorService::validate($arguments, [
            'first_name' => 'required|alpha|max:64',
            'last_name' => 'required|alpha|max:64',
            'email' => 'required|email',
            'phone' => 'required|max:16',
            'message' => 'required|max:1024',
        ]);

        try {
            $service = new LeadService();
            /** @var Lead $result */
            $lead = $service->allocate($arguments);

            return $lead->getId();
        } catch (Exception $exception) {
            throw new ExceptionApplication($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @throws ExceptionApplication
     */
    private static function GetCars($arguments): Car|array
    {
        $lastChanged = $arguments['last_changed'] ?? null;
        $page = $arguments['page'] ?? 1;
        try {
            $service = new CarService();

            return $service->getAll($lastChanged, $page, Car::PER_PAGE);
        } catch (Exception $exception) {
            throw new ExceptionApplication($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * @throws ExceptionApplication
     */
    private static function RefreshLeadSeller(): array
    {
        $waitMins = getenv('LEAD_PROCESS_WAIT_MINS');

        try {
            $service = new LeadService();

            return $service->refreshSellers($waitMins);
        } catch (Exception $exception) {
            throw new ExceptionApplication($exception->getMessage(), $exception->getCode());
        }
    }
}
