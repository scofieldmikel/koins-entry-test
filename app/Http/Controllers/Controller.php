<?php

namespace App\Http\Controllers;

use App\Http\Traits\HasApiResponse;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests, DispatchesJobs, HasApiResponse, ValidatesRequests;
}
