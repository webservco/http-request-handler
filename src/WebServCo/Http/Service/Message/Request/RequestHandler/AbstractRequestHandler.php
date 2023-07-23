<?php

declare(strict_types=1);

namespace WebServCo\Http\Service\Message\Request\RequestHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Controller\Contract\ControllerInstantiatorInterface;
use WebServCo\DependencyContainer\Contract\LocalDependencyContainerInterface;
use WebServCo\Route\Contract\RouteConfigurationInterface;
use WebServCo\View\Contract\ViewRendererListInterface;
use WebServCo\View\Contract\ViewRendererResolverInterface;

abstract class AbstractRequestHandler implements ViewRendererListInterface
{
    public function __construct(
        private ControllerInstantiatorInterface $controllerInstantiator,
        private LocalDependencyContainerInterface $localDependencyContainer,
        private ViewRendererResolverInterface $viewRendererResolver,
    ) {
    }

    protected function handleWithRouteConfiguration(
        ServerRequestInterface $request,
        RouteConfigurationInterface $routeConfiguration,
    ): ResponseInterface {
        // Get available View Renderers from child/implementing class.
        $availableViewRenderers = $this->getAvailableViewRenderers();

        $viewRenderer = $this->viewRendererResolver->getViewRendererClass($availableViewRenderers, $request);

        $controller = $this->controllerInstantiator->instantiateController(
            $this->localDependencyContainer,
            $routeConfiguration,
            $viewRenderer,
        );

        return $controller->handle($request);
    }
}
