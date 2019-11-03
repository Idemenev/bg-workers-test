<?php

namespace App;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class AMQPAccountEvents
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    private $channel;

    /**
     * @var string
     */
    private $queuePrefix = '';

    /**
     * @var array
     */
    private $declaredQueues = [];

    /**
     * AMQPPublisher constructor.
     * @param AMQPStreamConnection $amqpStreamConnection
     */
    public function __construct(AMQPStreamConnection $amqpStreamConnection, string $queuePrefix)
    {
        $this->connection = $amqpStreamConnection;
        $this->channel = $this->connection->channel();
        $this->queuePrefix = $queuePrefix;
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }

    /**
     * @param int $accountId
     * @param array $events
     * @return AMQPAccountEvents
     */
    public function publish(int $accountId, array $events): self
    {
        $queueName = $this->declareQueue($accountId);

        $message = new AMQPMessage(json_encode($events), ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        // Как вариант, каждое событие публиковать отдельно
        $this->channel->basic_publish($message, '', $queueName);

        return $this;
    }

    /**
     * @param int $accountId
     * @param callable $callback
     * @return void
     * @throws \ErrorException
     */
    public function consume(int $accountId, callable $callback): void
    {
        $queueName = $this->declareQueue($accountId);

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($queueName, '', false, false, false, false, $callback);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    /**
     * @param AMQPMessage $message
     */
    public function sendAck(AMQPMessage $message): void
    {
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    }

    /**
     * @param int $accountId
     * @return string queue name
     */
    private function declareQueue(int $accountId): string
    {
        $queueName = $this->queuePrefix . $accountId;

        if (!in_array($queueName, $this->declaredQueues)) {

            $args = new AMQPTable(['x-single-active-consumer' => true]);
            $this->channel->queue_declare($queueName, false, true, false, false, false, $args);
        }

        return $queueName;
    }
}
