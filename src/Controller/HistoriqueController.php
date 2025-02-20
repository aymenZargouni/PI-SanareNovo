<?php

namespace App\Controller;

use App\Entity\Equipment;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HistoriqueController extends AbstractController
{
   
    #[Route('/equipment/history', name: 'equipment_history')] // Route pour afficher tous équipements 
    public function history(ManagerRegistry $managerRegistry): Response
    {
        $equipmentRepository = $managerRegistry->getRepository(Equipment::class);
        $equipments = $equipmentRepository->findAll();

        return $this->render('historique/history.html.twig', [
            'equipments' => $equipments,
        ]);
    }
    #[Route('/equipment/{id}/details', name: 'equipment_details')] // Route pour afficher les equipements en détails avec rapport 
    public function details(Equipment $equipment): Response
    {
        return $this->render('historique/details.html.twig', [
            'equipment' => $equipment,
        ]);
    }




}
