<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected $statusCode;

    public function __construct($message = "An error occurred", $code = 500, Exception $previous = null)
    {
        $this->statusCode = $code;
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        $message = $this->getMessage();
        $data = null;

        if (config('app.debug')) {
            $data = [
                'file' => $this->getFile(),
                'line' => $this->getLine(),
                'trace' => $this->getTraceAsString()
            ];
            $message .= " in " . $this->getFile() . " on line " . $this->getLine();
        }

        return responseJson(
            $message,
            $data,
            false,
            $this->statusCode ?: 500
        );
    }
}
