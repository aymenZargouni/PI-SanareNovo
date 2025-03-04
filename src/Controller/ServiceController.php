<?php

namespace App\Controller;
use App\Entity\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Doctrine\Persistence\ManagerRegistry;



final class ServiceController extends AbstractController{
    #[Route('/service', name: 'app_service')]
    public function index(): Response
    {
        return $this->render('service/index.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }

    #[Route('/showservice', name: 'showservice')]
    public function showservice (ServiceRepository $serRep): Response
    {
        $Blog = $serRep->findAll();
        return $this->render('service/showservice.html.twig', [
            'tabservice' => $Blog,
        ]);
    }  

    #[Route('/addFormservice', name: 'addFormservice')]
    public function addFromservice( ManagerRegistry $m, Request $req): Response
    {
        $em = $m->getManager(); 
        $serv = new Service();
        $form = $this->createForm(ServiceType::class, $serv);
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($serv);
            $em->flush();
            return $this->redirectToRoute('showservice'); 
        }
    
        return $this->render('service/addFormservice.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/updateFormservice/{id}', name: 'updateFormservice')]
    public function updateFormBlog(ManagerRegistry $m, Request $req, $id, ServiceRepository $BlogRep): Response
    {

        $em = $m->getManager(); 
        $Blog = $BlogRep->find($id);
        $form=$this->createForm(ServiceType::class, $Blog);
        $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()){
            $em->persist($Blog);
            $em->flush();
            return $this->redirectToRoute('showservice'); 

        }
        return $this->render('service/modFormservice.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/deleteservice/{id}', name: 'deleteservice')]
    public function deleteFormBlog( $id,ManagerRegistry $m, ServiceRepository $BlogRep): Response
    {
        $em = $m->getManager();
    
        $Blog = $BlogRep->find($id);
      
        $em->remove($Blog);
        $em->flush();
        return $this->redirectToRoute('showservice'); // redige vers la liste des auteurs aprés l'ajout  

    }
}
