<?php

namespace App\Http\Controllers\Rest\Lib;

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
        $classPath = "App\\Http\\Controllers\\Rest\\$className";

        if (!class_exists($classPath)) {
            throw new ClassNotFoundException("Controller '$classPath' doesn't exist.", new ErrorException());
        }

        $instance = new $classPath();
        $instance->setTraitRequest($this->request);
        $this->controller = $instance;
    }
}