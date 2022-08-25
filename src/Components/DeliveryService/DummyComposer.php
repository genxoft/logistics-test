<?php

namespace App\Components\DeliveryService;

/**
 * Это глупая реализация укладчика без решения задачи оптимальной укладки, но с ограничением по массе каждого блока
 * TODO: Добавить алгоритм "Knapsack Problem"
 */
class DummyComposer implements ComposerInterface
{
    public function __construct(
        private readonly ?float $maxWeight = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function compose(array $items): array
    {
        $blocks = $this->groupByFromTo($items);
        $result = [];
        foreach ($blocks as $block) {
            $subBlocks = $this->groupByWeight($block);
            foreach ($subBlocks as $items) {
                $result[] = new Composition($items);
            }

        }

        return $result;
    }

    /**
     * @internal
     * @param ItemInterface[] $items
     * @return ItemInterface[][]
     */
    private function groupByWeight(array $items): array
    {
        $blocks = [];
        $currentBlock = 0;
        $currentWeight = 0.0;
        foreach($items as $item) {
            if ($this->maxWeight !== null && ($currentWeight + $item->getWeight()) > $this->maxWeight) {
                $currentBlock++;
                $currentWeight = 0;
            }
            $currentWeight += $item->getWeight();
            $blocks[$currentBlock][] = $item;
        }
        return $blocks;

    }

    /**
     * @internal
     * @param ItemInterface[] $items
     * @return ItemInterface[][]
     */
    private function groupByFromTo(array $items): array
    {
        $blocks = [];
        foreach ($items as $item) {
            $blocks[$item->getFrom() . '_' . $item->getTo()][] = $item;
        }
        return array_values($blocks);
    }
}
