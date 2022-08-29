<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
