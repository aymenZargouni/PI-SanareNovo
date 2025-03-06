<?php
namespace App\Controller;

use App\Entity\Medecin;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;

class MedecinController extends AbstractController
{
    private $doctrine;

    // Injection de ManagerRegistry via le constructeur
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/medecin/consultation/{id}', name: 'medecin_consultation')]
    public function consultation(int $id, Security $security): Response
    {
        // Récupérer le médecin connecté via ManagerRegistry
        $medecin = $this->doctrine
                        ->getRepository(Medecin::class)
                        ->find($id);

        // Vérifier si le médecin est connecté
        if (!$medecin || $medecin->getUser() !== $security->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Afficher la vue de la consultation vidéo
        return $this->render('consultation/teleconsultation.html.twig', [
            'medecin' => $medecin,
        ]);
    }
}
