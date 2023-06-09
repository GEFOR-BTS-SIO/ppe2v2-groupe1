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
    $data = json_decode(
        $request->getContent(),
        true
    );
    $content = $data['content'];
    $receiverId = $data['receiver_id'];

    $senderId = intval($this->getUser()->getId());


    $message = new Messages();
    $message->setContent($content);

    // Retrieve sender and receiver entities from their IDs
    //$receiver = $receiverId;
    $message->setSenderId($senderId);
    $message->setReceiverId($receiverId);



    // Persist the entity to the database
    $messagesRepository->save($message, true);
    dump($message);


  $conversationMessages = $messagesRepository->findConversationMessages($senderId, $receiverId);

$messages = [];
foreach ($conversationMessages as $message) {
    $messages[] = [
        'id' => $message->getId(),
        'content' => $message->getContent(),
        'sender_id' => $message->getSenderId(),
        'receiver_id' => $message->getReceiverId(),
    ];
}

$jsonContent = $serializerInterface->serialize($messages, 'json');
$response = new JsonResponse($jsonContent, 200, [
    'Content-Type' => 'application/json'
]);
return $response;
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
