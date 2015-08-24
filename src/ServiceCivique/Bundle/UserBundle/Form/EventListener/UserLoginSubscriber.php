<?php

namespace ServiceCivique\Bundle\UserBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use FOS\UserBundle\Doctrine\UserManager;

class UserLoginSubscriber implements EventSubscriberInterface
{
    protected $userManager;
    protected $encoderFactory;

    /**
     * __construct
     *
     * @param UserManager    $userManager
     * @param EncoderFactory $encoderFactory
     */
    public function __construct(UserManager $userManager, EncoderFactory $encoderFactory)
    {
        $this->userManager    = $userManager;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT  => 'onSubmit'
        ];
    }

    public function onSubmit(FormEvent $event)
    {
        // $form = $event->getForm();
        // $user = $form->get('user_registration');

        // die($user->getFirstName());

        // if (!$form->get('use_existing_account')->getData()) {
        //     return;
        // }

        // $userLogin = $form->get('user_login');

        // $user = $this->retrieveUser(
        //     $userLogin->get('_username')->getData(),
        //     $userLogin->get('_password')->getData()
        // );

        // if (!$user) {
        //     $form->get('user_login')->addError(
        //         new \Symfony\Component\Form\FormError('Mauvais mot de passe ou email')
        //     );

        //     return;
        // }

        // $event->setData($user);
    }

    /**
     * retrieveUser
     *
     * @param string $usernameOrEmail
     * @param string $password
     */
    // protected function retrieveUser($usernameOrEmail, $password)
    // {
    //     // check user exists
    //     if (!$user = $this->userManager->findUserByUsernameOrEmail($usernameOrEmail)) {
    //         return false;
    //     }

    //     $encoder = $this->encoderFactory->getEncoder($user);

    //     // check user password
    //     $passwordIsValid = ($encoder->isPasswordValid(
    //         $user->getPassword(),
    //         $password,
    //         $user->getSalt()
    //     )) ? true : false;

    //     return $passwordIsValid ? $user : false;
    // }
}
