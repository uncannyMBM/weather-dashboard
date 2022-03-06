<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::group(['middleware' => 'verifyApiKey'], function () {

    Route::post('dashboard/{id}', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('get-chart-data', [DashboardController::class, 'getChartData'])->name('get.chart.data');

    Route::get('get-avg-pm-chart-data', [DashboardController::class, 'getAvgPmChartData'])->name('get.avg.pm.chart.data');

    Route::get('get-max-pm-chart-data', [DashboardController::class, 'getMaxPmChartData'])->name('get.max.pm.chart.data');

    Route::get('solar-voltage-chart-data', [DashboardController::class, 'getSolarVoltageChartData'])->name('get.solar.voltage.chart.data');

    Route::get('solar-uv-chart-data', [DashboardController::class, 'getSolarUvChartData'])->name('get.solar.uv.chart.data');

    Route::post('historical-chart/{id}/{chart}', [DashboardController::class, 'historicalChart'])->name('historical.chart');
    Route::get('get-single-chart-data', [DashboardController::class, 'getSingleChartData'])->name('get.single.chart.data');

    Route::post('historical-rainfall-daily-chart/{id}', [DashboardController::class, 'historicalRainfallDailyChart'])->name('historical.rainfall.daily.chart');
    Route::get('get-rainfall-daily-data', [DashboardController::class, 'getRainfallChartDailyData'])->name('get.rainfall.daily.chart.data');

    Route::post('historical-rainfall-monthly-chart/{id}', [DashboardController::class, 'historicalRainfallMonthlyChart'])->name('historical.rainfall.monthly.chart');
    Route::get('get-rainfall-monthly-data', [DashboardController::class, 'getRainfallChartMonthlyData'])->name('get.rainfall.monthly.chart.data');

    Route::post('historical-widn-rose-chart/{id}', [DashboardController::class, 'historicalWindRoseChart'])->name('historical.wind.rose.chart');
    Route::get('get-wind-rose-data', [DashboardController::class, 'calulateSpeedWithDirection'])->name('get.wind.rose.chart.data');

    Route::post('historical-guast-wind-chart/{id}', [DashboardController::class, 'historicalGuastWindChart'])->name('historical.guast.wind.chart');
    Route::get('get-guast-wind-data', [DashboardController::class, 'getGuastWindChartData'])->name('get.guast.wind.chart.data');

    Route::post('historical-combine-chart/{id}', [DashboardController::class, 'historicalCombineChart'])->name('historical.combine.chart');
    Route::get('get-combine-chart-data', [DashboardController::class, 'getCombineChartData'])->name('get.combine.chart.data');
});

