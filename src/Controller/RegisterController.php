<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Patient;
use App\Form\PatientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegisterController extends AbstractController{
    #[Route('/register', name: 'app_register')]
    public function index(): Response
    {
        return $this->render('register/index.html.twig', [
            'controller_name' => 'RegisterController',
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response {
        
        $patient = new Patient();
        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Create a new user
            $user = new User();
            $user->setEmail($form->get('email')->getData());
            $user->setRoles(['ROLE_PATIENT']); // Assign role
            $hashedPassword = $passwordHasher->hashPassword($user,$form->get('password')->getData()
            );
            $user->setPassword($hashedPassword);

            // Associate user with patient
            $patient->setUser($user);

            // Save to database
            $entityManager->persist($user);
            $entityManager->persist($patient);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
