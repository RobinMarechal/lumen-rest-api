<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use ErrorException;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Debug\Exception\ClassNotFoundException;
use function camel_case;
use function class_exists;
use function strtoupper;
use function substr;

class RestController extends Controller
{
    public $request;
    public $controller;


    function __construct(Request $request)
    {
        $this->request = $request;
    }


    public function handleGet($namespace, $id = null, $relation = null, $relationId = null)
    {
        $this->prepareController($namespace);

        if ($relation) { // -> /api/users/5/posts
            $function = camel_case("get_" . $relation);

            return $this->controller->$function($id, $relationId);
        }
        else if ($id) { // -> /api/users/5
            return $this->controller->getById($id);
        }
        else { // -> /api/users
            return $this->controller->all();
        }
    }


    public function handlePost($namespace)
    {
        $this->prepareController($namespace);

        return $this->controller->post();
    }


    public function handlePut($namespace, $id)
    {
        $this->prepareController($namespace);

        return $this->controller->patch($id);
    }


    public function handleDelete($namespace, $id)
    {
        $this->prepareController($namespace);

        return $this->controller->delete($id);
    }


    protected function prepareController($namespace)
    {
        $className = strtoupper($namespace[0]) . camel_case(substr($namespace, 1)) . "Controller";
        $classPath = "App\\Http\\Controllers\\$className";

        if (!class_exists($classPath)) {
            throw new ClassNotFoundException("Controller '$classPath' doesn't exist.", new ErrorException());
        }

        $instance = new $classPath();
        $instance->setTraitRequest($this->request);
        $this->controller = $instance;
    }


    public function dispatch($resource, $id = null, $relation = null, $relatedId = null)
    {
        $request = $this->request;
        try {
            $controllerClassName = "App\\Http\\Controllers\\" . strtoupper($resource[0]) . camel_case(substr($resource, 1)) . "Controller";
            if (!class_exists($controllerClassName)) {
                throw new ClassNotFoundException("Controller '$controllerClassName' doesn't exist.", new ErrorException());
            }
            $controller = new $controllerClassName();
            $controller->setTraitRequest($request);
            if (!isset($id)) {
                if ($request->isMethod("get")) {
                    return $controller->all();
                }
                else if ($request->isMethod("post")) {
                    return $controller->post();
                }
                else {
                    goto EXCEPTION;
                }
            }
            if (!isset($relation)) // findById
            {
                if ($request->isMethod("get")) {
                    return $controller->getById($id);
                }
                else if ($request->isMethod("put")) {
                    return $controller->put($id);
                }
                else if ($request->isMethod("delete")) {
                    return $controller->delete($id);
                }
                else {
                    goto EXCEPTION;
                }
            }
            else {
                $function = camel_case("get_$relation");

                return $controller->$function($id, $relatedId);
            }
        } catch (Exception $e) {
            throw $e;
            throw new Exception("The given URL is not valid. It should look like one of these:
			\n - '.../api/[resource]/'
			\n - '.../api/[resource]/[id]/'
			\n - '.../api/[resource]/[id]/[relation]/'
			\n - '.../api/[resource]/[id]/[relation]/[relatedId]' \n 
			With: \n
			 - [resource] the wanted data in plural form (users, articles, news...) \n
			 - [id] the id of the wanted resource \n
			 - [relations] an existing relation of the wanted resource (e.g /users/1/courses; /articles/3/author) \n
			 - [relatedId] the id of the related resource (e.g /users/1/courses/2; /articles/2/medias/7)");
        }
        EXCEPTION:
        throw new Exception("The requested action is invalid. (" . $request->url() . " with method " . $request->method() . ")");
    }
}