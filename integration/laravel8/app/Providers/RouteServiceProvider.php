<?php

namespace App\Providers;

use App\Http\Controllers\NuxeoController;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider {

  public function map() {
    Route::get('/', [NuxeoController::class, 'index']);
  }

}
