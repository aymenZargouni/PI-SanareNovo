<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Comment;
use App\Entity\Rating;
use App\Form\BlogType;
use App\Form\CommentType;
use App\Form\RatingType;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BlogController extends AbstractController{
    
    #[Route('/blog', name: 'blog')]
    public function index(): Response
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }

    #[Route('/admin/showBlog', name: 'showBlog')]
    public function showBlogs(BlogRepository $BlogRep): Response
    {
        $Blog = $BlogRep->findByTitleSorted();
        return $this->render('blog/showBlog.html.twig', [
            'tabBlog' => $Blog,
        ]);
    }  

    #[Route('/showBlogPatient', name: 'showBlogPatient')]
    public function showBlogPatient(BlogRepository $blogRepository): Response
    {
        $blogs = $blogRepository->findAll();

        return $this->render('blog/showBlogPatient.html.twig', [
            'blogs' => $blogs,
        ]);
    }


    #[Route('/admin/addFormBlog', name: 'addFormBlog')]
    public function addFromBlog(ManagerRegistry $m, Request $req): Response
    {
        $em = $m->getManager();
        $Blog = new Blog();
        $form = $this->createForm(BlogType::class, $Blog);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($Blog);
            $em->flush();
            $this->addFlash('success', 'Le blog a été ajouté avec succès !');
            return $this->redirectToRoute('showBlog');
        }

        return $this->render('blog/addFormBlog.html.twig', [
            'formAddBlog' => $form->createView(),
        ]);
    }

    #[Route('/admin/updateFormBlog/{id}', name: 'updateFormBlog')]
    public function updateFormBlog(ManagerRegistry $m, Request $req, $id, BlogRepository $BlogRep): Response
    {
        $em = $m->getManager();
        $Blog = $BlogRep->find($id);
    
        if (!$Blog) {
            throw $this->createNotFoundException('Blog not found');
        }
    
        $form = $this->createForm(BlogType::class, $Blog);
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($Blog);
            $em->flush();
            
            // ✅ Ajout du message flash pour informer l'utilisateur
            $this->addFlash('success', 'Le blog a été modifié avec succès !');
            
            // ✅ Redirection vers la liste des blogs
            return $this->redirectToRoute('showBlog');
        }
    
        return $this->render('blog/updateFormBlog.html.twig', [
            'formUpdateBlog' => $form->createView(),
        ]);
    }
    

    #[Route('/admin/deleteFormBlog/{id}', name: 'deleteFormBlog')]
    public function deleteFormBlog( $id,ManagerRegistry $m, BlogRepository $BlogRep): Response
    {
        $em = $m->getManager();
        //var_dump($id).die();
        $Blog = $BlogRep->find($id);
        //var_dump($Blog).die();
        if ($Blog) { 
            $em->remove($Blog);
            $em->flush();
            $this->addFlash('danger', 'Le blog a été supprimé avec succès !');
        } else {
            $this->addFlash('warning', 'Le blog que vous essayez de supprimer n\'existe pas.');
        }
        return $this->redirectToRoute('showBlog'); // redige vers la liste des auteurs aprés l'ajout  

    }

    #[Route('/admin/blog_details/{id}', name: 'blog_details')]
    public function blogDetails(Blog $blog, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création d'un nouveau commentaire
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        // Traitement du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setBlog($blog);
            $comment->setCreatedAt(new \DateTimeImmutable()); // Assurez-vous que c'est DateTimeImmutable
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('blog_details', ['id' => $blog->getId()]);
        }
        // Rendu de la vue avec le formulaire
        return $this->render('blog/BlogDetails.html.twig', [
            'blog' => $blog,
            'form' => $form->createView() // 🔥 Envoi du formulaire à Twig
        ]);
    }


    
    #[Route("/admin/searchBlog", name:"search_blog")]
 
    public function searchBlog(Request $request, BlogRepository $blogRepository): Response
    {
        $query = $request->query->get('query', '');
        
        // Recherche des blogs par titre
        $tabBlog = $blogRepository->createQueryBuilder('b')
            ->where('b.title LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();

        return $this->render('blog/showBlog.html.twig', [
            'tabBlog' => $tabBlog,
        ]);
    }

    #[Route("/searchBlog", name:"search_blog")]
 
    public function searchBlogPatient(Request $request, BlogRepository $blogRepository): Response
    {
        $query = $request->query->get('query', '');
        
        // Recherche des blogs par titre
        $tabBlog = $blogRepository->createQueryBuilder('b')
            ->where('b.title LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();

        return $this->render('blog/showBlogPatient.html.twig', [
            'blogs' => $tabBlog,
        ]);
    }

    #[Route('/blog/{id}/rate', name: 'rate_blog')]
    public function rateBlog(Request $request, Blog $blog, EntityManagerInterface $entityManager)
    {
        $rating = new Rating();
        $rating->setBlog($blog);

        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rating);
            $entityManager->flush();

            $this->addFlash('success', 'Merci pour votre évaluation !');
            return $this->redirectToRoute('showBlog', ['id' => $blog->getId()]);
        }

        return $this->render('blog/rate.html.twig', [
            'form' => $form->createView(),
            'blog' => $blog,
        ]);
    }

}
