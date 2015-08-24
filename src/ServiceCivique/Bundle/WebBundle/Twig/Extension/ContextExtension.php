<?php

namespace ServiceCivique\Bundle\WebBundle\Twig\Extension;

use ServiceCivique\Bundle\MenuBundle\ContextResolver;
use ServiceCivique\Bundle\CoreBundle\Entity\Mission;
use ServiceCivique\Bundle\CoreBundle\Entity\Application;

class ContextExtension extends \Twig_Extension
{
    /**
     * contextResolver
     *
     * @var ContextResolver
     */
    protected $contextResolver;

    /**
     * @param ContextResolver $contextResolver
     */
    public function __construct(ContextResolver $contextResolver)
    {
        $this->contextResolver = $contextResolver;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'service_civique_context'      => new \Twig_Function_Method($this, 'getContextName'),
            'service_civique_context_name' => new \Twig_Function_Method($this, 'renderContextName'),
            'service_civique_is_homepage'  => new \Twig_Function_Method($this, 'isHomePage'),
            'getApplicationMissionStatus'  => new \Twig_Function_Method($this, 'getApplicationMissionStatus'),
            'formatMails'                  => new \Twig_Function_Method($this, 'formatMails'),
            'isReflate'                    => new \Twig_Function_Method($this, 'isReflate'),
            'guessExtension'                    => new \Twig_Function_Method($this, 'guessExtension'),
        );
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            'mailto'   => new \Twig_SimpleFilter(
                'mailto',
                array($this, 'mailto'),
                array('is_safe' => array('html'))
            ),
            'mediatype' => new \Twig_SimpleFilter(
                'mediatype',
                array($this, 'mediatype')
            ),
            'medianame' => new \Twig_SimpleFilter(
                'medianame',
                array($this, 'medianame')
            ),
            'mediaurl' => new \Twig_SimpleFilter(
                'mediaurl',
                array($this, 'mediaurl')
            ),
            'pressetype' => new \Twig_SimpleFilter(
                'pressetype',
                array($this, 'pressetype')
            ),
            'organizationtype' => new \Twig_SimpleFilter(
                'organizationtype',
                array($this, 'organizationtype')
            ),
            'statuslabel' => new \Twig_SimpleFilter(
                'statuslabel',
                array($this, 'statuslabel'),
                array('is_safe' => array('html'))
            ),
            'booleanLabel' => new \Twig_SimpleFilter(
                'booleanLabel',
                array($this, 'booleanLabel'),
                array('is_safe' => array('html'))
            ),
            'organizationlabel' => new \Twig_SimpleFilter(
                'organizationlabel',
                array($this, 'organizationlabel'),
                array('is_safe' => array('html'))
            ),
            'warningdate' => new \Twig_SimpleFilter(
                'warningdate',
                array($this, 'warningdate'),
                array('is_safe' => array('html'))
            ),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'service_civique_context';
    }

    /**
     * @return void
     */
    public function renderContextName()
    {
        echo 'context-' . $this->contextResolver->getContext();
    }

    /**
     * @return void
     */
    public function getContextName()
    {
        return $this->contextResolver->getContext();
    }

    /**
     * @return void
     */
    public function isHomePage()
    {
        return $this->contextResolver->isHomePage();
    }

    /**
     * @return void
     */
    public function getApplicationMissionStatus($status, $mission, $isPoked)
    {
        $now = new \Datetime();
        // cf: trads
        // 0: En attente
        // 1: Retenu
        // 2: Non retenu
        // 3: Relancé
        // 4: Mission pourvue
        // if ($mission->getStatus() == Mission::STATUS_AVAILABLE || $mission->getStatus() == Mission::STATUS_UNDER_VALIDATION) {
            if ($isPoked && $status == Application::WAITING_ANSWER) {
                $status = 3;
            }
        // }

        if ($mission->getStatus() == Mission::STATUS_FILLED) {
            $status = 4;
        }

        return 'service_civique.application.status.' . $status;
    }

    /**
     * @return void
     */
    public function formatMails($mails)
    {
        $contacts = '';
        foreach ($mails as $mail) {
            $contacts .= $mail . ',';
        }

        return trim($contacts, ',');
    }

    /**
     * @return void
     */
    public function isReflate($publishedDate)
    {
        $now = new \Datetime();
        $reflateDate = $publishedDate->modify('+1 week');
        if ($reflateDate < $now) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function guessExtension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    public function mailto($mail)
    {
        return  '<a class="link-action" href="mailto:' . $mail . '" target="_blank">' . $mail . '</a>';
    }

    public function mediatype($mediaType)
    {
        $mediaTypeMap = array(
            1 => 'icon-files-sc',
            2 => 'icon-uniE620',
            3 => 'icon-uniE61F',
        );

        return isset($mediaTypeMap[$mediaType]) ? $mediaTypeMap[$mediaType] : 'icon-files-sc';
    }

    public function pressetype($presseType)
    {
        $presseTypeMap = array(
            1 => 'Communiqué de presse',
            2 => 'Dossier de presse',
        );

        return isset($presseTypeMap[$presseType]) ? $presseTypeMap[$presseType] : 'comm';
    }

    public function medianame($mediaType)
    {
        $mediaTypeMap = array(
            1 => 'Presse',
            2 => 'Radio',
            3 => 'Télévision',
        );

        return isset($mediaTypeMap[$mediaType]) ? $mediaTypeMap[$mediaType] : 'Presse';
    }

    public function mediaurl($mediaPath, $host)
    {
        if (substr($mediaPath, 0, 7) === 'http://' || substr($mediaPath, 0, 8) === 'https://') {
            return $mediaPath;
        }

        return $host . '/uploads/media/'. $mediaPath;
    }

    public function statuslabel($labelText, $labelType)
    {
        $labelTypeMap = array(
            0 => 'label-default', // Brouillon
            1 => 'label-primary', // À pourvoir
            2 => 'label-success', // Pourvue
            3 => 'label-info', // En attente
            4 => 'label-warning', // Modif. en cours de valid.
        );

        $labelClass = isset($labelTypeMap[$labelType]) ? $labelTypeMap[$labelType] : 'label-default';

        return '<span class="label ' . $labelClass . '">' . $labelText . '</span>';
    }

    public function booleanLabel($bool, $text)
    {
        $labelMap = array(
            0 => 'label-default', // Disable
            1 => 'label-success', // Enable
        );

        $labelClass = isset($labelMap[$bool]) ? $labelMap[$bool] : 'label-default';

        return '<span class="label ' . $labelClass . '">' . $text . '</span>';
    }

    public function organizationtype($organizationType)
    {
        $organizationTypeMap = array(
            1 => 'Organisme agréé',
            2 => 'Organisme d\'accueil',
        );

        return isset($organizationTypeMap[$organizationType]) ? $organizationTypeMap[$organizationType] : 'Organisme agréé';
    }

    public function organizationlabel($bool, $text)
    {
        $labelMap = array(
            1 => 'label-info',
            2 => 'label-primary',
        );

        $labelClass = isset($labelMap[$bool]) ? $labelMap[$bool] : 'label-default';

        return '<span class="label ' . $labelClass . '">' . $text . '</span>';
    }

    public function warningdate($startDate, $status, $url)
    {
        $now = new \Datetime();
        if ($status == Mission::STATUS_AVAILABLE && $startDate < $now) {
            return ' <a data-toggle="tooltip" data-placement="top" href="' . $url . '" class="btn btn-primary" data-original-title="Date dépassée : à mettre à jour"><i class="warning-date glyphicon glyphicon-warning-sign"></i><span>Date dépassée : à mettre à jour</span></a>';
        }
        return '';
    }
}
