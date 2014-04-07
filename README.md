## How to run tests

Go to the root directory of this install and run the following:

    ./vendor/bin/phpunit Route



## To do

 - create a route writer which reads from a Route root and writes out the php as a cache file
 - need to work out how to read that cache file though - read from given location? probably set this during site bootstrap.
 - build the tests for RouteLinkArguments and Router
 - write the code for Router::get()
 - update RouteBuilder to allow (...) for optional parts of routes. Have this write out endpoints for each optional route section


$testLink = new \Route\RouteLink('Acme\Coyote\Meeper', 'Meep', array('id' => 1));

$builder = new \Route\RouteBuilder();
$builder->Add('testArticle', 'test/[id]/article/article-[articleId]', array('id' => '\d+', 'articleId' => '\d+'), array('get'), $testLink);
$builder->Add('testGallery', 'test/[id]/gallery/[galleryId]', array('id' => '\d+', 'galleryId' => '\d+'), array('post', 'get'), $testLink);
$builder->Add('test', 'test', array(), array('get'), $testLink);

$route = new \Route\Router($builder->GetRouteRoot(), $builder->GetRouteNames());
var_Dump($route->Find('test/12/article/article-12313', 'get'));
var_Dump($route->Find('test', 'get'));
