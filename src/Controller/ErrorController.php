<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends AbstractController
{
    /**
     * @param FlattenException $exception
     * @return Response
     */
    public function __invoke(FlattenException $exception): Response
    {
        $statusCode = $exception->getStatusCode();

        if ($statusCode === 404) {
            return $this->render('error/404.html.twig', [
                'status_code' => $statusCode,
            ]);
        }

        if ($statusCode === 403) {
            return $this->render('error/403.html.twig', [
                'status_code' => $statusCode,
            ]);
        }

        return $this->render('error/error.html.twig', [
            'status_code' => $statusCode,
        ]);
    }
}
