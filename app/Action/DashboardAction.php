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
            ->selectRaw('id ,sensors_id, TRIM(D' . $sensor['key'] . ') AS data')
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
            ->selectRaw('id ,sensors_id, SUM(D' . $sensor['key'] . ') AS data')
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
            ->selectRaw('id ,sensors_id, MIN(D' . $sensor['key'] . ') AS minData, MAX(D' . $sensor['key'] . ') AS maxData')
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
}