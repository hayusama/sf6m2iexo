<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CmsController extends AbstractController
{
    #[Route('/', name: 'app_cms')]
    public function index(): Response
    {
        $info = 5;
        return $this->render('cms/index.html.twig', [
            'controller_name' => 'CmsController',
            'info' => $info
        ]);
    }

    #[Route(path: '/info/{id}/{slug}', name:"app_test_uri")]
    public function testUri($id,$slug){
        $info = "Anthony";
        return $this->render('cms/index.html.twig', [
            'controller_name' => "id = {$id} / slug = {$slug}",
            'info' => $info
        ]);
    }

    #[Route(path: '/info2/{id}/{slug}.{!format}',
     name:"app_test_uri2",
     requirements : ['id' => '\d{2,4}', 'slug' => '[a-zA-Z0-9\-]{2,}', 'format' => "html|htm|php|json" ],
     defaults: ["format" => "html"])]
    public function testUri2($id,$slug,$format){
        $info = "Anthony";
        return $this->render('cms/index.html.twig', [
            'controller_name' => "id = {$id} / slug = {$slug}",
            'info' => $info,
            'id' => $id,
            'slug' => $slug,
            'format' => $format
        ]);
    }

    //_format permet de modifier l'entete HTML 
    #[Route(path: '/i/{slug}.{_format}',
    name:"app_test_uri4",
    requirements : [ 'slug' => '[a-zA-Z0-9\-]{2,}', '_format' => "html|json|csv" ],
    defaults: ["_format" => "html"])]
   public function format($slug){
       return new Response("ok");
   }



   #[Route(path: '/url', name:"app_url")]
   public function url(){
    $url = $this->generateUrl("app_test_uri2", ['id'=> 55, 'slug' => "Hello-All", 'format' => 'php'], UrlGeneratorInterface::ABSOLUTE_URL);
    return new Response($url);
   }

   #[Route(path: '/cms', name:"app_cms")]
   public function cmsTest(): Response{
    return new Response("<body>Hello World</body>");
   }


}
