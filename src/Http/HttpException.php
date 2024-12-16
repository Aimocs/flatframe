<?php

namespace Aimocs\Iis\Flat\Http;

class HttpException extends \Exception
{

    private int $statusCode = 404;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

}