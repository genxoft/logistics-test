<?php

namespace Tests\DeliveryService;

use App\DeliveryService\DummyComposer;
use App\DeliveryService\Item;
use App\DeliveryService\ItemInterface;
use Tests\TestCase;

class DummyComposerTest extends TestCase
{
    /**
     * @var ItemInterface[]
     */
    private array $items;

    protected function setUp(): void
    {
        parent::setUp();

        $this->items = $this->getMockItems();
    }

    public function testComposeWithMaxWeight()
    {
        $composer = new DummyComposer(1);
        $blocks = $composer->compose($this->items);

        $this->assertCount(3, $blocks);
    }

    public function testComposeWithoutMaxWeight()
    {
        $composer = new DummyComposer();
        $blocks = $composer->compose($this->items);

        $this->assertCount(2, $blocks);
    }

    /**
     * @return ItemInterface[]
     */
    public function getMockItems(): array
    {
        return [
            new Item("address1", "address2", 0.2),
            new Item("address1", "address2", 0.8),
            new Item("address1", "address2", 0.1),

            new Item("address3", "address4", 0.3),
            new Item("address3", "address4", 0.4),
        ];
    }
}
