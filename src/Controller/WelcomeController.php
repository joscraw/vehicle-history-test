<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

/**
 * Class OrganizationController
 * @package App\Controller
 */
class WelcomeController extends AbstractController
{
    /**
     * @Route("/", name="welcome", methods={"GET"}, options = { "expose" = true })
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function welcome(Request $request) {

        return new Response("Welcome page");

    }
}