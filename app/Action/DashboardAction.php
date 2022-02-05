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
                return ["aqi" => $aqi, "info" => $breaksPoints_pm_10[$key]['info'], "start_value" => $value["iLo"], "end_value" => $breaksPoints_pm_10[$key + 1]["iHi"]];
            }
        }
    }

    public function getSiteDataSourcesBySensorId($id, $siteId)
    {
        return DB::table('sites_data_sources')->select('id', 'sites_id', 'data_source_id', 'deleted_at')->whereNull('deleted_at')->where('data_source_id', $id)->where('sites_id', $siteId)->latest()->first();
    }

    public function paramsDerived($id)
    {
        return DB::table('parameters_derived')->select('id', 'name', 'site_data_sources_id', 'deleted_at')->whereNull('deleted_at')->where('site_data_sources_id', $id)->latest()->first();
    }

    public function getFuncArg($id)
    {
        return DB::table('functions_arguments')->select('id', 'functions_instances_id', 'arg2')->where('functions_instances_id', $id)->latest()->first();
    }

    public function getFDI($windSpeedInKmPerH, $droughtFactor, $humidity, $temperature)
    {
        $info = array('FDI' => null, 'FDR' => null, "errorStatus" => false, "errorShortInfo" => null, "errorInfo" => null);
        try {
            $FDI = round(2 * exp(-0.45 + (0.987 * log($droughtFactor)) - (0.0345 * $humidity) + (0.0338 * $temperature) + (0.0234 * $windSpeedInKmPerH)), 0);
            $info['FDI'] = $FDI;
            if ($FDI >= 0 && $FDI < 12) {
                $info['FDR'] = "Low-Moderate";
            } else if ($FDI >= 12 && $FDI < 25) {
                $info['FDR'] = "High";
            } else if ($FDI >= 25 && $FDI < 50) {
                $info['FDR'] = "Very High";
            } else if ($FDI >= 50 && $FDI < 75) {
                $info['FDR'] = "Severe";
            } else if ($FDI >= 75 && $FDI < 99) {
                $info['FDR'] = "Extreme";
            } else if ($FDI >= 100) {
                $info['FDR'] = "Catastrophic";
            }
            return $info;
        } catch (Exception $e) {
            $info['errorStatus'] = true;
            $info['errorShortInfo'] = "Error in deriveAwsFDI ";
            $info['errorInfo'] = $e;
            return $info;
        }
    }
}