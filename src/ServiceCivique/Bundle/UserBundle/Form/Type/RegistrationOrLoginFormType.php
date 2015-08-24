<?php

namespace ServiceCivique\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use ServiceCivique\Bundle\UserBundle\Form\EventListener\UserRegistrationSubscriber;

use ServiceCivique\Bundle\MailerBundle\TwigMandrillMailer;
use FOS\UserBundle\Util\TokenGeneratorInterface;

use FOS\UserBundle\Doctrine\UserManager;

use Symfony\Component\Form\FormInterface;
use ServiceCivique\Bundle\UserBundle\Entity\User;

class RegistrationOrLoginFormType extends AbstractType
{
    protected $userManager;
    protected $mailer;
    protected $tokenGenerator;

    /**
     * __construct
     *
     * @param UserManager             $userManager
     * @param TwigMandrillMailer      $mailer
     * @param TokenGeneratorInterface $tokenGenerator
     */
    public function __construct(
        UserManager $userManager,
        TwigMandrillMailer $mailer,
        TokenGeneratorInterface $tokenGenerator
    )
    {
        $this->userManager    = $userManager;
        $this->mailer         = $mailer;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // new user registration
        $builder->add('user_registration', 'service_civique_user_registration', array(
            'cascade_validation' => true,
            'light_mode'         => true,
            'required'           => true,
            'mapped'             => false,
            'validation_groups'  => function (FormInterface $form) {
                // if ($form->getParent()->get('use_existing_account')->getData()) {
                //     return array('Default');
                // }
                return array('Default', 'ServiceCiviqueRegistration2');
            }
        ));

        $builder->addEventSubscriber(new UserRegistrationSubscriber(
            $this->userManager,
            $this->mailer,
            $this->tokenGenerator
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'cascade_validation' => true,
            'data_class'         => 'ServiceCivique\Bundle\UserBundle\Entity\User'
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_registration_or_login';
    }
}
