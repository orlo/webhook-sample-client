<?php

namespace SocialSignIn\WebhookClient;

use Ramsey\Uuid\Uuid;
use SocialSignIn\WebhookClient\Model\Notification;

class Database
{

    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param int $companyId (optional)
     * @return array
     */
    public function getNotifications()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM notification ORDER BY created_ts DESC ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     */
    public function getNotification(Uuid $uuid)
    {

        $stmt = $this->pdo->prepare("SELECT * FROM notification WHERE webhook_uuid = ? ");

        $stmt->execute([$uuid->toString()]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param Notification $notification
     * @return boolean (whatever \PDOStatement->execute() returns)
     */
    public function saveNotification(Notification $n)
    {

        $sql = "INSERT INTO notification (webhook_uuid, content) VALUES (?, ?)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([$n->getWebHookUUID(), $n->getPayload()]);
    }

}
