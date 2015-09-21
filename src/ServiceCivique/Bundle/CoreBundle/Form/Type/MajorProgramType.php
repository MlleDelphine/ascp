<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use ServiceCivique\Bundle\CoreBundle\Entity\MajorProgram;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class MajorProgramType extends AbstractType
{
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('label' => 'Titre'))
            ->add('url', 'text', array('label' => 'URL'))
            ->add('icon','choice',array(
                'label' => 'Icône',
                'choices' => MajorProgram::getIcons()
            ))
            //->add('file', 'file', array('label' => 'Fichier', 'required' => false))
            ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                $count = $this->repository->getMajorProgramsCount();

                if (!$data->getId()) {
                    $count += 1;
                }

                $choices = array_combine(range(1, $count),range(1, $count));

                $form->add('position', 'choice', array(
                        'label' => 'Position',
                        'choices' => $choices,
                    )
                );

                $form->add('actions', 'form_actions');

                $form->add('save', 'submit', array(
                    'label' => 'Valider',
                    'attr' => array('class' => 'btn btn-sc-red'),
                ));

            }
        );

    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ServiceCivique\Bundle\CoreBundle\Entity\MajorProgram',
            'cascade_validation' => true,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_major_program';
    }
}
