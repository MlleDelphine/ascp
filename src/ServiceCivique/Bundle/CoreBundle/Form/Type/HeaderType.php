<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Routing\RouterInterface;

class HeaderType extends AbstractType
{

    protected $router;

    /**
     * __construct
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', null, array(
                'label' => 'service_civique.header.form.title.label',
                'attr' => array(
                    'maxlength'      => 200,
                    'class'          => 'show-tooltip',
                    'data-placement' => 'top',
                    'data-toggle'    => 'tooltip',
                    'data-html'      => true,
                    'data-trigger'   => 'focus',
                    'data-title'     => 'Le nombre de caractères ne doit pas excéder 200'
                )
            ))
            ->add('pinImage', 'file', array(
                'label' => 'service_civique.header.form.pinImage.label',
                'data_class' => null,
                'required' => false,
            ))
            ->add('pinUrl', null, array(
                'label' => 'service_civique.header.form.pinUrl.label',
                'required' => false,
            ))
            ->add('image', 'file', array(
                'label' => 'service_civique.header.form.image.label',
                'data_class' => null,
                'required' => false,
            ))
            ->add('startDate', 'service_civique_date', array(
                'label' => 'service_civique.header.form.startDate.label',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime(),
                'required' => false,
            ))
            ->add('endDate', 'service_civique_date', array(
                'label' => 'service_civique.header.form.endDate.label',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'required' => false,
            ))
            ->add('actions', 'form_actions')
            ->add('save', 'submit', array(
                'label' => 'Valider',
                'attr' => array('class' => 'btn btn-sc-red')
            ))
        ;

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ServiceCivique\Bundle\CoreBundle\Entity\Header'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'service_civique_header';
    }
}
