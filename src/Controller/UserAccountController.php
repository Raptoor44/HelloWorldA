<?php

namespace App\Controller;

use App\Dto\UserDto;
use App\Entity\Log;
use App\Repository\UserAccountRepository;
use App\Entity\UserAccount;

use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

class UserAccountController extends AbstractController
{
    private $UserAccountRepository;
    private $serializer;
    private $dataManager;
    private $passwordHasher;
    private $userService;
    public function __construct(UserAccountRepository $UserAccountRepository,
                                SerializerInterface $serializer,
                                EntityManagerInterface $manager,
                                UserPasswordHasherInterface $passwordHasher)
    {
        $this->UserAccountRepository = $UserAccountRepository;
        $this->serializer = $serializer;
        $this->dataManager = $manager;
        $this->passwordHasher = $passwordHasher;
        $this->userService = new UserService();
    }

    /**
     *
     *Cette route permet de récupérer la liste de tous les utilisateurs.
     *
     */
    #[Route("api/users", methods: ['GET'])] #Route log method id numéro 0
    #[OA\Tag(name:"UserAccount")]
    #[OA\Response(
        response: 200,
        description: 'Liste des tous les utilisateurs en base de données.',
    )]
    public function getAllPersonnes(TokenInterface $token): JsonResponse
    {
        $listPersonnes = $this->UserAccountRepository->findAllWithoutLogs();

        // Utilisation du Serializer pour convertir les objets en JSON
        $jsonData = $this->serializer->serialize($listPersonnes, "json");

        // Création d'une JsonResponse avec le contenu JSON
        $response = new JsonResponse($jsonData, 200, [], true, ['maxDepth' => 2]);

        $log = new Log();

        $log->setControllerLibelle("UserAccount");
        $log->setDateCreation(new \DateTime());
        $log->setMethodLibelle("GetAllPersones()");
        $log->setContent("Récupération de tous les utilisateurs.");

        #Partie récupération user :

        $user = $this->userService->GetUserWithTokenInterface($token);
        $log->setIdUser($user);

        $this->dataManager->persist($log);
        $this->dataManager->flush();

        return $response;
    }


    /**
     *
     * La route pemettant de créer un utilisateur
     *
     */
    #[Route("api/user", methods: ['POST'])]
    #[OA\Tag(name:"UserAccount")]  #Route log method id numéro 1
    #[OA\Parameter(
        name: 'user',
        description: "L'utilisateur et ses informations en paramètre.",
        in: 'query',
        required: true
    )]
    #[OA\Response(
        response: 201,
        description: "l'utilisateur a bien été créer.",
    )]
    public function addUser(Request $request, ?TokenInterface $token = null): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $userToSave = new UserAccount();

        $userToSave->setEmail($data["email"]);
        $userToSave->setLastName($data["lastName"]);
        $userToSave->setFirstName($data["firstName"]);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $userToSave,
            $data["password"]
        );

        $userToSave->setPassword($hashedPassword);
        $userToSave->setAtCreated(new \DateTime(date("Y-m-d H:i:s")));
        $this->dataManager->persist($userToSave);


        #Partie enregistrement de log :
        #id controller utilisateur : 0

        $log = new Log();

        $log->setDateCreation(new \DateTime());
        $log->setControllerLibelle("UserAccount");
        $log->setMethodLibelle("addUser()");
        $log->setContent("Creation de l'utilisateur pour id : " . $userToSave->getId() . "Pour nom étant de " . $userToSave->getFirstName());

        #Partie récupération user :
            $user = null;

        if($token){
            $user = $this->userService->GetUserWithTokenInterface($token);
        }

        if($user){
            $log->setIdUser($user);
        }

        $this->dataManager->persist($log);
        $this->dataManager->flush();

        return $this->json(['message' => 'User created successfully', 'idUser' => $userToSave->getId()]);
    }



    /**
     * Cette route permet de récupérer un utilisateur
     * ainsi que ses tweets qui créer par cette utilisateur.
     *
     */
    #[Route("api/user/{idUser}/tweets", methods: ['GET'])] #Route log method id numéro 2
    #[OA\Tag(name:"UserAccount")]
    #[OA\Response(
        response: 200,
        description: 'Le Json permettant de récupérer la liste des tweets par utilisateur.',
    )]
    public function getTweetsByIdUser(int $idUser, TokenInterface $token): JsonResponse
    {
        $user = $this->UserAccountRepository->findOneByIdWithTweets($idUser);

        if (!$user) {

            return $this->json(['error' => 'User not found'], 404);
        }

        $userData = [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'tweets' => [],
        ];

        foreach ($user->getTweets() as $tweet) {
            $tweetData = [
                'id' => $tweet->getId(),
                'content' => $tweet->getContent(),
                'numberLikes' => $tweet->getNumberLikes()
            ];

            $userData['tweets'][] = $tweetData;
        }

        $jsonData = $this->serializer->serialize($userData, 'json');

        $response = new JsonResponse($jsonData, 200, [], true);

        #Partie enregistrement de log :
        #id controller utilisateur : 0

        $log = new Log();
        $log->setDateCreation(new \DateTime());
        $log->setControllerLibelle("UserAccount");
        $log->setMethodLibelle("getTweetsByIdUser()");
        $log->setContent("Récupération de tous les tweets de l'utilisateur ayant pour identifiant : " . $user->getId() . " ayant pour nom : " . $user->getFirstName());

        #Partie récupération user :

        $user = $this->userService->GetUserWithTokenInterface($token);
        $log->setIdUser($user);

        $this->dataManager->persist($log);
        $this->dataManager->flush();

        return $response;
    }

}
