<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    // Ajouter un commentaire
    #[Route('/add_comment/{id}', name: 'add_comment')]
    public function addComment(Blog $blog, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setBlog($blog);
            $comment->setCreatedAt(new \DateTimeImmutable()); // Assure que c'est un DateTimeImmutable
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('show_comments', ['id' => $blog->getId()]);
        }

        return $this->render('comment/addComment.html.twig', [
            'blog' => $blog,
            'form' => $form->createView()
        ]);
    }

    // Afficher les commentaires d'un blog
    #[Route('/show_comments/{id}', name: 'show_comments')]
    public function showComments(Blog $blog): Response
    {
        return $this->render('comment/showComment.html.twig', [
            'blog' => $blog,
            'comments' => $blog->getComments()
        ]);
    }

    // Modifier un commentaire
    #[Route('/admin/update_comment/{id}', name: 'update_comment')]
    public function updateComment(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('show_comments', ['id' => $comment->getBlog()->getId()]);
        }

        return $this->render('comment/updateComment.html.twig', [
            'formComment' => $form->createView(),
            'comment' => $comment
        ]);
    }

    // Supprimer un commentaire
    #[Route('/admin/delete_comment/{id}', name: 'delete_comment')]
    public function deleteComment(Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $blogId = $comment->getBlog()->getId();
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('show_comments', ['id' => $blogId]);
    }
}
