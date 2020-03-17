<?php

namespace App\Controller;

use App\Entity\Question;
use App\Form\QuestionType;
use App\Entity\Answer;
use App\Form\AnswerType;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\File\File;
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
            'answerForm' => $renderedForm,
            'picture_directory' => $this->getParameter('picture_path')
        ]);
    }



    public function askQuestion(Request $request)
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
            $file = $form->get('picture')->getData();
            if ($file) {
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('picture_directory'), $filename
                );
                $question->setPicture($filename);
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
            'questionForm' => $form->createView(),
            'picture_directory' => $this->getParameter('picture_path')

        ]);
    }

    public function delete(Question $question)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($question);
        $entityManager->flush();

        return $this->redirectToRoute('app_main');
    }

    public function edit(Question $question, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

            $picture = $this->getParameter('picture_directory') . '/' . $question->getPicture();
            if (is_file($picture)) {
                $picture = new File($picture);
            } else {
                $picture = null;
            }

            $form = $this->createForm(QuestionType::class, $question, [
                'user' => $this->getUser(),
                'picture' => $picture
            ]);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $question = $form->getData();
                $file = $form->get('picture')->getData();
                if ($file) {
                    $filename = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move(
                        $this->getParameter('picture_directory'), $filename
                    );
                    $question->setPicture($filename);

                    $question->setUser($this->getUser() ?? null);
                    $question->setCreated(new \DateTime());
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);
                    $entityManager->flush();

                    return $this->redirectToRoute("app_question", ['id' => $question->getId()]);
                }
            }
        return $this->render('question/ask.html.twig', [
            'questionForm' => $form->createView(),
            'picture_directory' => $this->getParameter('picture_directory')
        ]);
    }

    public function post(Question $question)
    {
        return $this->render('question/show.html.twig', [
            'question' => $question
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->query->get('q');
        $questions = $this->QuestionRepository->searchByQuery($query);

        return $this->render('question/query_question.html.twig', [
            'questions' => $questions
        ]);
    }




}