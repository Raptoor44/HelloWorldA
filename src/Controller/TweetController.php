<?php

namespace App\Controller;

use App\Entity\Tweet;
use App\Entity\UserAccount;
use App\Repository\TweetRepository;
use App\Repository\UserAccountRepository;

use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
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
    #[Security(name: 'Bearer')]
    public function getAllTweets() : JsonResponse
    {
        $tweets = $this->tweetRepository->findAll();

        if(count($tweets) != 0){
            return $this->json(['tweets' => $tweets], 200);
        }else{
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
    #[Security(name: 'Bearer')]
    public function deleteTweet(int $id, TokenInterface $token): JsonResponse
    {

        $tweetToDelete = $this->tweetRepository->find($id);

        $user = $token->getUser();

        if (!($user instanceof UserAccount)) {
            $user = UserAccount::convertFrom($user);
        }

        if (!$tweetToDelete) {
            return $this->json(['error' => 'Tweet not found'], 403);
        }

        if ($user->getId() === $tweetToDelete->getUser()->getId() || in_array("ADMIN", $user->getRoles())) {
            $this->dataManager->remove($tweetToDelete);
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
    #[Security(name: 'Bearer')]
    public function incrementLikes(int $id): JsonResponse
    {

        $tweetToPatch = $this->tweetRepository->find($id);

        if (!$tweetToPatch) {
            return $this->json(['error' => 'Tweet not found'], 404);
        }

        $increment = $tweetToPatch->getNumberLikes() + 1;
        $tweetToPatch->setNumberLikes($increment);

        $this->dataManager->persist($tweetToPatch);
        $this->dataManager->flush();

        return $this->json(['message' => 'Tweet numberLikes increment successfully', 'idTweet' => $tweetToPatch->getId()]);
    }

    /**
     *
     * Cette route permet de désincrémenter un tweet en fonction d'une route.
     *
     */
    #[Route("api/tweet/unincrementLikes/{id}", name: "unincrementLikesTweet", methods: ['PATCH'])]
    #[OA\Tag(name: "Tweet")]
    #[OA\Response(
        response: 202, #Status code accepted
        description: 'Le tweet a bien reçu un désincrément de like',
    )]
    #[Security(name: 'Bearer')]
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
        $this->dataManager->flush();

        return $this->json(['message' => 'Tweet numberLikes Unincrement successfully', 'idTweet' => $tweetToPatch->getId()]);
    }

    /**
     *
     * Cette route permet de récupérer une liste de réponses en fonction d'un identifiant de tweet.
     *
     */
    #[Route("api/tweet/{idTweet}/responses", methods: ['GET'])]
    #[OA\Tag(name: "Tweet")]
    #[OA\Response(
        response: 200, #Statuts code od
        description: 'Liste de réponses',
    )]
    #[Security(name: 'Bearer')]
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

        $response = new JsonResponse($jsonData, 200, [], true);

        return $response;

    }
}