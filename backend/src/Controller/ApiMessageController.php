<?php
namespace App\Controller;
use App\Entity\Messages;
use App\Entity\User;
use App\Repository\MessagesRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

class ApiMessageController extends AbstractController
{
  #[Route('/apimessage', name: 'app_api_message', methods:['GET', 'POST'])]
public function index(Request $request, MessagesRepository $messagesRepository, SerializerInterface $serializerInterface): Response
{
    $data = json_decode($request->getContent(), true);
    $content = $data['content'] ?? null;
    $receiverId = isset($data['receiver_id']) ? (int) $data['receiver_id'] : null;


    $senderId = $this->getUser()->getId();
    $content = $data['content'] ?? '';
    $message = new Messages();
    $message->setContent($content);
    $message->setSenderId($senderId);
    $message->setReceiverId($receiverId);

    // Persist the entity to the database
    $messagesRepository->save($message, true);

    $conversationMessages = $messagesRepository->findConversationMessages($senderId, $receiverId);

    $messages = [];

    foreach ($conversationMessages as $conversationMessage) {
        $messages = [
            'id' => $conversationMessage->getId(),
            'content' => $conversationMessage->getContent(),
            'sender_id' => $conversationMessage->getSenderId(),
            'receiver_id' => $conversationMessage->getReceiverId(),
        ];
         return $this->json($messages, 200,[]);
    }

dump($messages);
    //$jsonContent = $serializerInterface->serialize($messages, 'json');
   
}



    #[Route('/apiuser', name: 'app_user_api', methods: ['POST','GET'])]
    public function callUser (UserRepository $userRepository, SerializerInterface $serializerInterface): Response
    {
        $users = $userRepository->findAll();
        //$userArray = array_map(fn(User $user) => $user->toArray(), $user_id);
        $jsonContent = $serializerInterface->serialize($users, 'json');
        $response = new JsonResponse($jsonContent, 200, [
            'Content-Type' => 'application/json'
        ]);
        return $response;
    }
}
