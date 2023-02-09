<?php

declare(strict_types=1);

use Jesperbeisner\Arbeitszeugnisgenerator\Controller;
use Jesperbeisner\Arbeitszeugnisgenerator\Stdlib\Request;

require __DIR__ . '/../vendor/autoload.php';

/** @var array<string, array<int, string>> $textsArray */
$textsArray = require __DIR__ . '/../config/texts.php';
$request = Request::fromGlobals();

if ($request->getUri() === '/') {
    (new Controller\IndexController($textsArray))->execute($request)->send();
}

if ($request->getUri() === '/download') {
    (new Controller\DownloadController($textsArray))->execute($request)->send();
}

(new Controller\NotFoundController())->execute($request)->send();
