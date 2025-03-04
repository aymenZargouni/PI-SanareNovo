<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Dossiermedicale;
use App\Form\DossiermedicaleType;
use App\Repository\DossiermedicaleRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DossiermedicaleController extends AbstractController
{
    #[Route('/dossiermedicale', name: 'app_dossiermedicale')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'DossiermedicaleController',
        ]);
    }

    #[Route('/showdm', name: 'app_showdm')]
    public function showdm(DossiermedicaleRepository $a,Request $req): Response 
    {
        $cons=$a->findAll();
        return $this->render('dossiermedicale/showdm.html.twig', [
            'showdm' => $cons,
        ]);
    }
        

    #[Route('/addformdm', name: 'app_addformdm')]
    public function addformdm(ManagerRegistry $m ,Request $req): Response
    {
        $em=$m->getManager();
        $authors=new Dossiermedicale();
        $forms=$this->createForm(DossiermedicaleType::class,$authors) ; 
        $forms->handleRequest($req);
        if($forms->isSubmitted() && $forms->isValid() ){ 
        $em->persist($authors); 
        $em->flush();
        return $this->redirectToRoute('app_showdm');
        }
        
        return $this->render('dossiermedicale/addformdm.html.twig', [
            'formadds' => $forms,
            'isUpdate' => false,
        ]);
        
    }

    #[Route('/updatedm/{id}', name: 'app_updatedm')]
    public function updateformdm($id,ManagerRegistry $m ,Request $req,DossiermedicaleRepository $repo): Response
    {
        $em=$m->getManager();
        $authors= $repo->find($id);
        $forms=$this->createForm(DossiermedicaleType::class,$authors) ; 
        $forms->handleRequest($req);
        if($forms->isSubmitted() && $forms->isValid() ){ 
        $em->persist(object: $authors); 
        $em->flush();//execute
        return $this->redirectToRoute('app_showdm');
        }

        return $this->render('dossiermedicale/addformdm.html.twig', [
            'formadds' => $forms,
            'isUpdate' => true,
        ]);
    }

    #[Route('/deletedm/{id}', name: 'app_deletedm')]
    public function deleteformdm($id,ManagerRegistry $m ,Request $req,DossiermedicaleRepository $repo): Response
    {
        $em=$m->getManager();
        $cons= $repo->find($id);
            $em->remove(object: $cons);
            $em->flush();
            return $this->redirectToRoute('app_showdm');
    }

}
