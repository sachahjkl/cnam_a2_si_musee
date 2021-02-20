<?php

namespace App\Controller;

use App\Repository\EmplacementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/emplacement", name: "emplacement")]
class EmplacementController extends AbstractController
{
    #[Route('/', name: '_root')]
    #[Route('/index', name: '_index')]
    public function index(EmplacementRepository $emplacementRepository): Response
    {
        $emplacements = $emplacementRepository->findAllFast();

        return $this->render('emplacement/index.html.twig', [
            'emplacements' => $emplacements,
            'title' => "Index des emplacements ayant des musées"
        ]);
    }

    #[Route('/search/', name: '_search')]
    public function search(Request $request, EmplacementRepository $villeRepository): Response
    {
        $query = $request->get("query", "");
        $emplacements = $villeRepository->findContainingWordInNameFast($query);
        return $this->render('emplacement/result.html.twig', [
            "query" => $query,
            "emplacements" => $emplacements,
            "title" => "Recherche des emplacements : " . $query
        ]);
    }

    #[Route('/show/{id}', name: '_show')]
    public function show(string $id, Request $request, EmplacementRepository $emplacementRepository): Response
    {
        $emplacement = $emplacementRepository->findById($id);
        return $this->render('emplacement/show.html.twig',
            [
                "title" => "Résumé de l'emplacement : " . $emplacement->getName(),
                "emplacement" => $emplacement,
                "id" => $id,
                "query" => $request->get("query")
            ]);
    }
}
