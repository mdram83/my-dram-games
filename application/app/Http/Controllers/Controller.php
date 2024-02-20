<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    public const MESSAGE_INTERNAL_ERROR = 'Internal error';
    public const MESSAGE_NOT_FOUND = 'Not found';
    public const MESSAGE_FORBIDDEN = 'Forbidden';
    public const MESSAGE_UNAUTHORIZED = 'Unauthorized';
}
