<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CoordinateurController extends AbstractController{
    #[Route('/coordinateur', name: 'app_coordinateur')]
    public function index(): Response
    {
        return $this->render('coordinateur/index.html.twig', [
            'controller_name' => 'CoordinateurController',
        ]);
    }
}
