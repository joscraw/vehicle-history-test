<?php

namespace App\Util;

use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

trait ServiceHelper
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Packages
     */
    private $assetsManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ValidatorInterface $validator
     */
    private $validator;

    /**
     * @var GuardAuthenticatorHandler $guardHandler,
     */
    private $guardHandler;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var TokenStorageInterface
     */
    private $securityToken;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var QuestionRepository
     */
    private $questionRepository;

    /**
     * @var AnswerRepository
     */
    private $answerRepository;

    /**
     * ServiceHelper constructor.
     * @param EntityManagerInterface $entityManager
     * @param Packages $assetsManager
     * @param RouterInterface $router
     * @param ValidatorInterface $validator
     * @param GuardAuthenticatorHandler $guardHandler
     * @param Environment $twig
     * @param TokenStorageInterface $securityToken
     * @param SerializerInterface $serializer
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param QuestionRepository $questionRepository
     * @param AnswerRepository $answerRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        Packages $assetsManager,
        RouterInterface $router,
        ValidatorInterface $validator,
        GuardAuthenticatorHandler $guardHandler,
        Environment $twig,
        TokenStorageInterface $securityToken,
        SerializerInterface $serializer,
        UserPasswordEncoderInterface $passwordEncoder,
        QuestionRepository $questionRepository,
        AnswerRepository $answerRepository
    ) {
        $this->entityManager = $entityManager;
        $this->assetsManager = $assetsManager;
        $this->router = $router;
        $this->validator = $validator;
        $this->guardHandler = $guardHandler;
        $this->twig = $twig;
        $this->securityToken = $securityToken;
        $this->serializer = $serializer;
        $this->passwordEncoder = $passwordEncoder;
        $this->questionRepository = $questionRepository;
        $this->answerRepository = $answerRepository;
    }

    /**
     * Returns the site url
     * @return string
     */
    public function getFullQualifiedBaseUrl() {
        $routerContext = $this->router->getContext();
        $port = $routerContext->getHttpPort();
        return sprintf('%s://%s%s%s',
            $routerContext->getScheme(),
            $routerContext->getHost(),
            ($port !== 80 ? ':'. $port : ''),
            $routerContext->getBaseUrl()
        );
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }
}
