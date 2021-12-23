@extends('layouts.app')
@push('extra_styles')
    <link href="https://cdn.anychart.com/releases/v8/css/anychart-ui.min.css" type="text/css" rel="stylesheet">
    <link href="https://cdn.anychart.com/releases/v8/fonts/css/anychart-font.min.css" type="text/css" rel="stylesheet">
    <style>
        [v-cloak] {
            display: none;
        }
    </style>
@endpush
@section('content')
    @include('partials.alert')
    <div id="main-charts">
        <div class="row">
            @if($is_air_temp)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6>Air Temp: <span v-cloak>@{{ air_temp }}</span><sup style="font-size: 16px;">°</sup></h6>
                        </div>
                        <div class="card-body">
                            <div id="air-temp-chart" style="width: 100%;height: 300px;"></div>
                        </div>
                    </div>
                </div>
            @endif
            @if($is_wind_speed || $is_gust_speed)
                <div class="col-md-6">
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
        </div>
        <div class="row mt-5">
            @if($is_atmospheric_pressure)
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6>Atmospheric pressure: <span v-cloak>@{{ atmospheric_pressure }}</span> <sub>(hPa)</sub>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="atmospheric-pressure-chart" style="width: 100%;height: 300px;"></div>
                        </div>
                    </div>
                </div>
            @endif
            @if($is_relative_humidity)
                <div class="col-md-4">
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
            @if($is_wind_direction)
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6>Wind direction: <span v-cloak>@{{ wind_direction }}</span><sup style="font-size: 16px;">°</sup></h6>
                        </div>
                        <div class="card-body">
                            <div id="wind-direction-chart" style="width: 100%;height: 300px;"></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
@push('extra_scripts')
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-ui.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-exports.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-circular-gauge.min.js"></script>
    @if($is_air_temp)
        <script src="{{ asset('js/air-temparature.js') }}"></script>
    @endif
    @if($is_wind_speed || $is_gust_speed)
        <script src="{{ asset('js/wind-gust-speed.js') }}"></script>
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
@endpush
@section('scripts')
    <script>
        let newVue = new Vue({
            el: "#main-charts",
            data: {
                is_air_temp: "{{ $is_air_temp }}",
                is_wind_speed: "{{ $is_wind_speed }}",
                is_gust_speed: "{{ $is_gust_speed }}",
                is_atmospheric_pressure: "{{ $is_atmospheric_pressure }}",
                is_relative_humidity: "{{ $is_relative_humidity }}",
                is_wind_direction: "{{ $is_wind_direction }}",
                air_temp: '0',
                wind_speed: '0',
                gust_speed: '0',
                atmospheric_pressure: '0',
                relative_humidity: '0',
                wind_direction: '0',
            },
            beforeMount() {
                this.getChartData();
            },
            methods: {
                getChartData() {
                    let _this = this;
                    axios.get("{{ route('get.chart.data') }}", {params: {id: "{{ $base->id }}"}}).then((response) => {
                        if (_this.is_air_temp) {
                            let airData = response.data.airTempData ? response.data.airTempData.data : 0;
                            gaugeAirTemp.data([airData]);
                            _this.air_temp = airData;
                        }
                        if (_this.is_wind_speed || _this.is_gust_speed) {
                            let windData = response.data.windSpeedData ? response.data.windSpeedData.data : 0;
                            let gustData = response.data.gustSpeedData ? response.data.gustSpeedData.data : 0;
                            gaugeWindGustSpeed.data([windData, gustData]);
                            _this.wind_speed = windData;
                            _this.gust_speed = gustData;
                        }
                        if (_this.is_atmospheric_pressure) {
                            let atmostphericData = response.data.atmosphericPressureData ? response.data.atmosphericPressureData.data : 0;
                            gaugeAtmosphericPressure.data([atmostphericData]);
                            _this.atmospheric_pressure = atmostphericData;

                        }
                        if (_this.is_relative_humidity) {
                            let humidityData = response.data.relativeHumidityData ? response.data.relativeHumidityData.data : 0;
                            gaugeRelativeHumidity.data([humidityData]);
                            _this.relative_humidity = humidityData;
                        }
                        if (_this.is_wind_direction) {
                            let directionData = response.data.windDirection ? response.data.windDirection.data : 0;
                            gaugeWindDirection.data([directionData]);
                            _this.wind_direction = directionData;
                        }
                    });
                },
            },
        });
    </script>
@endsection