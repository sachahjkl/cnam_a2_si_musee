<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/directeur', name: 'directeur')]
class DirecteurController extends AbstractController
{
    #[Route('/', name: '_root')]
    public function index(): Response
    {
        return $this->render('directeur/index.html.twig', [
            'controller_name' => 'DirecteurController',
        ]);
    }

    #[Route('/show/{id}', name: '_show')]
    public function show(): Response
    {
        return $this->render('ville/index.html.twig');
    }
}
