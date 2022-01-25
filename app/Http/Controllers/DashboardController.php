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
            $maxPm1Data = $action->findSensorKey($sensors, 'max_mass_concentration_pm1.0_(µg/m³)');
            $maxPm25Data = $action->findSensorKey($sensors, 'max_mass_concentration_pm2.5_(µg/m³)');
            $maxPm4Data = $action->findSensorKey($sensors, 'max_mass_concentration_pm4.0_(µg/m³)');
            $maxPm10Data = $action->findSensorKey($sensors, 'max_mass_concentration_pm10.0_(µg/m³)');

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

            $data['is_wind_direction'] = isset($windSensorsData['sensor']) ? true : false;
            $data['is_solar'] = isset($solarSensorsData['sensor']) ? true : false;
            $data['is_strikes'] = isset($strikesSensorsData['sensor']) ? true : false;
            $data['is_strike_distance'] = isset($strikeDistanceSensorsData['sensor']) ? true : false;
            $data['is_uv'] = isset($uvData['sensor']) ? true : false;
            $data['is_avg_pm1'] = isset($avgPm1Data['sensor']) ? true : false;
            $data['is_avg_pm25'] = isset($avgPm25Data['sensor']) ? true : false;
            $data['is_avg_pm4'] = isset($avgPm4Data['sensor']) ? true : false;
            $data['is_avg_pm10'] = isset($avgPm10Data['sensor']) ? true : false;
            $data['is_max_pm1'] = isset($maxPm1Data['sensor']) ? true : false;
            $data['is_max_pm25'] = isset($maxPm25Data['sensor']) ? true : false;
            $data['is_max_pm4'] = isset($maxPm4Data['sensor']) ? true : false;
            $data['is_max_pm10'] = isset($maxPm10Data['sensor']) ? true : false;
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
        $currentConvertedTimeStarDay = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($request->currentTime)->startOfDay(), $request->timeZone)->setTimezone(config('app.timezone'))->toDateTimeString();
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

        if (isset($airTempData['sensor'])) {
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
            if (isset($atmosphericPressureData['sensor'])) {
                $data['atmosphericPressureData'] = $action->getLatestChartData($atmosphericPressureData);
                $data['atmosphericPressureMinMax'] = $action->getMinMaxData($atmosphericPressureData, [$currentConvertedTimeStarDay, $currentConvertedTime]);
            }
        } else {
            $data['atmosphericPressureData'] = $action->getLatestChartData($atmosphericPressureData);
            $data['pressureMinMax'] = $action->getMinMaxData($atmosphericPressureData, [$currentConvertedTimeStarDay, $currentConvertedTime]);
        }

        $relativeHumidityData = $action->findSensorKey($sensors, 'relative_humidity_(%)');

        if (!isset($relativeHumidityData['sensor'])) {
            $relativeHumidityData = $action->findSensorKey($sensors, 'humidity_(%)');
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

        if (isset($avgPm25Data['sensor'])) {
            $data['avgPm25Data'] = $action->getAvgData($avgPm25Data, [$currentConvertedTime24hourBefore, $currentConvertedTime]);
            $data['pm25ChartData'] = $action->getPm25ChartData($data['avgPm25Data']->avgData);
        }

        $avgPm4Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm4.0_(µg/m³)');
        if (isset($avgPm4Data['sensor']))
            $data['avgPm4Data'] = $action->getAvgData($avgPm4Data, [$currentConvertedTime24hourBefore, $currentConvertedTime]);

        $avgPm10Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm10.0_(µg/m³)');
        if (isset($avgPm10Data['sensor'])) {
            $data['avgPm10Data'] = $action->getAvgData($avgPm10Data, [$currentConvertedTime24hourBefore, $currentConvertedTime]);
            $data['pm10ChartData'] = $action->getPm10ChartData($data['avgPm10Data']->avgData);
        }

        return response()->json($data);
    }

    public function getAvgPmChartData(Request $request, DashboardAction $action)
    {
        $convertedStartedDate = Carbon::createFromFormat('d/m/Y', $request->dateData[0], $request->timeZone)->startOfDay()->setTimezone(config('app.timezone'))->toDateTimeString();
        $convertedEndedDate = Carbon::createFromFormat('d/m/Y', $request->dateData[1], $request->timeZone)->endOfDay()->setTimezone(config('app.timezone'))->toDateTimeString();
        $sensors = $action->getSensorData($request->id);
        $avgPm1Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm1.0_(µg/m³)');
        $avgPm25Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm2.5_(µg/m³)');
        $avgPm4Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm4.0_(µg/m³)');
        $avgPm10Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm10.0_(µg/m³)');
        $sensorId = isset($avgPm1Data['sensor']) ? $avgPm1Data['sensor']->id : (isset($avgPm25Data['sensor']) ? $avgPm25Data['sensor']->id : (isset($avgPm4Data['sensor']) ? $avgPm4Data['sensor']->id : $avgPm10Data['sensor']->id));
        $coulms = 'id, sensors_id, created_at';
        $coulms .= !empty($avgPm1Data['key']) ? ', D' . $avgPm1Data['key'] . ' AS pm1' : '';
        $coulms .= !empty($avgPm25Data['key']) ? ', D' . $avgPm25Data['key'] . ' AS pm25' : '';
        $coulms .= !empty($avgPm4Data['key']) ? ', D' . $avgPm4Data['key'] . ' AS pm4' : '';
        $coulms .= !empty($avgPm10Data['key']) ? ', D' . $avgPm10Data['key'] . ' AS pm10' : '';

        $avgData = DB::table('base_station_sensors_data')
            ->selectRaw($coulms)
            ->where('sensors_id', $sensorId)
            ->whereBetween('created_at', [$convertedStartedDate, $convertedEndedDate])
            ->oldest()
            ->get();

        $pm1 = [];
        $pm25 = [];
        $pm4 = [];
        $pm10 = [];
        foreach ($avgData as $avg) {
            $labels = strtotime(Carbon::createFromFormat('Y-m-d H:i:s', $avg->created_at, config('app.timezone'))->setTimezone($request->timeZone)) * 1000;
            $pm1[] = [$labels, isset($avg->pm1) ? floatval($avg->pm1) : ''];
            $pm25[] = [$labels, isset($avg->pm25) ? floatval($avg->pm25) : ''];
            $pm4[] = [$labels, isset($avg->pm4) ? floatval($avg->pm4) : ''];
            $pm10[] = [$labels, isset($avg->pm10) ? floatval($avg->pm10) : ''];
        }
        $data['pm1'] = $pm1;
        $data['pm25'] = $pm25;
        $data['pm4'] = $pm4;
        $data['pm10'] = $pm10;
        return response()->json($data);
    }

    public function getMaxPmChartData(Request $request, DashboardAction $action)
    {
        $convertedStartedDate = Carbon::createFromFormat('d/m/Y', $request->dateData[0], $request->timeZone)->startOfDay()->setTimezone(config('app.timezone'))->toDateTimeString();
        $convertedEndedDate = Carbon::createFromFormat('d/m/Y', $request->dateData[1], $request->timeZone)->endOfDay()->setTimezone(config('app.timezone'))->toDateTimeString();
        $sensors = $action->getSensorData($request->id);
        $maxPm1Data = $action->findSensorKey($sensors, 'max_mass_concentration_pm1.0_(µg/m³)');
        $maxPm25Data = $action->findSensorKey($sensors, 'max_mass_concentration_pm2.5_(µg/m³)');
        $maxPm4Data = $action->findSensorKey($sensors, 'max_mass_concentration_pm4.0_(µg/m³)');
        $maxPm10Data = $action->findSensorKey($sensors, 'max_mass_concentration_pm10.0_(µg/m³)');
        $sensorId = isset($maxPm1Data['sensor']) ? $maxPm1Data['sensor']->id : (isset($maxPm25Data['sensor']) ? $maxPm25Data['sensor']->id : (isset($maxPm4Data['sensor']) ? $maxPm4Data['sensor']->id : $maxPm10Data['sensor']->id));
        $coulms = 'id, sensors_id, created_at';
        $coulms .= !empty($maxPm1Data['key']) ? ', D' . $maxPm1Data['key'] . ' AS pm1' : '';
        $coulms .= !empty($maxPm25Data['key']) ? ', D' . $maxPm25Data['key'] . ' AS pm25' : '';
        $coulms .= !empty($maxPm4Data['key']) ? ', D' . $maxPm4Data['key'] . ' AS pm4' : '';
        $coulms .= !empty($maxPm10Data['key']) ? ', D' . $maxPm10Data['key'] . ' AS pm10' : '';

        $maxData = DB::table('base_station_sensors_data')
            ->selectRaw($coulms)
            ->where('sensors_id', $sensorId)
            ->whereBetween('created_at', [$convertedStartedDate, $convertedEndedDate])
            ->oldest()
            ->get();

        $pm1 = [];
        $pm25 = [];
        $pm4 = [];
        $pm10 = [];
        foreach ($maxData as $max) {
            $labels = strtotime(Carbon::createFromFormat('Y-m-d H:i:s', $max->created_at, config('app.timezone'))->setTimezone($request->timeZone)) * 1000;
            $pm1[] = [$labels, isset($max->pm1) ? floatval($max->pm1) : ''];
            $pm25[] = [$labels, isset($max->pm25) ? floatval($max->pm25) : ''];
            $pm4[] = [$labels, isset($max->pm4) ? floatval($max->pm4) : ''];
            $pm10[] = [$labels, isset($max->pm10) ? floatval($max->pm10) : ''];
        }
        $data['pm1'] = $pm1;
        $data['pm25'] = $pm25;
        $data['pm4'] = $pm4;
        $data['pm10'] = $pm10;
        return response()->json($data);
    }

    public function getSolarVoltageChartData(Request $request, DashboardAction $action)
    {
        $convertedStartedDate = Carbon::createFromFormat('d/m/Y', $request->dateData[0], $request->timeZone)->startOfDay()->setTimezone(config('app.timezone'))->toDateTimeString();
        $convertedEndedDate = Carbon::createFromFormat('d/m/Y', $request->dateData[1], $request->timeZone)->endOfDay()->setTimezone(config('app.timezone'))->toDateTimeString();
        $sensors = $action->getSensorData($request->id);
        $solarData = $action->findSensorKey($sensors, 'solar_(W/m²)');
        $sensorId = $solarData['sensor']->id;
        $coulms = 'id, sensors_id, created_at';
        $coulms .= !empty($solarData['key']) ? ', D' . $solarData['key'] . ' AS solar' : '';

        $allSolarData = DB::table('base_station_sensors_data')
            ->selectRaw($coulms)
            ->where('sensors_id', $sensorId)
            ->whereBetween('created_at', [$convertedStartedDate, $convertedEndedDate])
            ->oldest()
            ->get();

        $allBatteriesData = DB::table('base_station_parameters')
            ->selectRaw('id, base_station_id, created_at, battery_voltage AS voltage')
            ->where('base_station_id', $request->id)
            ->whereBetween('created_at', [$convertedStartedDate, $convertedEndedDate])
            ->oldest()
            ->get();

        $solar = [];
        $voltage = [];

        foreach ($allSolarData as $singleSolarData) {
            $labels = strtotime(Carbon::createFromFormat('Y-m-d H:i:s', $singleSolarData->created_at, config('app.timezone'))->setTimezone($request->timeZone)) * 1000;
            $solar[] = [$labels, isset($singleSolarData->solar) ? floatval($singleSolarData->solar) : 0];
        }

        foreach ($allBatteriesData as $singleBatteryData) {
            $labels = strtotime(Carbon::createFromFormat('Y-m-d H:i:s', $singleBatteryData->created_at, config('app.timezone'))->setTimezone($request->timeZone)) * 1000;
            $voltage[] = [$labels, isset($singleBatteryData->voltage) ? floatval($singleBatteryData->voltage) : 0];
        }
        $data['solar'] = $solar;
        $data['voltage'] = $voltage;
        return response()->json($data);
    }

    public function getSolarUvChartData(Request $request, DashboardAction $action)
    {
        $convertedStartedDate = Carbon::createFromFormat('d/m/Y', $request->dateData[0], $request->timeZone)->startOfDay()->setTimezone(config('app.timezone'))->toDateTimeString();
        $convertedEndedDate = Carbon::createFromFormat('d/m/Y', $request->dateData[1], $request->timeZone)->endOfDay()->setTimezone(config('app.timezone'))->toDateTimeString();
        $sensors = $action->getSensorData($request->id);
        $solarData = $action->findSensorKey($sensors, 'solar_(W/m²)');
        $uvData = $action->findSensorKey($sensors, 'uv_index');
        $sensorId = isset($solarData['sensor']) ? $solarData['sensor']->id : $uvData['sensor']->id;
        $coulms = 'id, sensors_id, created_at';
        $coulms .= !empty($solarData['key']) ? ', D' . $solarData['key'] . ' AS solar' : '';
        $coulms .= !empty($uvData['key']) ? ', D' . $uvData['key'] . ' AS uv' : '';

        $allData = DB::table('base_station_sensors_data')
            ->selectRaw($coulms)
            ->where('sensors_id', $sensorId)
            ->whereBetween('created_at', [$convertedStartedDate, $convertedEndedDate])
            ->oldest()
            ->get();

        $solar = [];
        $uv = [];
        foreach ($allData as $singleData) {
            $labels = strtotime(Carbon::createFromFormat('Y-m-d H:i:s', $singleData->created_at, config('app.timezone'))->setTimezone($request->timeZone)) * 1000;
            $solar[] = [$labels, isset($singleData->solar) ? floatval($singleData->solar) : ''];
            $uv[] = [$labels, isset($singleData->uv) ? floatval($singleData->uv) : ''];
        }
        $data['solar'] = $solar;
        $data['uv'] = $uv;
        return response()->json($data);
    }

    public function historicalChart(DashboardAction $action, $id, $chart)
    {
        $chartContainer = [
            'rainfall-raw' => ['precipitation_(mm)'],
            'temperature' => ['air_temperature_(°C)'],
            'msl-pressure' => ['atmospheric_pressure_(hPa)', 'pressure_(hPa)'],
            'humidity' => ['relative_humidity_(%)', 'humidity_(%)'],
            'wind-rose' => ['wind_direction_(°)']
        ];

        abort_if(!array_key_exists($chart, $chartContainer) || !in_array($id, config('basestations.allow')), 404);

        $parameters = $chartContainer[$chart];
        $sensors = $action->getSensorData($id);
        $sensorFlag = false;
        $findSensor = [];
        $singleParam = '';

        foreach ($parameters as $param) {
            $findSensor = $action->findSensorKey($sensors, $param);
            if (isset($findSensor['sensor'])) {
                $sensorFlag = true;
                $singleParam = $param;
                break;
            }
        }

        abort_if(!$sensorFlag, 404);

        $sensorKey = $findSensor['key'];
        $paramId = $findSensor['sensor']->id;

        return view('pages.single-chart', compact('id', 'chart', 'sensorKey', 'singleParam', 'paramId'));
    }

    public function getSingleChartData(Request $request)
    {
        $convertedStartedDate = Carbon::createFromFormat('d/m/Y', $request->dateData[0], $request->timeZone)->startOfDay()->setTimezone(config('app.timezone'))->toDateTimeString();
        $convertedEndedDate = Carbon::createFromFormat('d/m/Y', $request->dateData[1], $request->timeZone)->endOfDay()->setTimezone(config('app.timezone'))->toDateTimeString();
        $sensorId = $request->id;

        $allData = DB::table('base_station_sensors_data')
            ->selectRaw('id, sensors_id, created_at, D' . $request->key . ' AS data')
            ->where('sensors_id', $sensorId)
            ->whereBetween('created_at', [$convertedStartedDate, $convertedEndedDate])
            ->oldest()
            ->get();

        $data = [];
        foreach ($allData as $singleData) {
            $labels = strtotime(Carbon::createFromFormat('Y-m-d H:i:s', $singleData->created_at, config('app.timezone'))->setTimezone($request->timeZone)) * 1000;
            $data[] = [$labels, isset($singleData->data) ? floatval($singleData->data) : 0];
        }
        return response()->json($data);
    }

    public function historicalGuastWindChart(DashboardAction $action, $id)
    {
        abort_if(!in_array($id, config('basestations.allow')), 404);

        $sensors = $action->getSensorData($id);
        $winData = $action->findSensorKey($sensors, 'wind_speed_(m/s)');
        $gustData = $action->findSensorKey($sensors, 'gust_wind_speed_(m/s)');

        abort_if(!isset($winData['sensor'], $gustData['sensor']), 404);

        return view('pages.gust-wind', compact('id'));
    }

    public function getGuastWindChartData(Request $request, DashboardAction $action)
    {
        $convertedStartedDate = Carbon::createFromFormat('d/m/Y', $request->dateData[0], $request->timeZone)->startOfDay()->setTimezone(config('app.timezone'))->toDateTimeString();
        $convertedEndedDate = Carbon::createFromFormat('d/m/Y', $request->dateData[1], $request->timeZone)->endOfDay()->setTimezone(config('app.timezone'))->toDateTimeString();
        $sensors = $action->getSensorData($request->id);
        $gustData = $action->findSensorKey($sensors, 'gust_wind_speed_(m/s)');
        $winData = $action->findSensorKey($sensors, 'wind_speed_(m/s)');
        $sensorId = isset($gustData['sensor']) ? $gustData['sensor']->id : $winData['sensor']->id;
        $coulms = 'id, sensors_id, created_at';
        $coulms .= !empty($gustData['key']) ? ', D' . $gustData['key'] . ' AS gust' : '';
        $coulms .= !empty($winData['key']) ? ', D' . $winData['key'] . ' AS wind' : '';

        $allData = DB::table('base_station_sensors_data')
            ->selectRaw($coulms)
            ->where('sensors_id', $sensorId)
            ->whereBetween('created_at', [$convertedStartedDate, $convertedEndedDate])
            ->oldest()
            ->get();

        $gust = [];
        $wind = [];
        foreach ($allData as $singleData) {
            $labels = strtotime(Carbon::createFromFormat('Y-m-d H:i:s', $singleData->created_at, config('app.timezone'))->setTimezone($request->timeZone)) * 1000;
            $gust[] = [$labels, isset($singleData->gust) ? floatval($singleData->gust) : ''];
            $wind[] = [$labels, isset($singleData->wind) ? floatval($singleData->wind) : ''];
        }
        $data['gust'] = $gust;
        $data['wind'] = $wind;
        return response()->json($data);
    }

    public function historicalRainfallDailyChart(DashboardAction $action, $id)
    {
        abort_if(!in_array($id, config('basestations.allow')), 404);

        $sensors = $action->getSensorData($id);

        $rainFall = $action->findSensorKey($sensors, 'precipitation_(mm)');

        abort_if(!isset($rainFall['sensor']), 404);

        $sensorKey = $rainFall['key'];
        $paramId = $rainFall['sensor']->id;

        return view('pages.rainfall-daily', compact('id', 'sensorKey', 'paramId'));
    }

    public function getRainfallChartDailyData(Request $request)
    {
        $sensorId = $request->id;

        $convertedStartedDate = Carbon::createFromFormat('F Y', $request->dateData['fromDate'], $request->timeZone)->startOfMonth()->setTimezone(config('app.timezone'))->toDateTimeString();
        $convertedEndedDate = Carbon::createFromFormat('F Y', $request->dateData['toDate'], $request->timeZone)->endOfMonth()->setTimezone(config('app.timezone'))->toDateTimeString();

        $allData = DB::table('base_station_sensors_data')
            ->selectRaw("id, sensors_id, created_at, D{$request->key} AS data")
            ->where('sensors_id', $sensorId)
            ->whereBetween('created_at', [$convertedStartedDate, $convertedEndedDate])
            ->oldest()
            ->get()
            ->map(function ($value) use ($request) {
                $createdAt = Carbon::parse($value->created_at)->setTimezone($request->timeZone);
                $value->created_at = $createdAt->toDateTimeString();
                $value->month = $createdAt->format('M');
                $value->day = $createdAt->format('d');
                return $value;
            })
            ->groupBy(['month', 'day']);

        $categories = [];
        $rainFall = [];
        foreach ($allData as $key => $singleData) {
            foreach ($singleData as $singleKey => $singleRow) {
                $categories[] = $key . '-' . $singleKey;
                $rainFall[] = $singleRow->sum('data');
            }
        }
        $data = [
            'categories' => $categories,
            'rainFall' => $rainFall
        ];

        return response()->json($data);
    }

    public function historicalRainfallMonthlyChart(DashboardAction $action, $id)
    {
        abort_if(!in_array($id, config('basestations.allow')), 404);

        $sensors = $action->getSensorData($id);

        $rainFall = $action->findSensorKey($sensors, 'precipitation_(mm)');

        abort_if(!isset($rainFall['sensor']), 404);

        $sensorKey = $rainFall['key'];
        $paramId = $rainFall['sensor']->id;

        return view('pages.rainfall-monthly', compact('id', 'sensorKey', 'paramId'));
    }

    public function getRainfallChartMonthlyData(Request $request)
    {
        $sensorId = $request->id;

        $convertedStartedDate = Carbon::createFromFormat('Y', $request->dateData['fromDate'], $request->timeZone)->startOfYear()->setTimezone(config('app.timezone'))->toDateTimeString();
        $convertedEndedDate = Carbon::createFromFormat('Y', $request->dateData['toDate'], $request->timeZone)->endOfYear()->setTimezone(config('app.timezone'))->toDateTimeString();

        $allData = DB::table('base_station_sensors_data')
            ->selectRaw("id, sensors_id, created_at, D{$request->key} AS data")
            ->where('sensors_id', $sensorId)
            ->whereBetween('created_at', [$convertedStartedDate, $convertedEndedDate])
            ->oldest()
            ->get()
            ->map(function ($value) use ($request) {
                $createdAt = Carbon::parse($value->created_at)->setTimezone($request->timeZone);
                $value->created_at = $createdAt->toDateTimeString();
                $value->year = $createdAt->format('Y');
                $value->month = $createdAt->format('M');
                return $value;
            })
            ->groupBy(['year', 'month']);

        $categories = [];
        $rainFall = [];
        foreach ($allData as $key => $singleData) {
            foreach ($singleData as $singleKey => $singleRow) {
                $categories[] = $singleKey . ' ' . $key;
                $rainFall[] = $singleRow->sum('data');
            }
        }
        $data = [
            'categories' => $categories,
            'rainFall' => $rainFall
        ];

        return response()->json($data);
    }

    public function historicalCombineChart(DashboardAction $action, $id)
    {
        abort_if(!in_array($id, config('basestations.allow')), 404);

        $sensors = $action->getSensorData($id);
        $rainFallData = $action->findSensorKey($sensors, 'precipitation_(mm)');
        $airTempData = $action->findSensorKey($sensors, 'air_temperature_(°C)');
        $atmosphericPressureData = $action->findSensorKey($sensors, 'atmospheric_pressure_(hPa)');
        $relativeHumidityData = $action->findSensorKey($sensors, 'relative_humidity_(%)');
        $windSensorsData = $action->findSensorKey($sensors, 'wind_direction_(°)');
        $windSpeedData = $action->findSensorKey($sensors, 'wind_speed_(m/s)');
        $gustSpeedData = $action->findSensorKey($sensors, 'gust_wind_speed_(m/s)');
        $avgPm1Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm1.0_(µg/m³)');
        $avgPm25Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm2.5_(µg/m³)');
        $avgPm4Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm4.0_(µg/m³)');
        $avgPm10Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm10.0_(µg/m³)');

        $data['is_rain_fall'] = isset($rainFallData['sensor']) ? true : false;
        $data['is_air_temp'] = isset($airTempData['sensor']) ? true : false;
        $data['is_wind_speed'] = isset($windSpeedData['sensor']) ? true : false;
        $data['is_gust_speed'] = isset($gustSpeedData['sensor']) ? true : false;

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

        $data['is_wind_direction'] = isset($windSensorsData['sensor']) ? true : false;
        $data['is_avg_pm1'] = isset($avgPm1Data['sensor']) ? true : false;
        $data['is_avg_pm25'] = isset($avgPm25Data['sensor']) ? true : false;
        $data['is_avg_pm4'] = isset($avgPm4Data['sensor']) ? true : false;
        $data['is_avg_pm10'] = isset($avgPm10Data['sensor']) ? true : false;
        $data['id'] = $id;
        return view('pages.combine', $data);
    }

    public function getCombineChartData(Request $request, DashboardAction $action)
    {
        $convertedStartedDate = Carbon::createFromFormat('d/m/Y', $request->dateData[0], $request->timeZone)->startOfDay()->setTimezone(config('app.timezone'))->toDateTimeString();
        $convertedEndedDate = Carbon::createFromFormat('d/m/Y', $request->dateData[1], $request->timeZone)->endOfDay()->setTimezone(config('app.timezone'))->toDateTimeString();
        $sensors = $action->getSensorData($request->id);
        $rainFallData = $action->findSensorKey($sensors, 'precipitation_(mm)');
        $airTempData = $action->findSensorKey($sensors, 'air_temperature_(°C)');
        $atmosphericPressureData = $action->findSensorKey($sensors, 'atmospheric_pressure_(hPa)');
        if (!isset($atmosphericPressureData['sensor'])) {
            $atmosphericPressureData = $action->findSensorKey($sensors, 'pressure_(hPa)');
        }
        $relativeHumidityData = $action->findSensorKey($sensors, 'relative_humidity_(%)');
        if (!isset($relativeHumidityData['sensor'])) {
            $relativeHumidityData = $action->findSensorKey($sensors, 'humidity_(%)');
        }
        $windDirectionData = $action->findSensorKey($sensors, 'wind_direction_(°)');
        $windSpeedData = $action->findSensorKey($sensors, 'wind_speed_(m/s)');
        $gustSpeedData = $action->findSensorKey($sensors, 'gust_wind_speed_(m/s)');
        $avgPm1Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm1.0_(µg/m³)');
        $avgPm25Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm2.5_(µg/m³)');
        $avgPm4Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm4.0_(µg/m³)');
        $avgPm10Data = $action->findSensorKey($sensors, 'avg_mass_concentration_pm10.0_(µg/m³)');

        $avgSensorId = isset($avgPm1Data['sensor']) ? $avgPm1Data['sensor']->id : (isset($avgPm25Data['sensor']) ? $avgPm25Data['sensor']->id : (isset($avgPm4Data['sensor']) ? $avgPm4Data['sensor']->id : $avgPm10Data['sensor']->id));
        $allSensorId = isset($rainFallData['sensor']) ? $rainFallData['sensor']->id : (isset($airTempData['sensor']) ? $airTempData['sensor']->id : (isset($atmosphericPressureData['sensor']) ? $atmosphericPressureData['sensor']->id : (isset($relativeHumidityData['sensor']) ? $relativeHumidityData['sensor']->id : (isset($windDirectionData['sensor']) ? $windDirectionData['sensor']->id : (isset($windSpeedData['sensor']) ? $windSpeedData['sensor']->id : $gustSpeedData['sensor']->id)))));

        $rainfall = [];
        $airTemp = [];
        $pressure = [];
        $humidity = [];
        $windDirection = [];
        $windSpeed = [];
        $gustSpeed = [];
        $pm1 = [];
        $pm25 = [];
        $pm4 = [];
        $pm10 = [];

        $allcoulms = 'id, sensors_id, created_at';
        $allcoulms .= !empty($rainFallData['key']) ? ', D' . $rainFallData['key'] . ' AS rainfall' : '';
        $allcoulms .= !empty($airTempData['key']) ? ', D' . $airTempData['key'] . ' AS airTemp' : '';
        $allcoulms .= !empty($atmosphericPressureData['key']) ? ', D' . $atmosphericPressureData['key'] . ' AS pressure' : '';
        $allcoulms .= !empty($relativeHumidityData['key']) ? ', D' . $relativeHumidityData['key'] . ' AS humidity' : '';
        $allcoulms .= !empty($windDirectionData['key']) ? ', D' . $windDirectionData['key'] . ' AS windDirection' : '';
        $allcoulms .= !empty($windSpeedData['key']) ? ', D' . $windSpeedData['key'] . ' AS windSpeed' : '';
        $allcoulms .= !empty($gustSpeedData['key']) ? ', D' . $gustSpeedData['key'] . ' AS gustSpeed' : '';

        if ($avgSensorId == $allSensorId) {
            $allcoulms .= !empty($avgPm1Data['key']) ? ', D' . $avgPm1Data['key'] . ' AS pm1' : '';
            $allcoulms .= !empty($avgPm25Data['key']) ? ', D' . $avgPm25Data['key'] . ' AS pm25' : '';
            $allcoulms .= !empty($avgPm4Data['key']) ? ', D' . $avgPm4Data['key'] . ' AS pm4' : '';
            $allcoulms .= !empty($avgPm10Data['key']) ? ', D' . $avgPm10Data['key'] . ' AS pm10' : '';
        } else {
            $avgcoulms = 'id, sensors_id, created_at';
            $avgcoulms .= !empty($avgPm1Data['key']) ? ', D' . $avgPm1Data['key'] . ' AS pm1' : '';
            $avgcoulms .= !empty($avgPm25Data['key']) ? ', D' . $avgPm25Data['key'] . ' AS pm25' : '';
            $avgcoulms .= !empty($avgPm4Data['key']) ? ', D' . $avgPm4Data['key'] . ' AS pm4' : '';
            $avgcoulms .= !empty($avgPm10Data['key']) ? ', D' . $avgPm10Data['key'] . ' AS pm10' : '';

            $avgData = DB::table('base_station_sensors_data')
                ->selectRaw($avgcoulms)
                ->where('sensors_id', $avgSensorId)
                ->whereBetween('created_at', [$convertedStartedDate, $convertedEndedDate])
                ->oldest()
                ->get();

            foreach ($avgData as $avg) {
                $labels = strtotime(Carbon::createFromFormat('Y-m-d H:i:s', $avg->created_at, config('app.timezone'))->setTimezone($request->timeZone)) * 1000;
                $pm1[] = [$labels, isset($avg->pm1) ? floatval($avg->pm1) : 0];
                $pm25[] = [$labels, isset($avg->pm25) ? floatval($avg->pm25) : 0];
                $pm4[] = [$labels, isset($avg->pm4) ? floatval($avg->pm4) : 0];
                $pm10[] = [$labels, isset($avg->pm10) ? floatval($avg->pm10) : 0];
            }
        }


        $allData = DB::table('base_station_sensors_data')
            ->selectRaw($allcoulms)
            ->where('sensors_id', $allSensorId)
            ->whereBetween('created_at', [$convertedStartedDate, $convertedEndedDate])
            ->oldest()
            ->get();

        foreach ($allData as $singleData) {
            $labels = strtotime(Carbon::createFromFormat('Y-m-d H:i:s', $singleData->created_at, config('app.timezone'))->setTimezone($request->timeZone)) * 1000;
            $rainfall[] = [$labels, isset($singleData->rainfall) ? floatval($singleData->rainfall) : 0];
            $airTemp[] = [$labels, isset($singleData->airTemp) ? floatval($singleData->airTemp) : 0];
            $pressure[] = [$labels, isset($singleData->pressure) ? floatval($singleData->pressure) : 0];
            $humidity[] = [$labels, isset($singleData->humidity) ? floatval($singleData->humidity) : 0];
            $windDirection[] = [$labels, isset($singleData->windDirection) ? floatval($singleData->windDirection) : 0];
            $windSpeed[] = [$labels, isset($singleData->windSpeed) ? floatval($singleData->windSpeed) : 0];
            $gustSpeed[] = [$labels, isset($singleData->gustSpeed) ? floatval($singleData->gustSpeed) : 0];
            if ($avgSensorId == $allSensorId) {
                $pm1[] = [$labels, isset($singleData->pm1) ? floatval($singleData->pm1) : 0];
                $pm25[] = [$labels, isset($singleData->pm25) ? floatval($singleData->pm25) : 0];
                $pm4[] = [$labels, isset($singleData->pm4) ? floatval($singleData->pm4) : 0];
                $pm10[] = [$labels, isset($singleData->pm10) ? floatval($singleData->pm10) : 0];
            }
        }
        $data['rainfall'] = $rainfall;
        $data['airTemp'] = $airTemp;
        $data['pressure'] = $pressure;
        $data['humidity'] = $humidity;
        $data['windDirection'] = $windDirection;
        $data['windSpeed'] = $windSpeed;
        $data['gustSpeed'] = $gustSpeed;
        $data['pm1'] = $pm1;
        $data['pm25'] = $pm25;
        $data['pm4'] = $pm4;
        $data['pm10'] = $pm10;
        return response()->json($data);
    }
}

