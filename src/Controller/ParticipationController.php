<?php

namespace App\Controller;

use App\Entity\Choice;
use App\Entity\Participation;
use App\Entity\Poll;
use App\Entity\Question;
use App\Entity\Answer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ParticipationController extends AbstractController
{
    #[Route('/users/{id}/participations', methods: ['GET'])]
    public function getUserParticipations(int $id, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface)
    {
        $repository = $entityManagerInterface->getRepository(User::class);

        $user = $repository->find($id);

        if(null === $user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $repository = $entityManagerInterface->getRepository(Participation::class);
        $participations = $repository->findBy(['user' => $user]);

        return new JsonResponse(
            $serializer->serialize($participations, 'json', ['ignored_attributes' => ['participations']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/users/{userId}/participations/{participationId}', methods: ['GET'])]
    public function getUserParticipation(int $userId, int $participationId, EntityManagerInterface $entityManagerInterface, SerializerInterface $serializer) {
        $repository = $entityManagerInterface->getRepository(User::class);

        $user = $repository->find($userId);

        if(null === $user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $repository = $entityManagerInterface->getRepository(Participation::class);
        $participation = $repository->find($participationId);

        if(null === $participation || $participation->getUser() !== $user) {
            return new JsonResponse(['message' => 'Participation not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            $serializer->serialize($participation, 'json', ['ignored_attributes' => ['participations']]),
            Response::HTTP_OK, 
            [],
            true
        );
    }

    #[Route('/users/{id}/participations', methods: ['POST'])]
    public function postUserParticipations(int $id, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {

        $repository = $entityManager->getRepository(User::class);
        $user = $repository->find($id);

        if(null === $user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $payload = json_decode($request->getContent(), true);
        $repository = $entityManager->getRepository(Poll::class);
        $poll = $repository->find($payload['poll']);
        if(null === $poll)
        {
            return new JsonResponse(['message' => 'Poll not found'], Response::HTTP_BAD_REQUEST);
        }

        $participation = (new Participation())
        ->setPoll($poll);

        $questionRepository = $entityManager->getRepository(Question::class);
        $answerRepository = $entityManager->getRepository(Answer::class);
        foreach ($payload['choices'] as $questionId => $answerId) {
            $question = $questionRepository->find($questionId);
            $answer = $answerRepository->find($answerId);
            if ($question === null || $answerId === null || $answer->getQuestion() !== $question) {
                return new JsonResponse(['message' => 'Poll not found'], Response::HTTP_BAD_REQUEST);
            }
            $choice = (new Choice())
                ->setQuestion($question)
                ->setAnswer($answer);

            $participation->addChoice($choice);
        }
        
        
        $user->addParticipation($participation);
        $entityManager->persist(($participation));
        $entityManager->flush();

        return new JsonResponse(
            $serializer->serialize($participation, 'json', ['ignored_attributes' => ['participations']]),
            Response::HTTP_CREATED,
            [],
            true
        );
    }
    

    #[Route('/users/{userId}/participations/{participationId}', methods: ['DELETE'])]
    public function deleteUserParticipation(int $userId, int $participationId, EntityManagerInterface $entityManagerInterface) {
        
        $repository = $entityManagerInterface->getRepository(User::class);
        $user = $repository->find($userId);
        $repository = $entityManagerInterface->getRepository(Participation::class);
        $participation = $repository->find($participationId);


        if(null !== $user && null !== $participation && $participation->getUser() === $user) {
            $entityManagerInterface->remove($participation);
            $entityManagerInterface->flush();
        }

        return new JsonResponse(
            null,
            Response::HTTP_NO_CONTENT, 
        );
    }

    // #[Route('/participation', name: 'app_user')]
    // public function index(): Response
    // {
    //     return $this->render('participation/index.html.twig', [
    //         'controller_name' => 'ParticipationController',
    //     ]);
    // }
}
