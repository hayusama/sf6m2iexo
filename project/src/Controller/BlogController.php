<?php

namespace App\Controller;

use App\Entity\Image;
use Twig\Environment;
use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Service\Proverbe;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

;

class BlogController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $article = $em->getRepository(Article::class)->jointure(1, true);
        dump($article);
        dump($article[0]->getImage()->getAlt());
        return $this->render("blog/index.html.twig");
    }




    /**
     * Ajout formulaire
     *
     * @return Response
     */
    #[Route('/add', name:"article_add")]
    public function add(Request $request, ManagerRegistry $doctrine):Response {
        dump($_POST);
        //CREATION D'UN OBJET ARTICLE
        $article = new Article;
        //CREATION DU FORMULAIRE ET ON PASSE NOTRE OBJET ARTICLE POUR POUVOIR L'HYDRATER
        $form = $this->createForm(ArticleType::class,$article);
        //HANDLEREQUEST PERMET dE RECUPERER LES DONNEES ENVOYEES UTILE EN CAS d4ERREUR DE FORMULAIRE
        $form->handleRequest($request);
        //VERIFICATION DE LA SOUMISSION
        if($form->isSubmitted() && $form->isValid()){
            //SURCHAGE LAST UPDATE DATE
            $article->setLastUpdateDate(new \DateTime());

            //SI ARTICLE PUBLIE == TRUE SURCHAGE DATE PUBLICATION
            if($article->isPublished()){
                $article->setPublicationDate(new \DateTime());
            }
            
            //GESTION IMAGE POUR EVITER DE CONSERVER LE FICHIER TEMPORAIRE NE BASE
            if($article->getImage()->getChemin() !== null){
                //RECUPERER LE CONTENU DU FICHIER
                $file = $form->get('image')->get('chemin')->getData();
                //JE GENERE UN NOM TOUJOURS ALEATOIRE POUR EVITER LES ECRASEMENT D'IMAGE
                $filename = uniqid().".".$file->guessExtension();

                try{
                    //ON BOUGE LE FICHIER TEMPORAIRE 2 PARAMETRES DOSSIER DESTINATION, NOM DU FICHIER
                    $file->move($this->getParameter('images_directory'), $filename);
                } catch(FileException $e) {
                    return new Response($e->getMessage());
                }
                //SURCHARGE DU CHEMIN
                $article->getImage()->setChemin($filename);
            }
            //AJOUT EN BASE je recupere le manager
            $em = $doctrine->getManager();
            //JE PERSISTE ARTICLE POUR DONNER LA MAIN A DOCTRINE SUR MON OBJET
            $em->persist($article);
            //JE LANCE LA TRANSACTION
            $em->flush();

            $this->addFlash('info', 'Votre article a été ajouté en base de données!');

            //REDIRECTION
            return $this->redirectToRoute('homepage');
            
        }
        return $this->render("blog/ajout.html.twig", ['form' => $form->createView()]);
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
        //RECUPERE L'ENTITY MANAGER
        $em = $doctrine->getManager();

       //SELECTIONNE LE REPOSITORY CATEGORY
        $categoryRP = $em->getRepository(Category::class);

        //LANCE LA METHODE FINDALL()
        $tabCategory = $categoryRP->findAll();

        //SELECTIONNE LE REPOSITORY CATEGORY ET LANCE UNE METHODE
        $categoryP = $em->getRepository(Category::class)
                        ->findOneBy(["published" => true],["id"=>"desc"]);
        
        dump($categoryP);

        //SELECTIONNE LE REPOSITORY ARTICLE ET LANCE UNE METHODE
        $article = $em->getRepository(Article::class)
                      ->findOneByTitle("Comment bien programmer?");

        dump($article);
        
        //DQL CUSTOM REQUEST
        $tabArticles = $doctrine->getManager()
                                ->getRepository(Article::class)
                                ->listingArticle(1);

        //QUERY BUILDER CUSTOM REQUEST
        $tabArticlesQB = $doctrine->getManager()
                                    ->getRepository(Article::class)
                                    ->listingArticleQB(1, true);

        //RAW SQL CUSTOM REQUEST
        $tabArticlesraw = $doctrine->getManager()
                                    ->getRepository(Article::class)
                                    ->listingArticleRawSql(1);


        dump($tabArticles, $tabArticlesQB, $tabArticlesraw);

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
