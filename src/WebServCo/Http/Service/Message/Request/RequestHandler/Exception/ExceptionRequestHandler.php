<?php

declare(strict_types=1);

namespace WebServCo\Http\Service\Message\Request\RequestHandler\Exception;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use UnexpectedValueException;
use WebServCo\Controller\Contract\ControllerInstantiatorInterface;
use WebServCo\DependencyContainer\Contract\LocalDependencyContainerInterface;
use WebServCo\Route\Contract\RouteConfigurationInterface;
use WebServCo\View\Contract\HTMLRendererInterface;
use WebServCo\View\Contract\JSONRendererInterface;
use WebServCo\View\Contract\ViewRendererResolverInterface;
use WebServCo\View\Service\HTMLRenderer;
use WebServCo\View\Service\JSONRenderer;

/**
 * Custom request handler in case of exception.
 *
 * Not extending `AbstractRequestHandler`, instead using custom functionality
 * in order to prevent further exceptions when handling the request.
 */
final class ExceptionRequestHandler implements RequestHandlerInterface
{
    private const AVAILABLE_VIEW_RENDERERS = [
        HTMLRendererInterface::class => HTMLRenderer::class,
        JSONRendererInterface::class => JSONRenderer::class,
    ];

    public function __construct(
        private ControllerInstantiatorInterface $controllerInstantiator,
        private LocalDependencyContainerInterface $localDependencyContainer,
        private LoggerInterface $logger,
        private ViewRendererResolverInterface $viewRendererResolver,
        private RouteConfigurationInterface $routeConfiguration,
    ) {
    }

    /**
     * Based on `AbstractRequestHandler`.`handleWithRouteConfiguration`.
     *
     * Customization: make sure there is a fallback view renderer.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * Customization based on `handleWithRouteConfiguration`.
         * Use a default renderer if no match in order to prevent unhandled exception.
         */
        try {
            $viewRenderer = $this->viewRendererResolver->getViewRendererClass(self::AVAILABLE_VIEW_RENDERERS, $request);
        } catch (UnexpectedValueException $e) {
            /**
             * No view renderer available, or view not set.
             * Indicates a problem with the request "Accept" header (missing, invalid, unhandled).
             * Possible situations:
             * - exception handler middleware runs after view renderer middleware and no renderer is available;
             * - exception handler middleware runs before view renderer middleware and renderer processing was not done
             * before the error occurred;
             */
            // Log error.
            $this->logger->error($e->getMessage(), ['throwable' => $e]);
            // Since browsers should generally use the "any" flag, assume mostly api access, so use JSON as fallback.
            $viewRenderer = JSONRenderer::class;
        }

        $controller = $this->controllerInstantiator->instantiateController(
            $this->localDependencyContainer,
            $this->routeConfiguration,
            $viewRenderer,
        );

        return $controller->handle($request);
    }
}
