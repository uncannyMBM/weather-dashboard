<?php

namespace App\Http\Controllers;

use App\Action\DashboardAction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $avgPm1Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm1.0_(µg/m³)');
            $avgPm25Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm2.5_(µg/m³)');
            $avgPm4Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm4.0_(µg/m³)');
            $avgPm10Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm10.0_(µg/m³)');

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
            $data['is_avg_pm1'] = isset($avgPm1Data['sensor']) ? true : false;
            $data['is_avg_pm25'] = isset($avgPm25Data['sensor']) ? true : false;
            $data['is_avg_pm4'] = isset($avgPm4Data['sensor']) ? true : false;
            $data['is_avg_pm10'] = isset($avgPm10Data['sensor']) ? true : false;
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
        $currentConvertedTimeStarDay = Carbon::createFromFormat('Y-m-d H:i:s', $localTime->startOfDay(), $request->timeZone)->setTimezone(config('app.timezone'))->toDateTimeString();
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
            $data['airTempMinMax'] = $action->getMinMaxData($airTempData, [$currentConvertedTimeStarDay, $currentConvertedTime]);
        }


        $windSpeedData = $action->findSensorKey($sensors, 'wind_speed_(m/s)');

        if (isset($windSpeedData['sensor'])) {
            $data['windSpeedData'] = $action->getLatestChartData($windSpeedData);
            $data['windSpeedMinMax'] = $action->getMinMaxData($windSpeedData, [$currentConvertedTimeStarDay, $currentConvertedTime]);
        }


        $gustSpeedData = $action->findSensorKey($sensors, 'gust_wind_speed_(m/s)');

        if (isset($gustSpeedData['sensor'])) {
            $data['gustSpeedData'] = $action->getLatestChartData($gustSpeedData);
            $data['gustSpeedMinMax'] = $action->getMinMaxData($gustSpeedData, [$currentConvertedTimeStarDay, $currentConvertedTime]);
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
            if (isset($atmosphericPressureData['sensor'])){
                $data['atmosphericPressureData'] = $action->getLatestChartData($atmosphericPressureData);
                $data['atmosphericPressureMinMax'] = $action->getMinMaxData($atmosphericPressureData, [$currentConvertedTimeStarDay, $currentConvertedTime]);
            }
        } else {
            $data['atmosphericPressureData'] = $action->getLatestChartData($atmosphericPressureData);
            $data['pressureMinMax'] = $action->getMinMaxData($atmosphericPressureData, [$currentConvertedTimeStarDay, $currentConvertedTime]);
        }

        $relativeHumidityData = $action->findSensorKey($sensors, 'relative_humidity_(%)');

        if (!isset($relativeHumidityData['sensor'])) {
            $relativeHumidityData = $action->findSensorKey($sensors, 'pressure_(hPa)');
            if (isset($relativeHumidityData['sensor']))
                $data['relativeHumidityData'] = $action->getLatestChartData($relativeHumidityData);
            $data['humidityMinMax'] = $action->getMinMaxData($relativeHumidityData, [$currentConvertedTimeStarDay, $currentConvertedTime]);
        } else {
            $data['relativeHumidityData'] = $action->getLatestChartData($relativeHumidityData);
            $data['humidityMinMax'] = $action->getMinMaxData($relativeHumidityData, [$currentConvertedTimeStarDay, $currentConvertedTime]);
        }


        $windSensorsData = $action->findSensorKey($sensors, 'wind_direction_(°)');

        if (isset($windSensorsData['sensor']))
            $data['windDirection'] = $action->getLatestChartData($windSensorsData);

        $solarData = $action->findSensorKey($sensors, 'solar_(W/m²)');

        if (isset($solarData['sensor'])) {
            $data['solarData'] = $action->getLatestChartData($solarData);
            $data['solarMinMax'] = $action->getMinMaxData($solarData, [$currentConvertedTimeStarDay, $currentConvertedTime]);
        }

        $strikesData = $action->findSensorKey($sensors, 'strikes');

        if (isset($strikesData['sensor']))
            $data['strikesData'] = $action->getSumChartData($strikesData, [$currentConvertedTime1hourBefore, $currentConvertedTime]);

        $strikeDistanceData = $action->findSensorKey($sensors, 'strike_distance_(Km)');

        if (isset($strikeDistanceData['sensor']))
            $data['strikeDistanceData'] = $action->getLatestChartData($strikeDistanceData);

        $avgPm1Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm1.0_(µg/m³)');
        if (isset($avgPm1Data['sensor']))
            $data['avgPm1Data'] = $action->getAvgData($avgPm1Data, [$currentConvertedTime24hourBefore, $currentConvertedTime]);

        $avgPm25Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm2.5_(µg/m³)');
        if (isset($avgPm25Data['sensor']))
            $data['avgPm25Data'] = $action->getAvgData($avgPm25Data, [$currentConvertedTime24hourBefore, $currentConvertedTime]);

        $avgPm4Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm4.0_(µg/m³)');
        if (isset($avgPm4Data['sensor']))
            $data['avgPm4Data'] = $action->getAvgData($avgPm4Data, [$currentConvertedTime24hourBefore, $currentConvertedTime]);

        $avgPm10Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm10.0_(µg/m³)');
        if (isset($avgPm10Data['sensor']))
            $data['avgPm10Data'] = $action->getAvgData($avgPm10Data, [$currentConvertedTime24hourBefore, $currentConvertedTime]);

        return response()->json($data);
    }

    public function getAvgPmChartData(Request $request, DashboardAction $action)
    {
        $sensors = $action->getSensorData($request->id);
        $avgPm1Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm1.0_(µg/m³)');
        $avgPm25Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm2.5_(µg/m³)');
        $avgPm4Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm4.0_(µg/m³)');
        $avgPm10Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm10.0_(µg/m³)');
        $avgData = DB::table('base_station_sensors_data')
            ->selectRaw('id ,sensors_id, created_at, AVG(D' . $avgPm1Data['key'] . ') AS pm1, AVG(D' . $avgPm25Data['key'] . ') AS pm25, AVG(D' . $avgPm4Data['key'] . ') AS pm4, AVG(D' . $avgPm10Data['key'] . ') AS pm10')
            ->where('sensors_id', $avgPm1Data['sensor']->id)
            ->whereDate('created_at', '>=', Carbon::createFromFormat('d/m/Y', $request->dateData[0]))
            ->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', $request->dateData[1]))
            ->oldest()
            ->groupBy([DB::raw("DATE_FORMAT(created_at, '%j')"), 'sensors_id'])
            ->get();
        return response()->json($avgData);
    }
}
