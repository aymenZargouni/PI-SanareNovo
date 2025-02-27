<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\RendezVous;
use App\Entity\Patient;
use App\Form\RendezvousType;
use App\Repository\RendezVousRepository;
use Doctrine\Persistence\ManagerRegistry;

final class RendezvousController extends AbstractController
{
    #[Route('/rendezvous', name: 'app_rendezvous')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'DossiermedicaleController',
        ]);
    }

    #[Route('/showrv', name: 'app_showrv')]
    public function showrv(RendezVousRepository $a,Request $req): Response 
    {
        $rv=$a->findAll();
        return $this->render('rendezvous/showrv.html.twig', [
            'showrv' => $rv,
        ]);
    }
        

    #[Route('/addformrv', name: 'app_addformrv')]
    public function addformrv(ManagerRegistry $m ,Request $req): Response
    {
        $em=$m->getManager();
        $user = $this->getUser();
        $authors=new RendezVous();
        
        $authors->setStatut('En attente');
        $patient = $em->getRepository(Patient::class)->findOneBy(['user' => $user]);
        $forms=$this->createForm(RendezvousType::class,$authors,[
            'is_update' => false,
            'patient' => $patient,
            
        ]) ; 
        $forms->handleRequest($req);
        
        if($forms->isSubmitted() && $forms->isValid() ){ 
        
        $em->persist($authors); 
        $em->flush();
        return $this->redirectToRoute('app_showrv');
        }
        
        return $this->render('rendezvous/addformrv.html.twig', [
            'formaddrv' => $forms,
        ]);
        
    }

    #[Route('/updatestatus/{id}/{status}', name: 'app_update_rdv_status')]
public function updateStatus($id, $status, ManagerRegistry $m, RendezVousRepository $repo): Response
{
    $em = $m->getManager();
    $rdv = $repo->find($id);

    if (!$rdv) {
        $this->addFlash('danger', 'Rendez-vous non trouvé.');
        return $this->redirectToRoute('app_showrv');
    }

    $validStatuses = ['En attente', 'Terminé', 'Annulé', 'Confirmé'];
    if (!in_array($status, $validStatuses)) {
        $this->addFlash('danger', 'Statut invalide.');
        return $this->redirectToRoute('app_showrv');
    }

    $rdv->setStatut($status);
    $em->persist($rdv);
    $em->flush();

    $this->addFlash('success', 'Statut mis à jour avec succès.');
    return $this->redirectToRoute('app_showrv');
}


    #[Route('/deleterv/{id}', name: 'app_deleterv')]
    public function deleteformdm($id,ManagerRegistry $m ,Request $req,RendezVousRepository $repo): Response
    {
        $em=$m->getManager();
        $cons= $repo->find($id);
            $em->remove(object: $cons);
            $em->flush();
            return $this->redirectToRoute('app_showrv');
    }
}
