<?php

namespace App\Controller;

use App\Service\OpenWeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    private OpenWeatherService $weatherService;
    private RequestStack $requestStack;

    public function __construct(OpenWeatherService $weatherService, RequestStack $requestStack)
    {
        $this->weatherService = $weatherService;
        $this->requestStack = $requestStack;
    }

    public function getWeatherForHeader(): Response
    {
        // Défaut: météo de Tunis
        $city = 'Tunis';

        // Vérifier si l'utilisateur a défini une ville dans la session
        $session = $this->requestStack->getSession();
        if ($session->has('weather_city')) {
            $city = $session->get('weather_city');
        }

        $weatherData = $this->weatherService->getWeather($city);

        return $this->render('service/weather_header.html.twig', [
            'weather' => $weatherData,
        ]);
    }
}
