<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use App\Event;
use App\EventEmitter;
use App\AMQPAccountEvents;

// опусташаем перед каждым запуском
fclose(fopen(LOG_PATH,'w'));

$eventsEmitter = new EventEmitter(ACCOUNTS_COUNT, EVENTS_PER_BLOCK_MINIMUM, EVENTS_PER_BLOCK_MAXIMUM);

$eventsTotalCount = 0;

// эмулируем отдельные запросы с блоком данных к web API
while ($eventsTotalCount < EVENTS_TOTAL_LIMIT) {
    // События генерировать случайными блоками, содержащими последовательноси по 1-5 для каждого аккаунта.
    $eventsBlock = $eventsEmitter->getEventsBlock(Event::class);
    if (!$eventsBlockCount = count($eventsBlock)) {
        break;
    }
    $eventsTotalCount += $eventsBlockCount;

    $accountEvents = new AMQPAccountEvents(
        new AMQPStreamConnection(getenv('AMQP_HOST') ?: AMQP_HOST, AMQP_PORT, AMQP_LOGIN, AMQP_PASSWORD),
        QUEUE_PREFIX
    );
    foreach ($eventsBlock as $accountId => $events) {
        $accountEvents->publish($accountId, $events);
    }
}
