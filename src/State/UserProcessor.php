<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserProcessor implements ProcessorInterface
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasherInterface,
    private EntityManagerInterface $entityManagerInterface,
    #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
    private ProcessorInterface $processorInterface)
    {

    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof User) {
            return $this->processorInterface->process($data, $operation, $uriVariables, $context);
        }

        if ($operation->getName() === 'user_register') {
            $data->setRoles(['ROLE_USER']);
        }

        if ($operation->getName() === 'user_create_admin') {
            $data->setRoles(['ROLE_ADMIN']);
        }

        $hashedPassword = $this->userPasswordHasherInterface->hashPassword($data, $data->getPassword());
        $data->setPassword($hashedPassword);

        return $this->processorInterface->process($data, $operation, $uriVariables, $context);
    }

}
