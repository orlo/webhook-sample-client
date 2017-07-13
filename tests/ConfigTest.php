<?php

class ConfigTest extends PHPUnit_Framework_TestCase
{

    public function testBasic()
    {
        $config = new \SocialSignIn\WebhookClient\Config(__DIR__ . '/../config.json');
        $this->assertNotEmpty($config->get('secret'));
    }

}
