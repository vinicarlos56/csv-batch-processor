<?php

namespace Helpers\CSV;

use Keboola\Csv\CsvFile as CsvFile;

class CsvHandler
{

    public function writeRow($csvFile,$row)
    {
        $file = new CsvFile($csvFile);
        $file->writeRow($row);
    }
    
}
    
