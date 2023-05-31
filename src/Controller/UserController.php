<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Entity\Company;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use \Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{

    private function make_response($code, $message, $data, $http_code)
    {
        return new JsonResponse([
            'code'    => $code,
            'message' => $message,
            'content' => $data
        ], $http_code);
    }

    private function check_missing_field($fields, $content)
    {

        foreach ($fields as $field) {
            if (!isset($content->$field)) {
                return $this->make_response("MISSING_" . strtoupper($field), "The '" . $field . "' field is required", [], 400);
            }
        }

        return false;
    }

    #[Route("/api/user/me", name: 'user_me', methods: ['GET'])]
    public function me(UserRepository $userRepository, Security $security, SerializerInterface $serializer): JsonResponse
    {
        $user = $security->getUser();

        $user = $userRepository->findOneBy(['email' => $user->getUserIdentifier()]);
        $usr = $userRepository->findOneByIdJoinedToCategory($user->getId());
        $jsonTrip = json_decode($serializer->serialize($usr, 'json', []));
        return new JsonResponse($jsonTrip);
    }
    #[Route("/api/reservations", name: 'user_add_reservation', methods: ['POST'])]
    public function postReservation(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UserRepository $userRepository, TripRepository $tripRepository)
    {
        $parameters = json_decode($request->getContent(), true);
        $user = $userRepository->findOneBy(['id' => $parameters['userId']]);
        $trip = $tripRepository->findOneBy(['id' => $parameters['tripId']]);
        $user->addReservation($trip);
        $em->persist($user);
        $em->flush();
        return new JsonResponse(json_decode($serializer->serialize($user, 'json', [])));
    }

    #[Route("/api/reservations", name: 'user_remove_trip', methods: ['DELETE'])]
    public function deleteReservation(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UserRepository $userRepository, TripRepository $tripRepository)
    {
        $parameters = json_decode($request->getContent(), true);
        $user = $userRepository->findOneBy(['id' => $parameters['userId']]);
        $trip = $tripRepository->findOneBy(['id' => $parameters['tripId']]);
        $user->removeReservation($trip);
        $em->persist($user);
        $em->flush();
        return new JsonResponse(json_decode($serializer->serialize($user, 'json', [])));
    }

    #[Route("/api/register", name: 'register', methods: ['GET'])]
    public function register(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository(User::class);
        $companyRepository = $doctrine->getRepository(Company::class);
        $user = new User();

        $content = json_decode($request->getContent());

        $required_fields = ['email', 'password', 'firstname', 'lastname'];

        if (($response = $this->check_missing_field($required_fields, $content)) !== false)
            return $response;

        $email = $content->email;
        $firstname = $content->firstname;
        $companyId = $content->companyId;
        $lastname = $content->lastname;
        $exist = $repository->findOneBy(['email' => $email]);

        if (isset($exist))
            return $this->make_response("USER_ALREADY_EXIST", "The email address is already in use by another user", ['email' => $email], 409);

        $password = $content->password;
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );

        $company = $companyRepository->find($companyId);

        $user->setEmail($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setPassword($hashedPassword);
        $user->setCompany($company);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->make_response("USER_REGISTER_SUCCESSFUL", "Registration successful", $user->getAll(), 201);
    }

    #[Route("/api/users", name: 'update_user', methods: ['PUT'])]
    public function userUpdate(Request $request, ManagerRegistry $doctrine, Security $security, JWTTokenManagerInterface $JWTManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $repository = $doctrine->getRepository(User::class);
        $entityManager = $doctrine->getManager();
        $user = $security->getUser();
        $user = $repository->findOneBy(['email' => $user->getUserIdentifier()]);
        $content = json_decode($request->getContent());

        $required_fields = ['email', 'password', 'firstname', 'lastname'];

        if (($response = $this->check_missing_field($required_fields, $content)) !== false)
            return $response;

        if ($user->getEmail() !== $content->email) {

            $email_exist = $repository->findOneBy(['email' => $content->email]);

            if ($email_exist)
                return new JsonResponse([
                    "code" => "EMAIL_ALREADY_EXIST",
                    "error" => "The email already exists"
                ], 409);

            $user->setEmail($content->email);
        }

        if ($content->password !== "") {
            if (strlen($content->password) < 4 || strlen($content->password) > 30)
                return $this->make_response("PASSWORD_WRONG_SIZE", "The password must contain at least 4 and at most 30 characters", [], 403);

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $content->password
            );
            $user->setPassword($hashedPassword);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        $jwt = $JWTManager->create($user);

        $response = $user->getAll();
        $response->jwt = $jwt;

        return new JsonResponse($response, 200);
    }
}
