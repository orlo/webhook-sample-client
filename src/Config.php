<?php
/**
 *
 */

namespace SocialSignIn\WebhookClient;


class Config
{

    private $stash = [];

    public function __construct($file)
    {
        if (is_file($file)) {
            $text = file_get_contents($file);
            $this->stash = json_decode($text, true);
        }
    }

    public function get($name, $default = null)
    {
        if (isset($this->stash[$name])) {
            return $this->stash[$name];
        }
        return $default;
    }
}