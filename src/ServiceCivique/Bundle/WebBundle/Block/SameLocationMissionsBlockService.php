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
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 *
 * Provides a recommended missions block
 */
class SameLocationMissionsBlockService extends BaseBlockService
{

    protected $repository;

    public function __construct($name, EngineInterface $templating, EntityRepository $repository, Request $request)
    {
        parent::__construct($name, $templating);
        $this->repository    = $repository;
        $this->request       = $request;
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
        return 'Same Location Missions';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'title'        => 'service_civique.mission.block.same_location_missions.title',
            'mission_id'   => null,
            'mission_area' => false,
            'mission_isoverseas' => false,
            'template'     => 'ServiceCiviqueWebBundle:Frontend/Mission/Block:same_location_missions.html.twig',
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

        if ($settings['mission_isoverseas']) {
            $settings['title'] = 'service_civique.mission.block.same_location_missions.foreign_title';
        }
        $missions = $this->repository->findRecommended($settings['mission_area'], $settings['mission_id'], 2, $settings['mission_isoverseas']);

        return $this->renderPrivateResponse($blockContext->getTemplate(), array(
            'missions'  => $missions,
            'block'     => $blockContext->getBlock(),
            'settings'  => $settings
        ), $response);
    }

}
