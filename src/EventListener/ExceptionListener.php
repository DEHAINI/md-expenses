<?php
// I added this listener at 28 October 2021
// I created this event listener in order to catch and handle the errors exceptions related to the call of our API (example: errors in the typing of the routes)
// and I cutomised the returned message to be compatible with the returns given by our API

// src/EventListener/ExceptionListener.php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();
        $message = sprintf(
            'My Error says: %s with code: %s',
            $exception->getMessage(),
            $exception->getCode()
        );

        // Customize your response object to display the exception details
        $response = new Response();
        $response->setContent($message);

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // here i will customize the error message
        $errors=array($response);
        $json_return=json_encode(['message'=>"The given route does not correspond to any service in our system, Please refer to README.txt file in order to see how to request our API",'errors'=>$errors]);
        $response->setContent($json_return);

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}

?>