<?php

namespace ServiceCivique\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AnswerMailController extends ResourceController
{
    public function getDescriptionAction(Request $request, $id)
    {
        $response = new JsonResponse();
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $repository = $this->getRepository();
            if ($resource = $repository->find($id)) {
                return $response->setData(
                    array(
                        'title' => $resource->getTitle(),
                        'text'  => $resource->getText(),
                    )
                );
            }
        }

        return $response->setData('error');
    }

    public function deleteAction(Request $request)
    {
        $resource = $this->findOr404($request);
        $this->domainManager->delete($resource);

        return $this->redirectHandler->redirectToIndex();
    }
}
