<?php

namespace Test;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    public function testBasic()
    {
        $config = new \SocialSignIn\WebhookClient\Config(__DIR__ . '/../config.json');
        $this->assertNotEmpty($config->get('secret'));
    }
}
