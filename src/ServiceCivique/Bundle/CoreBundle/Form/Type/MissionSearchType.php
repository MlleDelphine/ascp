<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ServiceCivique\Bundle\CoreBundle\Entity\MissionSearch;
use ServiceCivique\Bundle\CoreBundle\Entity\Mission;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonomyRepository;
use Departements\Provider;
use Symfony\Component\Routing\RouterInterface;
use ServiceCivique\Bundle\AddressingBundle\Form\Type\LocationType;
use ServiceCivique\Bundle\AddressingBundle\Form\EventListener\AlterDepartementFieldSubscriber;

class MissionSearchType extends AbstractType
{

    protected $taxonomyRepository;
    protected $departementProvider;
    protected $router;

    /**
     * __construct
     *
     * @param TaxonomyRepository $taxonomyRepository
     * @param Provider           $departementProvider
     * @param RouterInterface    $router
     */
    public function __construct(TaxonomyRepository $taxonomyRepository, Provider $departementProvider, RouterInterface $router)
    {
        $this->taxonomyRepository  = $taxonomyRepository;
        $this->departementProvider = $departementProvider;
        $this->router = $router;

    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $taxonomy = $this->taxonomyRepository->findOneByName('thématique');

        $locationOptions =  array(
            'required'           => false,
            'inherit_data'       => true,
            'precision'          => LocationType::DEPARTMENT_PRECISION,
            'required_precision' => -1
        );

        $builder
            ->add('statuses', 'choice', array(
                'label'    => 'service_civique.form.mission_search.status',
                'required' => false,
                'choices'  => array(
                    Mission::STATUS_DRAFT            => 'service_civique.mission.status.0',
                    Mission::STATUS_UNDER_REVIEW     => 'service_civique.mission.status.3',
                    Mission::STATUS_UNDER_VALIDATION => 'service_civique.mission.status.4',
                    Mission::STATUS_AVAILABLE        => 'service_civique.mission.status.1',
                    Mission::STATUS_FILLED           => 'service_civique.mission.status.2',
                ),
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('organization', null, array(
                'label' => 'service_civique.form.mission_search.organization.label',
            ))
            ->add('approval_number', null, array(
                'label' => 'service_civique.form.mission_search.approval_number.label',
                'required' => false,
            ))
            ->add('tag', 'entity', array(
                'class'    => 'ServiceCivique\Bundle\CoreBundle\Entity\Tag',
                'property' => 'title',
                'required' => false,
                'empty_value' => 'Ne pas tenir compte des tags',
                'empty_data'  => null
            ))
            ->add('optionsTag','choice', array(
                'choices' => array('at_least' => 'Au moins un tag','no-tag' => 'Aucun tag'),
                'multiple' => false,
                'expanded' => true,
                'mapped' => false,
                'required' => false,
                'empty_value' => 'Recherche normale',
                'label' => 'Options des tags'))
            ->add('is_overseas', 'choice', array(
                'choices' => array(
                    0 => 'service_civique.form.mission_search.is_overseas.value_france',
                    1 => 'service_civique.form.mission_search.is_overseas.value_foreign',
                ),
                'label'    => 'service_civique.form.mission_search.is_overseas.label',
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('location', 'location', $locationOptions)
            ->add('start_date', 'service_civique_date', array(
                'label'    => 'service_civique.form.mission_search.start_date.label',
                'widget'   => 'single_text',
                'format'   => 'yyyy-MM-dd',
                'required' => false,
            ))
            ->add('published', 'service_civique_date', array(
                'label'    => 'service_civique.form.mission_search.published.label',
                'widget'   => 'single_text',
                'format'   => 'yyyy-MM-dd',
                'required' => false,
            ))
            ->add('query', 'text', array(
                'label'    => 'service_civique.form.mission_search.query.label',
                'required' => false,
                'attr'     => array(
                    'placeholder' => 'service_civique.form.mission_search.query.placeholder',
                    'class'       => 'advanced-search'
                )
            ))
            ->add('taxons', 'sylius_taxon_choice', array(
                'label'    => 'service_civique.form.mission_search.taxon.label',
                'multiple' => true,
                'taxonomy' => $taxonomy,
                'filter'   => null,
                'required' => false,
                'expanded' => true
            ))
            ->add('search', 'submit', array(
                'label' => 'service_civique.form.mission_search.search.label',
                'attr'  => array('class' => 'btn btn-sc-red btn-lg btn-block')
            ))
            ->add('save', 'submit', array(
                'label' => 'service_civique.form.mission_search.save.label',
                'attr'  => array('class' => 'btn btn-sc-red btn-lg')
            ));

        $builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'onPreSetDataAndPostSubmit'));
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetDataAndPostSubmit'));

        $builder->addEventSubscriber(new AlterDepartementFieldSubscriber($this->departementProvider, $locationOptions));
    }

    public function onPreSetDataAndPostSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if ($data->getIsOverseas() == null) {
            $data->setIsOverseas(false);
        }

        $event->setData($data);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ServiceCivique\Bundle\CoreBundle\Entity\MissionSearch',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'service_civique_mission_search';
    }
}
