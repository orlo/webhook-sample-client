<?php

/**
 *
 */

namespace SocialSignIn\WebhookClient;

class Config
{
    private array $stash = [];

    public function __construct(string $file)
    {
        if (is_file($file)) {
            $text = file_get_contents($file);

            if (!is_string($text)) {
                throw new \InvalidArgumentException('Config file must contain a string');
            }
            $this->stash = json_decode($text, true, flags: JSON_THROW_ON_ERROR);
        }
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed|null
     */
    public function get(string $name, $default = null)
    {
        if (isset($this->stash[$name])) {
            return $this->stash[$name];
        }
        return $default;
    }
}
