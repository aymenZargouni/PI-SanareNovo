<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twilio\Rest\Client;
final class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    /*#[Route('/patient/add_comment/{id}', name: 'add_comment')]
    public function addComment(Blog $blog, Request $request, EntityManagerInterface $entityManager, \Symfony\Component\Mailer\MailerInterface $mailer, SessionInterface $session): Response
    {
        $user = $this->getUser();
            if (!$user instanceof User) {
                throw new \LogicException('L\'utilisateur doit être une instance de User.');
            }
                if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour commenter.');
            return $this->redirectToRoute('app_login');
        }

        if ($user->isBlocked()) {
            $this->addFlash('danger', 'Votre compte a été bloqué en raison de commentaires inappropriés.');
            return $this->redirectToRoute('home'); 
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bannedWords = ['قتل', 'اغتصاب', 'اقتلك', 'اغتصبها', '9atel', 'no9tlek', '9talha', 'no9tel', '9atlo', 'yo9tel', '3onef', '3ounef', '3anefha', '3anfo', '3anafha', '3anef', '3anif', 'mokhtal', 'mo5tal', 'yo9telha', 'yet8tasebha', 'yeghtasebha', 'ghtasebha', 'ye8tasebha', '8tasabha',]; 

            $commentContent = strtolower($comment->getContent());
            foreach ($bannedWords as $word) {
                if (str_contains($commentContent, $word)) {
                    $this->addFlash('danger', 'Votre commentaire contient des mots inappropriés. Veuillez le modifier.');

                    $attempts = $session->get('comment_attempts_' . $user->getId(), 0);
                    $attempts++;
                    $session->set('comment_attempts_' . $user->getId(), $attempts);

                    if ($attempts >= 4) {
                        $user->setIsBlocked(true);
                        $entityManager->persist($user);
                        $entityManager->flush();
                        $this->addFlash('danger', 'Votre compte a été bloqué après 3 tentatives de commentaires interdits.');
                        return $this->redirectToRoute('home'); 
                    }

                    try {
                        $email = (new \Symfony\Component\Mime\Email())
                            ->from('mhadhhich@gmail.com')
                            ->to('hiichem.mhadhbi@gmail.com')
                            ->subject('Alerte : Commentaire inapproprié')
                            ->text("L'utilisateur " . $user->getEmail() . " a essayé de poster un commentaire contenant des mots interdits :\n\n{$comment->getContent()}");
                    
                        $mailer->send($email);
                        $this->addFlash('success', 'Email envoyé avec succès.');
                    
                    } catch (\Exception $e) {
                        $this->addFlash('danger', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
                    }
                    return $this->render('comment/addComment.html.twig', [
                        'blog' => $blog,
                        'form' => $form->createView()
                    ]);
                }
            }

            $comment->setBlog($blog);
            $comment->setUser($user);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($comment);
            $entityManager->flush();

            $session->set('comment_attempts_' . $user->getId(), 0);

            $this->addFlash('success', 'Votre commentaire a été ajouté avec succès.');
            return $this->redirectToRoute('showBlogPatient', ['id' => $blog->getId()]);
        }

        return $this->render('comment/addComment.html.twig', [
            'blog' => $blog,
            'form' => $form->createView()
        ]);
    }*/


    #[Route('/patient/add_comment/{id}', name: 'add_comment')]
    public function addComment(Blog $blog, Request $request, EntityManagerInterface $entityManager, \Symfony\Component\Mailer\MailerInterface $mailer, SessionInterface $session): Response
    {
        #$this->denyAccessUnlessGranted('ROLE_USER');
        
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new \LogicException('L\'utilisateur doit être une instance de User.');
        }

        #if (!$user) {
        #    $this->addFlash('danger', 'Vous devez être connecté pour commenter.');
        #    return $this->redirectToRoute('app_login');
        #}

        if ($user->isBlocked()) {
            $this->addFlash('danger', 'Votre compte a été bloqué en raison de commentaires inappropriés.');
            return $this->redirectToRoute('home'); 
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bannedWords = ['قتل', 'اغتصاب', 'اقتلك', 'اغتصبها', 'إسرائيل', '9atel', 'no9tlek', '9talha', 'no9tel', '9atlo', 'yo9tel', '3onef', '3ounef', '3anefha', '3anfo', '3anafha', '3anef', '3anif', 'mokhtal', 'mo5tal', 'yo9telha', 'yet8tasebha', 'yeghtasebha', 'ghtasebha', 'ye8tasebha', '8tasabha', 'israel', 'Israel']; 

            $commentContent = strtolower($comment->getContent());
            foreach ($bannedWords as $word) {
                if (str_contains($commentContent, $word)) {
                    $this->addFlash('danger', 'Votre commentaire contient des mots inappropriés. Veuillez le modifier.');

                    $attempts = $session->get('comment_attempts_' . $user->getId(), 0);
                    $attempts++;
                    $session->set('comment_attempts_' . $user->getId(), $attempts);

                    if ($attempts >= 4) {
                        $user->setIsBlocked(true);
                        $entityManager->persist($user);
                        $entityManager->flush();
                        $this->addFlash('danger', 'Votre compte a été bloqué après 3 tentatives de commentaires interdits.');
                        return $this->redirectToRoute('home'); 
                    }

                    try {
                        $twilio = new Client($_ENV['TWILIO_SID'], $_ENV['TWILIO_AUTH_TOKEN']);
                        $twilio->messages->create(
                            "+21658888302", // Remplace avec le numéro de l'admin
                            [
                                "from" => $_ENV['TWILIO_PHONE_NUMBER'],
                                "body" => "Alerte : L'utilisateur " . $user->getEmail() . " a tenté de poster un commentaire inapproprié."
                            ]
                        );
                        $this->addFlash('success', 'Notification SMS envoyée.');
                    } catch (\Exception $e) {
                        $this->addFlash('danger', 'Erreur lors de l\'envoi du SMS : ' . $e->getMessage());
                    }                 

                    return $this->render('comment/addComment.html.twig', [
                        'blog' => $blog,
                        'form' => $form->createView()
                    ]);
                }
            }

            $comment->setBlog($blog);
            $comment->setUser($user);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($comment);
            $entityManager->flush();

            $session->set('comment_attempts_' . $user->getId(), 0);

            $this->addFlash('success', 'Votre commentaire a été ajouté avec succès.');
            return $this->redirectToRoute('showBlogPatient', ['id' => $blog->getId()]);
        }

        return $this->render('comment/addComment.html.twig', [
            'blog' => $blog,
            'form' => $form->createView()
        ]);
    }


    #[Route('/admin/show_comments/{id}', name: 'show_comments')]
    public function showComments(Blog $blog): Response
    {
        #$this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('comment/showComment.html.twig', [
            'blog' => $blog,
            'comments' => $blog->getComments()
        ]);
    }

    #[Route('/patient/showCommentPatient/{id}', name: 'showCommentPatient')]
    public function showCommentPatient(Blog $blog): Response
    {
        #$this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('comment/showCommentPatient.html.twig', [
            'blog' => $blog,
            'comments' => $blog->getComments()
        ]);
    }

    #[Route('/admin/update_comment/{id}', name: 'update_comment')]
    public function editComment(Comment $comment, Request $request, EntityManagerInterface $entityManager): Response
    {
        #$this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($comment->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous ne pouvez modifier que vos propres commentaires.");
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUpdatedAt(new \DateTime());

            $entityManager->flush();

            return $this->redirectToRoute('show_comments', ['id' => $comment->getBlog()->getId()]);
        }

        return $this->render('comment/editComment.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }


    #[Route('/admin/delete_comment/{id}', name: 'delete_comment')]
    public function deleteComment(Comment $comment, EntityManagerInterface $entityManager): Response
    {
        #$this->denyAccessUnlessGranted('ROLE_ADMIN');

        $blogId = $comment->getBlog()->getId();
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('show_comments', ['id' => $blogId]);
    }

    
}
