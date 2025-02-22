<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\RendezVous;
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
        $authors=new RendezVous();
        
        $authors->setStatut('En attente');

        $forms=$this->createForm(RendezvousType::class,$authors,[
            'is_update' => false,
            
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

    #[Route('/updaterv/{id}', name: 'app_updaterv')]
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
