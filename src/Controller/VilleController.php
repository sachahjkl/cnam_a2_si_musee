<?php

namespace App\Controller;

use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/ville", name: "ville")]
class VilleController extends AbstractController
{
    #[Route('/', name: '_root')]
    #[Route('/index', name: '_index')]
    public function index(): Response
    {
        return $this->render('ville/index.html.twig', [
            'controller_name' => 'VilleController',
        ]);
    }

    #[Route('/search/', name: '_search')]
    public function search(Request $request, VilleRepository $villeRepository): Response
    {
        $query = $request->get("query","none");
        $result = [];
        if ($query != "none") {
            $result = $villeRepository->findContaining($query);
        }
        return $this->render('ville/result.html.twig', [
            "query" => $query,
            "result" => $result,
            "title" => "Recherche par villes"
        ]);
    }

    #[Route('/show/{id}', name: '_show')]
    public function show(): Response
    {
        return $this->render('ville/index.html.twig',);
    }
}
