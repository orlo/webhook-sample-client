# Web hook sample client

SocialSignIn can be configured to send your organisation webhook messages in near real time - allowing you to integrate your systems with Social Media.

## What's this?

A minimal application which could form the basis of a webhook client implementation. 

It is capable of accepting web hook notifications from SocialSignIn.

It :

 * Validates the webhook notification 
 * Stores the notification in a temporary database
 * Returns the correct response back to SocialSignIn
 

## QuickStart 

### Docker 

```docker build -t ssi-webhook-image . ```

```docker run --rm -p 80:80 --name webhook -t ssi-webhook-image ```


Unless this is running on a publically accessible server, you probably want to investigate using a service like 'ngrok' to provide a public URL to provide to SocialSignIn.

### Traditional Unix Host

Requirements :

 * PHP7 + Sqlite
 * Apache 
 
Copy the code to an appropriate Unix host; map /path/to/code/public to a document root (example Apache config is in provisioning/apache-host)

##  then ...

Register a webhook with SocialSignIn. You will need to provide SocialSignIn with the destination URL for the webhook, as well as specifying a shared secret. 

The shared secret can be a string of any length - up to 255 characters.

The destination_url can be up to 255 characters in length and should map to http://your-server/notify


## config.json 

Edit config.json to specify the 'secret' key - this needs to match what you provide to SocialSignIn.

Optionally, change the database settings (e.g. to use MySQL).

Finally, run 'php setup.php' if you're sticking with the default temporary sqlite database.


# Webhook Specific Documentation 

When registering for a webhook with SocialSignIn, you provide :

  * dev_email (string, email address)
  * secret (string, shared secret)
  * destination_url (string, where notifications are sent to)
  * name (string, something meaningful to you)
 
 When SocialSignIn sends webhook notifications to your destination_url, the request to your destination url will look like :
 
 ```
SocialSignIn-HookId: <some uuid>
SocialSignIn-Hash: <some sha256 string>
  
  
{ .... some json payload here .... }
```

Where SocialSignIn-Hash is a sha256 hash_hmac of the body and the shared secret

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


Upon receiving the notification you must reply with a hash of that hash ... (so the equivlaent of :

```php 
hash_hmac('sha256', 'e123f721ae689ad2a06b5eed0838238f27e5c71d2159c258e73c651c98f17bdf', 'testsecret' );
```

(i.e. ```70d57128a1047c499ce30b3d518341aa4fc9e9200e30d9bb9152f99955ba081d```)


this confirms to SocialSignIn that the notification was successfully received by the correct recipient.


If you wish to answer multiple webhooks from one endpoint, you would need to use a URL parameter (e.g destination_url?hookId=2)) to differentiate between them.
