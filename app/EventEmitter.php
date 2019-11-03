<?php

namespace App;

class EventEmitter
{
    /**
     * @var int Кол-во аккаунтов
     */
    private $accountsCount;

    /**
     * @var int Минимальное кол-во генереруемых событий для каждого аккаунта
     */
    private $eventsMinimum;

    /**
     * @var int Максимальное кол-во генереруемых событий для каждого аккаунта
     */
    private $eventsMaximum;

    /**
     * @var array
     */
    private $accountSequences = [];

    /**
     * Producer constructor.
     * @param int $accountsCount Кол-во аккаунтов
     * @param int $eventsMinimum Минимальное кол-во генереруемых событий
     * @param int $eventsMaximum Максимальное кол-во генереруемых событий
     */
    public function __construct(int $accountsCount, int $eventsMinimum, int $eventsMaximum)
    {
        $this->accountsCount = abs($accountsCount);
        $this->eventsMinimum = abs($eventsMinimum);
        $this->eventsMaximum = abs($eventsMaximum);
    }

    /**
     * События генерировать случайными блоками, содержащими последовательноси по 1-5 для каждого аккаунта
     *
     * @param string $className Имя класса, реализующего EventInterface
     * @return [accountId => [events...]]
     */
    public function getEventsBlock(string $className): array
    {
        if (!is_a($className, 'App\EventInterface', true)) {
            throw new \Exception(sprintf('Класс "%s" должен реализовывать интерфейс EventInterface', $className));
        }

        $ret = [];
        for ($accountId = 0; $accountId < $this->accountsCount; ++$accountId) {
            if (!isset($this->accountSequences[$accountId])) {
                $this->accountSequences[$accountId] = 0;
            }
            $limit = mt_rand($this->eventsMinimum, $this->eventsMaximum);
            for ($eventI = 0; $eventI < $limit; ++$eventI) {
                $sequenceNum = $this->accountSequences[$accountId]++;
                $event = new $className('account-' . $accountId, 'event-' . $sequenceNum, $sequenceNum);
                $ret[$accountId][] = $event;
            }
        }
        return $ret;
    }
}
