<?php

declare(strict_types=1);

namespace StephBug\ApiDev\Middleware;

use Illuminate\Http\Request;
use Prooph\Common\Messaging\MessageFactory;
use StephBug\ApiDev\Exception\RuntimeException;
use StephBug\ApiDev\Exception\MissingMessageNameAttribute;
use StephBug\ApiDev\MetadataGatherer;
use StephBug\ApiDev\Response\ResponseStrategy;
use StephBug\ServiceBus\Bus\QueryBus;
use Symfony\Component\HttpFoundation\Response;

class QueryMessage
{
    const NAME_ATTRIBUTE = 'query_api';

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
     * @var QueryBus
     */
    private $queryBus;

    public function __construct(MessageFactory $messageFactory,
                                MetadataGatherer $metadataGatherer,
                                ResponseStrategy $responseStrategy,
                                QueryBus $queryBus)
    {
        $this->messageFactory = $messageFactory;
        $this->metadataGatherer = $metadataGatherer;
        $this->responseStrategy = $responseStrategy;
        $this->queryBus = $queryBus;
    }

    public function handle(Request $request): Response
    {
        $queryName = $this->requireQueryName($request);

        $payload = (array)$request->query();

        if ($request->isMethod('POST')) {
            //$payload['data'] = (array) json_decode($request->getContent(), true);
            $payload += (array) json_decode($request->getContent(), true);
        }

        try {
            $query = $this->messageFactory->createMessageFromArray($queryName, [
                'payload' => $payload,
                'metadata' => $this->metadataGatherer->fromRequest($request)
            ]);

            return $this->responseStrategy->fromPromise($this->queryBus->dispatch($query));
        } catch (\Throwable $exception) {
            throw new RuntimeException(
                sprintf('An error occurred during dispatching of query "%s"', $queryName),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $exception
            );
        }
    }

    private function requireQueryName(Request $request): string
    {
        if (null !== $queryName = $request->get(self::NAME_ATTRIBUTE)) {
            return $queryName;
        }

        throw MissingMessageNameAttribute::withKeys(self::NAME_ATTRIBUTE, 'query');
    }
}