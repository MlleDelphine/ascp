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
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfTokenManagerAdapter;

class UserLoginBlockService extends BaseBlockService
{

    protected $repository;
    protected $regionlocator;
    protected $formCsrfProvider;

    public function __construct($name, EngineInterface $templating, CsrfTokenManagerAdapter $formCsrfProvider)
    {
        $this->formCsrfProvider = $formCsrfProvider;
        parent::__construct($name, $templating);
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
        return 'Login';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'title'      => 'service_civique.user.block.login.title',
            'mission_id' => null,
            'region'     => false,
            'template'   => 'ServiceCiviqueWebBundle:Frontend/User/Block:login.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // Merge settings
        $settings = $blockContext->getSettings();

        $csrfToken = $this->formCsrfProvider->generateCsrfToken('authenticate');

        return $this->renderPrivateResponse($blockContext->getTemplate(), array(
            'csrf_token' => $csrfToken,
            'block'      => $blockContext->getBlock(),
            'settings'   => $settings
        ), $response);
    }

}
