<?php

class JeaHelperUtility
{
    
    public static function arrayToCSV($data)
    {
        $outstream = fopen("php://temp", 'r+');
        fputcsv($outstream, $data, ';', '"');
        rewind($outstream);
        $csv = fgets($outstream);
        fclose($outstream);
        return $csv;
    }

    protected function CSVToArray($data)
    {
        $instream = fopen("php://temp", 'r+');
        fwrite($instream, $data);
        rewind($instream);
        $csv = fgetcsv($instream, 9999999, ';', '"');
        fclose($instream);
        return $csv;
    }
}