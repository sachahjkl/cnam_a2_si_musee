<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/", name: "accueil")]
class IndexController extends AbstractController
{
    #[Route('/accueil', name: '_index')]
    #[Route('/', name: '_root')]
    public function index(): Response
    {
        return $this->render('accueil/index.html.twig');
    }
}
