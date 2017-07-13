<?php

namespace SocialSignIn\WebHookService;

require_once __DIR__ . '/../vendor/autoload.php';

use Slim\App;
use SocialSignIn\WebhookClient\ContainerFactory;
use SocialSignIn\WebhookClient\Controllers\NotificationController;


$container = ContainerFactory::build(); // logging, database, config.

$app = new App($container);


$app->get('/', NotificationController::class . ':listAll');

$app->post('/notification', NotificationController::class . ':receiveWebHook');

$app->get('/notification/{uuid}', NotificationController::class . ':getSingle');

$app->run();
