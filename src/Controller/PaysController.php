<?php

namespace App\Controller;

use App\Repository\PaysRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/pays", name: "pays")]
class PaysController extends AbstractController
{
    #[Route('/', name: '_root')]
    #[Route('/index', name: '_index')]
    public function index(): Response
    {
        return $this->render('pays/index.html.twig', [
            'controller_name' => 'PaysController',
        ]);
    }

    #[Route('/search/', name: '_search')]
    public function search(Request $request, PaysRepository $paysRepository): Response
    {
        $query = $request->get("query", "none");
        $result = [];
        if ($query != "none") {
            $result = $paysRepository->findContaining($query);
        }
        return $this->render('pays/result.html.twig', [
            "query" => $query,
            "result" => $result,
            "title" => "Recherche par pays"
        ]);
    }

    #[Route('/show/{id}', name: '_show')]
    public function show(): Response
    {
        return $this->render('pays/index.html.twig',);
    }
}
