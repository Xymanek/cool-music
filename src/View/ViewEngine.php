<?php
declare(strict_types=1);

namespace View;

use LogicException;

class ViewEngine
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * @return ViewEngine
     */
    public static function getInstance (): ViewEngine
    {
        return static::$instance;
    }

    public static function init ()
    {
        static::$instance = new static();
    }

    public function render (string $template, array $vars = []): string
    {
        $renderData = new RenderInfo($template);
        $renderData->vars = $vars;

        return $this->renderInternal($renderData);
    }

    private function renderInternal (RenderInfo $renderInfo): string
    {
        if (!$this->templateExists($renderInfo->getTemplate())) {
            throw new LogicException("template {$renderInfo->getTemplate()} does not exist");
        }

        $templatePath = $this->getTemplatePath($renderInfo->getTemplate());

        $renderFunc = static function () use ($renderInfo, $templatePath) {
            ob_start();
            extract($renderInfo->vars, EXTR_SKIP);

            /** @noinspection PhpIncludeInspection */
            require $templatePath;

            $renderInfo->content = ob_get_clean();
        };
        $renderFunc();

        if ($renderInfo->parent !== null) {
            $parentInfo = new RenderInfo($renderInfo->parent);
            $parentInfo->childContent = $renderInfo->content;
            $parentInfo->childBlocks = $renderInfo->blocks;
            $parentInfo->vars = $renderInfo->parentVars;

            return $this->renderInternal($parentInfo);
        }

        return $renderInfo->content;
    }

    public function templateExists (string $template)
    {
        return file_exists($this->getTemplatePath($template));
    }

    private function getTemplatePath (string $template)
    {
        return TEMPLATES_FIR . '/' . $template;
    }
}