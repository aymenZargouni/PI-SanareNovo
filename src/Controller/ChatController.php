<?php
namespace App\Controller;

use App\Service\PusherService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    #[Route('/send-message', name: 'send_message', methods: ['POST'])]
    public function sendMessage(Request $request, PusherService $pusherService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Pour debug : enregistre le contenu reçu
        file_put_contents('debug_log.txt', print_r($data, true));

        if ($data === null) {
            return new JsonResponse([
                'error' => 'Invalid JSON format',
                'raw' => $request->getContent()
            ], 400);
        }

        if (!isset($data['message'], $data['sender'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        $sender = (int)$data['sender'];

        // Si le message est envoyé par le médecin (ID = 4), on utilise un canal de diffusion dédié
        if ($sender === 4) {
            $channelName = 'chat_doctor_4';
        } else {
            // Ici vous pouvez définir la logique pour un message provenant d'un patient
            // Par exemple, envoyer au canal du médecin correspondant (ici, on suppose toujours le médecin d'ID 4)
            $channelName = 'chat_doctor_4';
        }

        $pusherService->trigger($channelName, 'new_message', [
            'sender' => $sender,
            'message' => $data['message'],
        ]);

        return new JsonResponse(['success' => true]);
    }

    #[Route('/chat', name: 'chat_interface')]
    public function chatInterface(): Response
    {
        return $this->render('chat/index.html.twig');
    }
}
