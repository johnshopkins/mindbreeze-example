<?php

date_default_timezone_set("America/New_York");

// start session
session_start();

// load dependencies
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/deps.php';

try {

  $page = $parser->getPage();
  $query = $parser->getQuery();

  // create mindbreeze request
  $request = new MindbreezeExample\Request($http);
  $request->setPage($page)->setQuery($query);

  if ($source = $parser->getSource()) {
    $request->addDatasourceConstraint($source);
  }

  if ($year = $parser->getYear()) {
    $request->addDateConstraint($year[0], $year[1]);
  }

  if ($type = $parser->getType()) {
    $request->addConstraint('Type', 'term', $type);
  }

  $response = $request->send();

  // print_r($response->records); die();

  echo $twig->loadTemplate("search.twig")->render([
    'query' => $query,
    'page' => $page,
    'response' => $response,
    'source' => $source
  ]);

} catch (\Exception $e) {

  print_r($e->getMessage()); die();

  // use eceptions to react differently to different errors
  // if ($e instanceof Mindbreeze\Exceptions\RequestException) {
  //   die('qeng undefined');
  // } else if ($e instanceOf Mindbreeze\Exceptions\ResponseException) {
  //   die('error from guzzle');
  // }

}
