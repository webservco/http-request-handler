<?php

declare(strict_types=1);

namespace WebServCo\Http\Service\Message\Request\RequestHandler\ThreePart;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;

use function array_key_exists;
use function is_string;
use function sprintf;

/**
 * A custom API specific implementation of the Three Part RequestHandler
 * Default implementation reads route from `RoutePartsInterface::ROUTE_PART_2`
 * This implementation reads route from both
 * `RoutePartsInterface::ROUTE_PART_2` and `RoutePartsInterface::ROUTE_PART_3`
 * (ROUTE_PART_2 is used for the API version)
 */
abstract class AbstractApiThreePartRequestHandler extends AbstractThreePartRequestHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // In this custom situation, the route contains also the API version/
        $route = sprintf('%s/%s', $this->getRoutePart2($request), $this->getRoutePart3($request) ?? '');

        if (!array_key_exists($route, $this->routesConfiguration)) {
            // Current route not found in the configuration list.
            throw new UnexpectedValueException(sprintf('Unhandled route: "%s".', $route));
        }

        return $this->handleWithRouteConfiguration($request, $this->routesConfiguration[$route]);
    }

    protected function getRoutePart3(ServerRequestInterface $request): ?string
    {
        $result = $request->getAttribute(self::ROUTE_PART_3, null);

        if ($result === null) {
            return null;
        }

        if (!is_string($result)) {
            // Sanity check, should never happen.
            throw new UnexpectedValueException('Route is not a string.');
        }

        return $result;
    }
}
