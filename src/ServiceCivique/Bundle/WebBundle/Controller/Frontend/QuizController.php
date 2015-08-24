<?php

namespace ServiceCivique\Bundle\WebBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class QuizController extends Controller
{

    public function showAction(Request $request)
    {
        $quiz = array();

        if ($request->query->has('first_question')) {
            $quiz['first_question'] = $request->query->get('first_question');
        }

        $form = $this->createForm('service_civique_quiz', $quiz);

        $form->handleRequest($request);

        $datas = array(
            'form' => $form->createView(),
        );

        if ($form->isValid()) {
            $datas['score'] = $this->getQuizzScore($form->getData());
        }

        return new Response($this->renderView(
            'ServiceCiviqueWebBundle:Frontend:quiz.html.twig',
            $datas
        ));
    }

    /**
     * getQuizzScore
     *
     * @param  array $quizResults
     * @return int   score
     */
    protected function getQuizzScore($quizResults)
    {
        $good_answers = array(
            'first_question'  => 1,
            'second_question' => 0,
            'third_question'  => 0,
            'fourth_question' => 1,
            'fifth_question'  => 0
        );

        return count($good_answers) - count(array_diff_assoc($good_answers, $quizResults));
    }
}
