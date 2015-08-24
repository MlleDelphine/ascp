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

use ServiceCivique\Bundle\MenuBundle\ContextResolver;
use ServiceCivique\Bundle\KeyValueStoreBundle\KeyValueStore;
use ServiceCivique\Bundle\KeyValueStoreBundle\Entity\KeyValue;

/**
 *
 * Provides a header block
 */
class HeaderBlockService extends BaseBlockService
{

    protected $repository;
    protected $contextResolver;
    protected $store;

    /**
     * __construct
     *
     * @param string           $name
     * @param EngineInterface  $templating
     * @param EntityRepository $repository
     * @param ContextResolver  $contextResolver
     * @param KeyValueStore    $keyValueStore
     */
    public function __construct(
        $name,
        EngineInterface $templating,
        EntityRepository $repository,
        ContextResolver $contextResolver,
        KeyValueStore $keyValueStore
    )
    {
        parent::__construct($name, $templating);
        $this->repository      = $repository;
        $this->contextResolver = $contextResolver;
        $this->store           = $keyValueStore;
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
        return 'Header';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'title'         => 'service_civique.header.block.header_block.title',
            'template'      => 'ServiceCiviqueWebBundle:Frontend/Header/Block:header_block.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // Merge settings
        $settings = $blockContext->getSettings();
        $vars = array(
            'block'     => $blockContext->getBlock(),
            'settings'  => $settings
        );
        if ($this->contextResolver->isHomePage()) {

            $videoBox = $this->store->get('header_videobox');

            $videoboxData = $videoBox instanceof KeyValue ? unserialize(stream_get_contents($videoBox->getDataValue())) : null;

            $vars['header'] = $this->repository->findOneRandom();
            $vars['videobox'] = $videoboxData;
        }

        return $this->renderPrivateResponse($blockContext->getTemplate(), $vars, $response);
    }

}
