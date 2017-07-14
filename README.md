# Web hook sample client

SocialSignIn can be configured to send your organisation webhook messages in near real time - allowing you to integrate your systems with Social Media.

## What's this?

A minimal application which could form the basis of a webhook client implementation. 

It is capable of accepting web hook notifications from SocialSignIn.

It :

 * Validates the webhook notification 
 * Stores the notification in a temporary database
 * Returns the correct response back to SocialSignIn
 

# QuickStart 

See [Install Docs](install.md)

# Webhook Specific Documentation 

When registering for a webhook with SocialSignIn, you provide :

  * dev\_email (string, email address - this is so we can contact you in the event of there being problems )
  * secret (string, shared secret - used for message validation and acknowledgement)
  * destination\_url (string, where notifications are sent to, e.g. https://my.superserver.com/webhooks )
  * name (string, something meaningful to you)
 
 When SocialSignIn sends webhook notifications to your destination\_url, the HTTP POST request to your destination url will look like the following :
 
 ```
Content-Type: application/json
SocialSignIn-HookId: 2fef7b90-0086-424d-9275-2719c9f72d43
SocialSignIn-Hash: e123f721ae689ad2a06b5eed0838238f27e5c71d2159c258e73c651c98f17bdf
  
{ .... some json payload here .... }
```
Where SocialSignIn-Hash is a sha256 hash\_hmac of the body and the shared secret

You should return something like the following to notify SocialSignIn of successful receipt.

```json
{
 "verification-hash": "sha256string"
}
```

Where 'sha256string' is a hmac hash using sha256 of the SocialSignIn-Hash and the subscription secret.


# Hash Examples

Assuming the shared **secret** is : ```testsecret```
 
For a **payload** of :

```{"test":"data"}```

SocialSignIn would generate a hash of :

```e123f721ae689ad2a06b5eed0838238f27e5c71d2159c258e73c651c98f17bdf```


This value will be provided to you in the **SocialSignIn-Hash** HTTP header on the received notification at your ***destination_url***

Upon receiving the notification you must reply with a hash of that hash ... like the equivalent of the following PHP code :

```php 
hash_hmac('sha256', 'e123f721ae689ad2a06b5eed0838238f27e5c71d2159c258e73c651c98f17bdf', 'testsecret' );
```

(i.e. ```70d57128a1047c499ce30b3d518341aa4fc9e9200e30d9bb9152f99955ba081d```)


this confirms to SocialSignIn that the notification was successfully received by the correct recipient.

If you wish to answer multiple webhooks from one endpoint, you would need to use a URL parameter (e.g destination_url?hookId=2)) to differentiate between them.
