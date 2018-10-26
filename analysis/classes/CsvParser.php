<?php

class CsvParser
{
  public function __construct($deliminator = ',') {
    $this->deliminator = $deliminator;
    ini_set("auto_detect_line_endings", true);
    mb_internal_encoding("ISO-8859-1");
  }

  public function parse($path)
  {
    return $this->csvToArray($path);
  }

  protected function parseCsv($file)
  {
    $handle = fopen($file, "r");

    if ($handle !== false) {
      while (($row = fgetcsv($handle, 1000, $this->deliminator)) !== false) {
        yield $row;
      }
    }

    fclose($handle);
  }

  protected function generateRows($data, $headers)
  {
    foreach ($data as $column) {
      if ($column == $headers) continue;
      $row = [];
      for ($i = 0; $i < count($column); $i++) {
        if (isset($headers[$i])) {
          $row[$headers[$i]] = !empty($column[$i]) ? $column[$i] : null;
        }
      }
      yield $row;
    }
  }

  protected function csvToArray($file)
  {
    $data = $this->parseCsv($file);
    $headers = $data->current();
    return $this->generateRows($data, $headers);
  }
}
