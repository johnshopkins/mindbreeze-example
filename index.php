<?php

// start session
session_start();

// load dependencies
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/deps.php';

try {

  $query = $parser->getQuery();
  $page = $parser->getPage();
  $source = $parser->getSource();

  // create mindbreeze request
  $request = new MindbreezeExample\Request($http);
  $request->setQuery($query)->setPage($page);

  if ($source) {
    $request->addDatasourceConstraint($source);
  }

  $response = $request->send();

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
