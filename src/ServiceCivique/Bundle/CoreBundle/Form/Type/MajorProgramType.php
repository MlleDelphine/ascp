<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

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
            ->add('file', 'file', array('label' => 'Fichier', 'required' => false));

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                $count = count($this->repository->findAll());
                $count = ($count > 1 ? $count : 1);
                if (!$data->getId()) {
                    $count += 1;
                }
                $choices = range(1, $count);

                $form->add('position', 'choice', array(
                        'label' => 'Position',
                        'choices' => $choices,
                    )
                );

            }
        );

        $builder->add('actions', 'form_actions');

        $builder->add('save', 'submit', array(
            'label' => 'Valider',
            'attr' => array('class' => 'btn btn-sc-red'),
        ));
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
