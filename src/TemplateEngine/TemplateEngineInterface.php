<?php

namespace Aimocs\Iis\Flat\TemplateEngine;

interface TemplateEngineInterface
{
    public function render(string $path,array $data):string;

}