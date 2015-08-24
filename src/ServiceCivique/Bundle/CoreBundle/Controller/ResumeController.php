<?php

namespace ServiceCivique\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ResumeController extends Controller
{
    public function showAction(Request $request, $slug)
    {
        // We need to be logged in to see a resume
        if (!$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createNotFoundException();
        }

        try {
            return $this->get('igorw_file_serve.response_factory')->create(sprintf('../web/uploads/cv/%s', $slug), 'application/octet-stream');
        } catch (\Exception $e) {
            throw $this->createNotFoundException();
        }
    }

    public function deleteUserResumeAction(Request $request)
    {
        $response = new JsonResponse();
        if ($request->isXmlHttpRequest()) {
            $profile = $this->container->get('security.context')->getToken()->getUser()->getProfile();
            $profile->setPath(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($profile);
            $em->flush();

            return $response->setData('ok');
        }

        return $response->setData('error');
    }

}
