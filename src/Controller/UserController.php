<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{

    private function make_response($code, $message, $data, $http_code) {
        return new JsonResponse([
            'code'    => $code,
            'message' => $message,
            'content' => $data
        ], $http_code);
    }

    private function check_missing_field($fields, $content) {

        foreach($fields as $field) {
            if(!isset($content->$field)) {
                return $this->make_response("MISSING_" . strtoupper($field), "The '".$field."' field is required", [], 400);
            }
        }

        return false;
    }

    /**
     * @Route("/api/user/me", name="user_me", methods={"GET"})
     */
    public function me(Request $request, ManagerRegistry $doctrine, Security $security): JsonResponse
    {
        $user = $security->getUser();

        $repository = $doctrine->getRepository(User::class);
        $user = $repository->findOneBy(['email' => $user->getUserIdentifier()]);

        return new JsonResponse($user->getAll());
    }

    /**
     * @Route("/api/register", name="register")
     */
    public function register(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository(User::class);
        $user = new User();

        $content = json_decode($request->getContent());

        $required_fields = ['email', 'password', 'firstname', 'lastname'];

        if(($response = $this->check_missing_field($required_fields, $content)) !== false)
            return $response;

        $email = $content->email;
        $firstname = $content->firstname;
        $lastname = $content->lastname;
        $exist = $repository->findOneBy(['email' => $email]);

        if (isset($exist))
            return $this->make_response("USER_ALREADY_EXIST", "The email address is already in use by another user", ['email' => $email], 409);

        $password = $content->password;
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );

        $user->setEmail($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->make_response("USER_REGISTER_SUCCESSFUL", "Registration successful", $user->getAll(), 201);
    }

    /**
     * @Route("/api/users", name="list_user", methods={"GET"})
     */
    public function listUser(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        // $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository(User::class);

        $list_user = $repository->findAll();
        $data = [];
        foreach ($list_user as $key => $user) {
            $tmp_obj = $user->getAll();
            $data[] = $tmp_obj;
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/api/users/{id}", name="user", methods={"GET"})
     */
    public function user(int $id, Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $repository = $doctrine->getRepository(User::class);
        $user = $repository->find($id);

        if (!isset($user))
            return $this->make_response("USER_NOT_FOUND", "The user was not found", [], 404);

        return new JsonResponse($user->getAll());
    }

    /**
     * @Route("/api/users", name="update_user", methods={"PUT"})
     */
    public function userUpdate(Request $request, ManagerRegistry $doctrine, Security $security, JWTTokenManagerInterface $JWTManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $repository = $doctrine->getRepository(User::class);
        $entityManager = $doctrine->getManager();
        $user = $security->getUser();
        $user = $repository->findOneBy(['email' => $user->getUserIdentifier()]);
        $content = json_decode($request->getContent());

        $required_fields = ['email', 'password', 'firstname', 'lastname'];

        if(($response = $this->check_missing_field($required_fields, $content)) !== false)
            return $response;

        if($user->getEmail() !== $content->email) {

            $email_exist = $repository->findOneBy(['email' => $content->email]);

            if($email_exist)
                return new JsonResponse([
                    "code" => "EMAIL_ALREADY_EXIST",
                    "error" => "The email already exists"
                ], 409);

            $user->setEmail($content->email);
        }

        if($content->password !== "") {
            if(strlen($content->password) < 4 || strlen($content->password) > 30)
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