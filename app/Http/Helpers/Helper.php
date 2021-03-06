<?php

namespace App\Http\Helpers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Helper
{
    public static function userWantsAll(Request $request)
    {
        return $request->has("all") && $request->get("all") == "true";
    }


    public static function getRelatedModelClassName(Controller $controller)
    {
        $fullName = get_class($controller);
        $reducedName = str_replace('Controller', '', array_last(explode('\\', $fullName)));

        return 'App\\' . str_singular($reducedName);
    }


    public static function arrayGetOrNull(array $array, $key)
    {
        return is_numeric($key) && isset($array[$key]) || array_key_exists($key, $array) ? $array[$key] : null;
    }
}