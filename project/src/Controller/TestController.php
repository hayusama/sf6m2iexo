<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController {
    #[Route('/testRouting', name:'test-route')]
    public function index() : Response {
        return new Response("<body>Bonjour à tous</body>");
    }
}