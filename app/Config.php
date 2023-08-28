<?php

namespace App;

class Config
{
    public static function setup(): void
    {
        $rootPath = $_SERVER['DOCUMENT_ROOT'];;
        $envFile = $rootPath.'/.env';

        if (file_exists($envFile)) {
            $envContent = file_get_contents($envFile);
            $envVariables = explode("\n", $envContent);

            foreach ($envVariables as $envVariable) {
                $envVariable = trim($envVariable);

                if ($envVariable && str_contains($envVariable, '=') && !str_starts_with($envVariable, '#')) {
                    list($envKey, $envValue) = explode('=', $envVariable, 2);

                    $envKey = trim($envKey);
                    $envValue = trim($envValue);

                    putenv($envKey.'='.$envValue);
                }
            }
        }
    }
}