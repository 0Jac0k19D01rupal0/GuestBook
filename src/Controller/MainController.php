<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Question;

class MainController extends AbstractController
{
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $questionsEntity = $em->getRepository(Question::class)->findBy(['Validate'=> true], ['created' => 'DESC']);
        $questions = $paginator->paginate(
        // Doctrine Query, not results
            $questionsEntity,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            25
        );

        return $this->render('main/index.html.twig', [
            'questions' => $questions
        ]);
    }
}
