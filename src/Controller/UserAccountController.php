<?php

namespace App\Controller;

use App\Repository\UserAccountRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserAccountController extends AbstractController
{
    private $UserAccountRepository;
    private $serializer;

    public function __construct(UserAccountRepository $UserAccountRepository, SerializerInterface $serializer)
    {
        $this->UserAccountRepository = $UserAccountRepository;
        $this->serializer = $serializer;
    }

    #[Route("/users")]
    public function getAllPersonnes(): JsonResponse
    {
        $listPersonnes = $this->UserAccountRepository->findAll();

        // Utilisation du Serializer pour convertir les objets en JSON
        $jsonData = $this->serializer->serialize($listPersonnes, "json");

        // Création d'une JsonResponse avec le contenu JSON
        $response = new JsonResponse($jsonData, 200, [], true);

        return $response;
    }

    #[Route("/user/{idUser}/tweets")]
    public function getTweetsByIdUser(int $idUser): JsonResponse
    {
        $user = $this->UserAccountRepository->findOneByIdWithTweets($idUser);

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
            ];

            $userData['tweets'][] = $tweetData;
        }

        $jsonData = $this->serializer->serialize($userData, 'json');

        $response = new JsonResponse($jsonData, 200, [], true);

        return $response;
    }

}
