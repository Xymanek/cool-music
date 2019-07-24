<?php
declare(strict_types=1);

use Action\BaseAction;
use Action\NotFoundException;

class Router
{
    const ASSETS_PREFIX = 'assets/';

    /**
     * @var self
     */
    private static $instance;

    /**
     * @var string
     */
    private $globalPrefix;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string[]
     */
    private $pathParts;

    /**
     * @var BaseAction[]
     */
    public $actions;

    /**
     * @var BaseAction
     */
    public $notFoundAction;

    public static function init ()
    {
        self::$instance = new self();
    }

    public function __construct ()
    {
        $this->setupGlobalPrefix();
        $this->parseRequest();
    }

    private function setupGlobalPrefix ()
    {
        $this->globalPrefix = $_SERVER['CONTEXT_PREFIX'];

        if (empty($this->globalPrefix)) {
            $this->globalPrefix = '/';
        } elseif ($this->globalPrefix[strlen($this->globalPrefix) - 1] !== '/') {
            // Ensure it always ends with a slash
            $this->globalPrefix .= '/';
        }
    }

    /*
     * Note that this function can be optimized using regex but I'm not great at it and this way is more clear
     */
    private function parseRequest ()
    {
        // Get the path we want to work with
        $this->path = Utilities::removeFromStringStart($_SERVER['REQUEST_URI'], $this->globalPrefix);

        // Split on question mark - everything past it isn't relevant for routing
        $pathSplitOnQuestion = explode('?', $this->path, 2);

        // Split on slash - this is the easiest way to make a human-friendly URL system
        $this->pathParts = explode('/', $pathSplitOnQuestion[0]);

        // Remove empty parts - this can happen if use types something//something - we should consider that as 1 slash
        $this->pathParts = array_filter($this->pathParts, function ($part) {
            return $part !== '';
        });

        // Remove the gaps in the array so it becomes 0->(n-1)
        // Otherwise it's consider a map and some things break
        $this->pathParts = array_values($this->pathParts);

        // If we were building a more serious router/framework, we would also handle query params here
        // alas we are not and query params can be fetched from $_GET[]
    }

    public function dispatchRequest () : HttpResponse
    {
        try {
            foreach ($this->actions as $action) {
                if ($action->matchRoute($this->pathParts)) {
                    $pareExecuteResponse = $action->preExecute();

                    if ($pareExecuteResponse instanceof HttpResponse) {
                        return $pareExecuteResponse;
                    }

                    return $action->execute();
                    break;
                }
            }
        }
        /** @noinspection PhpRedundantCatchClauseInspection */
        catch (NotFoundException $e) {
            // Fall through to the return
        }

        return $this->notFoundAction->execute();
    }

    /**
     * Guaranteed to end with a slash
     * @return string
     */
    public function getGlobalPrefix (): string
    {
        return $this->globalPrefix;
    }

    /**
     * @return string[]
     */
    public function getPathParts (): array
    {
        return $this->pathParts;
    }

    /**
     * @return Router
     */
    public static function getInstance (): Router
    {
        if (self::$instance === null) {
            throw new LogicException(self::class . " is not initialized");
        }

        return self::$instance;
    }
}