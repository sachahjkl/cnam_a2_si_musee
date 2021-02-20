<?php

namespace App\Controller;

use App\Repository\MuseeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/musee", name: "musee")]
class MuseeController extends AbstractController
{
    #[Route('/', name: '_root')]
    #[Route('/index', name: '_index')]
    public function index(MuseeRepository $museeRepository): Response
    {
        $musees = $museeRepository->findAllFast();

        return $this->render('musee/index.html.twig', [
            'musees' => $musees,
            'title' => "Index des musées"
        ]);
    }

    #[Route('/search', name: '_search')]
    public function search(Request $request, MuseeRepository $museeRepository): Response
    {
        $query = $request->get("query", "");
        $musees = $museeRepository->findContainingWordInNameFast($query);
        return $this->render('musee/result.html.twig', [
            "title" => "Recherche des musées : " . $query,
            "query" => $query,
            "musees" => $musees
        ]);
    }


    #[Route('/show/{id}', name: '_show')]
    public function show(string $id, MuseeRepository $museeRepository, Request $request): Response
    {
        $musee = $museeRepository->findById($id);
        return $this->render('musee/show.html.twig',
            [
                "title" => "Résumé du musée : " . $musee->getName(),
                "query" => $request->get("query"),
                "musee" => $musee,
                "id" => $id
            ]);
    }

}
