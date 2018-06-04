<?php

namespace SocialSignIn\WebhookClient\Controllers;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Ramsey\Uuid\Uuid;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use SocialSignIn\WebhookClient\Config;
use SocialSignIn\WebhookClient\Database;


class NotificationController
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Database
     */
    private $database;

    /**
     * @var Config
     */
    private $config;


    public function __construct(Container $container)
    {
        $this->logger = $container->has('logger') ? $container->get('logger') : new NullLogger();

        $this->database = $container->get('database');

        $this->config = $container->get('config');

    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args - url args from routing
     * @return Response
     */
    public function getSingle(Request $request, Response $response, $args)
    {
        if (empty($args) || !isset($args['uuid']) || !Uuid::isValid($args['uuid'])) {
            return $response->withStatus(500, 'uuid not specified or valid.');
        }

        $notification = $this->database->getNotification($args['uuid']);

        if (empty($notification)) {
            return $response->withStatus(404, 'Notification not found');
        }

        return $response->withJson($notification, 200);

    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function listAll(Request $request, Response $response)
    {
        $list = $this->database->getNotifications();

        return $response->withJson($list, 200);
    }

    /**
     *
     * Called when SocialSignIn sends a webhook notification to us.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     *
     * Must be a POST request.
     *
     */
    public function receiveWebHook(Request $request, Response $response)
    {

        if (strtolower($request->getMethod()) !== 'post') {
            throw new \InvalidArgumentException("Post required.");
        }

        $notification = \SocialSignIn\WebhookClient\Model\Notification::createFromHttpRequest($request);


        $body = "" . $request->getBody();

        $this->logger->info("Received web hook notification", [$body]);

        $shared_secret = $this->config->get('secret');


        if ($notification->isValid($shared_secret)) { // is it really from SocialSignIn ?
            $this->logger->info("Saving notification to db.");
            $this->database->saveNotification($notification);
            $this->logger->info("Sending verification-hash acknowledgement.");

            return $response->withJson([
                'verification-hash' => $notification->generateVerificationHash($shared_secret),
            ], 200);
        }

        return $response->withJson(['error' => 'hash mismatch', $notification], 400); // bad request
    }

}
