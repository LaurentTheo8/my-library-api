<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger,
        private SerializerInterface $serializer
    ) {}


    #[Route('/me', name: 'user_me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function me(): JsonResponse
    {
        $this->logger->debug(__METHOD__, [
            'user' => $this->getUser() ? $this->getUser()->getUserIdentifier() : 'anonymous',
        ]);

        /** @var \App\Entity\User|null $user */
        $user = $this->getUser();

        if (!$user) {
            $this->logger->warning('Unauthorized access attempt to /me endpoint.');
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $json = $this->serializer->serialize($user, 'json', ['groups' => ['user:read']]);

        return new JsonResponse($json, 200, [], true);
    }
}