<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('question', TextareaType::class, [])
            ->add('rank', NumberType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $csrfProtection = true;
        $request = $this->requestStack->getCurrentRequest();
        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        if ($acceptHeader->has('application/json')) {
            $csrfProtection = false;
        }

        $resolver->setDefaults([
            'data_class' => Question::class,
            'csrf_protection' => $csrfProtection,
            'allow_extra_fields' => true
        ]);
    }
}
