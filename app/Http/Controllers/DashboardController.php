<?php

namespace App\Http\Controllers;

use App\Action\DashboardAction;
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
            $data = $action->getSensorData($id);
            $data['base'] = $base;
            $data['is_wind_direction'] = isset($data['sensor']) ? true : false;
            return view('pages.dashboard', $data);
        } catch (\Exception $exception) {
            return back()->with('alert', $exception->getMessage());
        }
    }

    public function getChartData(Request $request, DashboardAction $action)
    {
        $data['windDirection'] = $action->getWindDirectionChartData($request);
        return response()->json($data);
    }
}
