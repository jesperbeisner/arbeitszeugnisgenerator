<?php

declare(strict_types=1);

namespace Jesperbeisner\Arbeitszeugnisgenerator\Stdlib;

use Jesperbeisner\Arbeitszeugnisgenerator\Interface\ResponseInterface;

final readonly class RedirectResponse implements ResponseInterface
{
    public function __construct(
        private string $location,
    ) {
    }

    public function send(): never
    {
        header(sprintf('Location: %s', $this->location));
        http_response_code(302);

        exit(0);
    }
}
