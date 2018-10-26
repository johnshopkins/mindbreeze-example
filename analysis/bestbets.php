<?php

date_default_timezone_set("America/New_York");
require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/classes/CsvParser.php';
require __DIR__ . '/classes/Table.php';

$parser = new CsvParser(';');
$data = $parser->parse('bestbets-jhu.csv');

foreach ($data as $keymatch) {
  // print_r($keymatch);
  // die();

  $title = str_replace('&', '&amp;', $keymatch['Title']);

  echo '<Promotion queries="' . $keymatch['Keywords'] . '" title="' . $title . '" url="' . $keymatch['url'] . '" />' . "\n";

  /*

  <Promotion id="AmericanGraphics0001"
    queries="american born chinese, American Born Chinese, abc, ABC"
    title="American Born Chinese"
    url="http://books.google.com/books?id=vawdZyrDw64C&dq=american+born+
    Chinese+gene+yang"
    description="Graphic novel. First-person account of growing up Asian
    American by Gene Luen Yang."
    image_url="http://146.74.224.231/archives/Gene%20Yang.jpg" />

*/

}


//
// // compile data
//
// $domains = [];
//
// foreach ($data as $row) {
//
//   if (!isset($row['mes:key']) || $row['fqcategory'] == 'BestBets:jhuedu-suggestions') {
//     continue;
//   }
//
//   if (preg_match('/^(https?):\/\/(www.)?([^\/]+)/', $row['mes:key'], $matches)) {
//     $protocol = $matches[1];
//     $www = $matches[2];
//     $domain = $matches[3];
//
//     if (!isset($domains[$domain])) {
//       $domains[$domain]['variations'] = [$matches[0]];
//       $domains[$domain]['count'] = 0;
//     } else if (!in_array($matches[0], $domains[$domain]['variations'])) {
//       $domains[$domain]['variations'][] = $matches[0];
//     }
//     $domains[$domain]['count']++;
//   }
//
// }
//
// ksort($domains);
//
// // print_r($domains); die();
//
// function domainCount($domains)
// {
//   foreach ($domains as $domain => $details) {
//     yield [$domain, $details['count']];
//   }
// }
//
// function domainVariations($domains)
// {
//   foreach ($domains as $domain => $details) {
//     if (count($details['variations']) > 1) {
//       yield [$domain, implode(', ', $details['variations'])];
//     }
//   }
// }
//
// $table = new Table();
// $table->display(domainCount($domains));
// // $table->display(domainVariations($domains));
