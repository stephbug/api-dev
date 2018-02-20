<?php

return [
    'messages'=> [

        'command' => [

        ],

        'query' => [

        ]
    ],

    'services' => [
        \StephBug\ApiDev\MetadataGatherer::class => \StephBug\ApiDev\NoOperationMetadata::class,
        \Prooph\Common\Messaging\MessageConverter::class => \Prooph\Common\Messaging\NoOpMessageConverter::class,
        \StephBug\ApiDev\Response\ResponseStrategy::class => \StephBug\ApiDev\Response\JsonResponse::class,
        \Prooph\Common\Messaging\MessageFactory::class => \Prooph\Common\Messaging\FQCNMessageFactory::class,
        \StephBug\ApiDev\Response\ApiResponse::class => \StephBug\ApiDev\Response\ApiRespondToException::class
    ]
];