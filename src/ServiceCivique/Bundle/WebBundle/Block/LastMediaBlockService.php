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
 * Provides a recommended actu block
 */
class LastMediaBlockService extends BaseBlockService
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
        return 'Media';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'title'        => 'service_civique.actu.block.last_media.title',
            'template'     => 'ServiceCiviqueWebBundle:Frontend/Media/Block:last.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // Merge settings
        $settings = $blockContext->getSettings();

        $lastMedias = $this->repository->findLast();

        return $this->renderPrivateResponse($blockContext->getTemplate(), array(
            'medias'   => $lastMedias,
            'block'    => $blockContext->getBlock(),
            'settings' => $settings
        ), $response);
    }

}
