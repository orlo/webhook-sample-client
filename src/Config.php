<?php
/**
 *
 */

namespace SocialSignIn\WebhookClient;


class Config
{

    /**
     * @var array
     */
    private $stash = [];

    /**
     * @param string $file
     */
    public function __construct($file)
    {
        if (is_file($file)) {
            $text = file_get_contents($file);
            $this->stash = json_decode($text, true);
        }
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed|null
     */
    public function get($name, $default = null)
    {
        if (isset($this->stash[$name])) {
            return $this->stash[$name];
        }
        return $default;
    }
}