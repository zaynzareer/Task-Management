<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    // This trait gives controllers the authorize() helper for policies.
    use AuthorizesRequests;
}
