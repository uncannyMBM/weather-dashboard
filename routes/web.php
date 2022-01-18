<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('home');

Route::get('dashboard/{id}/{tag}', [DashboardController::class, 'dashboard'])->name('dashboard');

Route::get('get-chart-data', [DashboardController::class, 'getChartData'])->name('get.chart.data');

Route::get('get-avg-pm-chart-data', [DashboardController::class, 'getAvgPmChartData'])->name('get.avg.pm.chart.data');

Route::get('get-max-pm-chart-data', [DashboardController::class, 'getMaxPmChartData'])->name('get.max.pm.chart.data');

Route::get('solar-voltage-chart-data', [DashboardController::class, 'getSolarVoltageChartData'])->name('get.solar.voltage.chart.data');

Route::get('solar-uv-chart-data', [DashboardController::class, 'getSolarUvChartData'])->name('get.solar.uv.chart.data');
