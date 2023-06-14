<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    #[Route('/users', methods: ['GET'])]
    public function getUsers(SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, Request $request)
    {

        $repository = $entityManagerInterface->getRepository(User::class);

        $firstname = $request->query->get('firstname');
        if (null !== $firstname) {
            $users = $repository->findBy(['firstname' => $firstname]);
        } else {
            $users = $repository->findAll();
        }
        

        $users = $serializer->serialize($users, 'json');

        return new JsonResponse(
            $serializer->serialize($users, 'json'),
            200, 
            [],
            true
        );
    }

    #[Route('/users/{id}', methods: ['GET'])]
    public function getOneUser(int $id, EntityManagerInterface $entityManagerInterface, SerializerInterface $serializer) {
        $repository = $entityManagerInterface->getRepository(User::class);
        $user = $repository->find($id);

        if($user === null) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(
            $serializer->serialize($user, 'json'),
            200, 
            [],
            true
        );
    }


    #[Route('/users', methods: ['POST'])]
    public function postUsers(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface) {

        $payload = json_decode($request->getContent(), true);

        $user = (new User())
            ->setFirstname($payload['firstname'])
            ->setLastname($payload['lastname'])
            ->setRole($payload['role'])
        ;

        $address = (new Address())
            ->setStreet($payload['address']['street'])
            ->setCity($payload['address']['city'])
            ->setCountry($payload['address']['country']);


        $user->setAddress($address);

        $entityManagerInterface->persist($user);
        $entityManagerInterface->flush();

        return new JsonResponse(
            $serializer->serialize($user, 'json'),
            Response::HTTP_CREATED, 
            [],
            true
        );
    }

    #[Route('/users/{id}', methods: ['DELETE'])]
    public function deleteUser(int $id, EntityManagerInterface $entityManagerInterface) {
        
        $repository = $entityManagerInterface->getRepository(User::class);
        $user = $repository->find($id);

        // REST
        // if(null === $user) {
        //     return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        // }

        // $entityManagerInterface->remove($user);
        // $entityManagerInterface->flush();


        // PROTOCOLE HTTP
        if(null !== $user) {
            $entityManagerInterface->remove($user);
            $entityManagerInterface->flush();
        }

        return new JsonResponse(
            null,
            Response::HTTP_NO_CONTENT, 
        );
    }

    #[Route('/users/{id}', methods: ['PUT'])]
    public function putUser(int $id, EntityManagerInterface $entityManagerInterface, SerializerInterface $serializer, Request $request) {

        $payload = json_decode($request->getContent(), true);
        $repository = $entityManagerInterface->getRepository(User::class);
        $user = $repository->find($id);

        if($user === null) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        if(!isset($payload['firstname'])) {
            return new JsonResponse(['error' => 'The value firstname should not be blank'], Response::HTTP_BAD_REQUEST);
        }
        if(!isset($payload['lastname'])) {
            return new JsonResponse(['error' => 'The value lastname should not be blank'], Response::HTTP_BAD_REQUEST);
        }
        if(!isset($payload['role'])) {
            return new JsonResponse(['error' => 'The value role should not be blank'], Response::HTTP_BAD_REQUEST);
        }
        if(!isset($payload['address'])) {
            return new JsonResponse(['error' => 'The value role address not be blank'], Response::HTTP_BAD_REQUEST);
        }

        $user->setFirstname($payload['firstname'])
            ->setLastname($payload['lastname'])
            ->setRole($payload['role'])
        ;

        $user->getAddress()->setStreet($payload['address']['street'])
        ->setCity($payload['address']['city'])
        ->setCountry($payload['address']['country']);

        $entityManagerInterface->flush();

        return new JsonResponse(
            $serializer->serialize($user, 'json'),
            Response::HTTP_OK, 
            [],
            true
        );
    }

    #[Route('/users/{id}', methods: ['PATCH'])]
    public function patchUser(int $id, EntityManagerInterface $entityManagerInterface, SerializerInterface $serializer, Request $request) {

        $payload = json_decode($request->getContent(), true);
        $repository = $entityManagerInterface->getRepository(User::class);
        $user = $repository->find($id);

        if($user === null) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        foreach($payload as $key => $value) {
            $method = 'set'.ucfirst($key);
            if(is_callable([$user, $method])){
                $user->$method($value);
            }
            else {
                return new JsonResponse(['error' => 'Attribute ' .$key. 'doesnt\'t exist. '], Response::HTTP_BAD_REQUEST);
            }
        }

        $entityManagerInterface->flush();

        return new JsonResponse(
            $serializer->serialize($user, 'json'),
            Response::HTTP_OK, 
            [],
            true
        );
    }

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
