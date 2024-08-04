<?php

namespace App\Exceptions;

use Exception;

class DuplicateApplicationNumberException extends Exception
{
    public function __construct(public string $message = 'The application number is already in use.'){}

    public function render($request)
    {
        return response()->json(["success" => false, "message" => $this->message]);
    }
}
