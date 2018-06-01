<?php

/**
 * Get the page number from the query string
 * In production, get this value from WordPress
 * @return integer
 */
function getPage()
{
  // deafult page number
  $page = 1;

  if (isset($_GET['page']) && $pageAsInt = (int) $_GET['page']) {
    // valid number set in query string
    return $pageAsInt;
  }

  return $page;
}

/**
 * Get the user query from the query string
 * In production, get this value from WordPress
 * @return string
 */
function getQuery()
{
  if (isset($_GET['q']) && !empty($_GET['q'])) {
    return htmlentities($_GET['q']);
  } else {
    throw new Exception('Please provide a query via the "q" query string parameter');
  }
}

/**
 * Generate the POST body
 * @param  string  $query User query
 * @param  integer $page  Requested page of results
 * @param  integer $count Number of results to return
 * @return string  JSON encoded string
 */
function getPostBody($query, $page = 1, $count = 10)
{
  // array of properties we want returned with each result (see below)
  $properties = [
    'title',
    'datasource/fqcategory',
    'content',
    'description',
    'mes:date',
    'url',
    'icon'
  ];

  // start constructing the body
  $body = [
    'content_sample_length' => 300,                         // how many characters the snippet should be
    'query' => ['unparsed' => $query],                      // user query
    'count' => $count,                                      // how many results to return
    'max_page_count' => 10,                                 // how many 'pages' worth of pagination information to return
    'alternatives_query_spelling_max_estimated_count' => 5, // how many spelling suggestings to return
    'properties' => getProperties($properties)
  ];


  // add pagination data if we're on page 2+
  if ($page > 1) {

    if (!isset($_SESSION['search_qeng'][$query])) {
      throw new Exception('Qeng variables not set. Cannot get page ' . $page . ' of results');
    }

    $body['result_pages'] = [
      // qeng IDs from previous request
      'qeng_ids' => $_SESSION['search_qeng'][$query],

      // generate the "current page" data based on the requested page and count.
      'pages' => [
        [
          'starts' => [($page - 1) * $count], // offset
          'counts' => [$count], // how many results to return
          'current_page' => true,
          'page_number' => $page // requested page number
          ]
        ]
    ];
  }

  return json_encode($body);
}

/**
 * Given a list of properties, transform them into
 * the format expected by Mindbreeze
 * @param  array $properties Array of property names
 * @return array Array of arrays
 */
function getProperties($properties)
{
  return array_map(function ($property) {
    return [
      'name' => $property,
      'formats' => ['HTML', 'VALUE']
    ];
  }, $properties);
}

/**
 * Clean up the results from Mindbreeze. Instead of having the
 * result properties numerically indexed, index the properties
 * by the property title for easier retrieval. Upone return,
 * cleaned up data can be found in $results->data
 * @param  array $results Array of results from Mindbreeze
 * @return array Array of cleaned up results
 */
function cleanResults($results)
{
  return array_map(function ($result) {

    $result->data = new \StdClass();

    foreach ($result->properties as $property) {
      $name = str_replace(':', '_', strtolower($property->id));
      $result->data->$name = $property->data[0];
    }

    unset($result->properties);

    return $result;

  }, $results);
}
