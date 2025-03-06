<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ConsultationRepository;

class ChartController extends AbstractController
{
    #[Route('/consultations-chart', name: 'consultations_chart')]
    public function consultationsChart(ConsultationRepository $consultationRepo): Response
    {
        // Fetch consultations grouped by motif
        $motifsCount = $consultationRepo->createQueryBuilder('c')
            ->select('c.motif, COUNT(c.id) as motif_count')
            ->groupBy('c.motif')
            ->getQuery()
            ->getResult();

        // Prepare data for the chart
        $chartData = [['Motif', 'Number of Consultations', ['role' => 'style']]]; // Added style role for dynamic colors
        $colors = ['#1b9e77', '#d95f02', '#7570b3', '#e7298a', '#66a61e', '#e6ab02', '#a6761d', '#666666']; // Custom colors for bars
        foreach ($motifsCount as $index => $motif) {
            $color = $colors[$index % count($colors)]; // Cycle through colors
            $chartData[] = [$motif['motif'], (int) $motif['motif_count'], "color: $color"]; // Add color to each bar
        }

        // Prepare options for the chart
        $chartOptions = [
            'chart' => [
                'title' => 'Consultations by Motif',
                'subtitle' => 'Number of consultations based on motif',
            ],
            'bars' => 'vertical',
            'vAxis' => ['format' => 'decimal'],
            'height' => 500,
            'legend' => ['position' => 'none'],
            'animation' => [ // Add chart animation
                'startup' => true,
                'duration' => 1000,
                'easing' => 'out',
            ],
        ];

        return $this->render('chart/index.html.twig', [
            'chartJson' => json_encode($chartData),
            'optionsJson' => json_encode($chartOptions),
        ]);
    }
}