<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/musee", name: "musee")]
class MuseeController extends AbstractController
{
    #[Route('/', name: '_root')]
    #[Route('/index', name: '_index')]
    public function index(): Response
    {
        return $this->render('musee/index.html.twig', [
            'controller_name' => 'MuseeController',
        ]);
    }

    #[Route('/search/', name: '_search')]
    public function search(): Response
    {
        return $this->render('musee/index.html.twig',);
    }

    #[Route('/show/{id}', name: '_show')]
    public function show(): Response
    {
        return $this->render('musee/index.html.twig',);
    }
}
