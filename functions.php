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
 * Get the source from the query string
 * @return string
 */
function getSource()
{
  if (isset($_GET['source']) && in_array($_GET['source'], ['hub', 'gazette', 'magazine'])) {
    return $_GET['source'];
  } else {
    return null;
  }

}

/**
 * Generate the POST body
 * @param  string  $query User query
 * @param  integer $page  Requested page of results
 * @param  integer $count Number of results to return
 * @return string  JSON encoded string
 */
function getPostBody($query, $page = 1, $count = 10, $source = null)
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


  if ($source) {
    $body['source_context']= [
      'constraints' => addConstraint($source)
    ];
  }


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

  // uncomment this print statement to see the POST body
  // print_r($body); die();

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
 * Limit which datasources are being queried
 * @param string $constraint Constraint (hub, magazine, gazette)
 * @return array
 */
function addConstraint($constraint)
{
  // list of all datasources on Hub index
  $datasources = [
    'Web:MagazineArchivesPages',
    'Web:MagazineArchivesWP',
    'Web:StagingHub',
    'Web:GazetteArchivesPages',
    'Web:GazetteArchivesWP'
  ];

  // list of the datasources that apply to certain constraints. ex: when you add
  // a constraint of 'magazine,' only the magazine datasources will be queried
  $constraints = [
    'gazette' => ['Web:GazetteArchivesPages', 'Web:GazetteArchivesWP'],
    'magazine' => ['Web:MagazineArchivesPages', 'Web:MagazineArchivesWP'],
    'hub' => ['Web:StagingHub']
  ];

  // datasources that will be queried
  $in = $constraints[$constraint];

  // datasources that will NOT be queried
  $out = array_diff($datasources, $in);

  return [
    'filter_base' => array_map(function ($datasource) {
      return createFilter('fqcategory', $datasource);
    }, array_values($in)),
    'filtered' => array_map(function ($datasource) {
      return createFilter('fqcategory', $datasource);
    }, array_values($out))
  ];
}

/**
 * Create boolean filter for datasource constraints
 * @param  string $label Filter label
 * @param  string $term  Value of filter
 * @return array  Filter array
 */
function createFilter($label, $term, $type = 'and')
{
  return [
    $type => [
      [
        'label' => $label,
        'quoted_term' => $term
      ]
    ]
  ];
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
      $name = str_replace([':', '/'], '_', strtolower($property->id));
      $result->data->$name = $property->data[0];
    }

    unset($result->properties);

    return $result;

  }, $results);
}
