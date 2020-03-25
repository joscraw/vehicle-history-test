<?php

namespace App\Form;

use App\Entity\Answer;
use App\Entity\Question;
use App\Form\EventType\EventChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnswerType extends AbstractType
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('answer', TextareaType::class, [])
            ->add('tags', EventChoiceType::class, [
                'multiple' => true
            ])->add('question', EntityType::class, [
                'class' => Question::class,
            ]);

        /**
         * This is needed because symfony wants the choice fields pre-populated with the expected data
         * Seeing as how this is used from an api and we are passing up the tags at runtime
         * we don't know this! So lets add them dynamically prior to form being submitted
         */
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if(isset($data['tags']) && is_array($data['tags'])) {
                $choices = array_combine($data['tags'], $data['tags']);
                $form->remove('tags');
                $form->add('tags', EventChoiceType::class, array(
                    'choices' => $choices,
                    'multiple' => true
                ));
            }
        });
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
            'data_class' => Answer::class,
            'csrf_protection' => $csrfProtection,
            'allow_extra_fields' => true
        ]);
    }
}
