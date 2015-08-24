<?php

namespace ServiceCivique\Bundle\UserBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\ChangePasswordController as OriginalChangePasswordController;

/**
 * Controller managing the password change
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class ChangePasswordController extends OriginalChangePasswordController
{

    /**
     * Generate the redirection url when the resetting is completed.
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

        return parent::getRedirectionUrl($user);
    }

}
