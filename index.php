<?php

// start session
session_start();

// load composer dependencies and functions file
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/functions.php';


/**
 * This stuff will come from the WordPress query vars, but for illustrative
 * purposes, I'm just grabbing from the query string. This isn't exactly secure,
 * so don't do this in production code :)
 *
 * My guess is the Cara has the fetching of these things already set up in your theme.
 */
$page = getPage();
$query = getQuery();
$count = 10;
$source = getSource(); // this will help illustrate how to get results from just one datasource


// create HTTP client
$guzzle = new \GuzzleHttp\Client();

// send the POST request
$response = $guzzle->post('https://search.jh.edu:23352/api/v2/search', [
  'body' => getPostBody($query, $page, $count, $source),
  'headers' => ['Content-Type' => 'application/json']
]);


// handle the response

if ($response->getStatusCode() !== 200) {
  // if not 200, display some kind of error message
  throw new Exception('failed to get a response from Mindbreeze');
}

// get the body of the response
$body = json_decode((string) $response->getBody());


if (!isset($body->resultset->results)) {
  $_SESSION['search_qeng'] = null;
  throw new Exception('no results');
}


// there are results, let's display them.

// data you will need for page 2 results; save in a session
// I don't think this is how I'll handle this data in the future,
// but it's the best solution I have so far. Honestly, I might just
// add this data to the query string.
$_SESSION['search_qeng'][$query] = $body->resultset->result_pages->qeng_ids;

// clean up the properties of each result
$results = cleanResults($body->resultset->results);

// display each result
foreach ($results as $result) {
  // print_r($result); die();
?>

  <p><a href='<?php echo $result->data->url->value->str; ?>'><?php echo $result->data->title->html; ?></a><br />
  Source: <?php echo $result->data->datasource_fqcategory->html; ?></br />
  <?php echo $result->data->mes_date->html; ?><br />
  Snippet: <?php echo $result->data->content->html; ?><br />
  Description: <?php echo $result->data->description->html; ?></p>

<?php
}

// some basic navigation

if ($body->resultset->prev_avail) {
  $newPage = $page - 1 ;
  echo "<p><a href='index.php?q={$query}&page={$newPage}'>PREV</a></p>";
}

if ($body->resultset->next_avail) {
  $newPage = $page + 1 ;
  echo "<p><a href='index.php?q={$query}&page={$newPage}'>NEXT</a></p>";
}
