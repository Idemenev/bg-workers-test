<?php

require_once __DIR__ . '/config.php';

function runWorker($workerPath, $accountId, $runInBackground)
{
    exec(
        sprintf('php %s -a %d %s', $workerPath, $accountId, $runInBackground ? '> /dev/null &' : '')
    );
}

$workerPath = __DIR__ . '/worker.php';

$limit = ACCOUNTS_COUNT - 1;
for ($i = 0; $i < $limit; ++$i) {
    runWorker($workerPath, $i, true);
}
echo 'workers started';
flush();
// meahh
runWorker($workerPath, $limit, false);
