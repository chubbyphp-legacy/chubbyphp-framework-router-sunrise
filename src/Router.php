<?php

declare(strict_types=1);

namespace Chubbyphp\Framework\Router\Sunrise;

use Chubbyphp\Framework\Router\Exceptions\MissingRouteByNameException;
use Chubbyphp\Framework\Router\Exceptions\RouteGenerationException;
use Chubbyphp\Framework\Router\Route;
use Chubbyphp\Framework\Router\RouteInterface;
use Chubbyphp\Framework\Router\RouteMatcherInterface;
use Chubbyphp\Framework\Router\UrlGeneratorInterface;
use Chubbyphp\HttpException\HttpException;
use Psr\Http\Message\ServerRequestInterface;
use Sunrise\Http\Router\Exception\InvalidAttributeValueException as SunriseInvalidAttributeValueException;
use Sunrise\Http\Router\Exception\MethodNotAllowedException as SunriseMethodNotAllowedException;
use Sunrise\Http\Router\Exception\MissingAttributeValueException as SunriseMissingAttributeValueException;
use Sunrise\Http\Router\Exception\RouteNotFoundException as SunriseRouteNotFoundException;
use Sunrise\Http\Router\RouteFactory;
use Sunrise\Http\Router\Router as SunriseRouter;

final class Router implements RouteMatcherInterface, UrlGeneratorInterface
{
    private SunriseRouter $router;

    /**
     * @param array<int, RouteInterface> $routes
     */
    public function __construct(array $routes, private string $basePath = '')
    {
        $this->router = $this->createRouter($routes);
    }

    public function match(ServerRequestInterface $request): RouteInterface
    {
        try {
            $sunriseRoute = $this->router->match($request);

            return Route::create(
                $request->getMethod(),
                $sunriseRoute->getPath(),
                $sunriseRoute->getName(),
                $sunriseRoute->getRequestHandler(),
                $sunriseRoute->getMiddlewares()
            )->withAttributes($sunriseRoute->getAttributes());
        } catch (SunriseRouteNotFoundException $exception) {
            throw HttpException::createNotFound([
                'detail' => sprintf(
                    'The page "%s" you are looking for could not be found.'
                    .' Check the address bar to ensure your URL is spelled correctly.',
                    $request->getRequestTarget()
                ),
            ]);
        } catch (SunriseMethodNotAllowedException $exception) {
            throw HttpException::createMethodNotAllowed([
                'detail' => sprintf(
                    'Method "%s" at path "%s" is not allowed. Must be one of: "%s"',
                    $request->getMethod(),
                    $request->getRequestTarget(),
                    implode('", "', $exception->getAllowedMethods()),
                ),
            ]);
        }
    }

    /**
     * @param array<string, string> $attributes
     * @param array<string, mixed>  $queryParams
     */
    public function generateUrl(
        ServerRequestInterface $request,
        string $name,
        array $attributes = [],
        array $queryParams = []
    ): string {
        $uri = $request->getUri();
        $requestTarget = $this->generatePath($name, $attributes, $queryParams);

        return $uri->getScheme().'://'.$uri->getAuthority().$requestTarget;
    }

    /**
     * @param array<string, string> $attributes
     * @param array<string, mixed>  $queryParams
     */
    public function generatePath(string $name, array $attributes = [], array $queryParams = []): string
    {
        try {
            $path = $this->router->generateUri($name, $attributes, true);
        } catch (SunriseRouteNotFoundException $exception) {
            throw MissingRouteByNameException::create($name);
        } catch (SunriseMissingAttributeValueException $exception) {
            $match = $exception->fromContext('match');

            $route = $this->router->getRoute($name);

            throw RouteGenerationException::create(
                $name,
                $route->getPath(),
                $attributes,
                new \RuntimeException(sprintf('Missing attribute "%s"', $match['name']))
            );
        } catch (SunriseInvalidAttributeValueException $exception) {
            $match = $exception->fromContext('match');
            $value = $exception->fromContext('value');

            $route = $this->router->getRoute($name);

            throw RouteGenerationException::create(
                $name,
                $route->getPath(),
                $attributes,
                new \RuntimeException(sprintf(
                    'Not matching value "%s" with pattern "%s" on attribute "%s"',
                    $value,
                    $match['pattern'],
                    $match['name']
                ))
            );
        }

        if ([] === $queryParams) {
            return $this->basePath.$path;
        }

        return $this->basePath.$path.'?'.http_build_query($queryParams);
    }

    /**
     * @param array<RouteInterface> $routes
     */
    private function createRouter(array $routes): SunriseRouter
    {
        $routeFactory = new RouteFactory();
        $router = new SunriseRouter();

        foreach ($routes as $route) {
            $router->addRoute($routeFactory->createRoute(
                $route->getName(),
                $route->getPath(),
                [$route->getMethod()],
                $route->getRequestHandler(),
                $route->getMiddlewares(),
                $route->getAttributes()
            ));
        }

        return $router;
    }
}
