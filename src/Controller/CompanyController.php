<?php

namespace App\Controller;

use App\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CompanyController extends AbstractController
{
    #[Route("/api/companybyauthcode/{auth_code}", name: 'company_by_code', methods: ['GET'])]
    public function getCompanyByAuthCode(string $auth_code, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $company = $em->getRepository(Company::class)->findOneBy(['auth_code' => $auth_code]);
    
        if (!$company) {
            return new JsonResponse(['code'    => "NOT_AUTHORIZED", 'message' => 'You are not authorized to obtain company information', 'content' => []], JsonResponse::HTTP_NOT_FOUND);
        }
    
        $companyName = $company->getName();
        $companyLogo = $company->getLogo();
    
        $jsonResponse = $serializer->serialize(['name' => $companyName, 'logo' => $companyLogo], 'json');
    
        return new JsonResponse($jsonResponse, JsonResponse::HTTP_OK, [], true);
    }
}
