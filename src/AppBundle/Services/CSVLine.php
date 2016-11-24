<?php

namespace AppBundle\Services;

/**
 * Class CSVLine
 * @package AppBundle\Services
 */
class CSVLine
{
    /**
     * @var array
     */
    private $line;


    /**
     * CSVLine constructor.
     * @param $head
     * @param $line
     */
    public function __construct($head, $line)
    {
        $this->line = array_combine($head, $line);
    }

    /**
     * @param $field
     * @return array|string
     */
    public function get($field)
    {
        if (isset($this->line[$field])) {
            return trim($this->line[$field]);
        } else {
            return $this->line;
        }
    }
}
