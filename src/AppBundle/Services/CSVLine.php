<?php

namespace AppBundle\Services;

class CSVLine
{
    private $line;

    public function __construct($head, $line)
    {
        $this->line = array_combine($head, $line);
    }

    public function get($field)
    {
        if (isset($this->line[$field])) {
            return trim($this->line[$field]);
        }
        else {
            return $this->line;
        }
    }
}
