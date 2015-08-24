<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ServiceCivique\Bundle\AddressingBundle\Form\Type\LocationType;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use ServiceCivique\Bundle\MailerBundle\TwigMandrillMailer;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ApplicationType extends AbstractType
{

    private $securityContext;

    public function __construct(SecurityContext $securityContext, EncoderFactory $encoderFactory, Router $router, TwigMandrillMailer $mailer)
    {
        $this->securityContext = $securityContext;
        $this->encoderFactory  = $encoderFactory;
        $this->router          = $router;
        $this->mailer          = $mailer;

    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location', 'location', array(
                'label'              => 'service_civique.application.form.location.label',
                'required'           => false,
                'virtual'            => true,
                'precision'          => LocationType::ADDRESS_PRECISION,
                'required_precision' => -1,
                'use_departement'    => false,
                'default_country'    => 'FR'
            ))
            ->add('phoneNumber', null, array('label' => 'Téléphone'))
            ->add('motivation', null, array(
                'label'    => 'service_civique.application.form.motivation.label',
                'required' => true,
                'attr'     => array(
                    'rows' => 20
                ),
            ))
            ->add('file', 'file', array(
                'label'    => 'service_civique.application.form.file.label',
                'required' => false,
            ))
            ->add('removeResume', 'hidden', array(
                'mapped'     => false,
                'required'   => false,
            ))
        ;

        $builder->add('actions', 'form_actions');

        // $builder->add('cancel', 'button', array(
        //     'label' => 'Annuler',
        //     'attr' => array('class' => 'btn btn-sc-red-2')
        // ));

        $builder->add('save', 'submit', array(
            'label' => 'Voir un aperçu de ma candidature',
            'attr'  => array('class' => 'btn btn-sc-red')
        ));

        if (!$this->securityContext->isGranted('ROLE_USER')) {
            // @TODO rename to remove _or_login
            $builder->add('user', 'service_civique_registration_or_login', array(
                'cascade_validation' => true,
                'label'              => 'Compte personnel'
            ));
        }

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form        = $event->getForm();
            $application = $event->getData();
            $user        = $application->getUser();

            if ($form->isValid() && !$this->securityContext->isGranted('ROLE_USER')) {
                // Generate password
                $generatedPassword = $this->generateRandomPassword();
                $encoder = $this->encoderFactory->getEncoder($user);
                $encodedPass = $encoder->encodePassword($generatedPassword, $user->getSalt());
                $user->setPassword($encodedPass);

                $context = array(
                    'user_name'            => $user->getFullName(),
                    'password'             => $generatedPassword,
                    'change_password_link' => $this->router->generate('fos_user_change_password', array(), true)
                );
                $this->mailer->sendMessage('ServiceCiviqueUserBundle:Registration:invitation_password.html.twig', $context, null, $user->getEmail());
                // Autologin
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $this->securityContext->setToken($token);
            }

            $user->getProfile()->setMotivation($application->getMotivation());
            $user->getProfile()->setPhoneNumber($application->getPhoneNumber());
            $user->getProfile()->setZipCode($application->getZipCode());
            $user->getProfile()->setCountry($application->getCountry());
            $user->getProfile()->setAddress($application->getAddress());
            $user->getProfile()->setCity($application->getCity());
            $user->getProfile()->setPath($application->getPath());
        });
    }

    public function generateRandomPassword($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'ServiceCivique\Bundle\CoreBundle\Entity\Application',
            'cascade_validation' => true
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_application';
    }
}
