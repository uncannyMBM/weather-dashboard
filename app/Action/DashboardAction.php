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
        return BaseStation::select('id', 'tag', 'name', 'iot_endpoint_model_id', 'deleted_at')->findOrFail($id);
    }

    public function getSensorData($id)
    {
        $data['sensor'] = DB::table('base_station_sensors')
            ->select('id', 'base_station_id', 'parameters')
            ->where('base_station_id', $id)
            ->first();

        if (!$data['sensor'])
            return false;
        $data['parameters'] = array_map('trim', explode(",", $data['sensor']->parameters));
        $data['key'] = array_search('wind_direction_(°)', $data['parameters']);
        return $data;
    }

    public function getWindDirectionChartData(Request $request)
    {
        $sensor = $this->getSensorData($request->id);
        if (!$sensor)
            return null;
        $sensorData = DB::table('base_station_sensors_data')
            ->selectRaw('id ,sensors_id, TRIM(D' . $sensor['key'] . ') AS data')
            ->where('sensors_id', $sensor['sensor']->id)
            ->latest()
            ->first();
        return $sensorData;
    }
}