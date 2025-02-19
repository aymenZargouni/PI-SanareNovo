<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController{
    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }


    #[Route('/admin/showCategory', name: 'showCategory')]
    public function showCategory(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('category/showCategory.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/admin/addFormCategory', name: 'addFormCategory')]
    public function addCategory(ManagerRegistry $registry, Request $request): Response
    {
        $em = $registry->getManager();
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'La catégorie a été ajoutée avec succès !');
            return $this->redirectToRoute('showCategory');
        }
    
        return $this->render('category/addFormCategory.html.twig', [
            'formCategory' => $form->createView(),
        ]);
    }

    #[Route('/admin/updateFormCategory/{id}', name: 'updateFormCategory')]
    public function updateCategory(ManagerRegistry $registry, Request $request, $id, CategoryRepository $categoryRepository): Response
    {
        $em = $registry->getManager();
        $category = $categoryRepository->find($id);
        if (!$category) {
            $this->addFlash('danger', 'Catégorie non trouvée.');
            return $this->redirectToRoute('showCategory');
        }
    
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'La catégorie a été mise à jour avec succès !');
            return $this->redirectToRoute('showCategory');
        }
    
        return $this->render('category/updateFormCategory.html.twig', [
            'formCategory' => $form->createView(),
        ]);
    }

    #[Route('/admin/deleteCategory/{id}', name: 'deleteCategory')]
    public function deleteCategory($id, ManagerRegistry $registry, CategoryRepository $categoryRepository): Response
    {
        $em = $registry->getManager();
        $category = $categoryRepository->find($id);
        
        if ($category) {
            $em->remove($category);
            $em->flush();
            $this->addFlash('danger', 'La catégorie a été supprimée avec succès !');
        } else {
            $this->addFlash('warning', 'La catégorie n\'existe pas.');
        }
    
        return $this->redirectToRoute('showCategory');
    }

}
