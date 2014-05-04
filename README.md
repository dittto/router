# Routing class

## What is it?

A series of classes that allow you to set up a series of routes and then retrieve them quickly and efficiently.

The routes are created as a tree (a trie) which is then traversed until either an endpoint is found or an exception occurs.

The routes are also stored by route name, so you can search by route name with some arguments and get a url returned.

A cache file of the route building process can be created to aid the route trie generation steps.

## How does it work

A route is made up of several parts:

 - A RouteLink which specifies the module, controller, and default parameters of a route endpoint
 - The RouteBuilder which takes multiple static and dynamic route urls and stores them against the endpoint RouteLinks
 - The Router object that takes the routes from the builder, as a tree, and a list of route names
 - A url or route name to retrieve either the module, controller, and arguments, or the url for

## How to use it

First you need to specify the routes you require and add them to the builder:

```php
    $routeLink = new RouteLink('Acme\Coyote\Meeper', 'Home', array('id' => 1));
    $testLink = new RouteLink('Acme\Coyote\Meeper', 'Meep', array('id' => 1));

    $builder = new RouteBuilder();
    $builder->Add('testArticle', 'test/[id]/article/article-[articleId]', array('id' => '\d+', 'articleId' => '\d+'), array('get'), $testLink);
    $builder->Add('testGallery', 'test/[id]/gallery/[galleryId]', array('id' => '\d+', 'galleryId' => '\d+'), array('post', 'get'), $testLink);
    $builder->Add('test', 'test', array(), array('get'), $testLink);
    $builder->Add('home', '', array(), array('get'), $routeLink);
    $builder->Add('testMulti', 'test/[id](/article/article-[articleId])(/gallery/gallery-[galleryId])', array('id' => '\d+', 'articleId' => '\d+', 'galleryId' => '\d+'), array('get'), $testLink);
```

You then need to create the router object using the tree created by the builder:

```php
    $router = new Router($builder->GetRouteRoot(), $builder->GetRouteNames());
```

Lastly you need either a url or a route name and arguments to retrieve data from the router:

```php
    echo $router->Get('testArticle', array('id' => '123112', 'articleId' => '12'));
    // returns "/test/123112/article/article-12"

    ...

    $route = $router->Find('test/12/article/article-12313', 'get');
    echo $testArticle->GetModule();
    // returns "Acme\Coyote\Meeper"

    echo $testArticle->GetController();
    // returns "Meep"

    echo $testArticle->GetArguments();
    // returns "array('id' => '12', 'articleId' => '12313')"

For more examples, have a look at the Test files, especially the RouteLinkTest, RouteBuilderTest, and the RouterTest pages.

## Caching the routes data

For more information on how to write the cache file, look at RouteWriterTest and the BaseRouterConfig.

## How to run tests

Go to the root directory of this install and run the following:

    ./vendor/bin/phpunit Route

## To dos
 - Write tests for RouterConfig and RouterInit