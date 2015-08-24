<?php

namespace ServiceCivique\Bundle\WebBundle\Block;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use ServiceCivique\Bundle\CoreBundle\Service\RegionLocator;

/**
 *
 * Provides a recommended missions block
 */
class RecommendedMissionsBlockService extends BaseBlockService
{

    protected $repository;
    protected $regionlocator;

    public function __construct($name, EngineInterface $templating, EntityRepository $repository, RegionLocator $regionLocator)
    {
        parent::__construct($name, $templating);
        $this->repository    = $repository;
        $this->regionLocator = $regionLocator;
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
        return 'Recommended Missions';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'title'         => 'service_civique.mission.block.recommended_missions.title',
            'mission_id'    => null,
            'mission_limit' => 3,
            'region'        => false,
            'view_mode'     => 'short',
            'template'      => 'ServiceCiviqueWebBundle:Frontend/Mission/Block:recommended_missions.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // Merge settings
        $settings = $blockContext->getSettings();

        $missions = array();
        $queryParams = array();
        $regionCode = null;

        if ($region = $this->regionLocator->getRegion()) {
            $regionCode = $region->getCode();
            $settings['title'] = 'service_civique.mission.block.recommended_missions.geocoded_title';
            $settings['region'] = $region;
        }

        $missions = $this->repository->findRecommended($regionCode, $settings['mission_id'], 3);

        return $this->renderPrivateResponse($blockContext->getTemplate(), array(
            'missions'  => $missions,
            'block'     => $blockContext->getBlock(),
            'settings'  => $settings
        ), $response);
    }

}
