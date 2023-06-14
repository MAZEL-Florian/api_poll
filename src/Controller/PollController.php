<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Answer;
use App\Entity\Poll;
use App\Entity\Question;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PollController extends AbstractController
{
    #[Route('/polls', methods: ['GET'])]
    public function getPolls(SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, Request $request)
    {

        $repository = $entityManagerInterface->getRepository(Poll::class);
        $polls = $repository->findAll();

        return new JsonResponse(
            $serializer->serialize($polls, 'json'),
            Response::HTTP_OK, 
            [],
            true
        );
    }

    #[Route('/polls/{id}', methods: ['GET'])]
    public function getPoll(int $id, EntityManagerInterface $entityManagerInterface, SerializerInterface $serializer) {
        $repository = $entityManagerInterface->getRepository(Poll::class);
        $poll = $repository->find($id);

        if($poll === null) {
            return new JsonResponse(['message' => 'Poll not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            $serializer->serialize($poll, 'json'),
            Response::HTTP_OK, 
            [],
            true
        );
    }


    #[Route('/polls', methods: ['POST'])]
    public function postPolls(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface) {
        $payload = json_decode($request->getContent(), true);
    
        $poll = (new Poll())
            ->setTitle($payload['title']);
    

        $repository = $entityManagerInterface->getRepository(Question::class);

        foreach($payload['questions'] as $questionId) {
            $question = $repository->find($questionId);
            $poll->addQuestion($question);
        }
    
        $entityManagerInterface->persist($poll);
        $entityManagerInterface->flush();
    
        $serializedPoll = $serializer->serialize($poll, 'json',  ['ignored_attributes' => ['question']]);
    
        return new JsonResponse(
            $serializedPoll,
            Response::HTTP_CREATED,
            [],
            true
        );
    }
    

    #[Route('/polls/{id}', methods: ['DELETE'])]
    public function deletePoll(int $id, EntityManagerInterface $entityManagerInterface) {
        
        $repository = $entityManagerInterface->getRepository(Poll::class);
        $poll = $repository->find($id);

        if(null !== $poll) {
            foreach($poll->getQuestions() as $question)
            {
                $question->setPoll(null);
                $entityManagerInterface->persist($question);
            }
            $entityManagerInterface->remove($poll);
            $entityManagerInterface->flush();
        }

        return new JsonResponse(
            null,
            Response::HTTP_NO_CONTENT, 
        );
    }

    #[Route('/polls/{id}', methods: ['PUT'])]
    public function putPoll(int $id, EntityManagerInterface $entityManagerInterface, SerializerInterface $serializer, Request $request) {
        $payload = json_decode($request->getContent(), true);
        $repository = $entityManagerInterface->getRepository(Poll::class);
        $poll = $repository->find($id);
    
        if ($poll === null) {
            return new JsonResponse(['message' => 'Poll not found'], Response::HTTP_NOT_FOUND);
        }
        if (!isset($payload['title'])) {
            return new JsonResponse(['error' => 'The value title should not be blank'], Response::HTTP_BAD_REQUEST);
        }
    
        $poll->setTitle($payload['title']);
        $entityManagerInterface->flush();
    
        $serializedPoll = $serializer->serialize($poll, 'json', ['ignored_attributes' => ['question']]);
    
        return new JsonResponse(
            $serializedPoll,
            Response::HTTP_OK,
            [],
            true
        );
    }
    

    #[Route('/polls/{id}', methods: ['PATCH'])]
    public function patchPoll(int $id, EntityManagerInterface $entityManagerInterface, SerializerInterface $serializer, Request $request) {

        $payload = json_decode($request->getContent(), true);
        $repository = $entityManagerInterface->getRepository(Poll::class);
        $poll = $repository->find($id);

        if($poll === null) {
            return new JsonResponse(['message' => 'Poll not found'], Response::HTTP_NOT_FOUND);
        }

        foreach($payload as $key => $value) {
            $method = 'set'.ucfirst($key);
            if(is_callable([$poll, $method])){
                $poll->$method($value);
            }
            else {
                return new JsonResponse(['error' => 'Attribute ' .$key. 'doesnt\'t exist. '], Response::HTTP_BAD_REQUEST);
            }
        }

        $entityManagerInterface->flush();

        return new JsonResponse(
            $serializer->serialize($poll, 'json'),
            Response::HTTP_OK, 
            [],
            true
        );
    }

    #[Route('/poll', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('question/index.html.twig', [
            'controller_name' => 'PollController',
        ]);
    }
}
