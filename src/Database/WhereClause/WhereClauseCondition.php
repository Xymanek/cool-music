<?php
declare(strict_types=1);

namespace Database\WhereClause;

interface WhereClauseCondition
{
    public function build (array &$params): string;
}