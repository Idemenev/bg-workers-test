<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\AMQPAccountEvents;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$options = getopt('a:');
$accountId = $options['a'] ?? null;

if (!is_numeric($accountId)) {
    throw new \Exception('Не верный id аккаунта');
}

$accountEvents = new AMQPAccountEvents(
    new AMQPStreamConnection(getenv('AMQP_HOST') ?: AMQP_HOST, AMQP_PORT, AMQP_LOGIN, AMQP_PASSWORD),
    QUEUE_PREFIX
);

$logger = new Logger('test');
$logger->pushHandler(new StreamHandler(LOG_PATH, Logger::INFO));

$accountEvents->consume($accountId, function(AMQPMessage $message) use ($accountEvents, $logger) {

    $events = json_decode($message->body);

    if (json_last_error() !== JSON_ERROR_NONE) { // php 7.3 has JSON_THROW_ON_ERROR
        throw new \Exception('invalid msg');
    }

    foreach ($events as $event) {
        sleep(1);

        $logger->info($event->accountName . ':pid ' . getmypid() . ' - ' . $event->sequenceNum);
    }
    $accountEvents->sendAck($message);
});
