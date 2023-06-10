<?php
namespace App\Controller;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
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
public function index(Request $request, MessageRepository $messageRepository, SerializerInterface $serializerInterface): Response
{
    $data = json_decode($request->getContent(), true);
    $content = $data['content'] ?? null;
   $receiverId = isset($data['receiverId']) ? intval($data['receiverId']) : null;
    $receiver = $messageRepository->find($receiverId);


    $sender = $this->getUser();
    $content = $data['content'] ?? '';
    $message = new Message();
    $message->setContent($content);
    $message->setSender($sender);
    $message->setReceiver($receiver);

    // Persist the entity to the database
    $messageRepository->save($message, true);
    return new Response("", 201);
}

#[Route('/apiconversation', name: 'app_api_conversation', methods:['GET'])]
public function getMessage(Request $request, MessageRepository $messageRepository, SerializerInterface $serializerInterface): Response
{
    $receiverId = $request->query->get('receiverId'); 
    if (!$receiverId){
        return $this->json([]);
    }
       
    $receiverId = isset($data['receiverId']) ? intval($data['receiverId']) : null;
    $receiver = $messageRepository->find($receiverId);
    $sender = $this->getUser();
    $conversationMessages = $messageRepository->findBy([
        'receiver' => $receiver,
        'sender' => $sender,
    ], [
        'id' => 'ASC', // Or any other ordering criteria you want to use
    ]);


    $messages = [];

    foreach ($conversationMessages as $conversationMessage) {
        $messages[]= [
            'id' => $conversationMessage->getId(),
            'content' => $conversationMessage->getContent(),
            'sender' => $conversationMessage->getSender()->getId(),
            'receiver' => $conversationMessage->getReceiver()->getId(),
        ];  
    }

    return $this->json($messages);

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
