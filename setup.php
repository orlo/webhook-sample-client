<?php

require_once __DIR__ . '/vendor/autoload.php';

/*
 * This will create a 'default' empty database (perhaps: /var/tmp/webhook_client.sqlite)
 * Otherwise it suggests a schema to use.
 */
$config = new \SocialSignIn\WebhookClient\Config(__DIR__ . '/config.json');

$dsn = $config->get('database_dsn');

$bits = parse_url($dsn);

if ($bits['scheme'] != 'sqlite') {
    echo "Create a table called 'notification' on your database server/platform of choice. It needs :
    - id autoincrement,
    - webhook_uuid varchar(36) Not null,
    - content text not null
    - created_ts TIMESTAMP 
    ";
    exit(1);
}
$path = $bits['path'];

if (file_exists($path)) {
    unlink($path);
}

$db = new PDO($dsn);

$db->exec("CREATE TABLE notification ( 
  id INTEGER PRIMARY KEY,
  webhook_uuid VARCHAR(36), 
  content TEXT NOT NULL, 
  created_ts DATE DEFAULT (datetime('now', 'localtime')) 
  )");

