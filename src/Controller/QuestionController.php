<?php

namespace App\Controller;

use App\Entity\Question;
use App\Form\QuestionType;
use App\Entity\Answer;
use App\Form\AnswerType;
use App\Repository\QuestionRepository;
use App\Service\FileUploader;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class QuestionController extends AbstractController
{

    /** @var QuestionRepository $QuestionRepository */
    private $QuestionRepository;

    public function __construct(QuestionRepository $QuestionRepository)
    {
        $this->QuestionRepository = $QuestionRepository;
    }

    /**
     * @Route("/question/{id}", name="app_question", requirements={"id"="\d+"})
     */
    public function index(Question $question, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $renderedForm = null;
        if($this->isGranted("IS_AUTHENTICATED_FULLY"))
        {
            $answer = new Answer();
            $form = $this->createForm(AnswerType::class, $answer);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $answer->setUser($this->getUser());
                $answer->setQuestion($question);
                $entityManager->persist($answer);
                $entityManager->flush();
                return $this->redirectToRoute("app_question", ['id' => $question->getId()]);
            }
            $renderedForm = $form->createView();
        }
        $question->setViews($question->getViews() + 1);
        $entityManager->persist($question);
        $entityManager->flush();
        return $this->render('question/index.html.twig', [
            'question' => $question,
            'answerForm' => $renderedForm
        ]);
    }


    /**
     * @Route("/question/ask", name="app_question_ask")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function askQuestion(Request $request, FileUploader $fileUploader)
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question, [
            'user' => $this->getUser(),
            'picture' => null
        ]);
        //dd($request);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form['picture']->getData();

            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $question->setBrochureFilename($brochureFileName);

            }
            $question = $form->getData();
            $question->setUser($this->getUser() ?? null);
            $question->setCreated(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->flush();
            return $this->redirectToRoute("app_question", ['id' => $question->getId()]);
        }

        return $this->render('question/ask.html.twig', [
            'questionForm' => $form->createView()
        ]);
    }


    /**
     * @Route("/question/{id}/delete", name="question_delete", requirements={"id":"\d+"})
     */
    public function delete(Question $question)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($question);
        $entityManager->flush();

        return $this->redirectToRoute('app_main');
    }

    /**
     * @Route("/question/{id}/edit", name="question_edit", requirements={"id":"\d+"})
     */
    public function edit(Question $question, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(QuestionType::class, $question, [
            'user' => $this->getUser(),
            'picture' => null
        ]);

        $form-> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $question = $form->getData();
            $question->setUser($this->getUser() ?? null);
            $question->setCreated(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute("app_question", ['id' => $question->getId()]);
        }
        return $this->render('question/ask.html.twig', [
            'questionForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/question/{id}", name="question_show", requirements={"id":"\d+"})
     */
    public function post(Question $question)
    {
        return $this->render('question/show.html.twig', [
            'question' => $question
        ]);
    }

    /**
     * @Route("/question/search", name="question_search")
     */
    public function search(Request $request)
    {
        $query = $request->query->get('q');
        $questions = $this->QuestionRepository->searchByQuery($query);

        return $this->render('question/query_question.html.twig', [
            'questions' => $questions
        ]);
    }




}