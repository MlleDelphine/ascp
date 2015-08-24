<?php

namespace ServiceCivique\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class TagController extends ResourceController
{
    // public function updatePositionsAction(Request $request)
    // {
    //     $response = new JsonResponse();
    //     if ($request->isXmlHttpRequest()) {
    //         $positions = $request->request->get('data');
    //         $em = $this->getDoctrine()->getManager();
    //         foreach ($positions as $key => $positionItem) {
    //             $repository = $this->getRepository();
    //             $resource = $repository->find($positionItem['id']);
    //             $resource->setPosition($positionItem['position']);
    //             $em->persist($resource);
    //         }
    //         $em->flush();

    //         return $response->setData('ok');
    //     }

    //     return $response->setData('error');
    // }

    public function deleteAction(Request $request)
    {
        $resource = $this->findOr404($request);
        $this->domainManager->delete($resource);

        return $this->redirectHandler->redirectToIndex();
    }
}
