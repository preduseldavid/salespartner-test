<?php

namespace App\Services;

use JsonRpc\Exceptions\ExceptionArgument;
use Rakit\Validation\Validator;

class ValidatorService extends BaseService
{
    /**
     * @throws ExceptionArgument
     */
    public static function validate(array $input, array $rules): int
    {
        $validator = new Validator();
        $validation = $validator->make($input, $rules);
        $validation->validate();

        if ($validation->fails()) {
            $errors = $validation->errors()->toArray();

            throw new ExceptionArgument($errors);
        }

        return 0;
    }
}