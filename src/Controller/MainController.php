<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Question;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_main")
     */
    public function index(QuestionRepository $questionRepository, Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $questionsEntity = $em->getRepository(Question::class)->findBy(['validate'=> true], ['created' => 'DESC']);

        //$allAppointmentsQuery = $questionRepository->createQueryBuilder('id')
        //  ->where('id.status != :status')
         // ->setParameter('status', 'canceled')->getQuery();
        $questions = $paginator->paginate(
        // Doctrine Query, not results
            $questionsEntity,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );



        return $this->render('main/index.html.twig', [
            'questions' => $questions
        ]);
    }
}
