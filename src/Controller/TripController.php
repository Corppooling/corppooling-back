<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Trip;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class TripController extends AbstractController
{
    #[Route('/trip', name: 'app_trip')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TripController.php',
        ]);
    }

    /**
     * @Route("/api/trips", name="list_trips", methods={"GET"})
     */
    public function listTrips(Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer): JsonResponse
    {
        $repository = $doctrine->getRepository(Trip::class);
        $json = [];
        $list_trips = $repository->findBy(
            [],
            ['departure_time' => 'DESC'],
            $request->query->get('limit') ?? 15,
            0
        );
        foreach ($list_trips as $key => $trip) {
            $json[] = json_decode($serializer->serialize($trip, 'json', ['groups' => 'list_trip']));
        }

        return new JsonResponse($json);
    }
}
