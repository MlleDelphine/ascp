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

/**
 *
 * Provides a recommended video block
 */
class LastVideoBlockService extends BaseBlockService
{

    protected $repository;

    public function __construct($name, EngineInterface $templating, EntityRepository $repository)
    {
        parent::__construct($name, $templating);
        $this->repository    = $repository;
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
        return 'Video';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'title'        => 'service_civique.block.last_video.title',
            'template'     => 'ServiceCiviqueWebBundle:Frontend/Video/Block:last.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // Merge settings
        $settings = $blockContext->getSettings();

        $lastVideo = $this->repository->findLast();

        return $this->renderPrivateResponse($blockContext->getTemplate(), array(
            'video'    => $lastVideo,
            'block'    => $blockContext->getBlock(),
            'settings' => $settings
        ), $response);
    }

}
