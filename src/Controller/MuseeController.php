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
    public function index(): Response
    {
        return $this->render('musee/index.html.twig', [
            'controller_name' => 'MuseeController',
        ]);
    }

    #[Route('/search/', name: '_search')]
    public function search(Request $request, MuseeRepository $museeRepository): Response
    {
        $query = $request->get("query","none");
        $result = [];
        if ($query != "none") {
            $result = $museeRepository->findContaining($query);
        }
        return $this->render('musee/result.html.twig', [
            "query" => $query,
            "result" => $result,
            "title" => "Recherche par musées"
        ]);
    }


    #[Route('/show/{id}', name: '_show')]
    public function show(): Response
    {
        return $this->render('musee/index.html.twig',);
    }
}
