<?php

namespace AppBundle\Services;

require_once 'CSVLine.php';

/**
 * Class CSVReaderService
 * @package AppBundle\Services
 */
class CSVReaderService
{
    /**
     * @var bool
     */
    private $hasHead = false;

    /**
     * @var array|bool
     */
    private $head;

    /**
     * @var
     */
    private $file;

    /**
     * @var
     */
    private $handle;

    /**
     * @var string
     */
    private $enclosure;

    /**
     * @var string
     */
    private $delimiter;

    /**
     * @var string
     */
    private $escape;

    /**
     * @var integer|bool
     */
    private $lineCount = null;


    /**
     * CSVReaderService constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $file
     * @param array|bool $head
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     */
    public function init($file, $head = true, $delimiter = ",", $enclosure = '"', $escape = "\\")
    {
        $this->handle = fopen($file, 'r');
        $this->file = $file;
        if (is_array($head)) {
            $this->head = $head;
        } else {
            $this->head = fgetcsv($this->handle, null, $delimiter, $enclosure, $escape);
            if ($this->head !== false) {
                $this->hasHead = true;
            }
        }
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape    = $escape;
    }

    /**
     * @return int|null
     */
    public function countLines() {
        if ($this->lineCount === null) {
            $lineCount = 0;
            $handle = fopen($this->file, "r");
            while(!feof($handle)){
                $line = fgets($handle, 4096);
                $lineCount = $lineCount + substr_count($line, PHP_EOL);
            }
            fclose($handle);
            $this->lineCount = $lineCount - $this->hasHead;
        }
        return $this->lineCount;
    }

    /**
     * @return array|bool
     */
    public function debug() {
        return $this->head;
    }

    /**
     * @return CSVLine|bool
     */
    public function readLine() {
        $rawLine = fgets($this->handle);
        if ($rawLine!==false) {
            $line = str_getcsv(utf8_encode($rawLine),$this->delimiter, $this->enclosure, $this->escape);
            return new CSVLine($this->head, $line);
        }
        return false;
    }
}
