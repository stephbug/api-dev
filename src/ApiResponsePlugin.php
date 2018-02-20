<?php

declare(strict_types=1);

namespace StephBug\ApiDev;

use Prooph\Common\Event\ActionEvent;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\AbstractPlugin;
use StephBug\ApiDev\Response\ApiResponse;
use StephBug\ServiceBus\Bus\CommandBus;

class ApiResponsePlugin extends AbstractPlugin
{
    /**
     * @var ApiResponse
     */
    private $apiResponse;

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function attachToMessageBus(MessageBus $messageBus): void
    {
        $this->listenerHandlers[] = $messageBus->attach(
            MessageBus::EVENT_FINALIZE,
            function (ActionEvent $actionEvent): void {

                $e = $actionEvent->getParam(MessageBus::EVENT_PARAM_EXCEPTION);
                $bus = $actionEvent->getTarget();

                if ($e instanceof \Throwable && $bus instanceof CommandBus) {
                    $actionEvent->setParam(MessageBus::EVENT_PARAM_EXCEPTION, null);

                    $actionEvent->setParam(MessageBus::EVENT_PARAM_MESSAGE_HANDLED,
                        $this->apiResponse->respondTo($e)
                    );
                }

                // todo query w/promise_rejected

            }, MessageBus::PRIORITY_INITIALIZE
        );
    }
}