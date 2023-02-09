<?php

declare(strict_types=1);

namespace Jesperbeisner\Arbeitszeugnisgenerator\Stdlib;

use Jesperbeisner\Arbeitszeugnisgenerator\Interface\ResponseInterface;
use RuntimeException;

final readonly class HtmlResponse implements ResponseInterface
{
    /**
     * @param array<string, mixed> $vars
     */
    public function __construct(
        private string $template,
        private array $vars = [],
        private int $statusCode = 200,
    ) {
    }

    public function send(): never
    {
        header('Content-Type: text/html; charset=UTF-8');
        http_response_code($this->statusCode);
        echo $this->render();

        exit(0);
    }

    public function get(string $key): mixed
    {
        if (!array_key_exists($key, $this->vars)) {
            throw new RuntimeException(sprintf('Variable with key "%s" does not exist.', $key));
        }

        return $this->vars[$key];
    }

    private function render(): string
    {
        $templateFile = sprintf(__DIR__ . '/../../views/%s', $this->template);
        $layoutFile = __DIR__ . '/../../views/layout.phtml';

        if (!is_file($templateFile)) {
            throw new RuntimeException(sprintf('Template file "%s" does not exist.', $templateFile));
        }

        ob_start();
        require $templateFile;
        if (false === $content = ob_get_clean()) {
            throw new RuntimeException(sprintf('"ob_get_clean" returned false for template file "%s".', $templateFile));
        }

        if (!is_file($layoutFile)) {
            throw new RuntimeException(sprintf('Layout file "%s" does not exist.', $layoutFile));
        }

        ob_start();
        require $layoutFile;
        if (false === $content = ob_get_clean()) {
            throw new RuntimeException(sprintf('"ob_get_clean" returned false for layout file "%s".', $layoutFile));
        }

        return $content;
    }
}
