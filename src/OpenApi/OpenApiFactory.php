<?php
namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\Schema;
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

            $schema = new Schema();

            $schema['type'] = 'object';
            $schema['properties'] = new ArrayObject([
                'username' => ['type' => 'string'],
                'password' => ['type' => 'string'],
            ]);
            $schema['required'] = ['username', 'password'];

            $requestBody = new RequestBody(
                description: "Data to create admin user",
                content: new ArrayObject([
                    'application/json' => new ArrayObject([
                        'schema' => $schema,
                    ]),
                ])
            );

            $newOperation = $operation
                ->withSummary('Create an admin user (cheat endpoint)')
                ->withDescription('Special route to create an administrator account to initialize the project.')
                ->withRequestBody($requestBody);

            $paths->addPath('/api/create-admin', $pathItem->withPost($newOperation));
        }

        return $openApi;
    }
}