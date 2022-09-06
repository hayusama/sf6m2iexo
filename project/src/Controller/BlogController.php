<?php

namespace App\Controller;

use App\Entity\Image;
use Twig\Environment;
use App\Entity\Article;
use App\Entity\Category;
use App\Service\Proverbe;
use Doctrine\Persistence\ManagerRegistry;
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

    #[Route('/fixadd', name: 'fixadd')]
    public function fixAdd(ManagerRegistry $doctrine){
        $em = $doctrine->getManager();

       
        $categoryRP = $em->getRepository(Category::class);
        
        $tabCategory = $categoryRP->findAll();

        $categoryP = $em->getRepository(Category::class)
                        ->findOneBy(["published" => true],["id"=>"desc"]);
        
                        dump($categoryP);

        // $image = new Image();
        // $image->setChemin('https://via.placeholder.com/700x120/111111/BABABA/?text=Mon+article+de+blog')
        //       ->setAlt('Information')
        //       ->setPublished(true);

        // $article = new Article;
        // $article->setTitle("Comment bien programmer?");
        // $article->setImage($image);
        // $article->addCategory($tabCategory[0]);
        // $article->addCategory($tabCategory[1]);
        // $article->setContent("Voici le contenu de mon premier article!");
        // $article->setLastUpdateDate(new \Datetime());
        // $article->setPublished(true);
        
        // $em->persist($article);
        // $em->flush();
        // dump($article);

        return new Response("<body>ok</body>");
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
