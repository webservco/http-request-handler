<?php

declare(strict_types=1);

namespace WebServCo\Http\Service\Message\Request\RequestHandler\ThreePart;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UnexpectedValueException;
use WebServCo\Controller\Contract\ControllerInstantiatorInterface;
use WebServCo\Http\Service\Message\Request\RequestHandler\AbstractRequestHandler;
use WebServCo\Route\Contract\ThreePart\RoutePartsInterface;
use WebServCo\View\Contract\ViewRendererListInterface;
use WebServCo\View\Contract\ViewRendererResolverInterface;

use function array_key_exists;
use function is_string;
use function sprintf;

/**
 * A simple request handler to test PSR-15.
 *
 * Uses the three part routing.
 * RouteMiddleware > ResourceMiddleware > this
 */
abstract class AbstractThreePartRequestHandler extends AbstractRequestHandler implements
    RequestHandlerInterface,
    RoutePartsInterface,
    ViewRendererListInterface
{
    /**
     * @param array<string,\WebServCo\Route\Service\ControllerView\RouteConfiguration> $routesConfiguration
     */
    public function __construct(
        ControllerInstantiatorInterface $controllerInstantiator,
        ViewRendererResolverInterface $viewRendererResolver,
        private array $routesConfiguration,
    ) {
        parent::__construct($controllerInstantiator, $viewRendererResolver);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $this->getRoutePart2($request);

        if (!array_key_exists($route, $this->routesConfiguration)) {
            // Current route not found in the configuration list.
            throw new UnexpectedValueException(sprintf('Unhandled route: "%s".', $route));
        }

        return $this->handleWithRouteConfiguration($request, $this->routesConfiguration[$route]);
    }

    protected function getRoutePart2(ServerRequestInterface $request): string
    {
        $result = $request->getAttribute(self::ROUTE_PART_2, null);

        if ($result === null) {
            // Throw exception, since we are not middleware and can not simply pass the request to the next handler.
            throw new UnexpectedValueException('Route is not defined.');
        }

        if (!is_string($result)) {
            // Sanity check, should never happen.
            throw new UnexpectedValueException('Route is not a string.');
        }

        return $result;
    }
}
