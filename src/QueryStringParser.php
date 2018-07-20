<?php

namespace MindbreezeExample;

class QueryStringParser
{
  /**
   * Get the page number from the query string
   * In production, get this value from WordPress
   * @return integer
   */
  public function getPage()
  {
    // deafult page number
    $page = 1;

    if (isset($_GET['page']) && $int = (int) $_GET['page']) {
      // valid number set in query string
      return $int;
    }

    return $page;
  }

  /**
   * Get the user query from the query string
   * In production, get this value from WordPress
   * @return string
   */
  public function getQuery()
  {
    if (isset($_GET['q']) && !empty($_GET['q'])) {
      return urlencode($_GET['q']);
    } else {
      return null;
    }
  }

  /**
   * Get the source from the query string
   * @return string
   */
  public function getSource()
  {
    if (isset($_GET['source']) && in_array($_GET['source'], ['hub', 'gazette', 'magazine'])) {
      return $_GET['source'];
    } else {
      return null;
    }
  }

  public function getYear()
  {
    if (!isset($_GET['year'])) {
      return null;

    }

    $int = (int) $_GET['year'];

    if ($int < 1994) {
      return null;
    }

    return [
      strtotime("{$int}-01-01 00:00:00 GMT"),
      strtotime("{$int}-12-31 23:59:59 GMT")
    ];
  }

  public function getType()
  {
    if (!isset($_GET['type'])) {
      return null;
    }

    $types = ['article', 'announcement', 'event', 'page'];
    $valid = [];

    foreach (explode(',', $_GET['type']) as $type) {
      if (in_array($type, $types)) {
        $valid[] = $type;
      }
    }

    return $valid;
  }
}
