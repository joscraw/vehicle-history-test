<?php

namespace App\Controller\Api;

use App\Entity\Question;
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

/**
 * Class OrganizationController
 * @package App\Controller
 * @Route("/api/questions")
 */
class QuestionController extends AbstractController
{
    use ServiceHelper;

    /**
     * @Route("/", name="question_index", methods={"GET"}, options = { "expose" = true })
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the questions in the platform",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Question::class, groups={"QUESTION"}))
     *     )
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="integer",
     *     description="The page you want to return"
     * )
     *
     * @SWG\Parameter(
     *     name="sort",
     *     in="query",
     *     type="string",
     *     description="Sort by ranking. ASC/DESC"
     * )
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function index(Request $request) {

        $page = $request->query->get('page', 1);
        $sort = $request->query->get('sort', 'ASC');

        $qb = $this->questionRepository->findAllQueryBuilder()
            ->addOrderBy('question.rank', $sort);
        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage($page);
        $questions = [];
        foreach ($pagerfanta->getCurrentPageResults() as $result) {
            $questions[] = $result;
        }

        $json = $this->serializer->serialize($questions, 'json', ['groups' => ['QUESTION']]);
        $questions = json_decode($json, true);

        return new JsonResponse([
            'success' => true,
            'total' => $pagerfanta->getNbResults(),
            'count' => count($questions),
            'questions' => $questions,
        ]);
    }

    /**
     * @Route("/new", name="question_new", options = {"expose" = true }, methods={"POST"})
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
     *              @SWG\Property(property="rank", type="integer", example=5),
     *              @SWG\Property(property="question", type="string", example="This is a question")
     *          )
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="Returns newly created question",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Invalid form submission with field errors",
     *     )
     *
     *  )
     *
     *
     * @param Request $request
     * @return ApiResponse
     * @throws \Exception
     */
    public function new(Request $request) {

        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question, [
            'validation_groups' => ['CREATE']
        ]);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);
            return new ApiResponse("Error submitting form.", [
                'success' => false,
            ], $errors, 400);
        } else {
            /** @var Question $question */
            $question = $form->getData();
            $this->entityManager->persist($question);
            $this->entityManager->flush();

            $json = $this->serializer->serialize($question, 'json', ['groups' => ['QUESTION']]);
            $question = json_decode($json, true);

            return new ApiResponse(sprintf("Question successfully created!"), [
                'success' => true,
                'question' => $question
            ]);
        }
    }

    /**
     * @Route("/{id}/edit", name="question_edit", options = {"expose" = true }, methods={"PATCH"})
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
     *              @SWG\Property(property="rank", type="integer", example=5),
     *              @SWG\Property(property="question", type="string", example="This is a question")
     *          )
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="Returns updated question",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Invalid form submission with field errors",
     *     )
     *
     * )
     *
     * @param Question $question
     * @param Request $request
     * @return ApiResponse
     */
    public function edit(Question $question, Request $request) {

        $form = $this->createForm(QuestionType::class, $question, [
            'validation_groups' => ['EDIT']
        ]);
        $form->submit($request->request->all(), false);
        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);
            return new ApiResponse("Error submitting form.", [
                'success' => false,
            ], $errors, 400);
        } else {
            /** @var Question $question */
            $question = $form->getData();
            $this->entityManager->persist($question);
            $this->entityManager->flush();

            $json = $this->serializer->serialize($question, 'json', ['groups' => ['QUESTION']]);
            $question = json_decode($json, true);

            return new ApiResponse(sprintf("Question successfully updated!"), [
                'success' => true,
                'question' => $question
            ]);
        }
    }

    /**
     * @Route("/{id}/delete", name="question_delete", options = {"expose" = true }, methods={"DELETE"})
     *
     * @SWG\Delete(
     *     produces={"application/json"},
     *
     *     @SWG\Response(
     *         response=200,
     *         description="Returned on successful deletion of question",
     *     )
     *
     * )
     *
     * @param Question $question
     * @param Request $request
     * @return ApiResponse
     */
    public function delete(Question $question, Request $request) {

        $this->entityManager->remove($question);
        $this->entityManager->flush();

        return new ApiResponse(sprintf("Question successfully deleted!"), [
            'success' => true,
        ]);
    }
}