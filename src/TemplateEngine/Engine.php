<?php
//
//namespace Aimocs\Iis\Flat\TemplateEngine;
//
//class Engine implements TemplateEngineInterface
//{
//
//    private string $templatesDirectory;
//
//
//    public function __construct(string $templatesDirectory)
//    {
//        $this->templatesDirectory = $templatesDirectory;
//    }
//
//    public function render(string $path, array $data): string
//    {
//        extract($data);
//        ob_start();
//        $content  = file_get_contents($this->templatesDirectory . "/".$path.".yolo");
//
/*        eval('?>'.$content);*/
//        $content = ob_get_clean();
//        dump($content);
//        return $content;
//    }
//}

namespace Aimocs\Iis\Flat\TemplateEngine;

class Engine implements TemplateEngineInterface
{
    private string $templatesDirectory;
    private string $cacheDirectory;

    public function __construct(string $templatesDirectory)
    {
        $this->templatesDirectory = rtrim($templatesDirectory, DIRECTORY_SEPARATOR);
        $this->cacheDirectory = dirname($templatesDirectory)."/cache12";

        // Ensure cache directory exists
        if (!is_dir($this->cacheDirectory) && !mkdir($this->cacheDirectory, 0777, true)) {
            throw new \RuntimeException("Failed to create cache directory: {$this->cacheDirectory}");
        }
    }

    public function render(string $path, array $data): string
    {
        $templatePath = $this->templatesDirectory . DIRECTORY_SEPARATOR . $path . ".yolo";
        $cachedPath = $this->cacheDirectory . DIRECTORY_SEPARATOR .$path . '.php';

        // Ensure the template file exists and is readable
        if (!file_exists($templatePath) || !is_readable($templatePath)) {
            throw new \RuntimeException("Template not found: {$templatePath}");
        }

        // Compile the template if the cache doesn't exist or is outdated
        if (!file_exists($cachedPath) || filemtime($cachedPath) < filemtime($templatePath)) {
            $processedTemplate = $this->processTemplate($templatePath);
            file_put_contents($cachedPath, $processedTemplate);
        }

        // Extract variables in a controlled environment and include the cached file
        return $this->renderTemplate($cachedPath, $data);
    }

    private function processTemplate(string $templatePath): string
    {
        $content = file_get_contents($templatePath);

        // Replace {{ }} with PHP echo statements
        $content = preg_replace('/{{\s*(.+?)\s*}}/', '<?php echo htmlspecialchars($1, ENT_QUOTES, "UTF-8"); ?>', $content);

        // Replace {% %} with PHP code blocks
        $content = preg_replace('/{%\s*(.+?)\s*%}/', '<?php $1 ?>', $content);

        return $content;
    }

    private function renderTemplate(string $cachedPath, array $data): string
    {
        // Isolate the variable scope
        extract($this->escapeData($data), EXTR_SKIP);

        // Start output buffering
        ob_start();

        try {
            include $cachedPath; // Include the preprocessed template
        } catch (\Throwable $e) {
            ob_end_clean(); // Discard buffer on error
            throw new \RuntimeException("Error rendering template: " . $e->getMessage(), 0, $e);
        }

        return ob_get_clean(); // Get and clean the output buffer
    }

    private function escapeData(array $data): array
    {
        // Automatically escape data to prevent XSS
        return array_map(function ($value) {
            return is_string($value) ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $value;
        }, $data);
    }
}
