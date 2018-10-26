<?php

date_default_timezone_set("America/New_York");
require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/classes/CsvParser.php';
require __DIR__ . '/classes/Table.php';

$parser = new CsvParser(';');
$data = $parser->parse('data.csv');

// compile data

$domains = [];

foreach ($data as $row) {

  if (!isset($row['mes:key']) || $row['fqcategory'] == 'BestBets:jhuedu-suggestions') {
    continue;
  }

  if (preg_match('/^(https?):\/\/(www.)?([^\/]+)/', $row['mes:key'], $matches)) {
    $protocol = $matches[1];
    $www = $matches[2];
    $domain = $matches[3];

    if (!isset($domains[$domain])) {
      $domains[$domain]['variations'] = [$matches[0]];
      $domains[$domain]['count'] = 0;
    } else if (!in_array($matches[0], $domains[$domain]['variations'])) {
      $domains[$domain]['variations'][] = $matches[0];
    }
    $domains[$domain]['count']++;
  }

}

ksort($domains);

// print_r($domains); die();

function domainCount($domains)
{
  foreach ($domains as $domain => $details) {
    yield [$domain, $details['count']];
  }
}

function domainVariations($domains)
{
  foreach ($domains as $domain => $details) {
    if (count($details['variations']) > 1) {
      yield [$domain, implode(', ', $details['variations'])];
    }
  }
}

foreach (domainVariations($domains) as $domain) {
  print_r($domain);
}
