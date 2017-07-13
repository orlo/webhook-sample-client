<?php

use Mockery as m;

class NotificationControllerTest extends PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    public function testGetSingleInvalidUUID()
    {

        $container = \SocialSignIn\WebhookClient\ContainerFactory::build();

        $notification = new \SocialSignIn\WebhookClient\Controllers\NotificationController($container);

        $request = m::mock(Slim\Http\Request::class);
        $response = m::mock(Slim\Http\Response::class);

        $response->shouldReceive('withStatus')->once()->withArgs([500, 'uuid not specified or valid.']);

        $realResponse = $notification->getSingle($request, $response, ['test-uuid']);
    }


    public function testListAll()
    {

        $container = \SocialSignIn\WebhookClient\ContainerFactory::build();

        $notification = new \SocialSignIn\WebhookClient\Controllers\NotificationController($container);

        $request = m::mock(Slim\Http\Request::class);
        $response = m::mock(Slim\Http\Response::class);

        $response->shouldReceive('withJson')->once()->withArgs(function ($body, $status) {
            $this->assertEquals(200, $status);
            $this->assertInternalType('array', $body);
            return true;
        });

        $realResponse = $notification->listAll($request, $response);
    }


    public function testReceiveNotification()
    {
        $container = \SocialSignIn\WebhookClient\ContainerFactory::build();

        $config = $container->get('config');

        $controller = new \SocialSignIn\WebhookClient\Controllers\NotificationController($container);

        $body = json_encode(['test' => 'message', 'hello' => 'world']);
        $hook_uuid = \Ramsey\Uuid\Uuid::uuid4();
        $hash_header = hash_hmac('sha256', $body, $container['config']->get('secret'));

        $notification = new \SocialSignIn\WebhookClient\Model\Notification($hook_uuid, $body, $hash_header);

        $request = m::mock(Slim\Http\Request::class);
        $response = m::mock(Slim\Http\Response::class);

        $request->shouldReceive('getMethod')->andReturn('post')->once();

        $request->shouldReceive('hasHeader')->twice()->andReturnTrue();

        $request->shouldReceive('getHeader')->once()->with('SocialSignIn-HookId')->andReturn([$hook_uuid]);
        $request->shouldReceive('getHeader')->once()->with('SocialSignIn-Hash')->andReturn([$hash_header]);
        $request->shouldReceive('getBody')->twice()->andReturn($body);


        $response->shouldReceive('withJson')->once()->withArgs(function ($body, $httpStatus) use (
            $notification,
            $config,
            $hash_header
        ) {
            $this->assertNotEmpty($body);
            $this->assertEquals($body['verification-hash'],
                $notification->generateVerificationHash($config->get('secret')));
            $this->assertNotEquals($body['verification-hash'], $hash_header);
            $this->assertEquals(200, $httpStatus);
            return true;
        })->andReturn($response);

        $controller->receiveWebHook($request, $response);

    }
}