<?php

use Mockery as m;

class NotificationTest extends PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        m::close();
    }

    public function testBasic()
    {

        $secret = 'abc12345';

        $payload = json_encode([
            'something' => 'else',
            'test' => ['lorem', 'ipsum', uniqid()]
        ]);


        $body_hash = hash_hmac('sha256', $payload, $secret);


        $verification_header = hash_hmac('sha256', $body_hash, $secret);


        $uuid = \Ramsey\Uuid\Uuid::uuid4(); // random.

        $notification = new \SocialSignIn\WebhookClient\Model\Notification($uuid, $payload, $body_hash);

        $this->assertTrue($notification->isValid($secret));

    }

    public function testCreateFromHttpRequest()
    {

        $hook_uuid = \Ramsey\Uuid\Uuid::uuid4();

        $body = json_encode([
            'data' => 'test'
        ]);

        $shared_secret = 'abc12345';

        $hash_header = hash_hmac('sha256', $body, $shared_secret);

        $req = m::mock(\Slim\Http\Request::class);
        $req->shouldReceive('hasHeader')->twice()->andReturnTrue();

        $req->shouldReceive('getHeader')->once()->with('SocialSignIn-HookId')->andReturn([$hook_uuid]);
        $req->shouldReceive('getHeader')->once()->with('SocialSignIn-Hash')->andReturn([$hash_header]);
        $req->shouldReceive('getBody')->once()->andReturn($body);

        $notification = \SocialSignIn\WebhookClient\Model\Notification::createFromHttpRequest($req);

        $this->assertTrue($notification->isValid($shared_secret));

        $this->assertEquals($hook_uuid, $notification->getWebHookUUID());
        $this->assertEquals($body, $notification->getPayload());
    }
}
