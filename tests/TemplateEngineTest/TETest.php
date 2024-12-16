<?php

namespace Aimocs\Iis\Tests\TemplateEngineTest;

use Aimocs\Iis\Flat\TemplateEngine\Engine;
use PHPUnit\Framework\TestCase;

class TETest extends TestCase
{
    public function testFileIsRendered()
    {
        $template_path = dirname(__DIR__,2)."/templates";
        $engine = new Engine($template_path);
        $content = $engine->render("index",["name"=>"Testing"]);
        $testpath = dirname(__DIR__)."/TemplateEngineTest/test.yolo";
        file_put_contents($testpath,$content);
        self::assertTrue(is_file($testpath));
    }

}