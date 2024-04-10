<?php

namespace App\Controller;

use App\Entity\Log;
use App\Entity\Tweet;
use App\Repository\TweetRepository;
use App\Repository\UserAccountRepository;

use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TweetController extends AbstractController
{
    private $tweetRepository;
    private $userRepository;
    private $serializer;
    private $dataManager;
    private $validator;
    private $userService;

    public function __construct(TweetRepository        $tweetRepository,
                                UserAccountRepository  $userRepository,
                                EntityManagerInterface $manager,
                                SerializerInterface    $serializer,
                                ValidatorInterface     $validator)
    {
        $this->tweetRepository = $tweetRepository;
        $this->userRepository = $userRepository;

        $this->dataManager = $manager;

        $this->serializer = $serializer;

        $this->validator = $validator;

        $this->userService = new  UserService();
    }

    /**
     *
     * Cette route permet de récupérer tous les tweets des utilisateurs.
     *
     */
    #[Route("api/tweets", name: "getAllTweets", methods: ['GET'])]
    #[OA\Tag(name: "Tweet")]
    #[OA\Response(
        response: 200,
        description: 'La liste des tweets de base de données.',
    )]
    public function getAllTweets(): JsonResponse
    {
        $tweets = $this->tweetRepository->findAll();

        if (count($tweets) != 0) {

            #Partie enregistrement de log :

            $log = new Log();

            $log->setDateCreation(new \DateTime());
            $log->setControllerLibelle("TweetController");
            $log->setMethodLibelle("getAllTweets()");
            $log->setContent("Récupération de tous les tweets");

            #Partie récupération user :

            $user = $this->getUser(); #Utilisation d'une autre méthode pour récupérer l'utilisateur connecté.

            if ($user) {
                $log->setIdUser($user);
            }

            $this->dataManager->persist($log);
            $this->dataManager->flush();
            return $this->json(['tweets' => $tweets], 200);
        } else {
            return $this->json(["error" => "No tweets are find in database"], 204);
        }


    }

    /**
     *
     * La route permettant de créer un tweet.
     *
     */
    #[Route("api/tweet", name: "createTweet", methods: ['POST'])]
    #[OA\Tag(name: "Tweet")]
    #[OA\Parameter(
        name: 'user',
        description: "Le tweet et ses informations en paramètre.",
        in: 'query',
        required: true
    )]
    #[OA\Response(
        response: 201,
        description: "Le tweet a bien été créer.",
    )]
    public function createTweet(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $tweetToSave = new Tweet();

        $tweetToSave->setContent($data["content"]);
        $tweetToSave->setNumberLikes($data["numberLikes"]);

        $userId = $data["idUser"];
        $userAccount = $this->userRepository->find($userId);

        if (!$userAccount) {

            return $this->json(['error' => 'User not found'], 404);
        }

        $tweetToSave->setUser($userAccount);
        $tweetToSave->setAtCreated(new \DateTime(date("Y-m-d H:i:s")));

        $errors = $this->validator->validate($tweetToSave);

        if (count($errors) > 0) {
            $errorsString = (string)$errors;
            return $this->json(['Error' => 'Tweet not register in database', $errorsString]);
        }


        $this->dataManager->persist($tweetToSave);

        #Partie enregistrement de log :

        $log = new Log();

        $log->setDateCreation(new \DateTime());
        $log->setControllerLibelle("TweetController");
        $log->setMethodLibelle("createTweet()");
        $log->setContent("Enregistrement d'un nouveau tweet ayant pour id : " . $tweetToSave->getId() . " et ayant pour utilisateur : " . $tweetToSave->getUser()->getId());

        #Partie récupération user :

        $user = $this->getUser();

        if ($user) {
            $log->setIdUser($user);
        }

        $this->dataManager->persist($log);
        $this->dataManager->flush();

        return $this->json(['message' => 'Tweet created successfully', 'idTweet' => $tweetToSave->getId()]);
    }


    /**
     *
     * Cette route permet de supprimer un tweet.
     *
     */
    #[Route("api/tweet/{id}", name: "deleteTweet", methods: ['DELETE'])]
    #[OA\Tag(name: "Tweet")]
    #[OA\Response(
        response: 202, #Status code accepted
        description: 'Code de validation de suppression de tweet.',
    )]
    public function deleteTweet(int $id): JsonResponse
    {

        $tweetToDelete = $this->tweetRepository->find($id);
        $user = $this->getUser();


        if (!$tweetToDelete) {
            return $this->json(['error' => 'Tweet not found'], 403);
        }

        if ($user->getId() === $tweetToDelete->getUser()->getId() || $this->isGranted('ROLE_ADMIN')) {
            $this->dataManager->remove($tweetToDelete);
            #Partie enregistrement de log :

            $log = new Log();

            $log->setDateCreation(new \DateTime());
            $log->setControllerLibelle("TweetController");
            $log->setMethodLibelle("deleteTweet()");
            $log->setContent("Suppression du tweet ayant pour id : " . $tweetToDelete->getId());

            #Partie récupération user :

            if ($user) {
                $log->setIdUser($user);
            }

            $this->dataManager->persist($log);
            $this->dataManager->flush();

            return $this->json(['message' => 'Tweet remove successfully', 'idTweet' => $id]);
        }
        return $this->json(['error' => 'Access Denied'], 401);#Non autorisé

    }


    /**
     *
     * Cette route permet d'incrémenter en nombre de like un tweet en question.
     *
     */
    #[Route("api/tweet/incrementLikes/{id}", name: "incrementLikesTweet", methods: ['PATCH'])]
    #[OA\Tag(name: "Tweet")]
    #[OA\Response(
        response: 202, #Status code accepted
        description: 'Le tweet a bien reçu un incément de like',
    )]
    public function incrementLikes(int $id): JsonResponse
    {

        $tweetToPatch = $this->tweetRepository->find($id);

        if (!$tweetToPatch) {
            return $this->json(['error' => 'Tweet not found'], 404);
        }

        $increment = $tweetToPatch->getNumberLikes() + 1;
        $tweetToPatch->setNumberLikes($increment);

        $this->dataManager->persist($tweetToPatch);

        #Partie enregistrement de log :

        $log = new Log();

        $log->setDateCreation(new \DateTime());
        $log->setControllerLibelle("TweetController");
        $log->setMethodLibelle("incrementLikes()");
        $log->setContent("Incémentation de like pour : " . $tweetToPatch->getId());

        #Partie récupération user :

        $user = $this->getUser();

        if ($user) {
            $log->setIdUser($user);
        }

        $this->dataManager->persist($log);
        $this->dataManager->flush();

        return $this->json(['message' => 'Tweet numberLikes increment successfully', 'idTweet' => $tweetToPatch->getId()]);
    }

    /**
     *
     * Cette route permet de désincrémenter le nombre de j'aime d'un tweet en fonction d'une route.
     *
     */
    #[Route("api/tweet/unincrementLikes/{id}", name: "unincrementLikesTweet", methods: ['PATCH'])]
    #[OA\Tag(name: "Tweet")]
    #[OA\Response(
        response: 202, #Status code accepted
        description: 'Le tweet a bien reçu un désincrément de like',
    )]
    public function unincrementLikes(int $id): JsonResponse
    {

        $tweetToPatch = $this->tweetRepository->find($id);

        if (!$tweetToPatch) {
            return $this->json(['error' => 'Tweet not found'], 404);
        }

        $increment = $tweetToPatch->getNumberLikes() - 1;

        if ($increment < 0) {
            return $this->json(['error' => 'Tweet has already numberLikes of 0'], 406); #Not acceptable
        }

        $tweetToPatch->setNumberLikes($increment);

        $this->dataManager->persist($tweetToPatch);
        #Partie enregistrement de log :

        $log = new Log();

        $log->setDateCreation(new \DateTime());
        $log->setControllerLibelle("TweetController");
        $log->setMethodLibelle("unincrementLikesTweet()");
        $log->setContent("désincrémentation de like pour : " . $tweetToPatch->getId());

        #Partie récupération user :

        $user = $this->getUser();

        if ($user) {
            $log->setIdUser($user);
        }

        $this->dataManager->persist($log);
        $this->dataManager->flush();


        return $this->json(['message' => 'Tweet numberLikes Unincrement successfully', 'idTweet' => $tweetToPatch->getId()]);
    }

    /**
     *
     * Cette route permet de récupérer une liste de réponses en fonction d'un identifiant de tweet.
     *
     */
    #[Route("api/tweet/{idTweet}/responses", methods: ['GET'])]
    #[OA\Tag(name: "Response")]
    #[OA\Response(
        response: 200, #Statuts code od
        description: 'Liste de réponses',
    )]
    public function getResponsesByTweet(int $idTweet): JsonResponse
    {
        $tweet = $this->tweetRepository->findOneByIdWithResponses($idTweet);

        if (!$tweet) {
            return $this->json(['error' => 'Tweet not found'], 404);
        }

        $tweetData = [
            'id' => $tweet->getId(),
            'content' => $tweet->getContent(),
            'responses' => []
        ];

        foreach ($tweet->getResponses() as $response) {

            $userData = [
                'id' => $response->getUserAccount()->getId(),
                'firstName' => $response->getUserAccount()->getFirstName(),
                'lastName' => $response->getUserAccount()->getLastName(),
            ];

            $responseData = [
                'id' => $response->getId(),
                'content' => $response->getContent(),
                'numberLikes' => $response->getNumberLikes(),
                'user' => $userData
            ];

            $tweetData['responses'][] = $responseData;
        }
        $jsonData = $this->serializer->serialize($tweetData, 'json');


        #Partie enregistrement de log :

        $log = new Log();

        $log->setDateCreation(new \DateTime());
        $log->setControllerLibelle("TweetController");
        $log->setMethodLibelle("getResponsesByTweet()");
        $log->setContent("Récupération des réponses du tweet ayant pour id : " . $idTweet);

        #Partie récupération user :

        $user = $this->getUser();

        if ($user) {
            $log->setIdUser($user);
        }
        $log->setIdUser($user);

        $this->dataManager->persist($log);
        $this->dataManager->flush();

        $response = new JsonResponse($jsonData, 200, [], true);

        return $response;

    }
}