<?php
declare(strict_types=1);

class HttpResponse
{
    /**
     * @var string
     */
    public $content = '';

    /**
     * @var string[]
     */
    public $headers = [];

    /**
     * @var int
     */
    public $code = 200;

    public static function redirect (string $location, bool $permanent = false)
    {
        $response = new static();

        $response->code = $permanent ? 301 : 302;
        $response->headers[] = 'Location: ' . $location;
        $response->content = "<a href='{$location}'>Redirecting...</a>";

        return $response;
    }
    
    public function send() {
        http_response_code($this->code);

        foreach ($this->headers as $header) {
            header($header);
        }

        echo $this->content;
    }
}