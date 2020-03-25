<?php

namespace App\Form\EventType;

use App\Form\DataTransformer\TagsTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EventChoiceType extends AbstractType
{
    /**
     * @var TagsTransformer
     */
    private $transformer;

    /**
     * EventChoiceType constructor.
     * @param TagsTransformer $transformer
     */
    public function __construct(TagsTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}