<?php

namespace ServiceCivique\Bundle\UserBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;
use ServiceCivique\Bundle\UserBundle\Entity\User;
use ServiceCivique\Bundle\CoreBundle\Entity\Organization;
use FOS\UserBundle\Model\UserInterface;
use Doctrine\Common\Persistence\ObjectRepository;

use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use ServiceCivique\Bundle\MailerBundle\TwigMandrillMailer;
use Symfony\Component\Form\FormError;
use Doctrine\Bundle\DoctrineBundle\Registry;
use ServiceCivique\Bundle\CoreBundle\Service\ApprovalNumberFinder;

class RegistrationFormHandler extends BaseHandler
{

    /**
     * __construct
     *
     * @param FormInterface           $form
     * @param Request                 $request
     * @param UserManagerInterface    $userManager
     * @param MailerInterface         $mailer
     * @param TokenGeneratorInterface $tokenGenerator
     * @param ObjectRepository        $organizationInvitationRepository
     * @param TwigMandrillMailer      $scMailer
     * @param Registry                $doctrine
     * @param ApprovalNumberFinder    $approvalNumberFinder
     */
    public function __construct(FormInterface $form, Request $request, UserManagerInterface $userManager, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, ObjectRepository $organizationInvitationRepository, TwigMandrillMailer $scMailer, Registry $doctrine, ApprovalNumberFinder $approvalNumberFinder)
    {
        parent::__construct($form, $request, $userManager, $mailer, $tokenGenerator);
        $this->organizationInvitationRepository = $organizationInvitationRepository;
        $this->scMailer = $scMailer;
        $this->doctrine = $doctrine;
        $this->approvalNumberFinder = $approvalNumberFinder;
    }

    /**
     * @param boolean $confirmation
     */
    public function process($confirmation = false)
    {
        $user = $this->createUser();
        $this->form->setData($user);

        if ('POST' === $this->request->getMethod()) {
            $this->form->bind($this->request);
            try {
                $approvalNumber = $this->form->get('organization')->get('approvalNumber')->getData();
                $isOrganizationForm = true;
            } catch (\OutOfBoundsException $e) {
                $isOrganizationForm = false;
            }
            if ($isOrganizationForm) {
                try {
                    $organizationType = $this->form->get('organization')->get('type')->getData();
                } catch(\OutOfBoundsException $e) { // In case of invitation form
                    $organizationType = Organization::TYPE_HOST;
                }
                $approvalNumberCheck = $this->approvalNumberFinder->checkApprovalNumber($approvalNumber, $organizationType);

                $formErrorTypes = array(1,2,3);
                if (in_array($approvalNumberCheck['errorType'], $formErrorTypes)) {
                    $approvalNumberError = $approvalNumberCheck['formError'];
                    $this->form->get('organization')->get('approvalNumber')->addError($approvalNumberError);
                } elseif ($approvalNumberCheck['errorType'] == 4) {
                    $organization = $this->form->get('organization')->getData();
                    $organization->setApprovedOrganization($approvalNumberCheck['organization']);
                    // $em = $this->doctrine->getEntityManager();
                    // $em->persist($organization);
                    // $em->flush($organization);
                } elseif ($approvalNumberCheck['errorType'] == 5) {
                    // nothing
                }
            }

            if ($this->form->isValid()) {
                $this->onSuccess($user, $confirmation);

                return true;
            }
        }

        return false;
    }

    private function isApprovalNumberUnique($approvalNumber, $organizationType)
    {
        if ($approvalNumber == 'XX-000-00-00000-00') {
            return false;
        }
        $approvalRepository = $this->doctrine->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Approval');
        if (!$approvalRepository->findOneByApprovalNumber($approvalNumber)) {
            return false;
        }

        $organizationRepository = $this->doctrine->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Organization');

        if (Organization::TYPE_HOST == $organizationType && $organizationRepository->findOneByApprovalNumber($approvalNumber)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function createUser()
    {
        $user = parent::createUser();

        if ($this->request->get('_route') == 'service_civique_organization_register') {
            $user->setType(User::ORGANIZATION_TYPE);
            $user->addRole('ROLE_ORGANIZATION');

            // handle organization invitation code
            $user = $this->processOrganizationInvitationCode(
                $user,
                $this->request->query->get('invitation_code')
            );

        } else {
            $user->setType(User::MISSION_SEEKER_TYPE);
            $user->addRole('ROLE_JEUNE');
        }

        return $user;
    }

    /**
     * processOrganizationInvitationCode
     *
     * @param User   $user
     * @param string $invitation_code
     */
    protected function processOrganizationInvitationCode(User $user, $invitation_code = null)
    {
        if (!$invitation_code) {
            return $user;
        }
        $flashBag = $this->request->getSession()->getFlashBag();

        // retreive unused invitation
        $invitation = $this->organizationInvitationRepository->findOneBy(array(
            'code'    => $invitation_code,
            'used_at' => null
        ));


        // if no valid invitation has been found
        if (!$invitation) {
            $flashBag->add('error', 'Cette invitation n’existe pas ou a déjà été utilisée');

            return $user;
        }

        // mark invitation as used
        $invitation->setUsedAt(new \DateTime());
        $organization = $invitation->getOrganization();
        $organization->setInvitation($invitation);

        $user->setOrganization($organization);
        $user->setEmail($invitation->getEmail());

        if ($this->request->getMethod() == 'GET') {
            $flashBag->add('success', 'Votre invitation est valide, merci de compléter votre inscription');
        }

        $context = array(
            'organizationName'         => $organization->getName(),
            'approvedOrganizationName' => $organization->getApprovedOrganization()->getName(),
        );

        $fromApprovedOrganizationMail = $organization->getApprovedOrganization()->getContactEmail();
        if ($fromApprovedOrganizationMail === null) {
            $fromApprovedOrganizationMail = $organization->getApprovedOrganization()->getUser()->getEmail();
        }
        $this->scMailer->sendMessage('ServiceCiviqueWebBundle:Frontend\Mission:_mail_organization_invitation_accepted.html.twig', $context, null, $fromApprovedOrganizationMail);

        return $user;
    }

    /**
     * @param boolean $confirmation
     */
    protected function onSuccess(UserInterface $user, $confirmation)
    {
        if ($confirmation) {
            $user->setEnabled(false);
            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($this->tokenGenerator->generateToken());
            }

            $this->mailer->sendConfirmationEmailMessage($user);
        } else {
            $user->setEnabled(true);
        }

        $this->userManager->updateUser($user);
    }

}
