<?php

$dotenv = new Symfony\Component\Dotenv\Dotenv();
$dotenv->load(BASE_PATH . '/.env');

$container = new League\Container\Container();
$container->delegate(new \League\Container\ReflectionContainer(true));

#parameters for application config
$routes = include BASE_PATH . '/routes/web.php';
$appEnv = $_SERVER['APP_ENV'];
$templatesPath = BASE_PATH . '/templates';

$container->add('APP_ENV', new \League\Container\Argument\Literal\StringArgument($appEnv));

#services

$container->add(
    \Dambo\Framework\Routing\RouterInterface::class,
    \Dambo\Framework\Routing\Router::class
);
$container
    ->extend(\Dambo\Framework\Routing\RouterInterface::class)
    ->addMethodCall(
        'setRoutes',
        [new \League\Container\Argument\Literal\ArrayArgument($routes)]
    );
$container->add(Dambo\Framework\Http\Kernel::class)
    ->addArgument(\Dambo\Framework\Routing\RouterInterface::class)
    ->addArgument($container);

$container->addShared('filesystem-loader', \Twig\Loader\FilesystemLoader::class)
    ->addArgument(new \League\Container\Argument\Literal\StringArgument($templatesPath));

$container->addShared('twig', \Twig\Environment::class)
    ->addArgument('filesystem-loader');
$container->add(\Dambo\Framework\Controller\AbstractController::class);
$container->inflector(\Dambo\Framework\Controller\AbstractController::class)
    ->invokeMethod('setContainer', [$container]);

return $container;