<?php

namespace Lns\Bundle\MenuBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Lns\Bundle\MenuBundle\Form\DataTransformer\ArrayToYamlDataTransformer;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;

class MenuItemType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'lns.menu_item.form.name.label'
            ])
            ->add('url', null, [
                'label' => 'lns.menu_item.form.url.label'
            ])
            ;

        $dumper = new Dumper();
        $parser = new Parser();

        $this->addConfigField($dumper, $parser, $builder, 'options');

        $builder->add('save', 'submit', array(
            'label' => 'Valider',
            'attr'  => array('class' => 'btn btn-primary')
        ));
    }

    protected function addConfigField($dumper, $parser, $builder, $name)
    {
        return $builder->add(
            $builder->create($name, 'textarea', array(
                'attr' => array('rows' => 15),
                'label' => 'lns.menu_item.form.' . $name . '.label'
            ))
                ->addModelTransformer(new ArrayToYamlDataTransformer($parser, $dumper)
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array());
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'lns_menu_item';
    }
}
