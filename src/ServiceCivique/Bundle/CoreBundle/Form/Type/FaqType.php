<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Routing\RouterInterface;

class FaqType extends AbstractType
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
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'faqType' => 'volontaire'
        ));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fileName = 'uploads/faq-' . $options['faqType'] . '.yml';
        $builder
            ->add('body', 'textarea', array(
                'label' => 'service_civique.faq.form.body.label',
                'attr' => array(
                    'rows' => 26,
                    'cols' => 15,
                ),
                'data' => $this->getFaqContent($fileName)
            ))
            ->add('save', 'submit', array(
                'label' => 'Valider',
                'attr' => array('class' => 'btn btn-sc-red')
            ))
        ;

    }

    public function getFaqContent($fileName)
    {
        if (file_exists($fileName)) {
            return file_get_contents($fileName);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'service_civique_faq';
    }
}
