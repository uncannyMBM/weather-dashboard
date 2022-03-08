<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('test', function () {
    $users = DB::table('notification_type_recipient_preferences')->where(['notification_types_id' => 4, 'active' => 1,])
        ->join('notification_recipients', 'notification_recipients.id', '=', 'notification_type_recipient_preferences.notification_recipients_id')
        ->join('companies', 'companies.id', '=', 'notification_recipients.company_id')
        ->select(DB::raw('(notification_recipients.email_id) as email, (notification_recipients.cc_email_ids) as ccemail ,companies.name,
       companies.id, notification_recipients.full_name '))
        ->get();
    foreach ($users as $user) {
        $companyid = $user->id;
        $company_email = $user->email;
        $company = $user->name;
        $data = DB::table('base_station_company')->join('companies', 'companies.id', '=', 'base_station_company.company_id')
            ->join('base_stations', 'base_stations.id', '=', 'base_station_company.base_station_id')
            ->select(DB::raw('base_stations.tag,(base_stations.name) as nam,
          base_stations.status,
          base_stations.status_log as value'))
            ->where('companies.id', $companyid)
            ->whereIn('base_stations.iot_endpoint_model_id', [13, 14, 16])
            ->whereNull('base_stations.deleted_at')
            ->get();
        $report = [];
        foreach ($data as $sensors) {
            $sensorValues = json_decode($sensors->value);
            foreach ($sensorValues->latestData as $sensorDetails) {
                if (isset($sensorDetails->sensorsId)) {
                    $sensor = DB::table('base_station_sensors')
                        ->select('id', 'base_station_id', 'parameters')
                        ->find($sensorDetails->sensorsId);

                    $parameters = array_map('trim', explode(",", $sensor->parameters));
                    $sensorKey = array_search('precipitation_(mm)', $parameters);
                    if ($sensorKey > -1) {
                        $key = $sensorKey + 1;
                        $rainFallSumMonthly = DB::table('base_station_sensors_data')
                            ->selectRaw('id ,sensors_id, ROUND(SUM(D' . $key . '), 2) AS data')
                            ->where('sensors_id', $sensor->id)
                            ->whereBetween('created_at', [today()->subMonth()->startOfMonth(), today()->subMonth()->endOfMonth()])
                            ->groupBy('sensors_id')
                            ->first();
                        $rainFallSumYearly = DB::table('base_station_sensors_data')
                            ->selectRaw('id ,sensors_id, ROUND(SUM(D' . $key . '), 2) AS data')
                            ->where('sensors_id', $sensor->id)
                            ->whereYear('created_at', today()->subMonth()->format('Y'))
                            ->groupBy('sensors_id')
                            ->first();
                        $report[] = [
                            'sensor_name' => $sensors->nam,
                            'rainfall_monthly' => $rainFallSumMonthly->data ?? 0,
                            'rainfall_yearly' => $rainFallSumYearly->data ?? 0,
                        ];
                    }
                }
            }
        }
        return $report;
    }
});

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

