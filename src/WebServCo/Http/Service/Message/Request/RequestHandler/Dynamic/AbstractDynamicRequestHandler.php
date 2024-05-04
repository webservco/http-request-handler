<?php

declare(strict_types=1);

namespace WebServCo\Http\Service\Message\Request\RequestHandler\Dynamic;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UnexpectedValueException;
use WebServCo\Controller\Contract\ControllerInstantiatorInterface;
use WebServCo\Http\Contract\Message\Request\Server\ServerRequestAttributeServiceInterface;
use WebServCo\Http\Service\Message\Request\RequestHandler\AbstractRequestHandler;
use WebServCo\Route\Contract\Dynamic\RoutePartsInterface;
use WebServCo\View\Contract\ViewRendererListInterface;
use WebServCo\View\Contract\ViewRendererResolverInterface;

use function array_key_exists;
use function sprintf;

/**
 * A simple request handler to test PSR-15.
 *
 * Uses dynamic parts routing.
 *
 * RouteMiddleware > ResourceMiddleware > this
 */
abstract class AbstractDynamicRequestHandler extends AbstractRequestHandler implements
    RequestHandlerInterface,
    RoutePartsInterface,
    ViewRendererListInterface
{
    /**
     * @param array<string,\WebServCo\Route\Service\ControllerView\RouteConfiguration> $routesConfiguration
     */
    public function __construct(
        ControllerInstantiatorInterface $controllerInstantiator,
        private ServerRequestAttributeServiceInterface $requestAttributeService,
        ViewRendererResolverInterface $viewRendererResolver,
        // Routes configuration not private because the class is abstract and can be extended.
        protected array $routesConfiguration,
    ) {
        parent::__construct($controllerInstantiator, $viewRendererResolver);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $this->requestAttributeService->getRoutePart(2, $request);
        if ($route === null) {
            // Throw exception, since we are not middleware and can not simply pass the request to the next handler.
            throw new UnexpectedValueException('Route is not defined.');
        }

        if (!array_key_exists($route, $this->routesConfiguration)) {
            // Current route not found in the configuration list.
            throw new UnexpectedValueException(sprintf('Unhandled route: "%s".', $route));
        }

        return $this->handleWithRouteConfiguration($request, $this->routesConfiguration[$route]);
    }
}
