<?php
declare(strict_types=1);

namespace Database\WhereClause;

class OrCombination implements WhereClauseCondition
{
    /**
     * @var WhereClauseCondition[]
     */
    public $nestedConditions;

    /**
     * @param WhereClauseCondition[] $nestedConditions
     * @return OrCombination
     */
    public static function fromConditions (array $nestedConditions): self
    {
        $orCombination = new self();
        $orCombination->nestedConditions = $nestedConditions;

        return $orCombination;
    }

    public function build (array &$params): string
    {
        $parts = [];

        foreach ($this->nestedConditions as $condition) {
            $parts[] = $condition->build($params);
        }

        return '(' . implode(' OR ', $parts) . ')';
    }
}