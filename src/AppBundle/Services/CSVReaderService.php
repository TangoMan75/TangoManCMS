<?php

namespace AppBundle\Services;

require_once 'CSVLine.php';

class CSVReaderService
{
    private $hasHead = false;
    private $head;
    private $file;
    private $handle;
    private $enclosure;
    private $delimiter;
    private $escape;
    private $lineCount = null;

    public function __construct()
    {
    }

    public function init($file, $head = true, $delimiter = ",", $enclosure = '"', $escape = "\\")
    {
        $this->handle = fopen($file,'r');
        $this->file = $file;
        if (is_array($head)) {
            $this->head = $head;
        }
        else {
            $this->head = fgetcsv($this->handle, null, $delimiter, $enclosure, $escape);
            if ($this->head !== false) {
                $this->hasHead = true;
            }
        }
        $this->delimiter    = $delimiter;
        $this->enclosure    = $enclosure;
        $this->escape       = $escape;
    }

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

    public function debug() {
        return $this->head;
    }

    public function readLine() {
        $rawLine = fgets($this->handle);
        if ($rawLine!==false) {
            $line = str_getcsv(utf8_encode($rawLine),$this->delimiter, $this->enclosure, $this->escape);
            return new CSVLine($this->head, $line);
        }
        return false;
    }
}
