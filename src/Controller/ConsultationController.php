<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Consultation;
use App\Entity\Service;
use Symfony\Component\HttpFoundation\JsonResponse;
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
public function showcons(ConsultationRepository $a, Request $req): Response 
{
    $searchTerm = $req->query->get('search', '');

    if ($searchTerm) {
        $cons = $a->createQueryBuilder('c')
            ->where('c.motif LIKE :search OR c.typeconsultation LIKE :search OR c.status LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
    } else {
        $cons = $a->findAll();
    }

    return $this->render('consultation/showcons.html.twig', [
        'showcons' => $cons,
        'search' => $searchTerm,
    ]);
}
        

    #[Route('/addformcons', name: 'app_addformcons')]
    public function addformcons(ManagerRegistry $m, Request $req, ServiceRepository $serviceRepo, ConsultationRepository $consultationRepo): Response
    {
        $em = $m->getManager();
        $consultation = new Consultation();
        $form = $this->createForm(ConsultationType::class, $consultation,[
            'is_update' => false,
            
        ]);
        
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            
            $service = $consultation->getNomService();
    
            
            if (!$service) {
                $this->addFlash('error', 'Le service associé à la consultation est introuvable.');
                return $this->redirectToRoute('app_addformcons');
            }
    
            
            $totalConsultations = $consultationRepo->count(['nom_service' => $service]);
    
           
            if ($totalConsultations >= $service->getCapacite()) {
                
                $service->setEtat(1);
                $em->persist($service); 
                $em->flush(); 
                $this->addFlash('error', 'La capacité du service est atteinte, vous ne pouvez plus ajouter de consultations.');
                return $this->redirectToRoute('app_addformcons'); 
            }
            $consultation->setStatus('En attente');

            
            $em->persist($consultation);
    
            
            if ($consultationRepo->count(['nom_service' => $service]) >= $service->getCapacite()) {
                $service->setEtat(1);
                $em->persist($service);
            }
    
            $em->flush(); 
    
            
            $this->addFlash('success', 'Consultation ajoutée avec succès.');
            return $this->redirectToRoute('app_showcons'); 
        }
    
        return $this->render('consultation/addformcons.html.twig', [
            'formadd' => $form->createView(),
            'isUpdate' => false,
        ]);
    }
    
    


    #[Route('/updatecons/{id}', name: 'app_updatecons')]
    public function updateformcons($id,ManagerRegistry $m ,Request $req,ConsultationRepository $repo): Response
    {
        $em=$m->getManager();
        $authors= $repo->find($id);
        $form=$this->createForm(ConsultationType::class,$authors,[
            'is_update' => true,
         
        ]) ; 
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid() ){ 
        $em->persist($authors); 
        $em->flush();
        return $this->redirectToRoute('app_showcons');
        }

        return $this->render('consultation/addformcons.html.twig', [
            'formadd' => $form,
            'isUpdate' => true,
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
    
    #[Route('/search-consultations', name: 'app_search_consultations', methods: ['GET'])]
    public function searchConsultations(Request $request, ConsultationRepository $consultationRepo): JsonResponse
    {
        $date = $request->query->get('date');
        $motif = $request->query->get('motif');
        $fullname = $request->query->get('fullname');
        $type = $request->query->get('typeconsultation');
        $status = $request->query->get('status');
    
        $queryBuilder = $consultationRepo->createQueryBuilder('c')
            ->leftJoin('c.patient', 'p'); 
    
        if (!empty($date)) {
            $queryBuilder->andWhere('c.date = :date')
                         ->setParameter('date', new \DateTime($date));
        }
    
        if (!empty($motif)) {
            $queryBuilder->andWhere('c.motif LIKE :motif')
                         ->setParameter('motif', '%' . $motif . '%');
        }
    
        if (!empty($fullname)) {
            $queryBuilder->andWhere('p.fullname LIKE :fullname')
                         ->setParameter('fullname', '%' . $fullname . '%');
        }
    
        if (!empty($status)) {
            $queryBuilder->andWhere('c.status = :status')
                         ->setParameter('status', $status);
        }
        
        if (!empty($type)) {
            $queryBuilder->andWhere('c.typeconsultation = :type')
                         ->setParameter('type', $type);
        }
    
        $consultations = $queryBuilder->getQuery()->getResult();
        
        return $this->json([
            'consultations' => array_map(fn($c) => [
                'id' => $c->getId(),
                'date' => $c->getDate()->format('Y-m-d'),
                'motif' => $c->getMotif(),
                'type' => $c->getTypeconsultation(),
                'status' => $c->getStatus(),
                'service' => $c->getNomService()->getNom(),
                'patient' => $c->getPatient() ? $c->getPatient()->getFullname() : 'Aucun',
            ], $consultations)
        ]);
    }




}
