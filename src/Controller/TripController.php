<?php

namespace App\Controller;

use App\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Trip;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class TripController extends AbstractController
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
    #[Route('/trip', name: 'app_trip')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TripController.php',
        ]);
    }

    #[Route("/api/trips/{id}", name: 'show_trip', methods: ['GET'])]
    public function getTrip(Trip $trip, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(json_decode($serializer->serialize($trip, 'json', ['groups' => 'show_trip'])));
    }

    #[Route("/api/trips", name: 'create_trip', methods: ['POST'])]
    public function postTrip(Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $userRepository = $doctrine->getRepository(User::class);
        $companyRepository = $doctrine->getRepository(Company::class);

        $content = json_decode($request->getContent());

        $required_fields = ['announcerId', "companyId", 'type', 'available_seats', 'departure_time', "arrival_location", "departure_location"];

        if (($response = $this->check_missing_field($required_fields, $content)) !== false)
            return $response;

        $trip = $serializer->deserialize($request->getContent(), Trip::class, 'json');
        $announcer = $userRepository->find($content->announcerId);
        $company = $companyRepository->find($content->companyId);
        $trip->setAnnouncer($announcer);
        $trip->setCompany($company);
        $trip->setCreatedAt(new \DateTimeImmutable());
        $trip->setUpdatedAt(new \DateTimeImmutable());

        $em->persist($trip);
        $em->flush();

        $jsonTrip = json_decode($serializer->serialize($trip, 'json', ['groups' => 'show_trip']));

        return new JsonResponse($jsonTrip, Response::HTTP_CREATED);
    }

    #[Route("/api/trips/{id}", name: 'update_trip', methods: ['PUT'])]
    public function updateTrip(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, Trip $currentTrip): JsonResponse
    {
        $updatedTrip = $serializer->deserialize(
            $request->getContent(),
            Trip::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentTrip]
        );
        $em->persist($updatedTrip);
        $em->flush();
        $jsonTrip = json_decode($serializer->serialize($updatedTrip, 'json', ['groups' => 'show_trip']));

        return new JsonResponse($jsonTrip, Response::HTTP_CREATED);
    }

    #[Route("/api/trips/{id}", name: 'delete_trip', methods: ['DELETE'])]
    public function deleteTrip(Trip $trip, EntityManagerInterface $em)
    {
        $em->remove($trip);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
