<?php
namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;

use ArrayObject;
use Psr\Log\LoggerInterface;

class OpenApiFactory implements OpenApiFactoryInterface
{

    public function __construct(private OpenApiFactoryInterface $decorated, private LoggerInterface $loggerInterface)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $paths = $openApi->getPaths();
        $pathItem = $paths->getPath('/api/create-admin');

        if ($pathItem) {
            $operation = $pathItem->getPost();

            $newOperation = $operation
                ->withSummary('Create an admin user (cheat endpoint)')
                ->withDescription('Special route to create an administrator account to initialize the project.');

            $paths->addPath('/api/create-admin', $pathItem->withPost($newOperation));


        }

        return $openApi;
    }
}