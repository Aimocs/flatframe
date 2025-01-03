<?php

namespace Aimocs\Iis\Flat\Http;

class Response
{
    public const HTTP_INTERNAL_SERVER_ERROR= 500;

    public function __construct(
        private ?string $content = '',
        private int $status = 200,
        private array $headers = []
    )
    {
        // Must be set before sending content
        // So this is as good of a place as any
        if(headers_sent()){
           echo"sons" ;
        }
        http_response_code($this->status);
    }

    public function send():void
    {
        echo $this->content;
    }
}