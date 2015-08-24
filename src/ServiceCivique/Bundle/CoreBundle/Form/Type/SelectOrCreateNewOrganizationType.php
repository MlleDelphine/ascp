<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use ServiceCivique\Bundle\CoreBundle\Form\DataTransformer\OrganizationToArrayTransformer;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityRepository;

use ServiceCivique\Bundle\UserBundle\Entity\User;

use Symfony\Component\Validator\Constraints as Constraints;

use ServiceCivique\Bundle\CoreBundle\Form\EventListener\SelectOrCreateNewOrganizationSubscriber;
use ServiceCivique\Bundle\CoreBundle\Validator\Constraints\UniqueOrganizationName;

class SelectOrCreateNewOrganizationType extends AbstractType
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
        $this->securityContext        = $securityContext;
        $this->invitationRepository   = $invitationRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $show_invitation_fields = true;

        // try find a matching organization from mission organizationName property (legacy)
        if (isset($options['mission'])) {
            $selectedOrganization = $this->findMissionOrganization($options['mission'], $options['user_organization']);
        } else {
            $selectedOrganization = false;
        }

        $show_invitation_fields = !$selectedOrganization;

        $builder
            ->add('is_new_organization', 'choice', array(
                'label'    => 'service_civique.select_or_create_new_organization.is_new_organization.label',
                'expanded' => true,
                'required' => false,
                'empty_value' => false,
                // 'data'     => $show_invitation_fields,
                'data'     => null,
                'choices'  => array(
                    false => 'Un organisme existant',
                    true  => 'Un nouvel organisme (invitation)',
                ),
            ))
            ->add('organization', 'entity', array(
                'label'             => 'service_civique.select_or_create_new_organization.organization.label',
                'class'             => 'ServiceCiviqueCoreBundle:Organization',
                'data'              => $selectedOrganization,
                'query_builder'     => function (EntityRepository $er) use ($options) {
                    $userOrganization = $options['user_organization'];

                    return $er->createQueryBuilder('o')
                        ->where('o.approvedOrganization = :organization')
                        ->orWhere('o.id = :organization_id')
                        ->setParameter('organization', $userOrganization)
                        ->setParameter('organization_id', $userOrganization->getId())
                        ;
                }
            ))
            ->add('new_organization_name', null, array(
                'label'       => 'service_civique.select_or_create_new_organization.new_organization_name.label',
                'required'    => true,
                'data'        => (isset($options['mission']) && $show_invitation_fields) ? $options['mission']->getFakeOrganizationName() : null,
                'constraints' => array(
                    new UniqueOrganizationName()
                )
            ))
            ->add('new_organization_user_email', 'email', array(
                'label'       => 'service_civique.select_or_create_new_organization.new_organization_user_email.label',
                'required'    => true,
                'data'        => (isset($options['mission']) && $show_invitation_fields) ? $options['mission']->getAdditionalEmailContact() : null,
                'constraints' => array(
                    new Constraints\Email()
                )
            ))
            ->addEventSubscriber(new SelectOrCreateNewOrganizationSubscriber($this->organizationRepository))
            ->addViewTransformer(new OrganizationToArrayTransformer(
                $this->entityManager,
                $this->organizationRepository,
                $this->invitationRepository,
                $this->securityContext
            ));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'mission'           => null,
            'user_organization' => null
        ));
    }

    /**
     * try to find an organization matching mission data and current user organizations
     *
     * @param mixed $mission
     * @param mixed $userOrganization
     */
    protected function findMissionOrganization($mission, $userOrganization)
    {
        $missionOrganization = $mission->getOrganization();

        // if mission use a real organization name
        if (!$mission->getFakeOrganizationName()) {
            return $missionOrganization;
        }

        // try to find a children organization matching this name
        return $userOrganization->findOrganization($mission->getFakeOrganizationName());
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'select_or_create_new_organization';
    }
}
