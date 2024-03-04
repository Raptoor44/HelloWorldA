<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{

    #[Route("/hello")]
    public function helloWorld(): Response
    {
        return $this->render('HelloWorld.html.twig');
    }

    #[Route("/base")]
    public function Base(): Response
    {
        return $this->render('base.html.twig');
    }

}

?>