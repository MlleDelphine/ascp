<?php

namespace ServiceCivique\Bundle\UserBundle\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Custom authentication success handler
 * to redirect admins to administration page
 *
 * @see DefaultAuthenticationSuccessHandler
 */
class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        // if ($request->query->get('inline-login')) {
        //     return $this->httpUtils->createRedirectResponse($request, $this->determineTargetUrl($request));
        // }

        $target_url = '';

        if ($token->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            $target_url = $this->httpUtils->generateUri($request, 'service_civique_backend_homepage');
        } elseif ($token->getUser()->hasRole('ROLE_ORGANIZATION')) {
            $target_url = $this->httpUtils->generateUri($request, 'service_civique_organization_mission_index');
        } elseif ($token->getUser()->hasRole('ROLE_JEUNE')) {
            $target_url = $this->httpUtils->generateUri($request, 'service_civique_application_list');
        } else {
            $target_url = $this->determineTargetUrl($request);
        }

        return $this->httpUtils->createRedirectResponse($request, $target_url);
    }
}
