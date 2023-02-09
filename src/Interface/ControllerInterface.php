<?php

declare(strict_types=1);

namespace Jesperbeisner\Arbeitszeugnisgenerator\Interface;

use Jesperbeisner\Arbeitszeugnisgenerator\Stdlib\Request;

interface ControllerInterface
{
    public function execute(Request $request): ResponseInterface;
}
