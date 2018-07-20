<?php

namespace MindbreezeExample;

class Request extends \Mindbreeze\request
{
  public $url = 'https://search.jh.edu:23352/api/v2/search';

  public $properties = [
    'title',                  // document title
    'Section',                // section (hub, magazine, gazette, atwork)
    'Type',                   // article, event, announcement
    'datasource/fqcategory',  // datasource (Hub, GazetteArchivesWp, etc...)
    'mes:date',               // publish date
    'description',            // meta descriotion
    'content',                // query matching content
    'url',                    // document url
    'icon'                    // screenshot
  ];

  public $facets = [
    'mes:date',
    'fqcategory'
  ];

  public $constraints = [
    'gazette' => ['Web:GazetteArchivesPages', 'Web:GazetteArchivesWP'],
    'hub' => ['Web:StagingHub'],
    'magazine' => ['Web:MagazineArchivesPages', 'Web:MagazineArchivesWP']
  ];
}
