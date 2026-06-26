<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;

/**
 * BaseApiController
 * Base class for all API controllers. Provides JSON helper methods via ApiResponse trait.
 */
abstract class BaseApiController extends Controller
{
    use ApiResponse;
}
