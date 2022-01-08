<?php

namespace App\Http\Controllers;

use App\Action\DashboardAction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(DashboardAction $action)
    {
        $bases = $action->getBaseStationsData();
        return view('pages.index', compact('bases'));
    }

    public function dashboard(DashboardAction $action, $id, $tag)
    {
        try {
            throw_if(!in_array($id, config('basestations.allow')), 'You are not allow to show the dashboard of this base station');

            $base = $action->getBaseStationById($id);
            $sensors = $action->getSensorData($id);
            $airTempData = $action->findSensorKey($sensors, 'air_temperature_(°C)');
            $windSpeedData = $action->findSensorKey($sensors, 'wind_speed_(m/s)');
            $gustSpeedData = $action->findSensorKey($sensors, 'gust_wind_speed_(m/s)');
            $rainFallData = $action->findSensorKey($sensors, 'precipitation_(mm)');
            $atmosphericPressureData = $action->findSensorKey($sensors, 'atmospheric_pressure_(hPa)');
            $relativeHumidityData = $action->findSensorKey($sensors, 'relative_humidity_(%)');
            $windSensorsData = $action->findSensorKey($sensors, 'wind_direction_(°)');

            $data['base'] = $base;

            $data['is_air_temp'] = isset($airTempData['sensor']) ? true : false;
            $data['is_wind_speed'] = isset($windSpeedData['sensor']) ? true : false;
            $data['is_gust_speed'] = isset($gustSpeedData['sensor']) ? true : false;
            $data['is_rain_fall'] = isset($rainFallData['sensor']) ? true : false;
            $data['is_atmospheric_pressure'] = isset($atmosphericPressureData['sensor']) ? true : false;
            $data['is_relative_humidity'] = isset($relativeHumidityData['sensor']) ? true : false;
            $data['is_wind_direction'] = isset($windSensorsData['sensor']) ? true : false;
            return view('pages.dashboard', $data);
        } catch (\Exception $exception) {
            return back()->with('alert', $exception->getMessage());
        }
    }

    public function getChartData(Request $request, DashboardAction $action)
    {

        $convertedUTCTime = Carbon::parse(Carbon::parse($request->currentTime)->setTimezone(config('app.timezone'))->toDateTimeString());
        $timeOf8Am = today()->addHours(8)->addMinutes(59);
        $today9Am = today()->addHours(9);
        $timeBetween = $convertedUTCTime->gt($timeOf8Am) == false ? [$today9Am->subDay()->toDateTimeString(), $timeOf8Am->toDateTimeString()] : [$today9Am->toDateTimeString(), now()->toDateTimeString()];
        $sensors = $action->getSensorData($request->id);

        $airTempData = $action->findSensorKey($sensors, 'air_temperature_(°C)');
        $data['airTempData'] = $action->getLatestChartData($airTempData);

        $windSpeedData = $action->findSensorKey($sensors, 'wind_speed_(m/s)');
        $data['windSpeedData'] = $action->getLatestChartData($windSpeedData);

        $gustSpeedData = $action->findSensorKey($sensors, 'gust_wind_speed_(m/s)');
        $data['gustSpeedData'] = $action->getLatestChartData($gustSpeedData);

        $rainFallData = $action->findSensorKey($sensors, 'precipitation_(mm)');
        $data['rainFallData'] = $action->getSumChartData($rainFallData, $timeBetween);

        $atmosphericPressureData = $action->findSensorKey($sensors, 'atmospheric_pressure_(hPa)');
        $data['atmosphericPressureData'] = $action->getLatestChartData($atmosphericPressureData);

        $relativeHumidityData = $action->findSensorKey($sensors, 'relative_humidity_(%)');
        $data['relativeHumidityData'] = $action->getLatestChartData($relativeHumidityData);

        $windSensorsData = $action->findSensorKey($sensors, 'wind_direction_(°)');
        $data['windDirection'] = $action->getLatestChartData($windSensorsData);
        return response()->json($data);
    }
}
