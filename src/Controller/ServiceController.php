<?php

namespace App\Controller;
use App\Entity\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
<<<<<<< HEAD
use App\Repository\SalleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
=======
use Doctrine\Persistence\ManagerRegistry;


>>>>>>> 76cd849eb886fb88c0dc2f63d25d196cdcb96271

final class ServiceController extends AbstractController{
    #[Route('/service', name: 'app_service')]
    public function index(): Response
    {
        return $this->render('service/index.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }

    #[Route('showservice', name: 'showservice')]
<<<<<<< HEAD
    public function showservice (ServiceRepository $serRep,Request $req): Response
    {
         // Get the search term from the request (if any)
    $searchTerm = $req->query->get('search', '');

    // If a search term is provided, filter the services by name
    if ($searchTerm) {
        $services = $serRep->findByName($searchTerm);
    } else {
        // Otherwise, fetch all services
        $services = $serRep->findAll();
    }

    return $this->render('service/showservice.html.twig', [
        'tabservice' => $services,
        'searchTerm' => $searchTerm,  // Pass the search term to the template
    ]);}
=======
    public function showservice (ServiceRepository $serRep): Response
    {
        $Blog = $serRep->findAll();
        return $this->render('service/showservice.html.twig', [
            'tabservice' => $Blog,
        ]);
    }  
>>>>>>> 76cd849eb886fb88c0dc2f63d25d196cdcb96271

    #[Route('/addFormservice', name: 'addFormservice')]
    public function addFromservice( ManagerRegistry $m, Request $req): Response
    {
        $em = $m->getManager(); 
        $serv = new Service();
        $form = $this->createForm(ServiceType::class, $serv);
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($serv);
            $em->flush();
            return $this->redirectToRoute('showservice'); 
        }
    
        return $this->render('service/addFormservice.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/updateFormservice/{id}', name: 'updateFormservice')]
    public function updateFormBlog(ManagerRegistry $m, Request $req, $id, ServiceRepository $BlogRep): Response
    {

        $em = $m->getManager(); 
        $Blog = $BlogRep->find($id);
        $form=$this->createForm(ServiceType::class, $Blog);
        $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()){
            $em->persist($Blog);
            $em->flush();
            return $this->redirectToRoute('showservice'); 

        }
        return $this->render('service/modFormservice.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/deleteservice/{id}', name: 'deleteservice')]
    public function deleteFormBlog( $id,ManagerRegistry $m, ServiceRepository $BlogRep): Response
    {
        $em = $m->getManager();
    
        $Blog = $BlogRep->find($id);
      
        $em->remove($Blog);
        $em->flush();
        return $this->redirectToRoute('showservice'); // redige vers la liste des auteurs aprés l'ajout  

    }
<<<<<<< HEAD

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashbord (ServiceRepository $serviceRepository, SalleRepository $salleRepository): Response
    {
        $services = $serviceRepository->findAll();

        $stats = [];
        foreach ($services as $service) {
            $salles = $service->getSalles(); // Supposant qu'il y a une relation OneToMany
            $totalSalles = count($salles);
            $sallesOccupees = count(array_filter($salles->toArray(), fn($salle) => $salle->isOccupee()));


            $stats[] = [
                'service' => $service->getNom(),
                'total_salles' => $totalSalles,
                'salles_occupees' => $sallesOccupees,
                'pourcentage_occupees' => $totalSalles > 0 ? round(($sallesOccupees / $totalSalles) * 100, 2) : 0
            ];
        }

        return $this->render('service/dashbord.html.twig', [
            'stats' => $stats
        ]);
    }
    public function searchSalle(Request $request, ServiceRepository $a)
    {
        // Récupérer la chaîne de recherche envoyée par Ajax
        $query = $request->get('query');
    
        // Rechercher les salles par ID en fonction de la requête
        if ($query) {
            // Utiliser la méthode personnalisée dans le repository pour filtrer les salles
            $salles = $a->searchById($query);
        } else {
            $salles = [];
        }
    
        // Retourner les résultats sous forme de JSON
        return new JsonResponse($salles);
    }
=======
>>>>>>> 76cd849eb886fb88c0dc2f63d25d196cdcb96271
}
