    <?php

    use Illuminate\Foundation\Application;
    use Illuminate\Foundation\Configuration\Exceptions;
    use Illuminate\Foundation\Configuration\Middleware;
    use App\Http\Middleware\AdminMiddleware;
    use App\Http\Middleware\SurveyorMiddleware;
    use App\Http\Middleware\CBEMiddleware;
    use App\Http\Middleware\TaxCollectorMiddleware;
    use App\Http\Middleware\RedirectIfAuthenticated; // Add this line to include the guest middleware


    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Auth\Sanctum\AuthenticationException;
    use Illuminate\Auth\Sanctum\UnauthorizedHttpException;
    use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
    use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
    use Symfony\Component\HttpKernel\Exception\RouteNotFoundException;
    use Illuminate\Support\Str;

    return Application::configure(basePath: dirname(__DIR__))
        ->withRouting(
            web: __DIR__ . '/../routes/web.php',
            commands: __DIR__ . '/../routes/console.php',
            health: '/up',
        )
        ->withMiddleware(function (Middleware $middleware) {
            // Use the alias method to map middleware aliases
            $middleware->alias([
                'admin' => AdminMiddleware::class,
                'surveyor' => SurveyorMiddleware::class,
                'cbe' => CBEMiddleware::class,
                'taxcollector' => TaxCollectorMiddleware::class,
                'guest' => RedirectIfAuthenticated::class, // Add the guest alias here
            ]);
        })
        ->withExceptions(function (Exceptions $exceptions) {
            $exceptions->render(function (NotFoundHttpException $e, Request $request) {
                if ($request->is('api/*')) {
                    return response()->json([
                        'code' => 404,
                        'message' => 'Record not found.'
                    ], 404);
                }
            });

            $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
                return response()->json([
                    'code' => 405,
                    'message' => 'Method Not Allowed'
                ], 405);
            });

            $exceptions->renderable(function ($e, Request $request) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Method Not Allowed'
                ], 500);
            });

            // authentication exception
            $exceptions->render(function (Throwable $e, Request $request) {
                if ($request->is('api/*')) {
                    if (Str::contains($e->getMessage(), 'login')) {
                        return response()->json([
                            'code' => 401,
                            'message' => 'Unauthenticated.'
                        ], 401);
                    }
                }
            });
        })->create();
