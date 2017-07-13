# Installation Guide 

### Docker 

```docker build -t ssi-webhook-image . ```

```docker run --rm -p 80:80 --name webhook -t ssi-webhook-image ```


Unless this is running on a publicaly accessible server, you probably want to investigate using a service like 'ngrok' to provide a public URL to provide to SocialSignIn.

### Traditional Unix Host

Requirements :

 * PHP7 + Sqlite (or another database)
 * Apache (otherwise, recreate the equivalent of what's in web/.htaccess)
 
Copy the code to an appropriate Unix host; map /path/to/code/public to a document root (example Apache config is in provisioning/apache-host)

Run ***setup.php*** to create a temporary Sqlite database in /var/tmp; You'll probably need to 

```bash
chown www-data /var/tmp/webhook_client.sqlite
```

##  then ...

Register a webhook with SocialSignIn. You will need to provide SocialSignIn with the destination URL for the webhook, as well as specifying a shared secret. 

The shared secret can be a string of any length - up to 255 characters.

The destination\_url can be up to 255 characters in length and should map to http://your-server/notify


## config.json 

Edit config.json to specify the 'secret' key - this needs to match what you provide to SocialSignIn.

Optionally, change the database settings (e.g. to use MySQL).

Finally, run 'php setup.php' if you're sticking with the default temporary sqlite database.

