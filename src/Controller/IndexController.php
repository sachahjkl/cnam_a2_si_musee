<?php

namespace App\Controller;

use App\Repository\MuseeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/", name: "accueil")]
class IndexController extends AbstractController
{
    #[Route('/accueil', name: '_index')]
    #[Route('/', name: '_root')]
    public function index(MuseeRepository $museeRepository): Response
    {
        $result = $museeRepository->findAll();
        return $this->render('accueil/index.html.twig', [
            "entries" => $result
        ]);
    }
}
