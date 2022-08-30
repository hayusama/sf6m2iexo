<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;

;

class BlogController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        return $this->render("blog/index.html.twig");
    }

    #[Route('/add', name:"article_add")]
    public function add():Response {
        return $this->render("blog/ajout.html.twig");
    }

    #[Route('/article/{id}/{url}', name:"article_show", requirements: ['id' => "\d+", 'url'=>'.{1,}'])]
    public function show($id,$url, Request $request, Environment $twig):Response {
        $globals = $twig->getGlobals();

        dump($globals['webmaster']);
        $tabInfo = [
            "id" => 8,
            "nom" => "Dupond",
            "prenom" => "Xavier",
            "age" => 60,
            "localisation" => "disparu"
        ];
        return $this->render("blog/lecture.html.twig", ['id' => $id, 'url'=> $url, 'tabInfo' => $tabInfo]);
    }

    #[Route('/edition/{id}', name:"article_edit", requirements: ['id' => "\d+"])]
    public function edit($id):Response {
        return $this->render("blog/edition.html.twig", ['id' => $id]);
    }

    #[Route('/suppression/{id}', name:"article_delete", requirements: ['id' => "\d+"])]
    public function remove($id):Response {
        return new Response("<body><h1>Supprimer article {$id}</h1></body>");
    }
}
