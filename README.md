## How to run tests

Go to the root directory of this install and run the following:

    ./vendor/bin/phpunit Route



## To do

 - create a route writer which reads from a Route root and writes out the php as a cache file
 - need to work out how to read that cache file though - read from given location? probably set this during site bootstrap.
 - build the tests for RouteLinkArguments and Router
 - write the code for Router::get()
 - update RouteBuilder to allow (...) for optional parts of routes. Have this write out endpoints for each optional route section
