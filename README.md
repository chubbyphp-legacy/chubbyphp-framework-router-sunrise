# chubbyphp-framework-router-sunrise

[![CI](https://github.com/chubbyphp/chubbyphp-framework-router-sunrise/workflows/CI/badge.svg?branch=master)](https://github.com/chubbyphp/chubbyphp-framework-router-sunrise/actions?query=workflow%3ACI)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/chubbyphp-framework-router-sunrise/badge.svg?branch=master)](https://coveralls.io/github/chubbyphp/chubbyphp-framework-router-sunrise?branch=master)
[![Infection MSI](https://badge.stryker-mutator.io/github.com/chubbyphp/chubbyphp-framework-router-sunrise/master)](https://dashboard.stryker-mutator.io/reports/github.com/chubbyphp/chubbyphp-framework-router-sunrise/master)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-framework-router-sunrise/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-sunrise)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-framework-router-sunrise/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-sunrise)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-framework-router-sunrise/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-sunrise)

[![bugs](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework-router-sunrise&metric=bugs)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework-router-sunrise)
[![code_smells](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework-router-sunrise&metric=code_smells)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework-router-sunrise)
[![coverage](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework-router-sunrise&metric=coverage)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework-router-sunrise)
[![duplicated_lines_density](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework-router-sunrise&metric=duplicated_lines_density)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework-router-sunrise)
[![ncloc](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework-router-sunrise&metric=ncloc)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework-router-sunrise)
[![sqale_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework-router-sunrise&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework-router-sunrise)
[![alert_status](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework-router-sunrise&metric=alert_status)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework-router-sunrise)
[![reliability_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework-router-sunrise&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework-router-sunrise)
[![security_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework-router-sunrise&metric=security_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework-router-sunrise)
[![sqale_index](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework-router-sunrise&metric=sqale_index)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework-router-sunrise)
[![vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-framework-router-sunrise&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-framework-router-sunrise)

## Description

Sunrise Router implementation for [chubbyphp-framework][1].

DEPRECATED: I suggest to use [chubbyphp-framework-router-fastroute][11].

## Requirements

 * php: ^8.0
 * [chubbyphp/chubbyphp-framework][1]: ^5.0.3
 * [chubbyphp/chubbyphp-http-exception][2]: ^1.0.1
 * [psr/http-message][3]: ^1.0.1
 * [sunrise/http-router][4]: ^2.6

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-framework-router-sunrise][10].

```bash
composer require chubbyphp/chubbyphp-framework-router-sunrise "^2.0"
```

## Usage

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\Middleware\ExceptionMiddleware;
use Chubbyphp\Framework\Middleware\RouterMiddleware;
use Chubbyphp\Framework\RequestHandler\CallbackRequestHandler;
use Chubbyphp\Framework\Router\Sunrise\Router;
use Chubbyphp\Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

$loader = require __DIR__.'/vendor/autoload.php';

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
    ])),
]);

$app->emit($app->handle((new ServerRequestFactory())->createFromGlobals()));
```

## Copyright

2023 Dominik Zogg

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-framework
[2]: https://packagist.org/packages/chubbyphp/chubbyphp-http-exception
[3]: https://packagist.org/packages/psr/http-message
[4]: https://packagist.org/packages/sunrise/http-router
[10]: https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-sunrise
[11]: https://packagist.org/packages/chubbyphp/chubbyphp-framework-router-fastroute
