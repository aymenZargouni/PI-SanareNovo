<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Medecin;
use App\Entity\Patient;
use App\Form\MedecinType;
use App\Entity\Technicien;
use App\Form\EditUserType;
use App\Form\MedecinAddType;
use App\Form\MedecinEditType;
use App\Form\TechnicienAddType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Repository\MedecinRepository;
use App\Repository\TechnicienRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AdminController extends AbstractController{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    

    #[Route('/admin/showMedecin', name: 'app_showMedecin')]
    public function showMedecin(MedecinRepository $repo): Response
    {

        $medecin = $repo->findAll();
        return $this->render('admin/showMedecin.html.twig', [
            'medecin' => $medecin,
        ]);
    }

    #[Route('/admin/ajouterMedecin', name: 'ajouter_medecin')]
    public function ajouterMedecin(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $medecin = new Medecin();
        $form = $this->createForm(MedecinAddType::class, $medecin);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $user = new User();
            $user->setEmail($form->get('email')->getData());
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));
            $user->setRoles(['ROLE_MEDECIN']);

            $medecin->setUser($user);

            $entityManager->persist($user);
            $entityManager->persist($medecin);
            $entityManager->flush();

            return $this->redirectToRoute('app_showMedecin');
        }
        return $this->render('admin/ajouterMedecin.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/admin/editMedecin/{id}', name: 'edit_medecin')]
    public function editMedecin(Request $request, Medecin $medecin, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        
        $user = $medecin->getUser();

        
        $form = $this->createForm(MedecinEditType::class, $medecin, [
            'user_email' => $user->getEmail(),
        ]);
    
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $user->setEmail($form->get('email')->getData());
    
            
            $newPassword = $form->get('password')->getData();
            if ($newPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            }
    
            
            $entityManager->flush();
    
            return $this->redirectToRoute('app_showMedecin');
        }

        return $this->render('admin/editMedecin.html.twig', [
            'form' => $form->createView(),
            'medecin' => $medecin,
        ]);
    }
    #[Route('/admin/medecin/delete/{id}', name: 'delete_medecin')]
    public function deleteMedecin(Request $request, Medecin $medecin, EntityManagerInterface $entityManager): Response
    {
        
            $user = $medecin->getUser();
            $entityManager->remove($medecin);
            $entityManager->remove($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_showMedecin');
     } 

     #[Route('/admin/showUsers', name: 'app_showUsers')]
     public function showUsers(UserRepository $repo, PaginatorInterface $paginator, Request $request): Response
     {
         $query = $repo->createQueryBuilder('u')->getQuery();
 
         $pagination = $paginator->paginate(
             $query, // Query object
             $request->query->getInt('page', 1), // Get the current page number from the URL (default: 1)
             5 // Number of users per page
         );
 
         return $this->render('admin/showUsers.html.twig', [
             'pagination' => $pagination,
         ]);
     }

     #[Route('/admin/ajouterUser', name: 'app_ajouterUser')]
     public function ajouterUser(
         Request $request,
         EntityManagerInterface $entityManager,
         UserPasswordHasherInterface $passwordHasher,
         MailerInterface $mailer // Inject MailerInterface
     ): Response {
         $user = new User();
         $form = $this->createForm(UserType::class, $user);
         $form->handleRequest($request);
     
         if ($form->isSubmitted() && $form->isValid()) {
             $plainPassword = $form->get('password')->getData();
             $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
             $user->setPassword($hashedPassword);
             $user->setRoles($form->get('roles')->getData());
     
             $entityManager->persist($user);
             $entityManager->flush();
     
             // ✅ Send Email with Credentials
             $email = (new Email())
                 ->from('aymen.zargouni1996@gmail.com') // Your Gmail
                 ->to($user->getEmail()) // User's email from form
                 ->subject('Votre compte a été créé')
                 ->text("Bonjour,\n\nVotre compte a été créé avec succès.\n\nEmail: {$user->getEmail()}\nMot de passe: {$plainPassword}\n\nMerci!")
                 ->html("<p>Bonjour,</p><p>Votre compte a été créé avec succès.</p><p><strong>Email:</strong> {$user->getEmail()}</p><p><strong>Mot de passe:</strong> {$plainPassword}</p><p>Merci!</p>");
     
             $mailer->send($email); // Send the email
     
             $this->addFlash('success', 'Utilisateur ajouté avec succès! Un email avec les identifiants a été envoyé.');
             return $this->redirectToRoute('app_showUsers');
         }
     
         return $this->render('admin/ajouterUser.html.twig', [
             'form' => $form->createView(),
         ]);
     }
     

    #[Route('/admin/editUser/{id}', name: 'app_editUser')]
    public function modifierUser(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
    $form = $this->createForm(EditUserType::class, $user);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        
        $newPassword = $form->get('password')->getData();
        if ($newPassword) {
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
        }
        $user->setRoles($form->get('roles')->getData());

        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur modifié avec succès!');
        return $this->redirectToRoute('app_showUsers');
    }

    return $this->render('admin/editUser.html.twig', [
        'form' => $form->createView(),
        'user' => $user
    ]);
    }


    #[Route('/admin/deleteUser/{id}', name: 'delete_user')]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        
            $entityManager->remove($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_showUsers');
     } 

     #[Route('/admin/showPatients', name: 'app_showPatients')]
     public function showPatients(EntityManagerInterface $entityManager): Response
     {
         
         $patients = $entityManager->getRepository(Patient::class)->findAll();
 
         return $this->render('admin/showPatient.html.twig', [
             'patients' => $patients,
         ]);
     }

     #[Route('/admin/deletePatient/{id}', name: 'delete_patient')]
     public function deletePatient(Request $request, Patient $patient, EntityManagerInterface $entityManager): Response
     {
         
        $user = $patient->getUser();
        $entityManager->remove($patient);
        $entityManager->remove($user);
        $entityManager->flush(); 
            
        return $this->redirectToRoute('app_showUsers');
      } 

      #[Route('/admin/techniciens', name: 'show_techniciens')]
    public function showTechniciens(TechnicienRepository $technicienRepository): Response
    {   
        return $this->render('admin/showTechniciens.html.twig', [
        'techniciens' => $technicienRepository->findAll(),
        ]);
    }

    #[Route('/admin/ajouter-technicien', name: 'ajouter_technicien')]
    public function ajouterTechnicien(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $technicien = new Technicien();
        $form = $this->createForm(TechnicienAddType::class, $technicien);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Create a new User entity for the technician
            $user = new User();
            $user->setEmail($form->get('email')->getData());
            $user->setRoles(['ROLE_TECHNICIEN']);
            
            // Hash password
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            );
            $user->setPassword($hashedPassword);

            // Associate user with technicien
            $technicien->setUser($user);

            // Persist & save
            $entityManager->persist($user);
            $entityManager->persist($technicien);
            $entityManager->flush();

            $this->addFlash('success', 'Technicien ajouté avec succès.');

            return $this->redirectToRoute('show_techniciens');
        }

        return $this->render('admin/ajouterTechnicien.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    }