<?php

declare(strict_types=1);

namespace StephBug\ApiDev\Middleware;

use Illuminate\Http\Request;
use Prooph\Common\Messaging\Message;
use Prooph\Common\Messaging\MessageFactory;
use StephBug\ApiDev\Exception\MissingMessageNameAttribute;
use StephBug\ApiDev\MetadataGatherer;
use StephBug\ApiDev\Response\ApiResponse;
use StephBug\ApiDev\Response\ResponseStrategy;
use StephBug\ServiceBus\Bus\CommandBus;
use Symfony\Component\HttpFoundation\Response;

class CommandMessage
{
    const NAME_ATTRIBUTE = 'command_api';

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var MetadataGatherer
     */
    private $metadataGatherer;

    /**
     * @var ResponseStrategy
     */
    private $responseStrategy;

    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var ApiResponse
     */
    private $apiResponse;

    public function __construct(MessageFactory $messageFactory,
                                MetadataGatherer $metadataGatherer,
                                ResponseStrategy $responseStrategy,
                                CommandBus $commandBus,
                                ApiResponse $apiResponse)
    {
        $this->messageFactory = $messageFactory;
        $this->metadataGatherer = $metadataGatherer;
        $this->responseStrategy = $responseStrategy;
        $this->commandBus = $commandBus;
        $this->apiResponse = $apiResponse;
    }

    public function handle(Request $request): Response
    {
        try {
            $commandName = $this->requireCommandNameFromRequest($request);

            $message = $this->createMessage($commandName, $request);

            $this->commandBus->dispatch($message);

            return $this->responseStrategy->withStatus(Response::HTTP_ACCEPTED);
        } catch (\Throwable $exception) {
            return $this->apiResponse->respondTo($exception);
        }
    }

    protected function requireCommandNameFromRequest(Request $request): string
    {
        if (null !== $commandName = $request->get(self::NAME_ATTRIBUTE)) {
            return $commandName;
        }

        throw MissingMessageNameAttribute::withKeys(self::NAME_ATTRIBUTE, 'command');
    }

    protected function createMessage(string $commandName, Request $request): Message
    {
        return $this->messageFactory->createMessageFromArray(
            $commandName, [
                'payload' => $request->json(),
                'metadata' => $this->metadataGatherer->fromRequest($request)
            ]
        );
    }
}