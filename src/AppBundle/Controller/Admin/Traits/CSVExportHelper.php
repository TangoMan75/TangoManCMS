<?php

namespace Traits\CSVExportHelper;

trait CSVExportHelper
{
    /**
     * Export object in csv format
     *
     * @param array  $scalar
     * @param string $delimiter
     * @param string $dateTimeFormat
     *
     * @return bool|string
     */
    public function exportCSV($scalar = [], $delimiter = ';', $dateTimeFormat = 'Y/m/d H:i:s')
    {
        // Create file in memory
        $file = fopen('php://memory', 'r+');

        // Build csv header from array keys
        $keys = [];
        if (count($scalar) > 0) {
            $keys = array_keys($scalar[0]);
        }

        fputcsv(
            $file,
            $keys,
            $delimiter
        );

        foreach ($scalar as $property) {
            $values = [];

            // Converts DateTime objects and arrays to string
            foreach (array_values($property) as $value) {

                if (is_array($value)) {
                    $value = implode(',', $value);
                } elseif ($value instanceof \DateTime) {
                    $value = $value->format($dateTimeFormat);
                } elseif (is_object($value)) {
                    $value = null;
                }

                $values[] = $value;
            }

            fputcsv(
                $file,
                $values,
                $delimiter
            );
        }

        rewind($file);
        $result = stream_get_contents($file);
        fclose($file);

        return $result;
    }
}
