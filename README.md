# Mindbreeze example

#### Get the example running on your local machine

1. Open Terminal
1. Clone this repository into a directory on your computer. Then `cd` into that directory.
1. Start the PHP server `php -S localhost:8080`
1. In a browser, navigate to `localhost:8080?q=science`

#### Using the app

1. Add a query: add the `q` query string parameter with your given query. Example: http://localhost:8080/?q=science
1. To add a source constraint, add the `source` query string parameter and give it a value of either `hub`, `gazette`, or `magazine` to limit the datasources that the query is run against. Example: http://localhost:8080/?q=science&source=gazette
1. Use the 'PREV' and 'NEXT' links at the bottom of the page to paginate through the results
1. Uncomment line 111 of functions.php to see a print out of the POST body.
