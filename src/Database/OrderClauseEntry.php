<?php
declare(strict_types=1);

namespace Database;

class OrderClauseEntry
{
    /**
     * @var string
     */
    public $column;

    /**
     * @var bool
     */
    public $doNotQuoteColumn;

    /**
     * @var bool
     */
    public $isDesc;

    public static function rand ()
    {
        $entry = new self();

        $entry->column = 'RAND()';
        $entry->doNotQuoteColumn = true;

        return $entry;
    }
}