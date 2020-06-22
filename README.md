# chubbyphp-framework-router-sunrise

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-framework-router-sunrise.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-framework-router-sunrise)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/chubbyphp-framework-router-sunrise/badge.svg?branch=master)](https://coveralls.io/github/chubbyphp/chubbyphp-framework-router-sunrise?branch=master)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-framework-router-sunrise/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-sunrise)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-framework-router-sunrise/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-sunrise)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-framework-router-sunrise/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-sunrise)
[![Latest Unstable Version](https://poser.pugx.org/chubbyphp/chubbyphp-framework-router-sunrise/v/unstable)](https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-sunrise)

## Description

Sunrise Router implementation for [chubbyphp-framework][1].

## Requirements

 * php: ^7.2
 * [chubbyphp/chubbyphp-framework][1]: ^3.0
 * [sunrise/http-router][2]: ^2.4

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-framework-router-sunrise][10].

```bash
composer require chubbyphp/chubbyphp-framework-router-sunrise "^1.0"
```

## Usage

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\ErrorHandler;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\Sunrise\Router;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

$loader = require __DIR__.'/vendor/autoload.php';

set_error_handler([new ErrorHandler(), 'errorToException']);

$responseFactory = new ResponseFactory();

$app = new Application([
    new ExceptionMiddleware($responseFactory, true),
    new RouterMiddleware(new SunriseRouter([
        Route::get('/hello/{name<[a-z]+>}', 'hello', new CallbackRequestHandler(
            function (ServerRequestInterface $request) use ($responseFactory) {
                $name = $request->getAttribute('name');
                $response = $responseFactory->createResponse();
                $response->getBody()->write(sprintf('Hello, %s', $name));

                return $response;
            }
        ))
    ]), $responseFactory),
]);

$app->emit($app->handle((new ServerRequestFactory())->createFromGlobals()));
```

## Copyright

Dominik Zogg 2020

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-framework
[2]: https://packagist.org/packages/sunrise/router
[10]: https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-sunrise
