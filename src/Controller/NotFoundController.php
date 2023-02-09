<?php

declare(strict_types=1);

namespace Jesperbeisner\Arbeitszeugnisgenerator\Controller;

use Jesperbeisner\Arbeitszeugnisgenerator\Interface\ControllerInterface;
use Jesperbeisner\Arbeitszeugnisgenerator\Interface\ResponseInterface;
use Jesperbeisner\Arbeitszeugnisgenerator\Stdlib\Request;
use Jesperbeisner\Arbeitszeugnisgenerator\Stdlib\HtmlResponse;

final readonly class NotFoundController implements ControllerInterface
{
    public function execute(Request $request): ResponseInterface
    {
        return new HtmlResponse('404.phtml', [], 404);
    }
}
