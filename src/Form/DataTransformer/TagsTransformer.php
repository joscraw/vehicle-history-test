<?php

namespace App\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TagsTransformer implements DataTransformerInterface
{

    /**
     * Transforms an object (record) to a string (number).
     *
     * @param $tagsAsString
     * @return array
     */
    public function transform($tagsAsString)
    {
        if(!$tagsAsString) {
            return;
        }

        return explode(', ', $tagsAsString);
    }

    /**
     * Transforms an id (record) to an object (issue).
     * @param $tagsAsArray
     * @return array|ArrayCollection
     */
    public function reverseTransform($tagsAsArray)
    {
        return implode(', ', $tagsAsArray);
    }
}