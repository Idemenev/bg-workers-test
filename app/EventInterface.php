<?php

namespace App;

interface EventInterface extends \JsonSerializable
{
    public function __construct(string $accountName, string $eventName, int $sequenceNum);
}
