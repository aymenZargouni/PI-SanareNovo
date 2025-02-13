<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController{
    #[Route('/home', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/department', name: 'department')]
    public function Departement(): Response
    {
        return $this->render('home/department.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/single_blog', name: 'single_blog')]
    public function Single_Blog(): Response     

    {
        return $this->render('home/single_blog.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/pages', name: 'pages')]
    public function Pages(): Response
    {
        return $this->render('home/¨pages.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/doctors', name: 'doctors')]
    public function Doctors(): Response
    {
        return $this->render('home/doctors.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function Contact(): Response
    {
        return $this->render('home/contact.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    #[Route('/about', name: 'about')]
    public function About(): Response
    {
        return $this->render('home/about.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    #[Route('/elements', name: 'elements')]
    public function Elements(): Response
    {
        return $this->render('home/elements.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    



}
