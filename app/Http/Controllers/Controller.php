<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Rest\RestRequestHandler;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use RestRequestHandler;
}
