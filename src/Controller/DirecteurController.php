<?php

namespace App\Controller;

use App\Repository\DirecteurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/directeur', name: 'directeur')]
class DirecteurController extends AbstractController
{
    #[Route('/', name: '_root')]
    #[Route('/index', name: '_index')]
    public function index(DirecteurRepository $directeurRepository): Response
    {
        $directeurs = $directeurRepository->findAllFast();

        return $this->render('directeur/index.html.twig', [
            'directeurs' => $directeurs,
            'title' => "Index des directeurs de musées"
        ]);
    }

    #[Route('/search', name: '_search')]
    public function search(Request $request, DirecteurRepository $directeurRepository): Response
    {
        $query = $request->get("query","");
        $directeurs = $directeurRepository->findContainingWordInNameFast($query);
        return $this->render('directeur/result.html.twig', [
            "query" => $query,
            "directeurs" => $directeurs,
            "title" => "Recherche des directeurs : " . $query
        ]);
    }


    #[Route('/show/{id}', name: '_show')]
    public function show(string $id, DirecteurRepository $directeurRepository, Request $request): Response
    {
        $directeur = $directeurRepository->findById($id);
        return $this->render('directeur/show.html.twig',
            [
                "title" => "Résumé du directeur : " . $directeur->getName(),
                "directeur" => $directeur,
                "id" => $id,
                "query" => $request->get("query")
            ]);
    }
}
