<?php

namespace Helpers;

class ProcessLocker
{
    private $lockFile;
    
    function __construct($lockFile)
    {
        $this->lockFile = $lockFile;
    }

    public function lockProcess()
    {
        file_put_contents($this->lockFile,'1');
    }

    public function isLocked()
    {
        return (file_exists($this->lockFile) and (bool) file_get_contents($this->lockFile));
    }

    public function unlockProcess()
    {
        file_put_contents($this->lockFile,'0');
    }
}

