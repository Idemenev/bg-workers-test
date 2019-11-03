<?php

namespace App;

class Event implements EventInterface
{
    /**
     * @var int
     */
    private $sequenceNum;

    /**
     * @var string
     */
    private $accountName;

    /**
     * @var string
     */
    private $eventName;

    public function __construct(string $accountName, string $eventName, int $sequenceNum)
    {
        $this->sequenceNum = $sequenceNum;
        $this->accountName = $accountName;
        $this->eventName = $eventName;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return [
            'sequenceNum' => $this->sequenceNum,
            'accountName' => $this->accountName,
            'eventName' => $this->eventName,
        ];
    }
}
