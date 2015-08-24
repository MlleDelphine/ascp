<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

use ServiceCivique\Bundle\CoreBundle\Entity\Organization;
use ServiceCivique\Bundle\UserBundle\Entity\User;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\SecurityContext;

class OrganizationToArrayTransformer implements DataTransformerInterface
{
    /**
     * __construct
     *
     * @param EntityManager    $entityManager
     * @param ObjectRepository $organizationRepository
     * @param ObjectRepository $invitationRepository
     * @param SecurityContext  $securityContext
     */
    public function __construct(EntityManager $entityManager, ObjectRepository $organizationRepository, ObjectRepository $invitationRepository, SecurityContext $securityContext)
    {
        $this->entityManager          = $entityManager;
        $this->organizationRepository = $organizationRepository;
        $this->invitationRepository   = $invitationRepository;
        $this->securityContext        = $securityContext;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($organization)
    {
        $output = array(
            'organization'                => null,
            'is_new_organization'         => false,
            'new_organization_name'       => '',
            'new_organization_user_email' => ''
        );

        if ($organization) {
            $output['organization'] = $organization;
        }

        return $output;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($datas)
    {
        if (isset($datas['organization'])) {
            if (is_object($datas['organization'])) {
                return $datas['organization'];
            } else {
                return $this->entityManager->getReference('ServiceCiviqueCoreBundle:Organization', $datas['organization']);
            }
        }

        if (isset($datas['new_organization_name'])) {
            return $this->createNewOrganization($datas['new_organization_name'], $datas['new_organization_user_email']);
        }

        return null;
    }

    /**
     * createNewOrganization
     *
     * @param string $organizationName
     * @param string $email
     */
    protected function createNewOrganization($organizationName, $email)
    {
        // create new organization
        $approvedOrganization = $this->getCurrentUserOrganization();

        if ($approvedOrganization) {
            $organization = $this->organizationRepository
                ->createNewHostOrganizationWithName($organizationName, $approvedOrganization);
        } else {
            $organization = $this->organizationRepository
                ->createNewWithName($organizationName);
        }

        // create new invitation
        $organizationInvitation = $this->invitationRepository
            ->createNewFromOrganizationAndEmail($organization, $email);

        $organization->setInvitation($organizationInvitation);

        return $organization;
    }

    /**
     * getCurrentUserOrganization
     *
     */
    protected function getCurrentUserOrganization()
    {
        $user = $this->securityContext->getToken()->getUser();

        if (!$user instanceof User || !$user->getOrganization()) {
            return null;
        }

        return $user->getOrganization();
    }
}
