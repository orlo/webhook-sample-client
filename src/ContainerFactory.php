<?php

namespace SocialSignIn\WebhookClient;

use Monolog\Handler\StreamHandler;
use Psr\Log\NullLogger;
use Slim\Container;

class ContainerFactory
{
    public static function build()
    {
        $container = new Container(['settings' => ['displayErrorDetails' => true]]);

        if (!is_file(__DIR__ . '/../config.json')) {
            throw new \InvalidArgumentException("Config file: ROOT/config.json missing");
        }

        $config = new Config(__DIR__ . '/../config.json');

        $container['config'] = $config;

        $container['database'] = function () use ($config) {

            $pdo = new \PDO($config->get('database_dsn'), $config->get('database_user'), $config->get('database_pass'));
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return new Database($pdo);
        };

        $container['logger'] = function () {
            if (defined('PHP_UNIT')) {
                return new NullLogger();
            }

            $logger = new \Monolog\Logger('webhook-client');
            $logger->pushHandler(new StreamHandler('php://stderr'));
            return $logger;
        };

        return $container;
    }
}