<?php

namespace ServiceCivique\Bundle\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\UserBundle\Controller\RegistrationController as BaseRegistrationController;

use ServiceCivique\Bundle\UserBundle\Entity\User;

class RegistrationController extends BaseRegistrationController
{
    public function confirmAction($token)
    {
        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);
        $user->setLastLogin(new \DateTime());

        $this->container->get('fos_user.user_manager')->updateUser($user);

        if (User::ORGANIZATION_TYPE == $user->getType()) {
            $redirectRoute = 'service_civique_organization_homepage';

            // add success message
            $this->container->get('session')->getFlashBag()->add(
                'success',
                $this->container->get('translator')->trans('registration.confirmed', array('%firstname%' => $user->getOrganization()->getName()), 'FOSUserBundle')
            );

        } else {
            $redirectRoute = 'fos_user_registration_confirmed';
        }

        $response = new RedirectResponse($this->container->get('router')->generate($redirectRoute));
        $this->authenticateUser($user, $response);

        return $response;
    }

    /**
     * We override this function to fix null email case
     * @inheritDoc()
     */
    public function checkEmailAction()
    {
        if (!$this->container->get('session')->has('fos_user_send_confirmation_email/email')) {
            throw new NotFoundHttpException();
        }

        return parent::checkEmailAction();
    }
}
