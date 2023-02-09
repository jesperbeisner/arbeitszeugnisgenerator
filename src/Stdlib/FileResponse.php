<?php

declare(strict_types=1);

namespace Jesperbeisner\Arbeitszeugnisgenerator\Stdlib;

use Jesperbeisner\Arbeitszeugnisgenerator\Interface\ResponseInterface;
use RuntimeException;

final readonly class FileResponse implements ResponseInterface
{
    public function __construct(
        private string $fileName,
    ) {
    }

    public function send(): never
    {
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . basename($this->fileName));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($this->fileName));
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");

        if (false === readfile($this->fileName)) {
            throw new RuntimeException(sprintf('Could not read file "%s".', $this->fileName));
        }

        if (false === unlink($this->fileName)) {
            throw new RuntimeException(sprintf('Could not unlink file "%s".', $this->fileName));
        }

        exit(0);
    }
}
