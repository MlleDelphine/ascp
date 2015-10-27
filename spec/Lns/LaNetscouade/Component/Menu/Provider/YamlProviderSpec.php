<?php

namespace spec\Lns\Component\Menu\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Lns\Component\Menu\Factory\ItemFactoryInterface;
use Lns\Component\Menu\Model\ItemInterface;

class YamlProviderSpec extends ObjectBehavior
{
    protected $file;
    protected $items = array();
    protected $itemFactory;

    function let(ItemFactoryInterface $itemFactory, ItemInterface $item) {
        $this->itemFactory = $itemFactory;

        $items['foo'] = clone $item;
        $items['bar'] = clone $item;
        $items['baz'] = clone $item;

        $this->itemFactory->create(
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any()
        )->will(function($args) use($items) {
            return $items[$args[1]];
        });

        $this->items = $items;

        $this->file = __DIR__ . '/menu.yml';
        $this->beConstructedWith($this->file, $this->itemFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Lns\Component\Menu\Provider\YamlProvider');
        $this->shouldImplement('Lns\Component\Menu\Provider\ProviderInterface');
    }

    function it_should_return_menu_item_by_id(ItemInterface $item) {
        $this->get('foo')->shouldReturn($this->items['foo']);
        $this->get('bar')->shouldReturn($this->items['bar']);
        $this->get('baz')->shouldReturn($this->items['baz']);
    }

    function it_should_return_all_items(ItemInterface $item) {
        $this->all()->shouldReturn($this->items);
    }
}
