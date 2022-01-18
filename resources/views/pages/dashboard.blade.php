@extends('layouts.app')
@push('extra_styles')
    <link href="https://cdn.anychart.com/releases/v8/css/anychart-ui.min.css" type="text/css" rel="stylesheet">
    <link href="https://cdn.anychart.com/releases/v8/fonts/css/anychart-font.min.css" type="text/css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <style>
        [v-cloak] {
            display: none;
        }

        .anychart-credits, .highcharts-credits {
            display: none !important;
        }
    </style>
@endpush
@section('content')
    @include('partials.alert')
    <div id="main-charts">
        <div class="row mt-1">
            <div class="col-md-12">
                <div class="p-3 px-md-4 mb-3 bg-secondary border-bottom box-shadow">
                    <ul class="mb-0 list-inline d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <li class="list-inline-item text-white">State: <span
                                    class="text-warning">{{ $base->status == 1 ? 'Active' : ($base->status == 2 ? 'Data Fault' : ($base->status == 3 ? 'Inactive' : (($base->status == 4 ? 'No Data' : (($base->status == 6 ? 'Terminated' : '')))))) }}</span>
                        </li>
                        <li class="list-inline-item text-white">Base Station Name: <span
                                    class="text-warning">{{ $base->name }}</span>
                        </li>
                        <li class="list-inline-item text-white">Battery voltage:
                            <span class="text-warning">{{ $baseParams->battery_voltage }} V</span></li>
                        <li class="list-inline-item text-white">RSSI: <span
                                    class="text-warning">{{ $baseParams->rssi }}</span>
                        </li>
                        <li class="list-inline-item text-white">External Power: <span
                                    class="text-warning">{{ $baseParams->is_external_power_available == 1 ? 'Yes' : 'No' }}</span>
                        </li>
                        <li class="list-inline-item text-white">Charging: <span
                                    class="text-warning">{{ $baseParams->is_charging == 1 ? 'Yes' : 'No' }}</span></li>
                        <li class="list-inline-item text-white">Data Transfer Mode: <span
                                    class="text-warning">{{ $baseParams->primary_data_source_types_id == 4 ? 'MQTT Mode' : ' ' }}</span>
                        </li>
                        <li class="list-inline-item text-white">At: <span
                                    class="text-warning">{{ \Carbon\Carbon::parse($base->latest_data_timestamp)->format('d-m-Y h:i:s A') }}</span>
                        </li>
                        <li class="list-inline-item text-white">Timezone: <span
                                    class="text-warning">{{ $base->time_zone }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            @if($is_air_temp)
                <div class="col-12 col-lg-3 mb-2 mb-lg-0">
                    <div class="card">
                        <div class="card-header">
                            <h6>Air Temp: <span v-cloak>@{{ air_temp }}</span><sup
                                        style="font-size: 16px;">°</sup></h6>
                        </div>
                        <div class="card-body">
                            <div id="air-temp-chart" style="width: 100%;height: 300px;"></div>
                        </div>
                    </div>
                </div>
            @endif
            @if($is_wind_speed || $is_gust_speed)
                <div class="col-12 col-lg-3 mb-2 mb-lg-0">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Wind Speed: <span v-cloak>@{{ wind_speed }}</span> <sub>(m/s)</sub></h6>
                                </div>
                                <div class="col-md-6 pull-right">
                                    <h6>Gust Speed: <span v-cloak>@{{ gust_speed }}</span> <sub>(m/s)</sub></h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="wind-gust-speed-chart" style="width: 100%;height: 300px;"></div>
                        </div>
                    </div>
                </div>
            @endif
            @if($is_rain_fall)
                <div class="col-6 col-lg-2">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>Rainfall: <span v-cloak>@{{ rain_fall }}</span> <sub>(mm)</sub></h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="rainfall-chart" style="width: 100%;height: 300px;"></div>
                        </div>
                    </div>
                </div>
            @endif
            @if($is_uv)
                <div class="col-6 col-lg-2 mb-2 mb-lg-0">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>UV: <span v-cloak>@{{ uv }}</span></h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="uv-chart" style="width: 100%;height: 300px;"></div>
                        </div>
                    </div>
                </div>
            @endif
            @if($is_wind_direction)
                <div class="col-12 col-lg-2 mb-2 mb-lg-0">
                    <div class="card">
                        <div class="card-header">
                            <h6>Wind Direction: <span v-cloak>@{{ wind_direction }}</span><sup
                                        style="font-size: 16px;">°</sup>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="wind-direction-chart" style="width: 100%;height: 300px;"></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="row mt-0 mt-lg-5">
            @if($is_atmospheric_pressure)
                <div class="col-12 col-lg-3 mb-2 mb-lg-0">
                    <div class="card">
                        <div class="card-header">
                            <h6>Atmospheric Pressure: <span v-cloak>@{{ atmospheric_pressure }}</span> <sub>(hPa)</sub>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="atmospheric-pressure-chart" style="width: 100%;height: 300px;"></div>
                        </div>
                    </div>
                </div>
            @endif
            @if($is_relative_humidity)
                <div class="col-12 col-lg-3 mb-2 mb-lg-0">
                    <div class="card">
                        <div class="card-header">
                            <h6>Relative Humidity: <span v-cloak>@{{ relative_humidity }}%</span></h6>
                        </div>
                        <div class="card-body">
                            <div id="relative-humidity-chart" style="width: 100%;height: 300px;"></div>
                        </div>
                    </div>
                </div>
            @endif
            @if($is_solar)
                <div class="col-12 col-lg-3 mb-2 mb-lg-0">
                    <div class="card">
                        <div class="card-header">
                            <h6>Solar: <span v-cloak>@{{ solar }}</span> <sub>(hPa)</sub>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="solar-chart" style="width: 100%;height: 300px;"></div>
                        </div>
                    </div>
                </div>
            @endif
            @if($is_strikes || $is_strike_distance)
                <div class="col-12 col-lg-3 mb-2 mb-lg-0">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Strikes: <span v-cloak>@{{ strikes }}</span></h6>
                                </div>
                                <div class="col-md-6 pull-right">
                                    <h6>Strike Distance: <span v-cloak>@{{ strike_distance }}</span>
                                        <sub>(Km)</sub>
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="strikes-chart" style="width: 100%;height: 300px;"></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="row mt-0 mt-lg-5 mb-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6>Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                            @if($is_rain_fall)
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Today Rainfall</h6>
                                        <p class="card-text blue-text"><i class="fas fa-cloud-rain fa-2x"></i>
                                            <span class="ml-2" v-cloak style="font-size: 30px;">@{{ rain_fall }}</span>
                                            mm
                                        </p>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">This Month Rainfall</h6>
                                        <p class="card-text blue-text"><i class="fas fa-cloud-rain fa-2x"></i>
                                            <span class="ml-2" v-cloak
                                                  style="font-size: 30px;">@{{ monthly_rain_fall }}</span> mm
                                        </p>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">This Year Rainfall</h6>
                                        <p class="card-text blue-text"><i class="fas fa-cloud-rain fa-2x"></i>
                                            <span class="ml-2" v-cloak
                                                  style="font-size: 30px;">@{{ yearly_rain_fall }}</span> mm
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if($is_relative_humidity)
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Humidity Min</h6>
                                        <p class="card-text blue-text"><i class="fas fa-cloud-sun fa-2x"></i>
                                            <span class="ml-2" v-cloak
                                                  style="font-size: 30px;">@{{ humidity_min }}</span>%
                                        </p>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Humidity Max</h6>
                                        <p class="card-text blue-text"><i class="fas fa-cloud-sun fa-2x"></i>
                                            <span class="ml-2" v-cloak
                                                  style="font-size: 30px;">@{{ humidity_max }}</span>%
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if($is_solar)
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Solar Min</h6>
                                        <p class="card-text blue-text"><i class="fas fa-bolt fa-2x"></i>
                                            <span class="ml-2" v-cloak style="font-size: 30px;">@{{ solar_min }}</span>
                                            hpa
                                        </p>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Solar Max</h6>
                                        <p class="card-text blue-text"><i class="fas fa-bolt fa-2x"></i>
                                            <span class="ml-2" v-cloak style="font-size: 30px;">@{{ solar_max }}</span>
                                            hpa
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if($is_gust_speed)
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Guast Min</h6>
                                        <p class="card-text blue-text"><i class="fas fa-wind fa-2x"></i>
                                            <span class="ml-2" v-cloak
                                                  style="font-size: 30px;">@{{ gust_speed_min }}</span> m/s
                                        </p>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Guast Max</h6>
                                        <p class="card-text blue-text"><i class="fas fa-wind fa-2x"></i>
                                            <span class="ml-2" v-cloak
                                                  style="font-size: 30px;">@{{ gust_speed_max }}</span> m/s
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="mt-2 d-flex flex-column flex-md-row justify-content-between align-items-center">
                            @if($is_wind_speed)
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Wind Min</h6>
                                        <p class="card-text blue-text"><i class="fas fa-wind fa-2x"></i>
                                            <span class="ml-2" v-cloak
                                                  style="font-size: 30px;">@{{ wind_speed_min }}</span> m/s
                                        </p>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Wind Max</h6>
                                        <p class="card-text blue-text"><i class="fas fa-wind fa-2x"></i>
                                            <span class="ml-2" v-cloak
                                                  style="font-size: 30px;">@{{ wind_speed_max }}</span> m/s
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if($is_air_temp)
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Temperature Min</h6>
                                        <p class="card-text blue-text"><i class="fas fa-temperature-low fa-2x"></i>
                                            <span class="ml-2" v-cloak style="font-size: 30px;">@{{ air_temp_min }}<sup
                                                        style="font-size: 16px;">°</sup></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Temperature Max</h6>
                                        <p class="card-text blue-text"><i class="fas fa-temperature-high fa-2x"></i>
                                            <span class="ml-2" v-cloak style="font-size: 30px;">@{{ air_temp_max }}<sup
                                                        style="font-size: 16px;">°</sup></span>
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if($is_atmospheric_pressure)
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">MSL Pressure Min</h6>
                                        <p class="card-text blue-text"><i
                                                    class="fas fa-tire-pressure-warning fa-2x"></i>
                                            <span class="ml-2" v-cloak
                                                  style="font-size: 30px;">@{{ msl_pressure_min }}</span> hPa
                                        </p>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">MSL Pressure Max</h6>
                                        <p class="card-text blue-text"><i
                                                    class="fas fa-tire-pressure-warning fa-2x"></i>
                                            <span class="ml-2" v-cloak
                                                  style="font-size: 30px;">@{{ msl_pressure_max }}</span> hPa
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if($is_avg_pm1)
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Average PM1</h6>
                                        <p class="card-text blue-text"><i class="fas fa-clock fa-2x"></i>
                                            <span class="ml-2" v-cloak style="font-size: 30px;">@{{ avg_pm_1 }}</span>
                                            µg/m³
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if($is_avg_pm25)
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Average PM2.5</h6>
                                        <p class="card-text blue-text"><i class="fas fa-clock fa-2x"></i>
                                            <span class="ml-2" v-cloak style="font-size: 30px;">@{{ avg_pm_25 }}</span>
                                            µg/m³
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if($is_avg_pm4)
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Average PM4</h6>
                                        <p class="card-text blue-text"><i class="fas fa-clock fa-2x"></i>
                                            <span class="ml-2" v-cloak style="font-size: 30px;">@{{ avg_pm_4 }}</span>
                                            µg/m³
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if($is_avg_pm10)
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Average PM10</h6>
                                        <p class="card-text blue-text"><i class="fas fa-clock fa-2x"></i>
                                            <span class="ml-2" v-cloak style="font-size: 30px;">@{{ avg_pm_10 }}</span>
                                            µg/m³
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($is_avg_pm1 || $is_avg_pm25 || $is_avg_pm4 || $is_avg_pm10)
            <div class="row mt-0 mt-lg-5 mb-5">
                <div class="col-md-12">
                    <div class="card avg_mass_chart_loader">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Dust Monitor Average Mass</h6>
                                </div>
                                <div class="col-md-6">
                                    <input class="float-right" type="text" name="avg_mass_datepicker" value=""
                                           readonly/>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="avg_mass_chart" style="width: 100%;height: 500px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if($is_max_pm1 || $is_max_pm25 || $is_max_pm4 || $is_max_pm10)
            <div class="row mt-0 mt-lg-5 mb-5">
                <div class="col-md-12">
                    <div class="card max_mass_chart_loader">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Dust Monitor Max Mass</h6>
                                </div>
                                <div class="col-md-6">
                                    <input class="float-right" type="text" name="max_mass_datepicker" value=""
                                           readonly/>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="max_mass_chart" style="width: 100%;height: 500px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if($is_solar)
            <div class="row mt-0 mt-lg-5 mb-5">
                <div class="col-md-12">
                    <div class="card solar_voltage_chart_loader">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Dust Monitor Solar and Battery Voltage</h6>
                                </div>
                                <div class="col-md-6">
                                    <input class="float-right" type="text" name="solar_voltage_datepicker" value=""
                                           readonly/>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="solar_voltage_chart" style="width: 100%;height: 500px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if($is_solar && $is_uv)
            <div class="row mt-0 mt-lg-5 mb-5">
                <div class="col-md-12">
                    <div class="card solar_uv_chart_loader">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Dust Monitor Solar and UV</h6>
                                </div>
                                <div class="col-md-6">
                                    <input class="float-right" type="text" name="solar_uv_datepicker" value=""
                                           readonly/>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="solar_uv_chart" style="width: 100%;height: 500px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
@push('extra_scripts')
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-ui.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-exports.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-circular-gauge.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-linear-gauge.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/boost.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    @if($is_air_temp)
        <script src="{{ asset('js/air-temparature.js') }}"></script>
    @endif
    @if($is_wind_speed || $is_gust_speed)
        <script src="{{ asset('js/wind-gust-speed.js') }}"></script>
    @endif
    @if($is_rain_fall)
        <script src="{{ asset('js/rainfall.js') }}"></script>
    @endif
    @if($is_uv)
        <script src="{{ asset('js/uv.js') }}"></script>
    @endif
    @if($is_atmospheric_pressure)
        <script src="{{ asset('js/atmospheric-pressure.js') }}"></script>
    @endif
    @if($is_relative_humidity)
        <script src="{{ asset('js/relative-humidity.js') }}"></script>
    @endif
    @if($is_wind_direction)
        <script src="{{ asset('js/wind-direction.js') }}"></script>
    @endif
    @if($is_solar)
        <script src="{{ asset('js/solar.js') }}"></script>
    @endif
    @if($is_strikes || $is_strike_distance)
        <script src="{{ asset('js/strikes.js') }}"></script>
    @endif
@endpush
@section('scripts')
    <script>
        let timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    </script>
    @if($is_avg_pm1 || $is_avg_pm25 || $is_avg_pm4 || $is_avg_pm10)
        <script>
            $(function () {
                let pm1Series = "{{ $is_avg_pm1 }}";
                let pm25Series = "{{ $is_avg_pm25 }}";
                let pm4Series = "{{ $is_avg_pm4 }}";
                let pm10Series = "{{ $is_avg_pm10 }}";
                $('input[name="avg_mass_datepicker"]').val(moment().subtract(7, 'days').format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY'));

                $('input[name="avg_mass_datepicker"]').daterangepicker({
                    autoUpdateInput: false,
                    startDate: moment().subtract(7, 'days'),
                    endDate: moment(),
                    minDate: moment().subtract(1, 'year'),
                    maxDate: moment(),
                    showDropdowns: true,
                    locale: {
                        cancelLabel: 'Clear'
                    }
                });

                $('input[name="avg_mass_datepicker"]').on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                    getAvgChartData();
                });

                function getAvgChartData() {
                    Notiflix.Block.dots('.avg_mass_chart_loader');
                    let dateData = $('input[name="avg_mass_datepicker"]').val().split("-").map(item => item.trim());
                    $.ajax({
                        url: "{{ route('get.avg.pm.chart.data') }}",
                        method: "GET",
                        data: {dateData: dateData, timeZone: timeZone, id: "{{ $base->id }}"},
                        success: function (response) {
                            let pmSeries = [];
                            let pmColors = [];
                            if (pm1Series){
                                pmColors.push('#1abc9c');
                                pmSeries.push({
                                    data: response.pm1,
                                    lineWidth: 0.5,
                                    name: 'PM 1.0'
                                });
                            }
                            if (pm25Series) {
                                pmColors.push('#9b59b6');
                                pmSeries.push({
                                    data: response.pm25,
                                    lineWidth: 0.5,
                                    name: 'PM 2.5'
                                });
                            }
                            if (pm4Series) {
                                pmColors.push('#f39c12');
                                pmSeries.push({
                                    data: response.pm4,
                                    lineWidth: 0.5,
                                    name: 'PM 4.0'
                                });
                            }
                            if (pm10Series) {
                                pmColors.push('#e74c3c');
                                pmSeries.push({
                                    data: response.pm10,
                                    lineWidth: 0.5,
                                    name: 'PM 10.0'
                                });
                            }
                            Highcharts.chart('avg_mass_chart', {
                                chart: {
                                    zoomType: 'x'
                                },
                                title: {
                                    text: ''
                                },
                                tooltip: {
                                    valueDecimals: 2,
                                    dateTimeLabelFormats: {
                                        day: "%A, %b %e, %Y, %H:%M"
                                    },
                                    formatter: function () {
                                        var tt = '',
                                            newDate = Highcharts.dateFormat('%d/%m/%Y %H:%M:%S', this.key);
                                        tt = '<b>' + newDate + '</b> <br/><br/>' + '<b>' + this.series.name + ': </b>' + this.y;
                                        return tt;
                                    }
                                },

                                xAxis: {
                                    type: 'datetime',
                                },
                                yAxis: {
                                    title: {
                                        text: 'Avrage Mass Concentration (µg/m³)'
                                    },
                                },
                                colors: pmColors,
                                series: pmSeries
                            });

                            Notiflix.Block.remove('.avg_mass_chart_loader');
                        }
                    });
                }

                getAvgChartData();
            });
        </script>
    @endif
    @if($is_max_pm1 || $is_max_pm25 || $is_max_pm4 || $is_max_pm10)
        <script>
            $(function () {
                let pm1Series = "{{ $is_max_pm1 }}";
                let pm25Series = "{{ $is_max_pm25 }}";
                let pm4Series = "{{ $is_max_pm4 }}";
                let pm10Series = "{{ $is_max_pm10 }}";
                $('input[name="max_mass_datepicker"]').val(moment().subtract(7, 'days').format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY'));

                $('input[name="max_mass_datepicker"]').daterangepicker({
                    autoUpdateInput: false,
                    startDate: moment().subtract(7, 'days'),
                    endDate: moment(),
                    minDate: moment().subtract(1, 'year'),
                    maxDate: moment(),
                    showDropdowns: true,
                    locale: {
                        cancelLabel: 'Clear'
                    }
                });

                $('input[name="max_mass_datepicker"]').on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                    getAvgChartData();
                });

                function getAvgChartData() {
                    Notiflix.Block.dots('.max_mass_chart_loader');
                    let dateData = $('input[name="max_mass_datepicker"]').val().split("-").map(item => item.trim());
                    $.ajax({
                        url: "{{ route('get.max.pm.chart.data') }}",
                        method: "GET",
                        data: {dateData: dateData, timeZone: timeZone, id: "{{ $base->id }}"},
                        success: function (response) {
                            let pmSeries = [];
                            let pmColors = [];
                            if (pm1Series){
                                pmColors.push('#1abc9c');
                                pmSeries.push({
                                    data: response.pm1,
                                    lineWidth: 0.5,
                                    name: 'PM 1.0'
                                });
                            }
                            if (pm25Series) {
                                pmColors.push('#9b59b6');
                                pmSeries.push({
                                    data: response.pm25,
                                    lineWidth: 0.5,
                                    name: 'PM 2.5'
                                });
                            }
                            if (pm4Series) {
                                pmColors.push('#f39c12');
                                pmSeries.push({
                                    data: response.pm4,
                                    lineWidth: 0.5,
                                    name: 'PM 4.0'
                                });
                            }
                            if (pm10Series) {
                                pmColors.push('#e74c3c');
                                pmSeries.push({
                                    data: response.pm10,
                                    lineWidth: 0.5,
                                    name: 'PM 10.0'
                                });
                            }
                            Highcharts.chart('max_mass_chart', {
                                chart: {
                                    zoomType: 'x'
                                },
                                title: {
                                    text: ''
                                },
                                tooltip: {
                                    valueDecimals: 2,
                                    dateTimeLabelFormats: {
                                        day: "%A, %b %e, %Y, %H:%M"
                                    },
                                    formatter: function () {
                                        var tt = '',
                                            newDate = Highcharts.dateFormat('%d/%m/%Y %H:%M:%S', this.key);
                                        tt = '<b>' + newDate + '</b> <br/><br/>' + '<b>' + this.series.name + ': </b>' + this.y;
                                        return tt;
                                    }
                                },

                                xAxis: {
                                    type: 'datetime',
                                },
                                yAxis: {
                                    title: {
                                        text: 'Max Mass Concentration (µg/m³)'
                                    },
                                },
                                colors: pmColors,
                                series: pmSeries
                            });

                            Notiflix.Block.remove('.max_mass_chart_loader');
                        }
                    });
                }

                getAvgChartData();
            });
        </script>
    @endif
    @if($is_solar)
        <script>
            $(function () {
                $('input[name="solar_voltage_datepicker"]').val(moment().subtract(7, 'days').format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY'));

                $('input[name="solar_voltage_datepicker"]').daterangepicker({
                    autoUpdateInput: false,
                    startDate: moment().subtract(7, 'days'),
                    endDate: moment(),
                    minDate: moment().subtract(1, 'year'),
                    maxDate: moment(),
                    showDropdowns: true,
                    locale: {
                        cancelLabel: 'Clear'
                    }
                });

                $('input[name="solar_voltage_datepicker"]').on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                    getAvgChartData();
                });

                function getAvgChartData() {
                    Notiflix.Block.dots('.solar_voltage_chart_loader');
                    let dateData = $('input[name="solar_voltage_datepicker"]').val().split("-").map(item => item.trim());
                    $.ajax({
                        url: "{{ route('get.solar.voltage.chart.data') }}",
                        method: "GET",
                        data: {dateData: dateData, timeZone: timeZone, id: "{{ $base->id }}"},
                        success: function (response) {
                            Highcharts.chart('solar_voltage_chart', {
                                chart: {
                                    zoomType: 'x'
                                },
                                title: {
                                    text: ''
                                },
                                tooltip: {
                                    valueDecimals: 2,
                                    dateTimeLabelFormats: {
                                        day: "%A, %b %e, %Y, %H:%M"
                                    },
                                    formatter: function () {
                                        var tt = '',
                                            newDate = Highcharts.dateFormat('%d/%m/%Y %H:%M:%S', this.key);
                                        tt = '<b>' + newDate + '</b> <br/><br/>' + '<b>' + this.series.name + ': </b>' + this.y;
                                        return tt;
                                    }
                                },

                                xAxis: {
                                    type: 'datetime',
                                },
                                yAxis: {
                                    title: {
                                        text: 'Solar and Voltage'
                                    },
                                },
                                colors: ['#1abc9c', '#9b59b6'],
                                series: [{
                                    data: response.solar,
                                    lineWidth: 0.5,
                                    name: 'solar_(W/m²)'
                                },{
                                    data: response.voltage,
                                    lineWidth: 0.5,
                                    name: 'voltage_(V)'
                                }]
                            });

                            Notiflix.Block.remove('.solar_voltage_chart_loader');
                        }
                    });
                }

                getAvgChartData();
            });
        </script>
    @endif
    @if($is_solar && $is_uv)
        <script>
            $(function () {
                $('input[name="solar_uv_datepicker"]').val(moment().subtract(7, 'days').format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY'));

                $('input[name="solar_uv_datepicker"]').daterangepicker({
                    autoUpdateInput: false,
                    startDate: moment().subtract(7, 'days'),
                    endDate: moment(),
                    minDate: moment().subtract(1, 'year'),
                    maxDate: moment(),
                    showDropdowns: true,
                    locale: {
                        cancelLabel: 'Clear'
                    }
                });

                $('input[name="solar_uv_datepicker"]').on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                    getAvgChartData();
                });

                function getAvgChartData() {
                    Notiflix.Block.dots('.solar_uv_chart_loader');
                    let dateData = $('input[name="solar_uv_datepicker"]').val().split("-").map(item => item.trim());
                    $.ajax({
                        url: "{{ route('get.solar.uv.chart.data') }}",
                        method: "GET",
                        data: {dateData: dateData, timeZone: timeZone, id: "{{ $base->id }}"},
                        success: function (response) {
                            Highcharts.chart('solar_uv_chart', {
                                chart: {
                                    zoomType: 'x'
                                },
                                title: {
                                    text: ''
                                },
                                tooltip: {
                                    valueDecimals: 2,
                                    dateTimeLabelFormats: {
                                        day: "%A, %b %e, %Y, %H:%M"
                                    },
                                    formatter: function () {
                                        var tt = '',
                                            newDate = Highcharts.dateFormat('%d/%m/%Y %H:%M:%S', this.key);
                                        tt = '<b>' + newDate + '</b> <br/><br/>' + '<b>' + this.series.name + ': </b>' + this.y;
                                        return tt;
                                    }
                                },

                                xAxis: {
                                    type: 'datetime',
                                },
                                yAxis: {
                                    title: {
                                        text: 'Solar and UV'
                                    },
                                },
                                colors: ['#1abc9c', '#9b59b6'],
                                series: [{
                                    data: response.solar,
                                    lineWidth: 0.5,
                                    name: 'solar_(W/m²)'
                                },{
                                    data: response.uv,
                                    lineWidth: 0.5,
                                    name: 'uv_index'
                                }]
                            });

                            Notiflix.Block.remove('.solar_uv_chart_loader');
                        }
                    });
                }

                getAvgChartData();
            });
        </script>
    @endif
    <script>
        let newVue = new Vue({
            el: "#main-charts",
            data: {
                timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                is_air_temp: "{{ $is_air_temp }}",
                is_wind_speed: "{{ $is_wind_speed }}",
                is_gust_speed: "{{ $is_gust_speed }}",
                is_rain_fall: "{{ $is_rain_fall }}",
                is_uv: "{{ $is_uv }}",
                is_atmospheric_pressure: "{{ $is_atmospheric_pressure }}",
                is_relative_humidity: "{{ $is_relative_humidity }}",
                is_wind_direction: "{{ $is_wind_direction }}",
                is_solar: "{{ $is_solar }}",
                is_strikes: "{{ $is_strikes }}",
                is_strike_distance: "{{ $is_strike_distance }}",
                is_avg_pm1: "{{ $is_avg_pm1 }}",
                is_avg_pm25: "{{ $is_avg_pm25 }}",
                is_avg_pm4: "{{ $is_avg_pm4 }}",
                is_avg_pm10: "{{ $is_avg_pm10 }}",
                air_temp: '0',
                air_temp_min: 0,
                air_temp_max: 0,
                wind_speed: '0',
                wind_speed_min: 0,
                wind_speed_max: 0,
                gust_speed: '0',
                gust_speed_min: 0,
                gust_speed_max: 0,
                rain_fall: '0',
                monthly_rain_fall: 0,
                yearly_rain_fall: 0,
                uv: '0',
                atmospheric_pressure: '0',
                msl_pressure_min: 0,
                msl_pressure_max: 0,
                relative_humidity: '0',
                humidity_min: 0,
                humidity_max: 0,
                wind_direction: '0',
                solar: '0',
                solar_min: 0,
                solar_max: 0,
                strikes: '0',
                strike_distance: '0',
                avg_pm_1: 0,
                avg_pm_25: 0,
                avg_pm_4: 0,
                avg_pm_10: 0,
            },
            mounted() {
                this.getChartData();
            },
            methods: {
                getChartData() {
                    let _this = this;
                    let currentTime = moment().format('YYYY-MM-DD H:mm:ss');
                    axios.get("{{ route('get.chart.data') }}", {
                        params: {
                            id: "{{ $base->id }}",
                            currentTime: currentTime,
                            timeZone: _this.timeZone
                        }
                    })
                        .then((response) => {
                            if (_this.is_air_temp) {
                                let airData = response.data.airTempData ? response.data.airTempData.data : 0;
                                gaugeAirTemp.data([airData]);
                                _this.air_temp = airData;
                                _this.air_temp_min = response.data.airTempMinMax ? response.data.airTempMinMax.minData : 0;
                                _this.air_temp_max = response.data.airTempMinMax ? response.data.airTempMinMax.maxData : 0;
                            }
                            if (_this.is_wind_speed || _this.is_gust_speed) {
                                let windData = response.data.windSpeedData ? response.data.windSpeedData.data : 0;
                                let gustData = response.data.gustSpeedData ? response.data.gustSpeedData.data : 0;
                                gaugeWindGustSpeed.data([windData, gustData]);
                                _this.wind_speed = windData;
                                _this.gust_speed = gustData;
                                _this.wind_speed_min = response.data.windSpeedMinMax ? response.data.windSpeedMinMax.minData : 0;
                                _this.wind_speed_max = response.data.windSpeedMinMax ? response.data.windSpeedMinMax.maxData : 0;
                                _this.gust_speed_min = response.data.gustSpeedMinMax ? response.data.gustSpeedMinMax.minData : 0;
                                _this.gust_speed_max = response.data.gustSpeedMinMax ? response.data.gustSpeedMinMax.maxData : 0;
                            }
                            if (_this.is_rain_fall) {
                                let rainData = response.data.rainFallData ? response.data.rainFallData.data : 0;
                                gaugeRainfall.data([rainData]);
                                _this.rain_fall = rainData;
                                _this.monthly_rain_fall = response.data.rainFallMonthData ? response.data.rainFallMonthData.data : 0;
                                _this.yearly_rain_fall = response.data.rainFallYearData ? response.data.rainFallYearData.data : 0;
                            }
                            if (_this.is_uv) {
                                let uvData = response.data.uvData ? response.data.uvData.data : 0;
                                gaugeUV.data([uvData]);
                                _this.uv = uvData;
                            }
                            if (_this.is_atmospheric_pressure) {
                                let atmostphericData = response.data.atmosphericPressureData ? response.data.atmosphericPressureData.data : 0;
                                gaugeAtmosphericPressure.data([atmostphericData]);
                                _this.atmospheric_pressure = atmostphericData;
                                _this.msl_pressure_min = response.data.pressureMinMax ? response.data.pressureMinMax.minData : 0;
                                _this.msl_pressure_max = response.data.pressureMinMax ? response.data.pressureMinMax.maxData : 0;

                            }
                            if (_this.is_relative_humidity) {
                                let humidityData = response.data.relativeHumidityData ? response.data.relativeHumidityData.data : 0;
                                gaugeRelativeHumidity.data([humidityData]);
                                _this.relative_humidity = humidityData;
                                _this.humidity_min = response.data.humidityMinMax ? response.data.humidityMinMax.minData : 0;
                                _this.humidity_max = response.data.humidityMinMax ? response.data.humidityMinMax.maxData : 0;
                            }
                            if (_this.is_wind_direction) {
                                let directionData = response.data.windDirection ? response.data.windDirection.data : 0;
                                gaugeWindDirection.data([directionData]);
                                _this.wind_direction = directionData;
                            }
                            if (_this.is_solar) {
                                let solarData = response.data.solarData ? response.data.solarData.data : 0;
                                gaugeSolar.data([solarData]);
                                _this.solar = solarData;
                                _this.solar_min = response.data.solarMinMax ? response.data.solarMinMax.minData : 0;
                                _this.solar_max = response.data.solarMinMax ? response.data.solarMinMax.maxData : 0;
                            }
                            if (_this.is_strikes || _this.is_strike_distance) {
                                let strikesData = response.data.strikesData ? response.data.strikesData.data : 0;
                                let strikeDistanceData = response.data.strikeDistanceData ? response.data.strikeDistanceData.data : 0;
                                gaugeStrikes.data([strikesData]);
                                _this.strikes = strikesData;
                                _this.strike_distance = strikeDistanceData;
                            }
                            if (_this.is_avg_pm1) {
                                _this.avg_pm_1 = response.data.avgPm1Data ? response.data.avgPm1Data.avgData : 0;
                            }
                            if (_this.is_avg_pm25) {
                                _this.avg_pm_25 = response.data.avgPm25Data ? response.data.avgPm25Data.avgData : 0;
                            }
                            if (_this.is_avg_pm4) {
                                _this.avg_pm_4 = response.data.avgPm4Data ? response.data.avgPm4Data.avgData : 0;
                            }
                            if (_this.is_avg_pm10) {
                                _this.avg_pm_10 = response.data.avgPm10Data ? response.data.avgPm10Data.avgData : 0;
                            }
                        });
                },
            },
        });
    </script>
@endsection