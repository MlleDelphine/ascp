<?php

namespace ServiceCivique\Bundle\WebBundle\Menu;

use JMS\TranslationBundle\Annotation\Ignore;
use Knp\Menu\FactoryInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\TranslatorInterface;

use Gedmo\Tree\RepositoryInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Iterator\CurrentItemFilterIterator;

/**
 * Abstract menu builder.
 */
abstract class MenuBuilder
{
    /**
     * Menu factory.
     *
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * Security context.
     *
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * Translator instance.
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Request.
     *
     * @var Request
     */
    protected $request;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * menuProvider
     *
     * @var MenuProviderInterface
     */
    protected $menuProvider;

    /**
     * menuRepository
     *
     * @var MenuProviderInterface
     */
    protected $menuRepository;

    /**
     * menuItemRepository
     *
     * @var RepositoryInterface
     */
    protected $menuItemRepository;

    /**
     * __construct
     *
     * @param FactoryInterface         $factory
     * @param SecurityContextInterface $securityContext
     * @param TranslatorInterface      $translator
     * @param EventDispatcherInterface $eventDispatcher
     * @param MenuProviderInterface    $menuProvider
     * @param ObjectRepository         $menuRepository
     * @param RepositoryInterface      $menuItemRepository
     * @param MatcherInterface         $matcher
     */
    public function __construct(
        FactoryInterface $factory,
        SecurityContextInterface $securityContext,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        MenuProviderInterface $menuProvider,
        ObjectRepository $menuRepository,
        RepositoryInterface $menuItemRepository,
        MatcherInterface $matcher
    )
    {
        $this->factory            = $factory;
        $this->securityContext    = $securityContext;
        $this->translator         = $translator;
        $this->eventDispatcher    = $eventDispatcher;
        $this->menuProvider       = $menuProvider;
        $this->menuRepository     = $menuRepository;
        $this->menuItemRepository = $menuItemRepository;
        $this->matcher            = $matcher;
    }

    /**
     * Sets the request the service
     *
     * @param Request $request
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * Translate label.
     *
     * @param string $label
     * @param array  $parameters
     *
     * @return string
     */
    protected function translate($label, $parameters = array())
    {
        return $this->translator->trans(/** @Ignore */ $label, $parameters, 'menu');
    }

    /**
     * getCurrentMenuItem
     *
     * @param mixed $menu
     */
    protected function getCurrentMenuItem($menu)
    {
        $currentItem = null;

        $menuIterator = new \RecursiveIteratorIterator(
            new \Knp\Menu\Iterator\RecursiveItemIterator($menu),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $currentIterator = new CurrentItemFilterIterator($menuIterator, $this->matcher);

        foreach ($currentIterator as $item) {
            $currentItem = $item;
        }

        return $currentItem;
    }
}
