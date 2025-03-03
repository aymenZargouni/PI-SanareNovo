<?php
namespace App\Controller;

use App\Entity\User;
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
        $data = \json_decode($request->getContent(), true);
    
        if ($data === null) {
            return new JsonResponse(['error' => 'Invalid JSON format'], 400);
        }
    
        // Check for required fields (corrected to check for 'receiver')
        if (!isset($data['message'], $data['sender'], $data['role'], $data['receiver'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }
    
        $sender = (int)$data['sender'];
        $receiver = (int)$data['receiver'];
    
        // Define the channel based on the consultation ID
        $channelName = 'chat_consultation_' . $receiver; // Use 'receiver' here
    
        // Trigger the Pusher event
        $pusherService->trigger($channelName, 'new_message', [
            'sender' => $sender,
            'message' => $data['message'],
        ]);
    
        return new JsonResponse(['success' => true]);
    }

    #[Route('/chat/{consultationId}', name: 'chat_interface')]
    public function chatInterface($consultationId)
{
    // Assuming you're getting the user and their role
    $user = $this->getUser();
    $userRole = $user->getRoles()[0]; // Assuming single role
    $userId = $user->getId();
    return $this->render('chat/index.html.twig', [
        'userRole' => $userRole,
        'userId' => $userId,
        'consultationId' => $consultationId,
    ]);
}
#[Route('/chat/patient/{patientId}', name: 'chat_interface2')]
    public function chatInterface2($patientId)
    {
        $user = $this->getUser();
        $userRole = $user->getRoles()[0];
        $userId = $user->getId();

        return $this->render('chat/index.html.twig', [
            'userRole' => $userRole,
            'userId' => $userId,
            'consultationId' => $patientId, // Ici patientId remplace consultationId
        ]);
}
}