<?php

require_once __DIR__.'/../vendor/autoload.php'; // load composer

use Symfony\Component\Process\PhpProcess;

$process = new PhpProcess(<<<EOF
    <?php echo 'Hello World'; ?>
EOF
);
$process->run();

echo $process->getOutput();

