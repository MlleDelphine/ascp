<?php

namespace ServiceCivique\Bundle\CoreBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use ServiceCivique\Bundle\CoreBundle\Entity\Organization;
use Symfony\Component\Form\FormError;

class ApprovalNumberFinder
{
    protected $doctrine;
    protected $em;
    protected $approvalRepository;
    protected $organizationRepository;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getManager();
        $this->approvalRepository = $this->doctrine->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Approval');
        $this->organizationRepository = $this->doctrine->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Organization');
    }

    public function checkApprovalNumber($approvalNumber, $myOrganizationType)
    {
        $result = array(
            'errorType' => null,
            'formError' => null,
        );

        if ($approvalNumber == 'XX-000-00-00000-00') {
            $result['formError'] = new FormError('Veuillez entrer un numéro d\'agrément valide.');

            $result['errorType'] = 1;
            return $result;
        } else {
            if (!$approvalOrganization = $this->approvalRepository->findOneByApprovalNumber($approvalNumber)) {
                $result['formError'] = new FormError('Le numéro d\'agrément est inconnu.');

                $result['errorType'] = 2;
                return $result;
            }
            if ($myOrganizationType == Organization::TYPE_APPROVED && $this->organizationRepository->findOneByApprovalNumber($approvalNumber)) {
                $result['formError'] = new FormError('Ce numéro d\'agrément est déjà utilisé par un autre compte.');

                $result['errorType'] = 3;
                return $result;
            } else {
                if ($organizationFound = $this->organizationRepository->findOneBy(
                    array(
                        'approvalNumber' => $approvalNumber,
                        'type' => Organization::TYPE_APPROVED,
                    )
                )) {
                    $result['formError'] = new FormError('ok1');

                    $result['errorType']    = 4;
                    $result['organization'] = $organizationFound;
                    return $result;
                } elseif ($organizationFound = $this->organizationRepository->findOneBy(
                    array(
                        'approvalNumber' => $approvalNumber,
                        'type' => Organization::TYPE_HOST,
                    )
                )) {
                    $result['formError'] = new FormError('ok2');

                    $result['errorType'] = 5;
                    return $result;
                }
            }
        }



        return $result;
    }

}
