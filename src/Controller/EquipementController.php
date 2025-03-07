<?php

namespace App\Controller;

use App\Entity\Claim;
use App\Entity\Equipment;
use App\Entity\Historique;
use App\Entity\Technicien;
use App\Entity\User;
use App\Form\ClaimFormType;
use App\Form\EquipmentEditType;
use App\Form\EquipmentType;
use App\Repository\EquipmentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\SecurityBundle\Security;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;
class EquipementController extends AbstractController
{
    #[Route('/equipment/new', name: 'app_equipment_new')] // Route pour Créer un nouveau équipement
    public function new(Request $request, ManagerRegistry $m): Response
    {
        $em = $m->getManager(); // Obtient l'EntityManager pour gérer les entités via Doctrine( ORM )
        $equipment = new Equipment();
        $form = $this->createForm(EquipmentType::class, $equipment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($equipment);
            $em->flush(); 
            $this->addFlash('success', 'equipment added successfully!'); 
            return $this->redirectToRoute('showequipment'); // Redirection après ajout
        }

        return $this->render('equipement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/showequipment', name: 'showequipment')] // Route pour afficher tous les équipement
    public function showEquipments(EquipmentRepository $equipmentRepository): Response
    {
        // Récupérer tous les équipements
        $equipments = $equipmentRepository->findAll();

        // Passer les équipements à la vue
        return $this->render('equipement/showequipments.html.twig', [
            'tabequipments' => $equipments, // Passer les équipements à la vue
        ]);
    }


    #[Route('/updateformequipment/{id}', name: 'app_updateformequipment')] // Route pour mettre à jour un équipement
    public function updateFormEquipment($id, EquipmentRepository $equipmentRepository, ManagerRegistry $managerRegistry, Request $request): Response
    {
        $em = $managerRegistry->getManager();
        $equipment = $equipmentRepository->find($id); // Trouver l'équipement par ID

        if (!$equipment) {
            throw $this->createNotFoundException('Aucun équipement trouvé pour l\'ID ' . $id); // Gérer erreur 404
        }

        $form = $this->createForm(EquipmentEditType::class, $equipment); // Créer le formulaire de mise à jour
        $form->handleRequest($request); // Gérer la requête

        if ($form->isSubmitted() && $form->isValid()) { // Vérifier si le formulaire a été soumis et est valide
            $em->persist($equipment); // Persister l'équipement mis à jour
            $em->flush(); // Sauvegarder dans la base de données

            $this->addFlash('success', 'Équipement mis à jour avec succès !'); // Message flash de succès
            return $this->redirectToRoute('showequipment'); // Rediriger vers la liste des équipements
        }

        return $this->render('equipement/edit.html.twig', [
            'form' => $form->createView(), // Passer le formulaire à la vue
        ]);
    }
    #[Route('/deleteequipment/{id}', name: 'app_deleteequipment')] // Route pour supprimer un équipement
    public function deleteEquipment($id, EquipmentRepository $equipmentRepository, ManagerRegistry $managerRegistry): Response
    {
        $em = $managerRegistry->getManager();
        $equipment = $equipmentRepository->find($id); // Trouver l'équipement par ID

        if (!$equipment) {
            throw $this->createNotFoundException('Aucun équipement trouvé pour l\'ID ' . $id); // Gérer erreur 404
        }

        $em->remove($equipment); // Supprimer l'équipement
        $em->flush(); // Enregistrer les modifications dans la base de données

        return $this->redirectToRoute('showequipment'); // Redirection vers la liste des équipements
    }
    
    #[Route('/{id}/claim', name: 'equipment_claim_submit')]
    public function claim(Equipment $equipment, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $claim = new Claim();
        $claim->setEquipment($equipment); // Associe l'équipement à la réclamation
    
        $form = $this->createForm(ClaimFormType::class, $claim);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le technicien depuis le formulaire
            $technicien = $claim->getTechnicien();
    
            if ($technicien) {
                $claim->setTechnicien($technicien); // Associe le technicien à la réclamation
            }
    
            // Mettre à jour le statut de l'équipement en panne
            $equipment->setStatus('panne');
            
            // Enregistrer la réclamation
            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($claim);
            $entityManager->flush();
    
            // Envoi du SMS au technicien
            $this->sendSmsToTechnician($technicien);

            // Afficher un message de succès
            $this->addFlash('success', 'La réclamation a bien été enregistrée et un SMS a été envoyé au technicien.');
    
            return $this->redirectToRoute('showequipment'); // Redirige vers la page des équipements
        }
    
        return $this->render('equipement/claim.html.twig', [
            'equipment' => $equipment,
            'form' => $form->createView(),
        ]);
    }

    // Fonction pour envoyer un SMS au technicien
private function sendSmsToTechnician($technicien): void
{
    $sid = $_ENV['TWILIO_SID'];
    $token = $_ENV['TWILIO_AUTH_TOKEN'];
    $twilioNumber = $_ENV['TWILIO_PHONE_NUMBER'];

    $client = new Client($sid, $token);

    // Le numéro du technicien est supposé être dans l'objet $technicien
    $technicienPhone = $technicien->getPhoneNumber(); 
    if ($technicienPhone) {
        // Récupérer la première réclamation associée au technicien
        $claim = $technicien->getClaims()->first();

        if ($claim && $claim->getEquipment()) {
            // Récupérer l'équipement à partir de la réclamation
            $equipement = $claim->getEquipment();
            
            $client->messages->create(
                $technicienPhone, // Numéro du technicien
                [
                    'from' => $twilioNumber,
                    'body' => 'Une réclamation a été enregistrée pour l\'équipement "' . $equipement->getName() . '" et vous avez été affecté à cette tâche.',
                ]
            );
        }
    }
}

//partie Technician 
#[Route('/technician/equipments', name: 'technician_equipments')]
public function technicianEquipments(Security $security, ManagerRegistry $managerRegistry): Response
{
    $user = $security->getUser();
    
    // Vérifier si l'utilisateur est un technicien
    if ($user instanceof User) {
        $technicianRepository = $managerRegistry->getRepository(Technicien::class);
        $technician = $technicianRepository->findOneBy(['user' => $user]);
        
        if ($technician) {
            // Si l'utilisateur est un technicien, récupérer les réclamations associées à ce technicien
            $claimRepository = $managerRegistry->getRepository(Claim::class);
            
            // Récupérer les réclamations de ce technicien
            $claims = $claimRepository->findBy(['technicien' => $technician]);
            
            $equipments = [];
            foreach ($claims as $claim) {
                // Pour chaque réclamation, on vérifie l'équipement associé
                $equipment = $claim->getEquipment();
                if ($equipment && in_array($equipment->getStatus(), ['panne', 'maintenance'])) {
                    $equipments[] = $equipment;
                }
            }
            
            return $this->render('equipement/equipmentsTech.html.twig', [
                'equipments' => $equipments,
            ]);
        }
    }
    
    // Si l'utilisateur n'est pas un technicien ou n'a pas de réclamations, rediriger ou afficher un message d'erreur
    return $this->redirectToRoute('showequipment');
}



    #[Route('/technician/equipment/{id}', name: 'technician_equipment_details')]  // Route pour afficher tout les détails des equipement 
    public function technicianEquipmentDetails(Equipment $equipment): Response
    {
        $claim = $equipment->getClaims(); // Accès à la réclamation associée à l'équipement
        return $this->render('equipement/detailsequipementTech.html.twig', [
            'equipment' => $equipment,
            'claim' => $claim, // Passe la réclamation à la vue
        ]);
    }


    #[Route('/technician/equipment/{id}/update', name: 'technician_update_equipment')] // Route pour mettre à jour le statut, rapport et date de réparation
    public function updateEquipment(Request $request, Equipment $equipment, ManagerRegistry $managerRegistry): Response
    {
        $entityManager = $managerRegistry->getManager();
    
        // Récupérer les données du formulaire
        $status = $request->request->get('status');
        $dateReparation = $request->request->get('dateReparation');
        $rapport = $request->request->get('rapport');
    
        // Mise à jour du statut de l'équipement
        if ($status) {
            $equipment->setStatus($status);
        }
    
        // Créer un nouvel historique si nécessaire
        if ($dateReparation || $rapport) {
            $historique = new Historique();
            $historique->setEquipment($equipment);
    
            if ($dateReparation) {
                $historique->setDateReparation(new \DateTime($dateReparation));
            }
    
            if ($rapport) {
                $historique->setRapportDetaille($rapport);
            }
    
            $entityManager->persist($historique);
        }
    
    
    
        $entityManager->persist($equipment);
        $entityManager->flush();
    
        $this->addFlash('success', 'L\'équipement a été mis à jour avec succès.');
    
        return $this->redirectToRoute('technician_equipments');
    }
    


    #[Route('/coordinator_dashboard', name: 'coordinator_dashboard')] // Route pour Récupère le nombre d’équipements en fonction de leur statut

    public function dashboard(EquipmentRepository $equipmentRepository): Response
    {
        // Récupérer le nombre d'équipements par statut
        $totalGoodCondition = $equipmentRepository->countByStatus('reparé');
        $totalInMaintenance = $equipmentRepository->countByStatus('maintenance');
        $totalOutOfService = $equipmentRepository->countByStatus('panne');
        
        $totalEquipments = $totalGoodCondition + $totalInMaintenance + $totalOutOfService;

        return $this->render('equipement/dashboard.html.twig', [
            'totalEquipments' => $totalEquipments,
            'totalGoodCondition' => $totalGoodCondition,
            'totalInMaintenance' => $totalInMaintenance,
            'totalOutOfService' => $totalOutOfService,
        ]);
    }


        #[Route('/tech_dashboard', name: 'tech_dashboard')] // Route pour Récupère le nombre d’équipements en fonction de leur statut

        public function tech_dashboard(EquipmentRepository $equipmentRepository): Response
        {
            // Récupérer le nombre d'équipements par statut
            $totalInMaintenance = $equipmentRepository->countByStatus('maintenance');
            $totalOutOfService = $equipmentRepository->countByStatus('panne');
            
            $totalEquipments =  $totalInMaintenance + $totalOutOfService;
    
            return $this->render('equipement/dahbordtech.html.twig', [
                'totalEquipments' => $totalEquipments,
                'totalInMaintenance' => $totalInMaintenance,
                'totalOutOfService' => $totalOutOfService,
            ]);


            
        }
     
        //calendar 
        #[Route('/technician/calendar', name: 'technician_calendar')]
        public function technicianCalendar(ManagerRegistry $managerRegistry, Security $security): Response
        {
            // Récupérer l'utilisateur connecté
            $user = $security->getUser();
            
            // Vérifier que l'utilisateur est bien connecté
            if (!$user) {
                throw $this->createAccessDeniedException('Accès non autorisé');
            }
        
            // Récupérer le technicien associé à cet utilisateur
            $technicianRepository = $managerRegistry->getRepository(Technicien::class);
            $technician = $technicianRepository->findOneBy(['user' => $user]);
        
            // Vérifier si le technicien existe
            if (!$technician) {
                throw $this->createNotFoundException('Technicien non trouvé');
            }
        
            // Récupérer les réclamations associées au technicien
            $claimRepository = $managerRegistry->getRepository(Claim::class);
            $claims = $claimRepository->findBy(['technicien' => $technician]);
        
            // Préparer les événements pour FullCalendar
            $events = [];
            foreach ($claims as $claim) {
                $events[] = [
                    'title' => 'Réclamation: ' . $claim->getEquipment()->getName(),
                    'start' => $claim->getCreatedAt()->format('Y-m-d H:i:s'),  // Date au format requis par FullCalendar
                    'url' => $this->generateUrl('technician_equipment_details', ['id' => $claim->getEquipment()->getId()]),
                ];
            }
        
            // Passer les événements au template
            return $this->render('equipement/calendar.html.twig', [
                'events' => json_encode($events),
            ]);
        }
        
        #[Route('/coordinator/download-report/{id}', name: 'download_report')]
public function downloadReport(Historique $historique): Response
{
    // Configuration de DomPDF
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $dompdf = new Dompdf($options);

    // Récupération des informations de l'historique
    $html = $this->renderView('rapport/rapport.html.twig', [
        'historique' => $historique,
    ]);

    // Charger le HTML dans DomPDF
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Réponse pour télécharger le fichier PDF
    return new Response(
        $dompdf->output(),
        200,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => ResponseHeaderBag::DISPOSITION_ATTACHMENT . '; filename="rapport_'.$historique->getId().'.pdf"',
        ]
    );
}


    }