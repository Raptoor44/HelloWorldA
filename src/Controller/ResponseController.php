<?php

namespace App\Controller;

use App\Entity\Response;
use App\Entity\Tweet;
use App\Entity\UserAccount;
use App\Repository\ResponseRepository;
use App\Repository\TweetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResponseController extends AbstractController
{
    private $responseRepository;
    private $serializer;
    private $tweetRepository;
    private $dataManager;
    private $validator;


    public function __construct(ResponseRepository     $responseRepositoryParam,
                                TweetRepository        $tweetRepository,
                                SerializerInterface    $serializerParam,
                                EntityManagerInterface $managerParam,
                                ValidatorInterface     $validator)
    {
        $this->responseRepository = $responseRepositoryParam;
        $this->tweetRepository = $tweetRepository;

        $this->serializer = $serializerParam;

        $this->dataManager = $managerParam;

        $this->validator = $validator;
    }

    /**
     * @Route("api/response", name="createResponse", methods={"POST"})
     * @OA\Tag(name="Response")
     * @OA\RequestBody(
     *     required=true,
     *     description="Request body for creating a response",
     *     @OA\JsonContent(
     *         type="object",
     *         required={"content", "idTweet"},
     *         @OA\Property(property="content", type="string", example="Your response content"),
     *         @OA\Property(property="idTweet", type="integer", example=123)
     *     ),
     *     @OA\MediaType(
     *         mediaType="application/x-www-form-urlencoded",
     *         @OA\Schema(
     *             type="object",
     *             required={"content", "idTweet"},
     *             @OA\Property(property="content", type="string", example="Your response content"),
     *             @OA\Property(property="idTweet", type="integer", example=123)
     *         )
     *     )
     * )
     */
    public function createResponse(Request $request, TokenInterface $token): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $token->getUser();

        if (!($user instanceof UserAccount)) {
            $user = UserAccount::convertFrom($user);
        }

        $responseToSave = new Response();

        $responseToSave->setContent($data["content"]);
        $responseToSave->setNumberLikes(0);
        $responseToSave->setUserAccount($user);

        $tweetToSave = $this->tweetRepository->find($data["idTweet"]);

        if (!$tweetToSave) {
            return $this->json(['error' => 'Tweet not found'], 404);
        }

        $responseToSave->setTweet($tweetToSave);

        $errors = $this->validator->validate($responseToSave);

        if (count($errors) > 0) {
            $errorsString = (string)$errors;
            return $this->json(['Error' => 'Response not register in database', $errorsString]);
        }

        $this->dataManager->persist($responseToSave);
        $this->dataManager->flush();

        return $this->json(['message' => 'Response created successfully', 'idResponse' => $responseToSave->getId()]);

    }

    #[Route("api/response/{id}", name: "deleteResponse", methods: ['DELETE'])]
    #[OA\Tag(name: "Response")]
    public function deleteResponse(int $id, TokenInterface $token): JsonResponse
    {

        $responseToDelete = $this->responseRepository->find($id);

        $user = $token->getUser();

        if (!($user instanceof UserAccount)) {
            $user = UserAccount::convertFrom($user);
        }

        if (!$responseToDelete) {
            return $this->json(['error' => 'response not found'], 403);
        }

        if ($user->getId() === $responseToDelete->getUserAccount()->getId() || in_array("ADMIN", $user->getRoles())) {
            $this->dataManager->remove($responseToDelete);
            $this->dataManager->flush();

            return $this->json(['message' => "Response remove successfully", 'idResponse' => $id]);
        }

        return $this->json(['error' => 'Access Denied'], 401);#Non autorisé
    }

    #[Route("api/response/unincrementLikes/{id}", name: "unincrementLikesResponse", methods: ['PATCH'])]
    #[OA\Tag(name: "Response")]
    public function unincrementLikes(int $id): JsonResponse
    {

        $responseToPatch = $this->responseRepository->find($id);

        if (!$responseToPatch) {
            return $this->json(['error' => 'Response not found'], 404);
        }

        $increment = $responseToPatch->getNumberLikes() - 1;

        if ($increment < 0) {
            return $this->json(['error' => 'Response has already numberLikes of 0'], 406); #Not acceptable
        }

        $responseToPatch->setNumberLikes($increment);

        $this->dataManager->persist($responseToPatch);
        $this->dataManager->flush();

        return $this->json(['message' => 'Response numberLikes Unincrement successfully', 'idTweet' => $responseToPatch->getId()]);
    }

    #[Route("api/response/incrementLikes/{id}", name: "incrementLikesResponse", methods: ['PATCH'])]
    #[OA\Tag(name: "Response")]
    public function incrementLikes(int $id): JsonResponse
    {

        $responseToPatch = $this->responseRepository->find($id);

        if (!$responseToPatch) {
            return $this->json(['error' => 'Response not found'], 404);
        }

        $increment = $responseToPatch->getNumberLikes() + 1;
        $responseToPatch->setNumberLikes($increment);

        $this->dataManager->persist($responseToPatch);
        $this->dataManager->flush();

        return $this->json(['message' => 'Response numberLikes increment successfully', 'idTweet' => $responseToPatch->getId()]);
    }
}