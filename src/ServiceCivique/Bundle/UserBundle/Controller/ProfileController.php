<?php

namespace ServiceCivique\Bundle\UserBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\ProfileController as OriginalProfileController;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller managing the user profile
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ProfileController extends OriginalProfileController
{

    /**
     * Generate the redirection url when editing is completed.
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     *
     * @return string
     */
    protected function getRedirectionUrl(UserInterface $user)
    {
        if ($user->isOrganization()) {
            return $this->container->get('router')->generate('service_civique_organization_profile_edit');
        }

        if ($user->isJeune()) {
            return $this->container->get('router')->generate('fos_user_profile_edit');
        }

        return parent::getRedirectionUrl($user);
    }

    /**
     * Edit the user
     */
    public function editAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        if ($profile = $user->getProfile()) {
            if ($profile->getHasProfileVisited() != 1) {
                $profile->setHasProfileVisited(1);
                $em = $this->container->get('doctrine')->getManager();
                $em->persist($profile);
                $em->flush($profile);
            }
        }

        $form = $this->container->get('fos_user.profile.form');
        $formHandler = $this->container->get('fos_user.profile.form.handler');

        $process = $formHandler->process($user);
        if ($process) {
            $this->setFlash('fos_user_success', 'profile.flash.updated');

            return new RedirectResponse($this->getRedirectionUrl($user));
        }

        return $this->container->get('templating')->renderResponse(
            'FOSUserBundle:Profile:edit.html.'.$this->container->getParameter('fos_user.template.engine'),
            array('form' => $form->createView())
        );
    }

}
