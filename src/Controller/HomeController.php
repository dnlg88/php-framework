<?php

namespace App\Controller;
use App\Widget;
use Dambo\Framework\Controller\AbstractController;
use Dambo\Framework\Http\Response;
use Twig\Environment;

class HomeController extends AbstractController
{
    public function __construct(private Widget $widget)
    {
    }

    public function index(): Response
    {
        $content = "<h1>Hello {$this->widget->name}</h1>";
        return new Response($content);
    }
}