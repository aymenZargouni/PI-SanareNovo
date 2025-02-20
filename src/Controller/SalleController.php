<?php

namespace App\Controller;

use App\Entity\Salle;
use App\Entity\Service;
use App\Form\SalleType;
use App\Repository\SalleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SalleController extends AbstractController{
    #[Route('/salle', name: 'app_salle')]
    public function index(): Response
    {
        return $this->render('salle/index.html.twig', [
            'controller_name' => 'SalleController',
        ]);
    }

    #[Route('showsalle', name: 'showsalle')]
    public function showservice (SalleRepository $serRep): Response
    {
        $Blog = $serRep->findAll();
        return $this->render('salle/showsalle.html.twig', [
            'tabservice' => $Blog,
        ]);
    }  

    #[Route('/addFormsalle', name: 'addFormsalle')]
    public function addFromservice(ManagerRegistry $m, Request $req): Response
    {
        $em = $m->getManager();
        $salle = new Salle();
    
        // Création du formulaire
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le service associé à la salle
            $service = $salle->getService();
    
            if (!$service) {
                throw $this->createNotFoundException('Service non trouvé.');
            }
    
            // Incrémenter le nombre de salles pour ce service
            $service->setNbrSalle($service->getNbrSalle() + 1);
    
            // Enregistrer la salle et mettre à jour le service
            $em->persist($salle);
            $em->persist($service);
            $em->flush();
    
            return $this->redirectToRoute('showsalle');
        }
    
        return $this->render('salle/addFormsalle.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/updateFormsalle/{id}', name: 'updateFormsalle')]
    public function updateFormBlog(ManagerRegistry $m, Request $req, $id, SalleRepository $BlogRep): Response
    {

        $em = $m->getManager(); 
        $Blog = $BlogRep->find($id);
        $form=$this->createForm(SalleType::class, $Blog);
        $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()){
            $em->persist($Blog);
            $em->flush();
            return $this->redirectToRoute('showsalle'); 

        }
        return $this->render('salle/modFormsalle.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/deletesalle/{id}', name: 'deletesalle')]
public function deleteFormBlog($id, ManagerRegistry $m, SalleRepository $BlogRep): Response
{
    $em = $m->getManager();

    // Récupérer l'entité Salle avec l'ID donné
    $Blog = $BlogRep->find($id);

    // Vérifier si l'entité a été trouvée
    if (!$Blog) {
        // Si l'entité n'est pas trouvée, rediriger avec un message d'erreur ou afficher un message
        $this->addFlash('error', 'Salle not found');
        return $this->redirectToRoute('showsalle'); // Redirection vers la liste des salles
    }

    // Si l'entité est trouvée, la supprimer
    $em->remove($Blog);
    $em->flush();

    // Rediriger après la suppression
    return $this->redirectToRoute('showsalle'); // Rediriger vers la liste des salles
}
#[Route('/addFormsalle2/{id}', name: 'addFormsalle2')]
public function addFromservic (int $id, ManagerRegistry $m, Request $req): Response
{
    $em = $m->getManager(); 

    // Récupérer le service correspondant
    $service = $em->getRepository(Service::class)->find($id);

    if (!$service) {
        throw $this->createNotFoundException('Service non trouvé.');
    }

    // Créer une nouvelle salle et l'associer au service
    $salle = new Salle();
    $salle->setService($service);

    $form = $this->createForm(SalleType::class, $salle);
    $form->handleRequest($req);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($salle);

        // Incrémenter le nombre de salles du service
        $service->setNbrSalle($service->getNbrSalle() + 1);
        $em->persist($service);

        $em->flush();
        return $this->redirectToRoute('showservice'); 
    }

    return $this->render('salle/addFormsalle.html.twig', [
        'form' => $form->createView(),
        'service' => $service
    ]);
}
#[Route('/service/{id}/salles', name: 'showSalleByService')]
public function showSalleByService(SalleRepository $salleRep, Service $service): Response
{
    // Récupérer les salles du service donné
    $salles = $salleRep->findBy(['service' => $service]);

    return $this->render('salle/showsalle2.html.twig', [
        'service' => $service,
        'salles' => $salles,
    ]);
}



}
