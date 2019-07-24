<?php
declare(strict_types=1);

namespace View;

class MenuItem
{
    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $url;

    /**
     * @var bool
     */
    public $current;

    /**
     * @var string|null
     */
    public $colour;

    public function __construct (string $label, string $url, bool $current, $colour = null)
    {
        $this->label = $label;
        $this->url = $url;
        $this->current = $current;
        $this->colour = $colour;
    }
}