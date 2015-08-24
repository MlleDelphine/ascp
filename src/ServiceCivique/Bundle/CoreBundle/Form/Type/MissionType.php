<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonomyRepository;
use Departements\Provider;
use Symfony\Component\Routing\RouterInterface;

use ServiceCivique\Bundle\AddressingBundle\Form\Type\LocationType;
use Symfony\Component\Security\Core\SecurityContext;

use ServiceCivique\Bundle\CoreBundle\Entity\Mission;
use ServiceCivique\Bundle\CoreBundle\Entity\Organization;

use Symfony\Component\OptionsResolver\Options;

use Doctrine\ORM\EntityManager;
use ServiceCivique\Bundle\CoreBundle\Form\EventListener\CreateNewOrganizationSubscriber;
use ServiceCivique\Bundle\AddressingBundle\Form\EventListener\AlterDepartementFieldSubscriber;

class MissionType extends AbstractType
{

    protected $taxonomyRepository;
    protected $organizationRepository;
    protected $departementProvider;
    protected $router;
    protected $context;

    /**
     * __construct
     *
     * @param TaxonomyRepository $taxonomyRepository
     * @param EntityManager      $entityManager
     * @param Provider           $departementProvider
     * @param RouterInterface    $router
     * @param SecurityContext    $context
     */
    public function __construct(TaxonomyRepository $taxonomyRepository, EntityManager $entityManager, Provider $departementProvider, RouterInterface $router, SecurityContext $context)
    {
        $this->taxonomyRepository     = $taxonomyRepository;
        $this->entityManager          = $entityManager;
        $this->departementProvider    = $departementProvider;
        $this->router                 = $router;
        $this->context                = $context;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $taxonomy = $this->taxonomyRepository->findOneByName('thématique');
        $durationChoices = array();
        for ($i = 6; $i < 13; $i++) {
            $durationChoices[$i] = $i . ' mois';
        }
        $weeklyWorkingHoursChoices = array('Dérogation à la durée légale');
        for ($i = 24; $i <= 48; $i++) {
            $weeklyWorkingHoursChoices[$i] = $i . ' heures';
        }
        $weeklyWorkingHoursPreferredChoices = $weeklyWorkingHoursChoices;
        unset($weeklyWorkingHoursPreferredChoices[0]);
        $locationOptions = array(
            'inherit_data'       => true,
            'precision'          => LocationType::ADDRESS_PRECISION,
            'required_precision' => LocationType::CITY_PRECISION,
            'data'               => isset($options['data']) ? $options['data'] : null
        );

        $isOverseas = 0;
        if (isset($options['data']) && $options['data']->getIsOverseas()) {
            $isOverseas = $options['data']->getIsOverseas();
        }

        $builder
            ->add('title', null, array(
                'label' => 'service_civique.mission.form.title.label',
                'attr' => array(
                    'maxlength'      => 200,
                    'class'          => 'show-tooltip',
                    'data-placement' => 'top',
                    'data-toggle'    => 'tooltip',
                    'data-html'      => true,
                    'data-trigger'   => 'focus',
                    'data-title'     => 'Le nombre de caractères ne doit pas excéder 200. Nous vous conseillons de reprendre l\'intitulé qui apparait sur votre agrément (informations sur la droite)'
                )
            ))
            ->add('approvalNumber', 'service_civique_approval_number', array(
                'label' => 'service_civique.organization.form.approvalNumber.label',
                'attr' => array(
                    'data-placement' => 'top',
                    'data-toggle'    => 'tooltip',
                    'data-html'      => true,
                    'data-trigger'   => 'focus',
                    'data-title'     => 'Entrez ici votre numéro d’agrément. Si vous n’en disposez pas, référez-vous à la page <a tabindex="-1" href="http://www.service-civique.cirrus-cloud.net.fr/page/comment-obtenir-un-agrement">Comment obtenir un agrément</a>. Le numéro d’agrément doit être au format XX-000-00-00000-00. Il est composé de deux lettres, national (NA) ou régional (RG), puis de trois chiffres 000, 2 chiffres indiquant l\'année de la demande initiale, 5 chiffres de numéro de saisie et 2 chiffres indiquant les avenants.'
                )
            ))
            ->add('start_date', 'service_civique_date', array(
                'label' => 'service_civique.mission.form.start_date.label',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'required' => true,
                'attr' => array(
                    'class'          => 'show-tooltip',
                    'data-placement' => 'right',
                    'data-toggle'    => 'tooltip',
                    'data-html'      => true,
                    'data-trigger'   => 'focus',
                    'data-title'     => 'Il est nécessaire de prévoir au moins deux semaines entre la date de la mise en ligne de mission et sa date de début prévisionnelle, afin de vous permettre de recevoir suffisamment de candidatures.'
                )
            ))
            ->add('duration', 'choice', array(
                'label' => 'service_civique.mission.form.duration.label',
                'choices' => $durationChoices,
                'data' => isset($options['data']) ? $options['data']->getDuration() : 6,
            ))
            ->add('weeklyWorkingHours', 'choice', array(
                'label' => 'service_civique.mission.form.weeklyWorkingHours.label',
                'choices' => $weeklyWorkingHoursChoices,
                'data' => isset($options['data']) ? $options['data']->getWeeklyWorkingHours() : 24,
                'preferred_choices' => array_keys($weeklyWorkingHoursPreferredChoices),
            ))
            ->add('is_overseas', 'choice', array(
                'label'    => 'service_civique.mission.form.is_overseas.label',
                'choices'  => array(
                    1 => 'service_civique.mission.form.is_overseas.value_foreign',
                    0 => 'service_civique.mission.form.is_overseas.value_france'
                ),
                'required' => true,
                'expanded' => true,
                'data' => $isOverseas,
            ))
            ->add('location', 'location', $locationOptions)
            ->add('description', null, array(
                'label' => 'service_civique.mission.form.description.label',
                'attr' => array(
                    'rows'           => 26,
                    'class'          => 'show-tooltip',
                    'data-placement' => 'top',
                    'data-toggle'    => 'tooltip',
                    'data-html'      => true,
                    'data-trigger'   => 'focus',
                    'data-title'     => <<<HTML
<p>La description de la mission doit être suffisamment développée (minimum 10 lignes). Les missions ne remplissant pas ces critères devront être retravaillées en lien avec l'organisme agréé, les référents et l'Agence du Service Civique.</p>
    Veillez à ce que vos missions :
        <ul>
            <li>Ne correspondent pas à des emplois (chargé de communication, graphiste, community manager, éducateur sportif, surveillant scolaire, régisseur, ...)</li>
            <li>Ne consistent pas en des tâches logistiques ou administratives liées au fonctionnement courant de l’organisme d'accueil</li>
            <li>Favorisent les contacts avec le public bénéficiaire et proposent des actions sur le terrain</li>
            <li>N'exigent pas une qualification, un diplôme ou des expériences spécifiques</li>
            <li>Ne relèvent pas d'un encadrement en autonomie d’un groupe de mineurs</li>
        </ul>
HTML
                )
            ))
            ->add('taxon', 'sylius_taxon_choice', array(
                'label' => 'service_civique.mission.form.taxon.label',
                'multiple' => false,
                'taxonomy' => $taxonomy,
                'filter'   => null,
                'required' => true,
                'expanded' => false
            ))
            ->add('vacancies', 'text', array(
                'label' => 'service_civique.mission.form.vacancies.label',
            ))
            ->add('contact', null, array(
                'label' => 'service_civique.mission.form.contact.label',
                'attr' => array(
                    'class'          => 'show-tooltip',
                    'data-placement' => 'top',
                    'data-toggle'    => 'tooltip',
                    'data-html'      => true,
                    'data-trigger'   => 'focus',
                    'data-title'     => 'Indiquez le nom du contact qui sera en lien avec les volontaires.'
                )
            ))
            ->add('phoneNumber', 'tel', array(
                'label' => 'service_civique.mission.form.phoneNumber.label',
                'data_class' => null,
                'default_region' => 'FR',
                'required' => false,
                'invalid_message' => 'Ce numéro de téléphone est invalide',
                'attr' => array(
                    'class'          => 'show-tooltip',
                    'data-placement' => 'top',
                    'data-toggle'    => 'tooltip',
                    'data-html'      => true,
                    'data-trigger'   => 'focus',
                    'data-title'     => 'Indiquez un numéro de téléphone où les volontaires puissent vous joindre.'
                )
            ))
            ->add('duplicate', 'hidden', array())
            ->add('website', null, array(
                'label'      => 'service_civique.mission.form.website.label',
                'attr' => array(
                    'class'          => 'show-tooltip',
                    'data-placement' => 'top',
                    'data-toggle'    => 'tooltip',
                    'data-html'      => true,
                    'data-trigger'   => 'focus',
                    'data-title'     => 'Indiquez l\'adresse du site internet de l\'organisme.'
                )
            ))
            ->add('organizationDescription', null, array(
                'label' => 'service_civique.mission.form.organizationDescription.label',
                'required' => true,
                'attr'  => array(
                    'rows' => 15,
                    'class'          => 'show-tooltip',
                    'data-placement' => 'top',
                    'data-toggle'    => 'tooltip',
                    'data-html'      => true,
                    'data-trigger'   => 'focus',
                    'data-title'     => 'Décrivez en quelques lignes les activités de votre organisme.'
                )
            ))
            ->add('actions', 'form_actions');

        $builder->addEventSubscriber(new CreateNewOrganizationSubscriber(
            $this->context
        ));

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $mission = $event->getData();

                // if mission is new add saveAsDraft button
                if ($mission instanceof Mission && (!$mission->getId() || $mission->isDraft())) {
                    $form->get('actions')->add('saveAsDraft', 'submit', array(
                        'label' => 'service_civique.mission.form.save_as_draft.label',
                        'attr' => array('class' => 'btn btn-sc-red-2')
                    ));
                }

                $form->get('actions')->add('preview', 'submit', array(
                    'label' => 'service_civique.mission.form.validate.label',
                    'attr' => array('class' => 'btn btn-sc-red')
                ));
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                // if "En france" radio button is selected set country to 'FR'
                if (isset($data['is_overseas']) && !$data['is_overseas']) {
                    $data['location']['country'] = 'FR';
                }

                $event->setData($data);
            }
        );

        $builder->addEventSubscriber(new AlterDepartementFieldSubscriber($this->departementProvider, $locationOptions));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ServiceCivique\Bundle\CoreBundle\Entity\Mission'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'service_civique_mission';
    }
}
