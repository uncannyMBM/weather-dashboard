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
            $baseParams = $action->getBaseStationByBaseStationId($id);
            $sensors = $action->getSensorData($id);
            $airTempData = $action->findSensorKey($sensors, 'air_temperature_(°C)');
            $windSpeedData = $action->findSensorKey($sensors, 'wind_speed_(m/s)');
            $gustSpeedData = $action->findSensorKey($sensors, 'gust_wind_speed_(m/s)');
            $rainFallData = $action->findSensorKey($sensors, 'precipitation_(mm)');
            $uvData = $action->findSensorKey($sensors, 'uv_index');
            $atmosphericPressureData = $action->findSensorKey($sensors, 'atmospheric_pressure_(hPa)');
            $relativeHumidityData = $action->findSensorKey($sensors, 'relative_humidity_(%)');
            $windSensorsData = $action->findSensorKey($sensors, 'wind_direction_(°)');
            $solarSensorsData = $action->findSensorKey($sensors, 'solar_(W/m²)');
            $strikesSensorsData = $action->findSensorKey($sensors, 'strikes');
            $strikeDistanceSensorsData = $action->findSensorKey($sensors, 'strike_distance_(Km)');

            $data['base'] = $base;
            $data['baseParams'] = $baseParams;

            $data['is_air_temp'] = isset($airTempData['sensor']) ? true : false;
            $data['is_wind_speed'] = isset($windSpeedData['sensor']) ? true : false;
            $data['is_gust_speed'] = isset($gustSpeedData['sensor']) ? true : false;
            $data['is_rain_fall'] = isset($rainFallData['sensor']) ? true : false;

            if (!isset($atmosphericPressureData['sensor'])) {
                $atmosphericPressureData = $action->findSensorKey($sensors, 'pressure_(hPa)');
                $data['is_atmospheric_pressure'] = isset($atmosphericPressureData['sensor']) ? true : false;
            } else {
                $data['is_atmospheric_pressure'] = true;
            }

            if (!isset($relativeHumidityData['sensor'])) {
                $relativeHumidityData = $action->findSensorKey($sensors, 'humidity_(%)');
                $data['is_relative_humidity'] = isset($relativeHumidityData['sensor']) ? true : false;
            } else {
                $data['is_relative_humidity'] = true;
            }

            $data['is_relative_humidity'] = isset($relativeHumidityData['sensor']) ? true : false;
            $data['is_wind_direction'] = isset($windSensorsData['sensor']) ? true : false;
            $data['is_solar'] = isset($solarSensorsData['sensor']) ? true : false;
            $data['is_strikes'] = isset($strikesSensorsData['sensor']) ? true : false;
            $data['is_strike_distance'] = isset($strikeDistanceSensorsData['sensor']) ? true : false;
            $data['is_uv'] = isset($uvData['sensor']) ? true : false;
            return view('pages.dashboard', $data);
        } catch (\Exception $exception) {
            return back()->with('alert', $exception->getMessage());
        }
    }

    public function getChartData(Request $request, DashboardAction $action)
    {
        $localTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->currentTime)->setTimezone($request->timeZone);
        $today = Carbon::parse($request->currentTime)->format('Y-m-d');
        $beginingMonth9AmConvertedData = Carbon::createFromFormat('Y-m-d H:i:s', $localTime->firstOfMonth()->addHours(9)->format('Y-m-d H:i:s'), $request->timeZone)->setTimezone(config('app.timezone'))->toDateTimeString();
        $beginingYear9AmConvertedData = Carbon::createFromFormat('Y-m-d H:i:s', $localTime->firstOfYear()->addHours(9)->format('Y-m-d H:i:s'), $request->timeZone)->setTimezone(config('app.timezone'))->toDateTimeString();
        $timeOf8Am = Carbon::parse($today)->addHours(8)->addMinutes(59)->addSeconds(59);
        $today9Am = Carbon::parse($today)->addHours(9);
        $currentConvertedTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->currentTime, $request->timeZone)->setTimezone(config('app.timezone'))->toDateTimeString();
        $currentConvertedTime24hourBefore = Carbon::createFromFormat('Y-m-d H:i:s', $request->currentTime, $request->timeZone)->subHours(23)->setTimezone(config('app.timezone'))->toDateTimeString();
        $currentConvertedTime1hourBefore = Carbon::createFromFormat('Y-m-d H:i:s', $request->currentTime, $request->timeZone)->subHour()->setTimezone(config('app.timezone'))->toDateTimeString();
        $timeBetween = $localTime->gt($timeOf8Am) == false ? [
            Carbon::createFromFormat('Y-m-d H:i:s', $today9Am->subDay(1), $request->timeZone)->setTimezone(config('app.timezone'))->toDateTimeString(),
            $currentConvertedTime
        ] :
            [
                Carbon::createFromFormat('Y-m-d H:i:s', $today9Am, $request->timeZone)->setTimezone(config('app.timezone'))->toDateTimeString(),
                $currentConvertedTime
            ];

        $sensors = $action->getSensorData($request->id);

        $airTempData = $action->findSensorKey($sensors, 'air_temperature_(°C)');

        if (isset($airTempData['sensor'])){
            $data['airTempData'] = $action->getLatestChartData($airTempData);
            $data['airTempMinMax'] = $action->getMinMaxData($airTempData, [$currentConvertedTime24hourBefore, $currentConvertedTime]);
        }


        $windSpeedData = $action->findSensorKey($sensors, 'wind_speed_(m/s)');

        if (isset($windSpeedData['sensor'])) {
            $data['windSpeedData'] = $action->getLatestChartData($windSpeedData);
            $data['windSpeedMinMax'] = $action->getMinMaxData($windSpeedData, [$currentConvertedTime24hourBefore, $currentConvertedTime]);
        }


        $gustSpeedData = $action->findSensorKey($sensors, 'gust_wind_speed_(m/s)');

        if (isset($gustSpeedData['sensor'])) {
            $data['gustSpeedData'] = $action->getLatestChartData($gustSpeedData);
            $data['gustSpeedMinMax'] = $action->getMinMaxData($gustSpeedData, [$currentConvertedTime24hourBefore, $currentConvertedTime]);
        }


        $rainFallData = $action->findSensorKey($sensors, 'precipitation_(mm)');

        if (isset($rainFallData['sensor'])) {
            $data['rainFallData'] = $action->getSumChartData($rainFallData, $timeBetween);
            $data['rainFallMonthData'] = $action->getSumChartData($rainFallData, [$beginingMonth9AmConvertedData, $currentConvertedTime]);
            $data['rainFallYearData'] = $action->getSumChartData($rainFallData, [$beginingYear9AmConvertedData, $currentConvertedTime]);
        }

        $uvData = $action->findSensorKey($sensors, 'uv_index');

        if (isset($uvData['sensor']))
            $data['uvData'] = $action->getLatestChartData($uvData);

        $atmosphericPressureData = $action->findSensorKey($sensors, 'atmospheric_pressure_(hPa)');

        if (!isset($atmosphericPressureData['sensor'])) {
            $atmosphericPressureData = $action->findSensorKey($sensors, 'pressure_(hPa)');
            if (isset($atmosphericPressureData['sensor']))
                $data['atmosphericPressureData'] = $action->getLatestChartData($atmosphericPressureData);
        } else {
            $data['atmosphericPressureData'] = $action->getLatestChartData($atmosphericPressureData);
        }

        $relativeHumidityData = $action->findSensorKey($sensors, 'relative_humidity_(%)');

        if (!isset($relativeHumidityData['sensor'])) {
            $relativeHumidityData = $action->findSensorKey($sensors, 'pressure_(hPa)');
            if (isset($relativeHumidityData['sensor']))
                $data['relativeHumidityData'] = $action->getLatestChartData($relativeHumidityData);
            $data['humidityMinMax'] = $action->getMinMaxData($relativeHumidityData, [$currentConvertedTime24hourBefore, $currentConvertedTime]);
        } else {
            $data['relativeHumidityData'] = $action->getLatestChartData($relativeHumidityData);
            $data['humidityMinMax'] = $action->getMinMaxData($relativeHumidityData, [$currentConvertedTime24hourBefore, $currentConvertedTime]);
        }


        $windSensorsData = $action->findSensorKey($sensors, 'wind_direction_(°)');

        if (isset($windSensorsData['sensor']))
            $data['windDirection'] = $action->getLatestChartData($windSensorsData);

        $solarData = $action->findSensorKey($sensors, 'solar_(W/m²)');

        if (isset($solarData['sensor'])) {
            $data['solarData'] = $action->getLatestChartData($solarData);
            $data['solarMinMax'] = $action->getMinMaxData($solarData, [$currentConvertedTime24hourBefore, $currentConvertedTime]);
        }

        $strikesData = $action->findSensorKey($sensors, 'strikes');

        if (isset($strikesData['sensor']))
            $data['strikesData'] = $action->getSumChartData($strikesData, [$currentConvertedTime1hourBefore, $currentConvertedTime]);

        $strikeDistanceData = $action->findSensorKey($sensors, 'strike_distance_(Km)');

        if (isset($strikeDistanceData['sensor']))
            $data['strikeDistanceData'] = $action->getLatestChartData($strikeDistanceData);
        return response()->json($data);
    }
}
