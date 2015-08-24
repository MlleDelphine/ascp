<?php

namespace ServiceCivique\Bundle\WebBundle\Block;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

use ServiceCivique\Bundle\CoreBundle\Entity\MissionSearch;

use Symfony\Component\Form\FormFactory;

/**
 *
 * Provides a form to search missions
 */
class MissionSearchBlockService extends BaseBlockService
{
    protected $formFactory;
    protected $request;
    protected $searchFilterService;
    protected $missionSearchRepository;

    public function __construct($name, EngineInterface $templating, FormFactory $formFactory, $searchFilterService, Request $request, $missionSearchRepository)
    {
        parent::__construct($name, $templating);
        $this->formFactory             = $formFactory;
        $this->request                 = $request;
        $this->searchFilterService     = $searchFilterService;
        $this->missionSearchRepository = $missionSearchRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'service_civique.block.mission_search';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'title'                 => 'service_civique.mission.block.search.title',
            'template'              => 'ServiceCiviqueWebBundle:Block:block_mission_search.html.twig',
            'vacancies'             => null,
            'nbResults'             => null,
            'search_action'         => '',
            'search_options_action' => '',
            'criteria'              => array(),
            'display'               => array(
                'form' => true,
                'options' => true,
                'filters' => true
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();

        $criteria = $settings['criteria'];
        $missionSearch = $this->createNewMissionSearch($criteria);

        $form = $this->formFactory->createNamed('criteria', 'service_civique_mission_search', $missionSearch, array(
            'action'          => $settings['search_action'],
            'method'          => 'POST',
            'csrf_protection' => false,
        ));

        $sortForm = $this->formFactory->createNamed(
            '',
            'service_civique_mission_search_options',
            array(
                'paginate' => $this->request->query->get('paginate', 12),
                'sorting' => key($this->request->query->get('sorting', array('start_date' => 'asc')))
            ),
            array(
                'action'          => $settings['search_options_action'],
                'method'          => 'POST',
                'csrf_protection' => false,
            )
        );

        $activeFilters = $this->searchFilterService->getMissionSearchActiveFilters($missionSearch, $criteria);

        return $this->renderPrivateResponse($blockContext->getTemplate(), array(
            'block'          => $blockContext->getBlock(),
            'settings'       => $settings,
            'form'           => $form->createView(),
            'is_advanced'    => $missionSearch->isAdvanced(),
            'search_options' => $activeFilters,
            'sortForm'       => $sortForm->createView()
        ), $response);
    }

    protected function createNewMissionSearch($criteria)
    {
        return $this->missionSearchRepository->createNewFromArray($criteria);
    }
}
