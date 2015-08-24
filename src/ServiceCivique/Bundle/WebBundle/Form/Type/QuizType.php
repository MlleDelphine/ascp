<?php

namespace ServiceCivique\Bundle\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuizType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('first_question', 'choice', array(
            'label'     => '1- La mission que vous souhaitez proposer relève-t-elle d’une mission d’intérêt général ?',
            'choices'   => array(true => 'Oui', false => 'Non'),
            'expanded'  => true
        ))->add('second_question', 'choice', array(
            'label'     => '2- Attendez-vous du volontaire d’avoir des compétences particulières ?',
            'choices'   => array(true => 'Oui', false => 'Non'),
            'expanded'  => true
        ))->add('third_question', 'choice', array(
            'label'     => '3- Allez-vous confier au volontaire des tâches administratives ou relatives au fonctionnement courant de votre structure (secrétariat, accueil, communication, gestion du budget ou des demandes de subventions etc.) ?',
            'choices'   => array(true => 'Oui', false => 'Non'),
            'expanded'  => true
        ))->add('fourth_question', 'choice', array(
            'label'     => '4- La mission que vous voulez proposer s’effectue-t-elle au contact du public ?',
            'choices'   => array(true => 'Oui', false => 'Non'),
            'expanded'  => true
        ))->add('fifth_question', 'choice', array(
            'label'     => '5- La mission que vous proposez doit-elle vous permettre de remplacer un poste vacant (congé maternité, départ etc.) au sein de votre organisme ?',
            'choices'   => array(true => 'Oui', false => 'Non'),
            'expanded'  => true
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
        return 'service_civique_quiz';
    }
}
