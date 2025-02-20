<?php

namespace App\Controller;

use App\Entity\Claim;
use App\Form\ClaimFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ClaimController extends AbstractController
{
    // Route pour afficher toutes les réclamations
    #[Route('/claims', name: 'claim_list')]
    public function list(ManagerRegistry $managerRegistry): Response
    {
        $claimRepository = $managerRegistry->getRepository(Claim::class);
        $claims = $claimRepository->findAll(); // Récupérer toutes les réclamations

        return $this->render('claim/list.html.twig', [
            'claims' => $claims, // Passez la liste des réclamations au template
        ]);
    }

    // Route pour afficher les détails d'une réclamation spécifique
    #[Route('/claim/{id}', name: 'claim_details')]
    public function details(Claim $claim): Response
    {
        return $this->render('claim/details.html.twig', [
            'claim' => $claim,
        ]);
    }

    // Route pour modifier une réclamation
    #[Route('/claim/{id}/edit', name: 'claim_edit')]
    public function edit(int $id, ManagerRegistry $managerRegistry, Request $request): Response
    {
        // Récupérer la réclamation à modifier
        $claimRepository = $managerRegistry->getRepository(Claim::class);
        $claim = $claimRepository->find($id);

        if (!$claim) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }

        // Créer un formulaire à partir du ClaimFormType
        $form = $this->createForm(ClaimFormType::class, $claim);

        // Traiter la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour l'entité claim
            $manager = $managerRegistry->getManager();
            $manager->persist($claim);
            $manager->flush();

            // Rediriger vers la page de détails de la réclamation après modification
            return $this->redirectToRoute('claim_details', ['id' => $claim->getId()]);
        }

        // Rendre la vue avec le formulaire
        return $this->render('claim/edit.html.twig', [
            'form' => $form->createView(), // Passer le formulaire à la vue
            'claim' => $claim,
        ]);
    }
   
    #[Route('/claim/{id}/delete', name: 'claim_delete')]
public function delete(int $id, ManagerRegistry $managerRegistry): Response
{
    $claimRepository = $managerRegistry->getRepository(Claim::class);
    $claim = $claimRepository->find($id);

    if (!$claim) {
        throw $this->createNotFoundException('Réclamation non trouvée');
    }

    // Récupérer l'équipement associé à la réclamation
    $equipment = $claim->getEquipment();

    if ($equipment) {
        // Mettre à jour le statut de l'équipement (par exemple, "fonctionnel" après la suppression)
        $equipment->setStatus('reparé'); // Vous pouvez définir ici le statut approprié
        $manager = $managerRegistry->getManager();
        $manager->persist($equipment);
    }

    // Suppression de la réclamation
    $manager = $managerRegistry->getManager();
    $manager->remove($claim);
    $manager->flush();

    // Rediriger après la suppression
    $this->addFlash('success', 'Réclamation supprimée avec succès');
    return $this->redirectToRoute('claim_list'); // Rediriger vers la liste des réclamations
}

}
