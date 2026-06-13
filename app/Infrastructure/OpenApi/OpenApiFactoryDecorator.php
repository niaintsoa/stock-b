<?php

namespace App\Infrastructure\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\Response;
use ArrayObject;

class OpenApiFactoryDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $paths = $openApi->getPaths();

        // 1. Ajouter /api/login
        $loginPathItem = new PathItem(
            ref: 'Login',
            post: new Operation(
                operationId: 'postLoginItem',
                tags: ['Auth'],
                responses: [
                    '200' => new Response(
                        description: 'Get Sanctum API token',
                        content: new ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'token' => ['type' => 'string', 'readOnly' => true],
                                        'message' => ['type' => 'string'],
                                    ],
                                ],
                            ],
                        ])
                    ),
                ],
                summary: 'Get JWT / Sanctum API token to login.',
                requestBody: new RequestBody(
                    description: 'Generate new JWT / Sanctum Token',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'email' => ['type' => 'string', 'example' => 'admin@example.com'],
                                    'password' => ['type' => 'string', 'example' => 'password'],
                                ],
                            ],
                        ],
                    ])
                )
            )
        );
        $paths->addPath('/api/login', $loginPathItem);

        // 2. Ajouter /api/logout
        $logoutPathItem = new PathItem(
            ref: 'Logout',
            post: new Operation(
                operationId: 'postLogoutItem',
                tags: ['Auth'],
                responses: [
                    '200' => new Response(
                        description: 'Token revoked successfully',
                        content: new ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string'],
                                    ],
                                ],
                            ],
                        ])
                    ),
                ],
                summary: 'Logout and revoke current API token.',
                security: [['Bearer Token' => []]]
            )
        );
        $paths->addPath('/api/logout', $logoutPathItem);

        // 3. Ajouter /api/me
        $mePathItem = new PathItem(
            ref: 'Me',
            get: new Operation(
                operationId: 'getMeItem',
                tags: ['Auth'],
                responses: [
                    '200' => new Response(
                        description: 'Get current authenticated user',
                        content: new ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'integer'],
                                        'name' => ['type' => 'string'],
                                        'email' => ['type' => 'string'],
                                        'profile_type' => ['type' => 'string'],
                                    ],
                                ],
                            ],
                        ])
                    ),
                ],
                summary: 'Get current authenticated user details.',
                security: [['Bearer Token' => []]]
            )
        );
        $paths->addPath('/api/me', $mePathItem);

        return $openApi;
    }
}
