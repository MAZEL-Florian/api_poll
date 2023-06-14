<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class QuestionController extends AbstractController
{
    #[Route('/questions', methods: ['GET'])]
    public function getQuestions(SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, Request $request)
    {

        $repository = $entityManagerInterface->getRepository(Question::class);
        $questions = $repository->findAll();

        $questions = $serializer->serialize($questions, 'json', ['ignored_attributes' => ['question']]);

        return new JsonResponse(
            $serializer->serialize($questions, 'json', ['ignored_attributes' => ['question']]),
            Response::HTTP_OK, 
            [],
            true
        );
    }

    #[Route('/questions/{id}', methods: ['GET'])]
    public function getQuestion(int $id, EntityManagerInterface $entityManagerInterface, SerializerInterface $serializer) {
        $repository = $entityManagerInterface->getRepository(Question::class);
        $question = $repository->find($id);

        if($question === null) {
            return new JsonResponse(['message' => 'Question not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            $serializer->serialize($question, 'json', ['ignored_attributes' => ['question']]),
            Response::HTTP_OK, 
            [],
            true
        );
    }


    #[Route('/questions', methods: ['POST'])]
    public function postQuestions(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface) {
        $payload = json_decode($request->getContent(), true);
    
        $question = (new Question())
            ->setWording($payload['wording']);
    
        $answers = $payload['answers'] ?? [];
        foreach ($answers as $answerData) {
            $answer = (new Answer())
                ->setWording($answerData['wording']);
    
            $question->addAnswer($answer);
        }
    
        $entityManagerInterface->persist($question);
        $entityManagerInterface->flush();
    
        $serializedQuestion = $serializer->serialize($question, 'json', ['ignored_attributes' => ['question']]);
    
        return new JsonResponse(
            $serializedQuestion,
            Response::HTTP_CREATED, 
            [],
            true
        );
    }
    
    

    #[Route('/questions/{id}', methods: ['DELETE'])]
    public function deleteQuestion(int $id, EntityManagerInterface $entityManagerInterface) {
        
        $repository = $entityManagerInterface->getRepository(Question::class);
        $question = $repository->find($id);

        if(null !== $question) {
            $entityManagerInterface->remove($question);
            $entityManagerInterface->flush();
        }

        return new JsonResponse(
            null,
            Response::HTTP_NO_CONTENT, 
        );
    }

    #[Route('/questions/{id}', methods: ['PUT'])]
    public function putQuestion(int $id, EntityManagerInterface $entityManagerInterface, SerializerInterface $serializer, Request $request) {
        $payload = json_decode($request->getContent(), true);
        $repository = $entityManagerInterface->getRepository(Question::class);
        $question = $repository->find($id);
    
        if ($question === null) {
            return new JsonResponse(['message' => 'Question not found'], Response::HTTP_NOT_FOUND);
        }
        if (!isset($payload['wording'])) {
            return new JsonResponse(['error' => 'The value wording should not be blank'], Response::HTTP_BAD_REQUEST);
        }
        if (!isset($payload['answers'])) {
            return new JsonResponse(['error' => 'The value answers should not be blank'], Response::HTTP_BAD_REQUEST);
        }
    
        $question->setWording($payload['wording']);
        $question->getAnswers()->clear();
        $entityManagerInterface->flush();

        foreach ($payload['answers'] as $answer) {
            $answer = (new Answer())
                ->setWording($answer['wording']);
                $question->addAnswer($answer);
        }
         
        $entityManagerInterface->flush();
    
        $serializedQuestion = $serializer->serialize($question, 'json', ['ignored_attributes' => ['question']]);
    
        return new JsonResponse(
            $serializedQuestion,
            Response::HTTP_OK,
            [],
            true
        );
    }
    

    #[Route('/questions/{id}', methods: ['PATCH'])]
    public function patchQuestion(int $id, EntityManagerInterface $entityManagerInterface, SerializerInterface $serializer, Request $request) {

        $payload = json_decode($request->getContent(), true);
        $repository = $entityManagerInterface->getRepository(Question::class);
        $question = $repository->find($id);

        if($question === null) {
            return new JsonResponse(['message' => 'Question not found'], Response::HTTP_NOT_FOUND);
        }

        foreach($payload as $key => $value) {
            $method = 'set'.ucfirst($key);
            if(is_callable([$question, $method])){
                $question->$method($value);
            }
            else {
                return new JsonResponse(['error' => 'Attribute ' .$key. 'doesnt\'t exist. '], Response::HTTP_BAD_REQUEST);
            }
        }

        $entityManagerInterface->flush();

        return new JsonResponse(
            $serializer->serialize($question, 'json', ['ignored_attributes' => ['question']]),
            Response::HTTP_OK, 
            [],
            true
        );
    }

    #[Route('/question', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('question/index.html.twig', [
            'controller_name' => 'QuestionController',
        ]);
    }
}
