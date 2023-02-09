<?php

declare(strict_types=1);

namespace Jesperbeisner\Arbeitszeugnisgenerator\Controller;

use Jesperbeisner\Arbeitszeugnisgenerator\Interface\ControllerInterface;
use Jesperbeisner\Arbeitszeugnisgenerator\Interface\ResponseInterface;
use Jesperbeisner\Arbeitszeugnisgenerator\Stdlib\Request;
use Jesperbeisner\Arbeitszeugnisgenerator\Stdlib\HtmlResponse;

final readonly class IndexController implements ControllerInterface
{
    /**
     * @param array<string, array<int, string>> $textsArray
     */
    public function __construct(
        private array $textsArray,
    ) {
    }

    public function execute(Request $request): ResponseInterface
    {
        return new HtmlResponse('index.phtml', [
            'textsArray' => $this->textsArray,
        ]);
    }
}
