<?php

namespace Angelika\Router\Tests;

use Angelika\Router\Route;
use Angelika\Router\RouteAlreadyExistException;
use Angelika\Router\RouteNotFoundException;
use Angelika\Router\Tests\Fictures\FooController;
use Angelika\Router\Tests\Fictures\HomeController;
use PHPUnit\Framework\TestCase;
use Angelika\Router\Router;

/**
 * Class Routertest
 * @package Angelika\Router\Tests
 */
class RouterTest extends TestCase
{
    public function test()
    {
        $router = new Router();

        $routeHome = new Route("home", "/", [HomeController::class, "index"]);

        $routeFoo = new Route("foo", "/foo/{bar}", [FooController::class, "bar"]);

        $routeArticle = new Route("article", "/blog/{id}/{slug}", function (string $slug, string $id) {
            return sprintf("%s : %s", $id, $slug);
        });

        $router->add($routeHome);
        $router->add($routeFoo);
        $router->add($routeArticle);

        $this->assertCount(3,$router->getRouteCollection());

        $this->assertContainsOnlyInstancesOf(Route::class,$router->getRouteCollection());

        $this->assertEquals($routeHome, $router->get("home"));

        $this->assertEquals($routeHome,$router->match("/"));
        $this->assertEquals($routeArticle,$router->match("/blog/12/mon-article"));

        $this->assertEquals("Hello", $router->call("/"));

        $this->assertEquals("12 : mon-article",$router->call("/blog/12/mon-article"));

        $this->assertEquals(
            "bar",
            $router->call("/foo/bar")
        );
    }

    /**
     * @return void
     * @throws RouteNotFoundException
     */
    public function testIfRouteNotFoundByMatch(): void
    {
        $router = new Router();
        $this-> expectException(RouteNotFoundException::class);
        $router->match("/");
    }

    public function testIfRouteNotFoundByGet()
    {
        $router = new Router();
        $this-> expectException(RouteNotFoundException::class);
        $router->get("fail");
    }

    /**
     * @return void
     */
    public function testIfRouteAlreadyExist(): void
    {
        $router = new Router();
        $router->add(new Route("home", "/", function (){}));
        $this-> expectException(RouteAlreadyExistException::class);
        $router->add(new Route("home", "/", function (){}));
    }
}