<?php

namespace App\Controller;

use Twig\Environment;
use App\Service\Proverbe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function show($id,$url):Response {
        return $this->render("blog/lecture.html.twig", ['id' => $id, 'url'=> $url]);
    }

    #[Route('/edition/{id}', name:"article_edit", requirements: ['id' => "\d+"])]
    public function edit($id):Response {
        return $this->render("blog/edition.html.twig", ['id' => $id]);
    }

    #[Route('/suppression/{id}', name:"article_delete", requirements: ['id' => "\d+"])]
    public function remove($id):Response {
        return new Response("<body><h1>Supprimer article {$id}</h1></body>");
    }


    public function menu(){
        $listMenu = [
            ['title'=> "Mon blog", "text"=>'Accueil', "url"=> $this->generateUrl('homepage')],
            ['title' => "login", "text" => "Connexion", "url" => "/login"]
        ];

        return $this->render("parts/menu.html.twig", ["listMenu" => $listMenu]);
    }

    public function proverbe(Proverbe $proverbe){
        return $this->render("parts/proverbe.html.twig", ["proverbe" => $proverbe->getProverbe()]); 
    }
}
