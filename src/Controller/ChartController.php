<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ConsultationRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart;

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
        $chartData = [['Motif', 'Number of Consultations']];
        foreach ($motifsCount as $motif) {
            $chartData[] = [$motif['motif'], (int) $motif['motif_count']];
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
            'colors' => ['#1b9e77', '#d95f02'],
            'legend' => ['position' => 'none'],
        ];

        return $this->render('chart/index.html.twig', [
            'chartJson' => json_encode($chartData),
            'optionsJson' => json_encode($chartOptions),  // Passing chart options to the template
        ]);
    }
}
