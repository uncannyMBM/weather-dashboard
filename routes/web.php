<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('home');

Route::get('dashboard/{id}/{tag}', [DashboardController::class, 'dashboard'])->name('dashboard');

Route::get('get-chart-data', [DashboardController::class, 'getChartData'])->name('get.chart.data');

Route::get('get-avg-pm-chart-data', [DashboardController::class, 'getAvgPmChartData'])->name('get.avg.pm.chart.data');
