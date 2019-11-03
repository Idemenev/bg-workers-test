<?php

const AMQP_HOST = 'localhost';
const AMQP_PORT = 5672;
const AMQP_LOGIN = 'guest';
const AMQP_PASSWORD = 'guest';

const QUEUE_PREFIX = 'account-';

const ACCOUNTS_COUNT = 1000;
const EVENTS_TOTAL_LIMIT = 10000;

const EVENTS_PER_BLOCK_MINIMUM = 1;
const EVENTS_PER_BLOCK_MAXIMUM = 5;

const LOG_PATH = __DIR__ . '/workers.log';