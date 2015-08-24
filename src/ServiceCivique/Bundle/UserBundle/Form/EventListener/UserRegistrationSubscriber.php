<?php

namespace ServiceCivique\Bundle\UserBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use FOS\UserBundle\Doctrine\UserManager;
use ServiceCivique\Bundle\MailerBundle\TwigMandrillMailer;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use ServiceCivique\Bundle\UserBundle\Entity\User;

class UserRegistrationSubscriber implements EventSubscriberInterface
{
    protected $userManager;
    protected $mailer;
    protected $tokenGenerator;

    /**
     * __construct
     *
     * @param UserManager             $userManager
     * @param TwigMandrillMailer      $mailer
     * @param TokenGeneratorInterface $tokenGeneratorInterface
     */
    public function __construct(
        UserManager $userManager,
        TwigMandrillMailer $mailer,
        TokenGeneratorInterface $tokenGenerator
    )
    {
        $this->userManager     = $userManager;
        $this->mailer          = $mailer;
        $this->tokenGenerator  = $tokenGenerator;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT        => 'onSubmit',
            FormEvents::POST_SUBMIT   => 'onPostSubmit',
            FormEvents::POST_SET_DATA => 'onPostSetData'
        ];
    }

    public function onPostSetData(FormEvent $event)
    {
        $user = $event->getData();

        $event->getForm()->get('user_registration')->setData($user);

        $event->setData(null);
    }

    /**
     * onSubmit
     *
     * @param FormEvent $event
     */
    public function onSubmit(FormEvent $event)
    {
        $form = $event->getForm();

        // if ($form->get('use_existing_account')->getData()) {
        //     return;
        // }

        $registrationForm = $form->get('user_registration');

        $user = $registrationForm->getData();

        $user->setEnabled(true);
        $user->setType(User::MISSION_SEEKER_TYPE);

        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }

        $event->setData($user);
    }

    public function onPostSubmit(FormEvent $event)
    {
        // $form = $event->getForm();

        // if ($form->get('use_existing_account')->getData()) {
        //     return;
        // }

        // $user = $event->getData();

        // // Generate password
        // $generatedPassword = $this->generateRandomPassword();
        // $encoder = $this->encoderFactory->getEncoder($user);
        // $encodedPass = $encoder->encodePassword($generatedPassword, $user->getSalt());
        // $user->setPassword($encodedPass);

        // $context = array(
        //     'user_name'            => $user->getFullName(),
        //     'password'             => $generatedPassword,
        //     'change_password_link' => $this->router->generate('fos_user_change_password', array(), true)
        // );
        // $this->mailer->sendMessage('ServiceCiviqueUserBundle:Registration:invitation_password.html.twig', $context, null, $user->getEmail());
        // // Autologin
        // $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        // $this->securityContext->setToken($token);
    }

    // public function generateRandomPassword($length = 10) {
    //     $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    //     $randomString = '';
    //     for ($i = 0; $i < $length; $i++) {
    //         $randomString .= $characters[rand(0, strlen($characters) - 1)];
    //     }

    //     return $randomString;
    // }
}
