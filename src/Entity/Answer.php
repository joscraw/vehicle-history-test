<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnswerRepository")
 */
class Answer
{
    /**
     * @Groups({"ANSWER"})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"ANSWER"})
     * @Assert\NotBlank(message="Don't forget an answer!", groups={"CREATE", "EDIT"})
     * @ORM\Column(type="text")
     */
    private $answer;

    /**
     * @Groups({"ANSWER"})
     * @SWG\Property(type="array", @SWG\Items(type="string"))
     * @Assert\NotBlank(message="Don't forget at least one tag!", groups={"CREATE", "EDIT"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $tags;

    /**
     * @SWG\Property(type="integer")
     * @Groups({"ANSWER"})
     * @Assert\NotNull(message="Don't forget to pass up a question id!", groups={"CREATE", "EDIT"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Question", inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $question;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }
}
