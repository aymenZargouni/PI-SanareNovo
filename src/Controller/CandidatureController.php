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
    
            // Préparer le contenu de l'email en fonction du statut
            $emailSubject = '';
            $emailContent = '';
    
            if ($statut === 'accepte') {
                $emailSubject = '🎉 Félicitations ! Votre candidature a été acceptée !';
                $emailContent = "
                    <h2 style='color: #2d89ef;'>Bonjour {$candidature->getPrenom()} {$candidature->getNom()},</h2>
                    <p>🎉 Félicitations ! Nous avons le plaisir de vous informer que votre candidature pour le poste <strong>{$candidature->getOffre()->getTitre()}</strong> a été acceptée.</p>
                    <p>Notre équipe vous contactera bientôt pour les prochaines étapes.</p>
                    <p>📞 Si vous avez des questions, n'hésitez pas à nous contacter.</p>
                    <br>
                    <p style='font-size: 14px; color: #777;'>Cordialement,</p>
                    <p style='font-size: 16px; color: #333; font-weight: bold;'>L'équipe RH</p>
                    <p style='font-size: 12px; color: #999;'>[SanarNovo] | [SanarNovo@esprit.tnt]</p>
                ";
            } elseif ($statut === 'rejete') {
                $emailSubject = '❌ Votre candidature a été refusée';
                $emailContent = "
                    <h2 style='color: #d9534f;'>Bonjour {$candidature->getPrenom()} {$candidature->getNom()},</h2>
                    <p>Nous vous remercions d'avoir postulé pour le poste <strong>{$candidature->getOffre()->getTitre()}</strong>.</p>
                    <p>Après examen de votre candidature, nous avons décidé de ne pas donner suite à votre demande. Toutefois, nous garderons votre profil dans notre base pour d’éventuelles opportunités futures.</p>
                    <p>Nous vous souhaitons bonne continuation dans votre recherche d'emploi.</p>
                    <br>
                    <p style='font-size: 14px; color: #777;'>Cordialement,</p>
                    <p style='font-size: 16px; color: #333; font-weight: bold;'>L'équipe RH</p>
                    <p style='font-size: 12px; color: #999;'>[SanarNovo] | [SanarNovo@esprit.tnt]</p>
                ";
            }
    
            // Envoi de l'e-mail uniquement si le statut est "accepté" ou "rejeté"
            if ($statut === 'accepte' || $statut === 'rejete') {
                $email = (new Email())
                    ->from('SanarNovo@esprit.tn')
                    ->to($candidature->getEmail())
                    ->subject($emailSubject)
                    ->html($emailContent);
    
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
