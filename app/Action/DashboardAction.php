<?php

namespace App\Action;


use App\Models\BaseStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardAction
{
    public function getBaseStationsData()
    {
        return BaseStation::select('id', 'tag', 'name', 'iot_endpoint_model_id', 'deleted_at')
            ->where('iot_endpoint_model_id', '>=', 13)
            ->where('iot_endpoint_model_id', '<=', 16)
            ->whereNull('deleted_at')
            ->paginate();
    }

    public function getBaseStationById($id)
    {
        return BaseStation::select('id', 'tag', 'name', 'iot_endpoint_model_id', 'time_zone', 'status', 'latest_data_timestamp', 'deleted_at')->findOrFail($id);
    }

    public function getBaseStationByBaseStationId($id)
    {
        return DB::table('base_station_parameters')->select('id', 'battery_voltage', 'rssi', 'is_external_power_available', 'is_charging', 'primary_data_source_types_id')->where('base_station_id', $id)->latest()->first();
    }

    public function getSensorData($id)
    {
        $sensors = DB::table('base_station_sensors')
            ->select('id', 'base_station_id', 'parameters')
            ->where('base_station_id', $id)
            ->get();
        return $sensors;
    }

    public function getLatestChartData($sensor)
    {
        if (!isset($sensor['sensor']))
            return null;
        $sensorData = DB::table('base_station_sensors_data')
            ->selectRaw('id ,sensors_id, ROUND(TRIM(D' . $sensor['key'] . '), 2) AS data')
            ->where('sensors_id', $sensor['sensor']->id)
            ->latest()
            ->first();
        return $sensorData;
    }

    public function getSumChartData($sensor, $timeBetween)
    {
        if (!isset($sensor['sensor']))
            return null;
        $sensorData = DB::table('base_station_sensors_data')
            ->selectRaw('id ,sensors_id, ROUND(SUM(D' . $sensor['key'] . '), 2) AS data')
            ->where('sensors_id', $sensor['sensor']->id)
            ->whereBetween('created_at', $timeBetween)
            ->groupBy('sensors_id')
            ->first();
        return $sensorData;
    }

    public function getMinMaxData($sensor, $timeBetween)
    {
        if (!isset($sensor['sensor']))
            return null;
        $sensorData = DB::table('base_station_sensors_data')
            ->selectRaw('id ,sensors_id, ROUND(MIN(D' . $sensor['key'] . '), 2) AS minData, ROUND(MAX(D' . $sensor['key'] . '), 2) AS maxData')
            ->where('sensors_id', $sensor['sensor']->id)
            ->whereBetween('created_at', $timeBetween)
            ->groupBy('sensors_id')
            ->first();
        return $sensorData;
    }

    public function getAvgData($sensor, $timeBetween)
    {
        if (!isset($sensor['sensor']))
            return null;
        $sensorData = DB::table('base_station_sensors_data')
            ->selectRaw('id ,sensors_id, ROUND(AVG(D' . $sensor['key'] . '), 2) AS avgData')
            ->where('sensors_id', $sensor['sensor']->id)
            ->whereBetween('created_at', $timeBetween)
            ->groupBy('sensors_id')
            ->first();
        return $sensorData;
    }

    public function findSensorKey($sensors, $key)
    {
        if (!$sensors)
            return false;
        foreach ($sensors as $sensor) {
            $data['parameters'] = array_map('trim', explode(",", $sensor->parameters));
            $data['key'] = array_search($key, $data['parameters']);
            if ($data['key'] > -1) {
                $data['sensor'] = $sensor;
                $data['key'] = $data['key'] + 1;
                return $data;
            }
        }
        return $data;
    }

    public function getPm25ChartData($avg)
    {
        $breaksPoints_pm_2_5 = config('aqibreakpoints.pm25');
        foreach ($breaksPoints_pm_2_5 as $key => $value) {
            if (($value["bpLo"] <= $avg) && ($avg <= $value["bpHi"])) {
                $aqi = ((($value["iHi"] - $value["iLo"]) / ($value["bpHi"] - $value["bpLo"])) * ($avg - $value["bpLo"])) + $value["iLo"];
                $aqi = round($aqi, 2);
                if ($key == 6) {
                    return ["aqi" => $aqi, "info" => $breaksPoints_pm_2_5[$key]['info'], "start_value" => $value["iLo"], "end_value" => $breaksPoints_pm_2_5[$key]["iHi"]];
                } else {
                    return ["aqi" => $aqi, "info" => $breaksPoints_pm_2_5[$key]['info'], "start_value" => $value["iLo"], "end_value" => $breaksPoints_pm_2_5[$key + 1]["iHi"]];
                }
            }
        }
    }

    public function getPm10ChartData($avg)
    {
        $breaksPoints_pm_10 = config('aqibreakpoints.pm10');
        foreach ($breaksPoints_pm_10 as $key => $value) {
            if (($value["bpLo"] <= $avg) && ($avg <= $value["bpHi"])) {
                $aqi = ((($value["iHi"] - $value["iLo"]) / ($value["bpHi"] - $value["bpLo"])) * ($avg - $value["bpLo"])) + $value["iLo"];
                $aqi = round($aqi, 2);
                return ["aqi" => $aqi, "start_value" => $value["iLo"], "end_value" => $breaksPoints_pm_10[$key + 1]["iHi"]];
            }
        }
    }
}