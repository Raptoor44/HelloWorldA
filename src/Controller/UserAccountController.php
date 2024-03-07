<?php

namespace App\Controller;

use App\Repository\UserAccountRepository;
use App\Entity\UserAccount;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

class UserAccountController extends AbstractController
{
    private $UserAccountRepository;
    private $serializer;

    private $dataManager;

    private $passwordHasher;

    public function __construct(UserAccountRepository $UserAccountRepository, SerializerInterface $serializer, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->UserAccountRepository = $UserAccountRepository;
        $this->serializer = $serializer;
        $this->dataManager = $manager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route("api/users", methods: ['GET'])]
    #[OA\Tag(name:"UserAccount")]
    public function getAllPersonnes(): JsonResponse
    {
        $listPersonnes = $this->UserAccountRepository->findAll();

        // Utilisation du Serializer pour convertir les objets en JSON
        $jsonData = $this->serializer->serialize($listPersonnes, "json");

        // Création d'une JsonResponse avec le contenu JSON
        $response = new JsonResponse($jsonData, 200, [], true);

        return $response;
    }

    #[Route("api/user", methods: ['POST'])]
    #[OA\Tag(name:"UserAccount")]
    public function addUser(Request $request): JsonResponse
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

        $this->dataManager->flush();

        return $this->json(['message' => 'User created successfully', 'idUser' => $userToSave->getId()]);
    }

    #[Route("api/user/{idUser}/tweets", methods: ['GET'])]
    #[OA\Tag(name:"UserAccount")]
    public function getTweetsByIdUser(int $idUser): JsonResponse
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

        return $response;
    }

}
