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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Knp\Component\Pager\PaginatorInterface;
final class BlogController extends AbstractController{
    
    #[Route('/blog', name: 'blog')]
    public function index(): Response
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }

    #[Route('/patient/chat', name: 'blog')]
    public function chat(): Response
    {
        #$this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('blog/chat.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }

    
    #[Route('/admin/showBlog', name: 'showBlog')]
    public function showBlogs(BlogRepository $blogRepository): Response
    {
        #$this->denyAccessUnlessGranted('ROLE_ADMIN');

        $blogs = $blogRepository->findByTitleSorted();
        
        return $this->render('blog/showBlog.html.twig', [
            'tabBlog' => $blogs
        ]);
    }
    

    /*#[Route('/patient/showBlogPatient', name: 'showBlogPatient')]
    public function showBlogPatient(BlogRepository $blogRepository): Response
    {
        $blogs = $blogRepository->findAllSortedByRating();
        
        return $this->render('blog/showBlogPatient.html.twig', [
            'blogs' => array_map(fn($result) => $result[0], $blogs), // 🔥 On ne prend que l'objet Blog
        ]);
    }*/


    /*#[Route('/patient/showBlogPatient', name: 'showBlogPatient')]
    public function showBlogPatient(BlogRepository $blogRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $blogRepository->createQueryBuilder('b')
            ->orderBy('b.createdAt', 'DESC') 
            ->getQuery();

        $pagination = $paginator->paginate(
            $query, 
            $request->query->getInt('page', 1), 
            3 
        );

        return $this->render('blog/showBlogPatient.html.twig', [
            'pagination' => $pagination,
        ]);
    }*/

    #[Route('/patient/showBlogPatient', name: 'showBlogPatient')]
    public function showBlogPatient(BlogRepository $blogRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $blogRepository->createQueryBuilder('b')
            ->leftJoin('b.ratings', 'r')
            ->addSelect('AVG(r.score) as HIDDEN avgRating') // Calcul de la moyenne
            ->groupBy('b.id')
            ->orderBy('avgRating', 'DESC') // Trier par la note moyenne la plus élevée
            ->addOrderBy('b.createdAt', 'DESC') // En cas d'égalité, trier par date
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('blog/showBlogPatient.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    

    #[Route('/admin/addFormBlog', name: 'addFormBlog')]
    public function addFromBlog(ManagerRegistry $m, Request $req): Response
    {
        #$this->denyAccessUnlessGranted('ROLE_ADMIN');

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
        #$this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $m->getManager();
        $Blog = $BlogRep->find($id);
    
        if (!$Blog) {
            throw $this->createNotFoundException('Blog not found');
        }
    
        $form = $this->createForm(BlogType::class, $Blog);
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $Blog->setUpdatedAt(new \DateTimeImmutable());
    
            $em->persist($Blog);
            $em->flush();
    
            $this->addFlash('success', 'Le blog a été modifié avec succès !');
            return $this->redirectToRoute('showBlog');
        }
    
        return $this->render('blog/updateFormBlog.html.twig', [
            'formUpdateBlog' => $form->createView(),
            'blog' => $Blog,
        ]);
    }
    

    #[Route('/admin/deleteFormBlog/{id}', name: 'deleteFormBlog')]
    public function deleteFormBlog( $id,ManagerRegistry $m, BlogRepository $BlogRep): Response
    {
        #$this->denyAccessUnlessGranted('ROLE_ADMIN');

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
        return $this->redirectToRoute('showBlog'); 

    }

    #[Route('/patient/blog_details/{id}', name: 'blog_details')]
    public function blogDetails(Blog $blog, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setBlog($blog);
            $comment->setCreatedAt(new \DateTimeImmutable()); 
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('blog_details', ['id' => $blog->getId()]);
        }
        return $this->render('blog/BlogDetails.html.twig', [
            'blog' => $blog,
            'form' => $form->createView() 
        ]);
    }


    
    #[Route("/admin/searchBlog", name:"search_blog")]
    public function searchBlog(Request $request, BlogRepository $blogRepository): Response
    {
        $query = $request->query->get('query', '');

        $tabBlog = $query ? $blogRepository->searchByTitle($query) : [];

        return $this->render('blog/showBlog.html.twig', [
            'tabBlog' => $tabBlog,
        ]);
    }

    #[Route('/search/blogs', name: 'search_blog_ajax', methods: ['GET'])]
    public function searchBlogAjax(Request $request, BlogRepository $blogRepository): JsonResponse
    {
        $query = $request->query->get('query', '');
        dump($query); // ⚠️ Vérifie la requête dans le log de Symfony
        $blogs = $query ? $blogRepository->searchByTitleAjax($query) : [];
    
        return $this->json($blogs, 200, [], ['groups' => 'blogs']);
    }
    
    

    #[Route('/blog/{id}/rate', name: 'rate_blog')]
    public function rateBlog(Request $request, Blog $blog, EntityManagerInterface $entityManager)
    {
        #$this->denyAccessUnlessGranted('ROLE_USER');
        $rating = new Rating();
        $rating->setBlog($blog);

        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rating);
            $entityManager->flush();

            $this->addFlash('success', 'Merci pour votre évaluation !');
            return $this->redirectToRoute('showBlogPatient', ['id' => $blog->getId()]);
        }

        return $this->render('blog/rate.html.twig', [
            'form' => $form->createView(),
            'blog' => $blog,
        ]);
    }

    #[Route('/admin/blog_stats/category', name: 'blog_stats_category')]
    public function blogStatsCategories(BlogRepository $blogRepository): Response
    {
        #$this->denyAccessUnlessGranted('ROLE_ADMIN');
        $blogsWithMostCategories = $blogRepository->findBlogsWithMostCategories();

        return $this->render('blog/blogStatsCategory.html.twig', [
            'blogsWithMostCategories' => $blogsWithMostCategories,
        ]);
    }

    #[Route('/admin/blog_stats/comments', name: 'blog_stats_comments')]
    public function blogStatsComments(BlogRepository $blogRepository): Response
    {
        #$this->denyAccessUnlessGranted('ROLE_ADMIN');

        $blogsWithMostComments = $blogRepository->findBlogsWithMostComments();

        // Convertir en tableau pour JSON
        $blogsWithMostCommentsArray = array_map(function ($blog) {
            return [
                'title' => $blog->getTitle(),
                'comments_count' => count($blog->getComments()),
            ];
        }, $blogsWithMostComments);

        return $this->render('blog/blogStatsComment.html.twig', [
            'blogsWithMostComments' => $blogsWithMostCommentsArray,
        ]);
    }

    
    #[Route('/blog/{id}/download', name: 'blog_download_pdf')]
    public function downloadBlogPdf(Blog $blog, Environment $twig, ParameterBagInterface $params, Request $request): Response
    {
        #$this->denyAccessUnlessGranted('ROLE_USER');

        $projectDir = $params->get('kernel.project_dir');
        
        $serverUrl = $request->getSchemeAndHttpHost();

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isRemoteEnabled', true); // Permet de charger les images

        $dompdf = new Dompdf($pdfOptions);

        $imagePath = $blog->getImage() 
            ? $serverUrl . '/uploads/images/' . $blog->getImage() 
            : null;

        $html = $twig->render('blog/blog_pdf.html.twig', [
            'blog' => $blog,
            'imagePath' => $imagePath
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="blog_' . $blog->getId() . '.pdf"'
        ]);
    }


    
}
