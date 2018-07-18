<?php

// query string parsing helper
$parser = new MindbreezeExample\QueryStringParser();

// http client
$http = new HttpExchange\Adapters\Guzzle6(new \GuzzleHttp\Client());

// twig template engine
$twig = new \Twig_Environment(new \Twig_Loader_Filesystem(dirname(__DIR__) . '/templates'));
