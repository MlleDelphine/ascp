<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class HeaderVideoboxType extends AbstractType
{

    private $videoboxValue;

    /**
     *
     */
    public function __construct($videoboxValue)
    {
        $this->videoboxValue = $videoboxValue;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('header_videobox_title', 'text', array(
                'data' => $this->videoboxValue['title'],
                'label' => 'Titre du bouton vidéo',
                'required' => false,
            ))
            ->add('header_videobox_url', 'text', array(
                'data' => $this->videoboxValue['url'],
                'label' => 'Lien du bouton vidéo',
                'required' => false,
            ))
            ->add('header_videobox_videolinkurl', 'text', array(
                'data' => $this->videoboxValue['videolinkurl'],
                'label' => 'Lien Youtube de la vidéo',
                'required' => false,
            ))
            ->add('header_videobox_videourl', 'text', array(
                'data' => $this->videoboxValue['videourl'],
                'label' => 'Lien Embed Youtube de la vidéo',
                'required' => false,
                'attr' => array(
                    'class'          => 'show-tooltip',
                    'data-placement' => 'top',
                    'data-toggle'    => 'tooltip',
                    'data-html'      => true,
                    'data-trigger'   => 'focus',
                    'data-title'     => 'Exemple: https://www.youtube.com/embed/ID-DE_LA_VIDEO?rel=0&amp;controls=0&amp;showinfo=0&amp;autoplay=1&amp;loop=1&amp;playlist=ID-DE_LA_VIDEO'
                )
            ))
            ->add('save', 'submit', array(
                'label' => 'Valider',
                'attr' => array('class' => 'btn btn-sc-red')
            ))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'service_civique_header_videobox';
    }

}
