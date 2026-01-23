<?php
namespace TnFAT\Planner;

abstract class AbstractEntityToCsvReader extends AbstractEntityReader {
    // override @fileName in each concrete class to hold the desired download file name
    protected $fileName;
    
    protected function setHeader(): void {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $this->fileName . '"');
    }

    protected function convertToCsv($data,$separator = ','): string {
        $handle = fopen('php://temp', 'r+');
        foreach($data as $line) {
            fputcsv($handle, $line, $separator, '"');
        }
        rewind($handle);
        $contents = '';
        while (!feof($handle)) {
            $contents .= fread($handle, 8192);
        }
        fclose($handle);
        return $contents;
    }
}