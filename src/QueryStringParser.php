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
  public function getQuery()
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
  public function getSource()
  {
    if (isset($_GET['source']) && in_array($_GET['source'], ['hub', 'gazette', 'magazine'])) {
      return $_GET['source'];
    } else {
      return null;
    }

  }
}
