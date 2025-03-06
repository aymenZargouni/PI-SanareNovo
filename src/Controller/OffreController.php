<?php
namespace App\Controller;

use App\Entity\Offre;
use App\Form\OffreType;
use App\Repository\OffreRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class OffreController extends AbstractController
{
    #[Route('/listOffre', name: 'listOffre')]
    public function listOffre(OffreRepository $offreRepository): Response
    {
        $offres = $offreRepository->findAll();

        return $this->render('offre/index.html.twig', [
            'offres' => $offres,
        ]);
    }

    #[Route('/listOffreCondidat', name: 'listOffreCondidat')]
public function listOffreCondidat(OffreRepository $offreRepository): Response
{
    $offres = $offreRepository->findAll();

    return $this->render('offre/offreCondidat.html.twig', [
        'offres' => $offres,
    ]);
}
    #[Route('/offre_new', name: 'offre_new')]
    public function offre_new(Request $request, ManagerRegistry $doctrine): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Utilisation du ManagerRegistry directement dans la fonction
            $entityManager = $doctrine->getManager();
            $entityManager->persist($offre);
            $entityManager->flush();

            $this->addFlash('success', 'Offre ajoutée avec succès !');
            return $this->redirectToRoute('listOffre');
        }

        return $this->render('offre/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'offre_edit')]
        public function edit(Request $request, Offre $offre, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Utilisation du ManagerRegistry directement dans la fonction
            $doctrine->getManager()->flush();

            $this->addFlash('success', 'Offre modifiée avec succès !');
            return $this->redirectToRoute('listOffre');
        }

        return $this->render('offre/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'offre_delete')]
        public function delete(Offre $offre, ManagerRegistry $doctrine): Response
    {
        // Utilisation du ManagerRegistry directement dans la fonction
        $entityManager = $doctrine->getManager();
        $entityManager->remove($offre);
        $entityManager->flush();

        $this->addFlash('danger', 'Offre supprimée avec succès !');
        return $this->redirectToRoute('listOffre');
    }
    #[Route('/offres/supprimer-expirees', name: 'offres_supprimer_expirees', methods: ['POST'])]
    public function supprimerOffresExpirees(ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $now = new \DateTime(); // Date actuelle
    
        $offresExpirees = $entityManager->getRepository(Offre::class)->findExpiredOffers($now);
    
        if (count($offresExpirees) > 0) {
            foreach ($offresExpirees as $offre) {
                $entityManager->remove($offre);
            }
            $entityManager->flush();
    
            return new JsonResponse(['success' => true, 'message' => count($offresExpirees) . ' offres expirées supprimées.']);
        }
    
        return new JsonResponse(['success' => false, 'message' => 'Aucune offre expirée trouvée.']);
    }
    
}
