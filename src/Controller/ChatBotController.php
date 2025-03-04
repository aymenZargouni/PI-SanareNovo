<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ChatBotController extends AbstractController
{
    #[Route('/chat', name: 'chat_page')]
    public function chat()
    {
        // Charger le fichier JSON
        $data = file_get_contents($this->getParameter('kernel.project_dir') . '/public/chat_bot.json');
        $questions = json_decode($data, true);

        // Passer les questions à la vue Twig
        return $this->render('service/chatbot.html.twig', [
            'questions' => $questions['questions']
        ]);
    }
}
