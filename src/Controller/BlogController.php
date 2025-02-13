<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
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

    #[Route('/showBlog', name: 'showBlog')]
    public function showBlogs(BlogRepository $BlogRep): Response
    {
        $Blog = $BlogRep->findAll();
        return $this->render('blog/showBlog.html.twig', [
            'tabBlog' => $Blog,
        ]);
    }  

    #[Route('/addFormBlog', name: 'addFormBlog')]
    public function addFromBlog(ManagerRegistry $m, Request $req): Response
    {
        $em = $m->getManager(); 
        $Blog = new Blog();
        $form = $this->createForm(BlogType::class, $Blog);
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($Blog);
            $em->flush();
            return $this->redirectToRoute('showBlog'); // Redirection après ajout
        }
    
        return $this->render('blog/addFormBlog.html.twig', [
            'formAddBlog' => $form->createView(),
        ]);
    }
    


    #[Route('/updateFormBlog/{id}', name: 'updateFormBlog')]
    public function updateFormBlog(ManagerRegistry $m, Request $req, $id, BlogRepository $BlogRep): Response
    {

        $em = $m->getManager(); 
        $Blog = $BlogRep->find($id);
        $form=$this->createForm(BlogType::class, $Blog);
        $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()){
            $em->persist($Blog);
            $em->flush();
            return $this->redirectToRoute('showBlog'); // redige vers la liste des auteurs aprés l'ajout  

        }
        return $this->render('blog/addFormBlog.html.twig', [
            'formAddBlog' => $form,
        ]);
    }

    #[Route('/deleteFormBlog/{id}', name: 'deleteFormBlog')]
    public function deleteFormBlog( $id,ManagerRegistry $m, BlogRepository $BlogRep): Response
    {
        $em = $m->getManager();
        //var_dump($id).die();
        $Blog = $BlogRep->find($id);
        //var_dump($Blog).die();
        $em->remove($Blog);
        $em->flush();
        return $this->redirectToRoute('showBlog'); // redige vers la liste des auteurs aprés l'ajout  

    }

}
