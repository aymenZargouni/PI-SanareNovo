<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Consultation;
use App\Entity\Service;
use App\Repository\ServiceRepository;
use App\Form\ConsultationType;
use App\Form\DossiermedicaleType;
use App\Repository\ConsultationRepository;
use App\Repository\DossiermedicaleRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ConsultationController extends AbstractController{
    #[Route('/consultation', name: 'app_consultation')]
    public function index(): Response
    {
        return $this->render('consultation/index.html.twig', [
            'controller_name' => 'ConsultationController',
        ]);
    }

    #[Route('/showcons', name: 'app_showcons')]
    public function showcons(ConsultationRepository $a,Request $req): Response //AuthorRepository  est dans $A a partir use App\Repository\AuthorRepository; on peut acceder apartir IDD
    {
        $cons=$a->findAll();
        return $this->render('consultation/showcons.html.twig', [
            'showcons' => $cons,
        ]);
    }
        

    #[Route('/addformcons', name: 'app_addformcons')]
    public function addformcons(ManagerRegistry $m, Request $req, ServiceRepository $serviceRepo, ConsultationRepository $consultationRepo): Response
    {
        $em = $m->getManager();
        $consultation = new Consultation();
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le service lié à la consultation
            $service = $consultation->getNomService();
    
            // Vérifier si le service existe
            if (!$service) {
                $this->addFlash('error', 'Le service associé à la consultation est introuvable.');
                return $this->redirectToRoute('app_addformcons');
            }
    
            // Compter le nombre de consultations pour ce service
            $totalConsultations = $consultationRepo->count(['nom_service' => $service]);
    
            // Vérifier si la capacité du service est atteinte
            if ($totalConsultations >= $service->getCapacite()) {
                // Mettre à jour l'état du service à 'false' (service complet)
                $service->setEtat(1);
                $em->persist($service); // Persister le service mis à jour
                $em->flush(); // IMPORTANT : appliquer le changement dans la DB
    
                // Afficher un message d'erreur et rediriger
                $this->addFlash('error', 'La capacité du service est atteinte, vous ne pouvez plus ajouter de consultations.');
                return $this->redirectToRoute('app_addformcons'); // Rester sur la même page en cas d'erreur
            }
    
            // Enregistrer la consultation
            $em->persist($consultation);
    
            // Vérifier si la capacité du service est maintenant atteinte (si besoin)
            if ($consultationRepo->count(['nom_service' => $service]) >= $service->getCapacite()) {
                $service->setEtat(1);
                $em->persist($service);
            }
    
            $em->flush(); // Effectuer un seul flush pour toutes les modifications
    
            // Redirection après l'ajout
            $this->addFlash('success', 'Consultation ajoutée avec succès.');
            return $this->redirectToRoute('app_showcons'); // Rediriger vers la page des consultations
        }
    
        return $this->render('consultation/addformcons.html.twig', [
            'formadd' => $form->createView(),
        ]);
    }
    
    


    #[Route('/updaterepas/{id}', name: 'app_updatecons')]
    public function updateformcons($id,ManagerRegistry $m ,Request $req,ConsultationRepository $repo): Response
    {
        $em=$m->getManager();
        $authors= $repo->find($id);
        $form=$this->createForm(ConsultationType::class,$authors) ; // getmanager recuperer les donner et les donner existe dans authors
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid() ){ // si il a cliquer sur button et (envoyer) et form et ramplit
        $em->persist($authors); 
        $em->flush();//execute
        return $this->redirectToRoute('app_showcons');
        }

        return $this->render('consultation/addformcons.html.twig', [
            'formadd' => $form,
        ]);
    }

    #[Route('/deletecons/{id}', name: 'app_deletecons')]
    public function deleteformauthors($id,ManagerRegistry $m ,Request $req,ConsultationRepository $repo): Response
    {
        $em=$m->getManager();
        $cons= $repo->find($id);
            $em->remove($cons);
            $em->flush();
            return $this->redirectToRoute('app_showcons');
    }



}