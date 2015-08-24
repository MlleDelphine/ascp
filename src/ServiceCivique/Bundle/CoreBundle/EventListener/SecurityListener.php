<?php

namespace ServiceCivique\Bundle\CoreBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\SecurityContext;
// use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
class SecurityListener
{
    protected $router;
    protected $security;
    protected $dispatcher;

    public function __construct(Router $router, SecurityContext $security, $dispatcher)
    {
        $this->router = $router;
        $this->security = $security;
        $this->dispatcher = $dispatcher;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $this->dispatcher->addListener(KernelEvents::RESPONSE, array($this, 'onKernelResponse'));
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($this->security->isGranted('ROLE_JEUNE')) {
            $event->getResponse()->headers->set('Location', $this->router->generate('service_civique_application_list'));
        } elseif ($this->security->isGranted('ROLE_ORGANIZATION')) {
            $organization = $this->security->getToken()->getUser()->getOrganization();
            if ($organization->getApprovalNumber() == 'XX-000-00-00000-00') {
                $event->getResponse()->headers->set('Location', $this->router->generate('service_civique_organization_update_approval_number'));
            }
        }
    }
}
