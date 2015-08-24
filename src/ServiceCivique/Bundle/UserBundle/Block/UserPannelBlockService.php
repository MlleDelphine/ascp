<?php

namespace ServiceCivique\Bundle\UserBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\Response;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use ServiceCivique\Bundle\MenuBundle\ContextResolver;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UserPannelBlockService extends BaseBlockService
{
    protected $securityContext;
    protected $contextResolver;

    /**
     * __construct
     *
     * @param mixed                    $name
     * @param EngineInterface          $templating
     * @param SecurityContextInterface $SecurityContextInterface
     */
    public function __construct($name, EngineInterface $templating, SecurityContextInterface $securityContext, ContextResolver $contextResolver)
    {
        $this->securityContext = $securityContext;
        $this->contextResolver = $contextResolver;

        return parent::__construct($name, $templating);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'template' => 'ServiceCiviqueUserBundle:Block:block_user_pannel.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $block    = $blockContext->getBlock();
        $settings = array_merge($blockContext->getSettings(), $block->getSettings());

        $user_type = null;

        if ($this->securityContext->isGranted('ROLE_ORGANIZATION')) {
            $user_type = 'organization';
        } elseif ($this->securityContext->isGranted('ROLE_USER')) {
            $user_type = 'user';
        }

        return $this->renderResponse($blockContext->getTemplate(), array(
            'block'     => $blockContext->getBlock(),
            'settings'  => $settings,
            'user_type' => $user_type,
            'context'   => $this->contextResolver->getContext()
        ), $response);
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
        return 'User pannel';
    }
}
