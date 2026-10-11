# webservco/http-request-handler

A PHP component/library. Please see [webservco/sandbox](https://webservco.github.io/sandbox/) for an example project using these types of components.

PSR-15 request handlers, including a middleware stack, base handlers that pass a route to its controller, and a handler that renders error pages.

- Implements: [PSR-15: HTTP Server Request Handlers](https://www.php-fig.org/psr/psr-15/)
- Related: `webservco/middleware` (middleware for the stack), `webservco/controller` (creates the controllers), `webservco/route` (route files), `webservco/view` (chooses the renderer)

---

## Installation

```shell
composer require webservco/http-request-handler
```

---

## Usage

### Middleware stack

The fallback handler runs when no middleware returns a response. The `webservco/sandbox` project uses an `ExceptionRequestHandler` that shows the 404 page.

```php
use WebServCo\Http\Service\Message\Request\RequestHandler\Exception\ExceptionRequestHandler;
use WebServCo\Http\Service\Message\Request\RequestHandler\StackHandler;
use WebServCo\Route\Service\ControllerView\RouteConfiguration;

// \Psr\Http\Server\RequestHandlerInterface
$stackHandler = new StackHandler(
    new ExceptionRequestHandler(
        // \WebServCo\Controller\Contract\ControllerInstantiatorInterface
        $controllerInstantiator,
        // \Psr\Log\LoggerInterface
        $logger,
        // \WebServCo\View\Contract\ViewRendererResolverInterface
        $viewRendererResolver,
        new RouteConfiguration(NotFoundController::class),
    ),
);
// Middleware runs in the order it's added.
$stackHandler->addMiddleware($routeMiddleware);
$stackHandler->addMiddleware($resourceMiddleware);

$response = $stackHandler->handle($request);
```

### Module handler

Each module (`/example/...`, `/api/...`) gets a handler that looks up route part 2 in the module's route file. It also lists the renderers the module supports.

```php
use WebServCo\Http\Service\Message\Request\RequestHandler\Dynamic\AbstractDynamicRequestHandler;
use WebServCo\View\Contract\HTMLRendererInterface;
use WebServCo\View\Contract\JSONRendererInterface;
use WebServCo\View\Service\HTMLRenderer;
use WebServCo\View\Service\JSONRenderer;

final class ExampleRequestHandler extends AbstractDynamicRequestHandler
{
    public function getAvailableViewRenderers(): array
    {
        return [
            HTMLRendererInterface::class => HTMLRenderer::class,
            JSONRendererInterface::class => JSONRenderer::class,
        ];
    }
}
```

Its constructor takes the controller instantiator, the request attribute service, the renderer resolver and the routes loaded with `webservco/route`.

---

## Contracts

| Interface | Implementation | Purpose |
| --- | --- | --- |
| `\Psr\Http\Server\RequestHandlerInterface` | `Service\Message\Request\RequestHandler\StackHandler` | Runs middleware in order, then the fallback handler. |
| | `Service\Message\Request\RequestHandler\Dynamic\AbstractDynamicRequestHandler` | Routes by part 2 (`/module/route`). |
| | `Service\Message\Request\RequestHandler\Dynamic\AbstractApiDynamicRequestHandler` | Routes by parts 2 to 4 (`/api/v1/route`). |
| | `Service\Message\Request\RequestHandler\Exception\ExceptionRequestHandler` | Renders one controller, with a renderer fallback so error pages always work. |

Namespaces are relative to `WebServCo\Http\`.

---

## Notes

A route that isn't in the module's route file throws an `UnexpectedValueException`. Put an `ExceptionHandlerMiddleware` (from `webservco/middleware`) in the stack to turn it into an error page.

`ExceptionRequestHandler` supports HTML, JSON and JSON:API. If the request accepts none of them, it logs the problem and answers in JSON.

---

## Development

```shell
# Start project.
ddev start
# Install dependencies.
ddev c u
# Run all validations.
ddev c c
# Run unit tests.
ddev c test:dox
```

---

## License

[MIT](LICENSE.md)
