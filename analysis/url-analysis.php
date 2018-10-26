<?php

date_default_timezone_set("America/New_York");
require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/classes/CsvParser.php';
require __DIR__ . '/classes/Table.php';

$parser = new CsvParser(';');
$data = $parser->parse('data.csv');

function documents($data)
{
  foreach ($data as $row) {
    if (!isset($row['mes:key']) || $row['fqcategory'] == 'BestBets:jhuedu-suggestions') {
      continue;
    }

    // if ($row['fqcategory'] != 'Web:jhuedu-sais') {
    //   continue;
    // }

    yield [
      // $row['fqcategory'],
      // $row['title'],
      $row['mes:key']
    ];
  }
}

function makeCsv($documents)
{
  $fp = fopen('jhuedu.csv', 'w');

  foreach ($documents as $doc) {
      fputcsv($fp, $doc);
  }

  fclose($fp);
}

$table = new Table();
$documents = documents($data);


$table->display($documents);
// makeCsv($documents);
