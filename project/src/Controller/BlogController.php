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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

;

class BlogController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(ManagerRegistry $doctrine): Response
    {
        // $em = $doctrine->getManager();
        // $article = $em->getRepository(Article::class)->jointure(1, true);
        // dump($article);
        // dump($article[0]->getImage()->getAlt());

        //PAS BESOIN DE PASSER PAR GETMANAGER CAR LES DONNEES SONT DEJA EN BASES (C'EST FACULTATIF DANS CE CAS)
        //MAIS SINON VOUS POUVEZ FAIRE $em = $doctrine->getManager() ca marche aussi
        $articles = $doctrine->getRepository(Article::class)->jointureIndex();
        dump($articles);
        return $this->render("blog/index.html.twig",["articles" => $articles]);
    }




    /**
     * Ajout formulaire
     *
     * @return Response
     */
    #[Route('/add', name:"article_add")]
    public function add(Request $request, ManagerRegistry $doctrine):Response {
        //VERIFICATION DU ROLE
        //AUTRE POSSIBILITE
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

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
    #[ParamConverter('Article', class: Article::class)]
    public function show(article $article):Response {
        return $this->render("blog/lecture.html.twig", ['article'=> $article]);
    }


    #[Route('/edition/{id}', name:"article_edit", requirements: ['id' => "\d+"])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Article $article, ManagerRegistry $doctrine, Request $request):Response {
        $oldImage = $article->getImage()->getChemin();
        $form = $this->createForm(ArticleType::class,$article);
        // $form['image']['chemin']->setData('ok');
        // dump($form['image']['chemin']->getData());
        dump($article);
        //ATTENTION APRES HANDLE REQUEST L'OBJET ARTICLE EST MODIFIE
        $form->handleRequest($request);
        dump($article);
        if($form->isSubmitted() && $form->isValid()){
            $article->setLastUpdateDate(new \DateTime());

            if($article->isPublished()){
                $article->setPublicationDate(new \DateTime());
            }

            if($article->getImage()->getChemin() !== null && $article->getImage()->getChemin() !== $oldImage){
                $file = $form->get('image')->get('chemin')->getData();
                $fileName = uniqid(). '.'.$file->guessExtension();

                try{
                    $file->move($this->getParameter('images_directory'),$fileName);
                    $article->getImage()->setChemin($fileName);
                }catch(FileException $e){
                    return new Response($e->getMessage());
                }
            } else {
                $article->getImage()->setChemin($oldImage);
            }

        $em = $doctrine->getManager();
        //PAS DE PERSIST CAR ARTICLE VIENT DE LA BASE
        $em->flush();

        $this->addFlash('info', 'Votre article a été modifié!');

        return $this->redirectToRoute('homepage');

        }
        return $this->render("blog/edition.html.twig", ['article' => $article, 'form' => $form->createView()]);
    }

    #[Route('/suppression/{id}', name:"article_delete", requirements: ['id' => "\d+"])]
    #[ParamConverter('Article', class: Article::class)]
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
            ['title'=> "Connexion", "text"=>'Connexion', "url"=> $this->generateUrl('app_login'), "user"=>false],
            ['title'=> "Création de compte", "text"=>'Création de compte', "url"=> $this->generateUrl('app_register'), "user"=>false],
            ['title'=> "Déconnexion", "text"=>'Déconnexion', "url"=> $this->generateUrl('app_logout'), "user"=>true],
        ];

        return $this->render("parts/menu.html.twig", ["listMenu" => $listMenu]);
    }

    public function proverbe(Proverbe $proverbe){
        return $this->render("parts/proverbe.html.twig", ["proverbe" => $proverbe->getProverbe()]); 
    }
}
