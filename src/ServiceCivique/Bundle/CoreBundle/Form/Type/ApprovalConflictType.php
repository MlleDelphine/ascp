<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

class ApprovalConflictType extends AbstractType
{

    private $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $user = $this->securityContext->getToken()->getUser();

        $builder
            ->add('approval_number', 'text', array('label' => 'Numéro d\'agrément'))
            ->add('problem_type', 'choice', array(
                'label' => 'Problème',
                'choices'   => array(
                    '1' => 'Ce numéro d\'agrément est déjà utilisé par un organisme',
                    '2' => 'Ce numéro d\'agrément n\'est pas reconnu par notre système',
                    '3' => 'Autre',
                ),
            ))
            ->add('organization_name', 'text', array('label' => 'Nom de l\'organisme agrée'))
            ->add('contact_email', 'email', array('label' => 'Email'))
            ->add('firstname', 'text', array('label' => 'Prénom'))
            ->add('lastname', 'text', array('label' => 'Nom'))
            ->add('comment', 'textarea', array('label' => 'Commentaire'))
        ;

        if ($this->securityContext->isGranted('ROLE_ADMIN')) {
            $builder->add('isResolved', 'choice', array(
                'label' => 'Résolu',
                'choices'   => array(
                    '0' => 'Non',
                    '1' => 'Oui',
                ),
            ));
        }


        $builder->add('actions', 'form_actions');

        $builder->add('save', 'submit', array(
            'label' => 'Valider',
            'attr' => array('class' => 'btn btn-sc-red')
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'ServiceCivique\Bundle\CoreBundle\Entity\ApprovalConflict'
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_approval_conflict';
    }
}
