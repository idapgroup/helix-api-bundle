<?php

namespace IdapGroup\HelixApiBundle\EventListener;

use IdapGroup\HelixApiBundle\Bundle\ApiResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    /**
     * @param LoggerInterface $errorLogger
     */
    public function __construct(public LoggerInterface $errorLogger)
    {
    }

    /**
     * @param ExceptionEvent $event
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $event->setResponse(new ApiResponse(
            $exception->data ?? [],
            false,
            $exception->getMessage(),
            $exception->getCode() <= 100 ? Response::HTTP_BAD_REQUEST : $exception->getCode()
        ));

        $this->errorLogger->error($exception->getMessage() ?? 'Error', $exception->data ?? []);
    }
}