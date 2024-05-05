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
 * This implementation reads route from both
 * route part 3 and route part 4
 * (route part 2 is used for the API version)
 */
abstract class AbstractApiDynamicRequestHandler extends AbstractDynamicRequestHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routePart3 = $this->requestAttributeService->getRoutePart(3, $request);
        if ($routePart3 === null) {
            throw new UnexpectedValueException('Route part 3 is required.');
        }

        // In this custom situation, the route contains also the API version/
        $route = sprintf(
            '%s/%s',
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
