<?php

declare(strict_types=1);

namespace WebServCo\Http\Service\Message\Request\RequestHandler\Dynamic;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;

use function array_key_exists;
use function sprintf;

/**
 * A custom API specific implementation of the Dynamic RequestHandler
 * Default implementation reads route from route part 2
 * This implementation reads route from route parts:
 * 2 (api version), 3 (mandatory), 4 (optional)
 */
abstract class AbstractApiDynamicRequestHandler extends AbstractDynamicRequestHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routePart2 = $this->requestAttributeService->getRoutePart(2, $request);
        if ($routePart2 === null) {
            throw new UnexpectedValueException('Route part 2 is required.');
        }

        $routePart3 = $this->requestAttributeService->getRoutePart(3, $request);
        if ($routePart3 === null) {
            throw new UnexpectedValueException('Route part 3 is required.');
        }

        // In this custom situation, the route contains also the API version/
        $route = sprintf(
            '%s/%s/%s',
            $routePart2,
            $routePart3,
            $this->requestAttributeService->getRoutePart(4, $request) ?? '',
        );

        if (!array_key_exists($route, $this->routesConfiguration)) {
            // Current route not found in the configuration list.
            throw new UnexpectedValueException(sprintf('Unhandled route: "%s".', $route));
        }

        return $this->handleWithRouteConfiguration($request, $this->routesConfiguration[$route]);
    }
}
