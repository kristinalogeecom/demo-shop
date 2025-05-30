<?php

namespace DemoShop\Infrastructure\Response;

/**
 * Represents an HTML-based response.
 */
class HtmlResponse extends Response
{
    protected string $templateOrPath;
    protected array $params = [];

    /**
     * HtmlResponse constructor.
     *
     * @param string $templateOrPath
     * @param array $params
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct(string $templateOrPath, array $params = [], int $statusCode = 200, array $headers = [])
    {
        $this->templateOrPath = $templateOrPath;
        $this->params = $params;

        if(!isset($this->headers['Content-Type'])) {
            $this->headers['Content-Type'] = 'text/html';
        }

        parent::__construct($statusCode, $headers);
    }

    /**
     * Sends the HTML response to the client.
     *
     * @return void
     */
    public function send(): void
    {
        parent::send();

        // Render static HTML file if full path provided
        if(str_ends_with($this->templateOrPath, '.html') || str_contains($this->templateOrPath,  DIRECTORY_SEPARATOR)) {
            if (!file_exists($this->templateOrPath)) {
                echo "Static HTML file not found";
                return;
            }

            readfile($this->templateOrPath);
            return;
        }

        // Render dynamic PHTML template
        $templatePath = __DIR__ . '/../../../resources/pages/' . $this->templateOrPath . '.phtml';

        if (!file_exists($templatePath)) {
            echo "Template not found." . $templatePath;
            return;
        }

        ob_start();
        extract($this->params);
        include $templatePath;
        echo ob_get_clean();
    }

}