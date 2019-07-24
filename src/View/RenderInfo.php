<?php
declare(strict_types=1);

namespace View;

use LogicException;

class RenderInfo
{
    /**
     * @var string
     */
    private $template;

    /**
     * @var string|null
     */
    public $parent = null;

    /**
     * @var array
     */
    public $vars = [];

    /**
     * @var array
     */
    public $parentVars = [];

    /**
     * @var string
     */
    public $content;

    /**
     * @var string|null
     */
    public $childContent;

    /**
     * Blocks declared in current template
     * @var callable[]
     */
    public $blocks = [];

    /**
     * Blocks passed from child template
     * @var callable[]
     */
    public $childBlocks;

    public function __construct (string $template)
    {
        $this->template = $template;
    }

    public function getTemplate (): string
    {
        return $this->template;
    }

    /**
     * A trick to allow use of {} and visually separate the code inside the block from rest of code
     * Also makes IDEs auto-indent block code
     *
     * @param string   $name
     * @param callable $renderFunc
     */
    public function prepareBlock (string $name, callable $renderFunc)
    {
        $this->blocks[$name] = $renderFunc;
    }

    public function renderBlock (string $name, bool $optional = true): string
    {
        if (isset($this->childBlocks[$name])) {
            ob_start();
            $this->childBlocks[$name]();
            return ob_get_clean();
        }

        if (!$optional) {
            throw new LogicException("Required block $name does not exist");
        }

        return '';
    }
}