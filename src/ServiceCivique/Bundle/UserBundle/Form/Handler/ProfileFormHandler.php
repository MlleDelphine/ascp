<?php

namespace ServiceCivique\Bundle\UserBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\ProfileFormHandler as BaseHandler;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ProfileFormHandler extends BaseHandler
{
    public function __construct(FormInterface $form, Request $request, UserManagerInterface $userManager)
    {
        $this->form = $form;
        $this->request = $request;
        $this->userManager = $userManager;
    }

    public function process(UserInterface $user)
    {
        $this->form->setData($user);

        if ('POST' === $this->request->getMethod()) {
            /* Ajout F. Zilbermann 30/09/2015 */

            // Si l'utilisateur courant n'a pas le rôle Super Admin
            if (!$user->hasRole('ROLE_SUPER_ADMIN')) {
                // on enlève le champ 'enabled' (Activation)
                $this->form->remove('enabled');
                // Si le formulaire a une partie 'organization' (Organisme)
                if ($this->form->has('organization')) {
                    // On enlève le champ 'type'
                    $this->form->get('organization')->remove('type');
                    // On enlève le champ 'approvedOrganization' (Organisme parent)
                    $this->form->get('organization')->remove('approvedOrganization');
                }
            }

            $this->form->bind($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess($user);

                return true;
            }

            // Reloads the user to reset its username. This is needed when the
            // username or password have been changed to avoid issues with the
            // security layer.
            $this->userManager->reloadUser($user);
        }

        return false;
    }
}
