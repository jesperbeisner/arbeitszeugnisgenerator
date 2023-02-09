<?php

declare(strict_types=1);

namespace Jesperbeisner\Arbeitszeugnisgenerator\Interface;

interface ResponseInterface
{
    public function send(): never;
}
