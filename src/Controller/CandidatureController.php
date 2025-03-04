<?php
namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Offre;
use App\Form\CandidatureType;
use App\Repository\CandidatureRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class CandidatureController extends AbstractController
{
    // Route pour postuler à une offre
    #[Route('/postuler/{id}', name: 'candidature_postuler')]
    public function postuler(Request $request, Offre $offre, ManagerRegistry $doctrine): Response
    {
        $candidature = new Candidature();
        $candidature->setOffre($offre);
        $candidature->setDateCandidature(new \DateTime());
    
        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer le fichier CV
            $cvFile = $form->get('cv')->getData();
    
            if ($cvFile) {
                // Sauvegarder le fichier dans le répertoire approprié
                $candidature->setCv($cvFile->getClientOriginalName());
                $cvFile->move(
                    $this->getParameter('cv_directory'), // Par exemple : 'public/uploads/cv'
                    $cvFile->getClientOriginalName()
                );
            }
    
            $entityManager = $doctrine->getManager();
            $entityManager->persist($candidature);
            $entityManager->flush();
    
            $this->addFlash('success', 'Votre candidature a été envoyée avec succès.');
            return $this->redirectToRoute('listOffreCondidat');
        }
    
        return $this->render('candidature/postuler.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
        ]);
    }
    
    // Liste des candidatures
    #[Route('/candidature_liste', name: 'candidature_liste')]
    public function liste(CandidatureRepository $candidatureRepository, Request $request, ManagerRegistry $doctrine): Response
    {
        $candidatures = $candidatureRepository->findAll();
        $formStatut = [];
    
        // Créez un formulaire pour chaque candidature
        foreach ($candidatures as $candidature) {
            $formStatut[$candidature->getId()] = $this->createFormBuilder()
                ->add('statut', ChoiceType::class, [
                    'choices' => [
                        'En attente' => 'en_attente',
                        'Accepté' => 'accepte',
                        'Rejeté' => 'rejete',
                    ],
                    'data' => $candidature->getStatut(), // Statut actuel de la candidature
                    'label' => 'Statut'
                ])
                ->add('save', SubmitType::class, ['label' => 'Changer le statut'])
                ->getForm();
            
            $formStatut[$candidature->getId()]->handleRequest($request);
    
            if ($formStatut[$candidature->getId()]->isSubmitted() && $formStatut[$candidature->getId()]->isValid()) {
                // Récupérer le nouveau statut et mettre à jour la candidature
                $nouveauStatut = $formStatut[$candidature->getId()]->get('statut')->getData();
                $candidature->setStatut($nouveauStatut);
                $entityManager = $doctrine->getManager();
                $entityManager->flush();
    
                $this->addFlash('success', 'Le statut de la candidature a été mis à jour.');
            }
        }
    
        return $this->render('candidature/liste.html.twig', [
            'candidatures' => $candidatures,
            'form_statut' => $formStatut,
        ]);
    }

    // Changer le statut d'une candidature
    #[Route('/admin/{id}/changer-statut/{statut}', name: 'candidature_changer_statut')]
    public function changerStatut(Candidature $candidature, string $statut, ManagerRegistry $doctrine, MailerInterface $mailer): Response
    {
        $statutsValides = ['en_attente', 'accepte', 'rejete'];
    
        if (in_array($statut, $statutsValides)) {
            $candidature->setStatut($statut);
    
            $entityManager = $doctrine->getManager();
            $entityManager->flush();
    
            $this->addFlash('success', 'Le statut de la candidature a été mis à jour.');
    
            // *Envoyer un e-mail si la candidature est acceptée*
            if ($statut === 'accepte') {
                $email = (new Email())
                    ->from('noreply@votre-site.com')
                    ->to($candidature->getEmail())
                    ->subject('Votre candidature a été acceptée !')
                    ->html("
                        <h2>Bonjour {$candidature->getPrenom()} {$candidature->getNom()},</h2>
                        <p>Félicitations ! Votre candidature a été acceptée.</p>
                        <p>Nous vous contacterons sous peu pour la suite.</p>
                        <p>Cordialement,</p>
                        <p>L'équipe RH</p>
                    ");
    
                $mailer->send($email);
            }
        } else {
            $this->addFlash('error', 'Statut invalide.');
        }
        return $this->redirectToRoute('candidature_liste');
    }
    // Supprimer une candidature
    #[Route("/candidature/delete/{id}", name:"candidature_delete")]
    public function delete(Candidature $candidature, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        // Supprimer le fichier CV si il existe
        if ($candidature->getCv()) {
            $cvPath = $this->getParameter('kernel.project_dir') . '/public/uploads/cv/' . $candidature->getCv();
            if (file_exists($cvPath)) {
                unlink($cvPath);  // Supprime le fichier du système
            }
        }

        // Supprimer l'entité
        $em->remove($candidature);
        $em->flush();

        // Message de confirmation
        $this->addFlash('success', 'Candidature supprimée avec succès.');

        // Redirection vers la liste des candidatures
        return $this->redirectToRoute('candidature_liste');
    }
}