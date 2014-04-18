## How to run tests

Go to the root directory of this install and run the following:

    ./vendor/bin/phpunit Route



## To do
 - Write tests for RouteWriter


$testLink = new \Route\RouteLink('Acme\Coyote\Meeper', 'Meep', array('id' => 1));

$builder = new \Route\RouteBuilder();
$builder->Add('testArticle', 'test/[id](/article/article-[articleId])(/gallery/gallery-[galleryId])(/other)', array('id' => '\d+', 'articleId' => '\d+', 'galleryId' => '\d+'), array('get'), $testLink);
// $builder->Add('testGallery', 'test/[id]/gallery/[galleryId]', array('id' => '\d+', 'galleryId' => '\d+'), array('post', 'get'), $testLink);
// $builder->Add('test', 'test', array(), array('get'), $testLink);
// $builder->Add('home', '', array(), array('get'), $testLink);

$route = new \Route\Router($builder->GetRouteRoot(), $builder->GetRouteNames());
// var_Dump($route->Find('test/12/article/article-12313', 'get'));
// var_Dump($route->Find('test/1211/other', 'get'));
// var_Dump($route->Find('test', 'get'));

// var_dump($route->Get('testArticle', array('galleryId' => '12')));
// var_dump($route->Get('testArticle', array('articleId' => '78')));
// var_dump($route->Get('testArticle', array('galleryId' => '12', 'articleId' => '78')));
//var_dump($route->Get('testArticle'));

// $get = $route->Get('testArticle', array('articleId' => '12'));
// var_dump($get);
// var_Dump($route->Find($get, 'get'));

$writer = new \Route\RouteWriter($builder->GetRouteRoot(), $builder->GetRouteNames());
$code = $writer->OutputCode();
foreach ($code as $line) {
    eval($line);
}
var_dump($rootNode == $builder->GetRouteRoot(), $routes == $builder->GetRouteNames());