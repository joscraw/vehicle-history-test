<?php

namespace App\Controller\Api;

use App\Entity\Answer;
use App\Entity\Question;
use App\Form\AnswerType;
use App\Form\QuestionType;
use App\Http\ApiResponse;
use App\Util\ServiceHelper;
use Nelmio\ApiDocBundle\Annotation\Model;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * Class AnswerController
 * @package App\Controller
 * @Route("/api/answers")
 */
class AnswerController extends AbstractController
{
    use ServiceHelper;

    /**
     * @Route("/", name="answer_index", methods={"GET"}, options = { "expose" = true })
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the answers in the platform",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Answer::class, groups={"ANSWER"}))
     *     )
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="integer",
     *     description="The page you want to return"
     * )
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function index(Request $request) {

        $page = $request->query->get('page', 1);

        $qb = $this->answerRepository->findAllQueryBuilder();

        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage($page);
        $answers = [];
        foreach ($pagerfanta->getCurrentPageResults() as $result) {
            $answers[] = $result;
        }

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'tags' => function ($tags) {
                    return explode(", ", $tags);
                },
                'question' => function (Question $question = null) {
                    return $question->getId();
                }
            ],
            'groups' => ['ANSWER'],
        ];

        $json = $this->serializer->serialize($answers, 'json', $defaultContext);
        $answers = json_decode($json, true);

        return new JsonResponse([
            'success' => true,
            'total' => $pagerfanta->getNbResults(),
            'count' => count($answers),
            'answers' => $answers,
        ]);
    }

    /**
     * @Route("/new", name="answer_new", options = {"expose" = true }, methods={"POST"})
     *
     * @SWG\Post(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         description="JSON payload",
     *         format="application/json",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="question", type="integer", example=1),
     *              @SWG\Property(property="answer", type="string", example="This is an answer"),
     *              @SWG\Property(property="tags", type="array", @SWG\Items(type="string", example="This is a tag")))
     *          )
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="Returns newly created answer",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Invalid form submission with field errors",
     *     )
     *
     * )
     * @param Request $request
     * @return ApiResponse
     * @throws \Exception
     */
    public function new(Request $request) {

        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer, [
            'validation_groups' => ['CREATE']
        ]);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);
            return new ApiResponse("Error submitting form.", [
                'success' => false,
            ], $errors, 400);
        } else {
            /** @var Answer $answer */
            $answer = $form->getData();
            $this->entityManager->persist($answer);
            $this->entityManager->flush();

            $defaultContext = [
                AbstractNormalizer::CALLBACKS => [
                    'tags' => function ($tags) {
                        return explode(", ", $tags);
                    },
                    'question' => function (Question $question = null) {
                        return $question->getId();
                    }
                ],
                'groups' => ['ANSWER'],
            ];

            $json = $this->serializer->serialize($answer, 'json', $defaultContext);
            $answer = json_decode($json, true);

            return new ApiResponse(sprintf("Answer successfully created!"), [
                'success' => true,
                'answer' => $answer
            ]);
        }
    }

    /**
     * @Route("/{id}/edit", name="answer_edit", options = {"expose" = true }, methods={"PATCH"})
     *
     * @SWG\Patch(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         description="JSON payload",
     *         format="application/json",
     *         @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="question", type="integer", example=1),
     *              @SWG\Property(property="answer", type="string", example="This is an answer"),
     *              @SWG\Property(property="tags", type="array", @SWG\Items(type="string", example="This is a tag")))
     *          )
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="Returns updated answer",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Invalid form submission with field errors",
     *     )
     *
     * )
     *
     * @param Answer $answer
     * @param Request $request
     * @return ApiResponse
     */
    public function edit(Answer $answer, Request $request) {

        $form = $this->createForm(AnswerType::class, $answer, [
            'validation_groups' => ['EDIT']
        ]);
        $form->submit($request->request->all(), false);
        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);
            return new ApiResponse("Error submitting form.", [
                'success' => false,
            ], $errors, 400);
        } else {
            /** @var Answer $answer */
            $answer = $form->getData();
            $this->entityManager->persist($answer);
            $this->entityManager->flush();

            $defaultContext = [
                AbstractNormalizer::CALLBACKS => [
                    'tags' => function ($tags) {
                        return explode(", ", $tags);
                    },
                    'question' => function (Question $question = null) {
                        return $question->getId();
                    }
                ],
                'groups' => ['ANSWER'],
            ];

            $json = $this->serializer->serialize($answer, 'json', $defaultContext);
            $answer = json_decode($json, true);

            return new ApiResponse(sprintf("Answer successfully updated!"), [
                'success' => true,
                'answer' => $answer
            ]);
        }
    }

    /**
     * @Route("/{id}/delete", name="answer_delete", options = {"expose" = true }, methods={"DELETE"})
     *
     * @SWG\Delete(
     *     produces={"application/json"},
     *
     *     @SWG\Response(
     *         response=200,
     *         description="Returned on successful deletion of answer",
     *     )
     *
     * )
     *
     * @param Answer $answer
     * @param Request $request
     * @return ApiResponse
     */
    public function delete(Answer $answer, Request $request) {

        $this->entityManager->remove($answer);
        $this->entityManager->flush();

        return new ApiResponse(sprintf("Answer successfully deleted!"), [
            'success' => true,
        ]);
    }
}