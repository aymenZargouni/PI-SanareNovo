<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\RendezVous;
use App\Form\RendezvousType;
use App\Repository\RendezVousRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class RendezvousController extends AbstractController
{
    #[Route('/patient/rendezvous', name: 'app_rendezvous')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'DossiermedicaleController',
        ]);
    }

    #[Route('/patient/showrv', name: 'app_showrv')]
    public function showrv(RendezVousRepository $rendezVousRepository): Response 
    {
        // Get authenticated user
        $user = $this->getUser();
    
        // Ensure the user has a patient profile
        if (!$user || !$user->getPatient()) {
            throw $this->createAccessDeniedException('Vous devez être un patient pour voir vos rendez-vous.');
        }
    
        // Get the patient's rendez-vous
        $patient = $user->getPatient();
        $rv = $rendezVousRepository->findBy(['patient' => $patient]);
    
        return $this->render('rendezvous/showrv.html.twig', [
            'showrv' => $rv,
        ]);
    }
    
        

    #[Route('/patient/addformrv', name: 'app_addformrv')]
    public function addformrv(ManagerRegistry $m, Request $req): Response
    {
        $em = $m->getManager();
        $user = $this->getUser(); // Get the authenticated user
    
        if (!$user instanceof User || !$user->getPatient()) {
            throw $this->createAccessDeniedException('Vous devez être un patient pour prendre un rendez-vous.');
        }
    
        $patient = $user->getPatient(); // Get the associated Patient entity
    
        $rendezvous = new RendezVous();
        $rendezvous->setStatut('En attente');
        $rendezvous->setPatient($patient); // Assign the authenticated patient
    
        $form = $this->createForm(RendezvousType::class, $rendezvous, [
            'is_update' => false,
        ]);
    
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) { 
            $em->persist($rendezvous); 
            $em->flush();
    
            $this->addFlash('success', '✅ Rendez-vous ajouté avec succès.');
            return $this->redirectToRoute('app_showrv');
        }
    
        return $this->render('rendezvous/addformrv.html.twig', [
            'formaddrv' => $form,
        ]);
    }    

    #[Route('/patient/updaterv/{id}', name: 'app_updaterv')]
    public function updateformdm($id,ManagerRegistry $m ,Request $req,RendezVousRepository $repo): Response
    {
        $em=$m->getManager();
        $authors= $repo->find($id);
        $forms=$this->createForm(RendezvousType::class,$authors,[
            'is_update' => true,
        ]) ; 
        $forms->handleRequest($req);
        if($forms->isSubmitted() && $forms->isValid() ){ 
        $em->persist(object: $authors); 
        $em->flush();
        return $this->redirectToRoute('app_showrv');
        }

        return $this->render('rendezvous/addformrv.html.twig', [
            'formaddrv' => $forms,
            'isUpdate' => true,
        ]);
    }

    #[Route('/patient/deleterv/{id}', name: 'app_deleterv')]
    public function deleteformdm($id,ManagerRegistry $m ,Request $req,RendezVousRepository $repo): Response
    {
        $em=$m->getManager();
        $cons= $repo->find($id);
            $em->remove(object: $cons);
            $em->flush();
            return $this->redirectToRoute('app_showrv');
    }

    #[Route('/medecin/rendezvous', name: 'app_medecin_rendezvous')]
public function medecinRendezVous(RendezVousRepository $rendezVousRepository): Response
{
    // Get the authenticated user
    $user = $this->getUser();

    // Ensure the user is a medecin
    if (!$user || !$user->getMedecin()) {
        throw $this->createAccessDeniedException('Vous devez être un médecin pour voir vos rendez-vous.');
    }

    // Get the medecin entity
    $medecin = $user->getMedecin();

    // Fetch the medecin's rendezvous
    $rendezvous = $rendezVousRepository->findByMedecin($medecin);

    return $this->render('rendezvous/showrvMedecin.html.twig', [
        'rendezvous' => $rendezvous,
    ]);
}
}
