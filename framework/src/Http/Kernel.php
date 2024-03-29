<?php

namespace Dambo\Framework\Http;

use Dambo\Framework\Routing\Router;
use Dambo\Framework\Routing\RouterInterface;
use Psr\Container\ContainerInterface;

class Kernel
{
    private string $appEnv;
    public function __construct(
        private RouterInterface $router,
        private ContainerInterface $container
    )
    {
        $this->appEnv = $this->container->get('APP_ENV');
    }

    public function handle(Request $request) : Response
    {
        try
        {
            [$routeHandler, $var] = $this->router->dispatch($request, $this->container);
            $response = call_user_func_array($routeHandler, $var);
        }
        catch (\Exception $exception)
        {
            $response = $this->createExceptionResponse($exception);
        }
        return $response;
    }

    /**
     * @throws \Exception $exception
     */
    private function createExceptionResponse(\Exception $exception): Response
    {
        if (in_array($this->appEnv, ['dev', 'test']))
        {
            throw $exception;
        }
        if ($exception instanceof HttpException)
        {
            return new Response($exception->getMessage(), $exception->getStatusCode());
        }
        return new Response('Server error', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}