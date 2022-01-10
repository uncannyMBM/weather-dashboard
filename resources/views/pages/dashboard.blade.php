@extends('layouts.app')
@push('extra_styles')
    <link href="https://cdn.anychart.com/releases/v8/css/anychart-ui.min.css" type="text/css" rel="stylesheet">
    <link href="https://cdn.anychart.com/releases/v8/fonts/css/anychart-font.min.css" type="text/css" rel="stylesheet">
    <style>
        [v-cloak] {
            display: none;
        }

        .anychart-credits {
            display: none !important;
        }
    </style>
@endpush
@section('content')
    @include('partials.alert')
    <div id="main-charts">
        <div class="row">
            <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom box-shadow">
                <ul class="list-inline">
                    <li class="list-inline-item">State: <span class="text-success">{{ $base->status == 1 ? 'Active' : ($base->status == 2 ? 'Data Fault' : ($base->status == 3 ? 'Inactive' : (($base->status == 4 ? 'No Data' : (($base->status == 6 ? 'Terminated' : '')))))) }}</span></li>
                    <li class="list-inline-item">Base Station Name: <span class="text-success">{{ $base->name }}</span></li>
                    <li class="list-inline-item">Battery voltage: <span class="text-success">{{ $baseParams->battery_voltage }} V</span></li>
                    <li class="list-inline-item">RSSI: <span class="text-success">{{ $baseParams->rssi }}</span></li>
                    <li class="list-inline-item">External Power: <span class="text-success">{{ $baseParams->is_external_power_available == 1 ? 'Yes' : 'No' }}</span></li>
                    <li class="list-inline-item">Charging: <span class="text-success">{{ $baseParams->is_charging == 1 ? 'Yes' : 'No' }}</span></li>
                    <li class="list-inline-item">Data Transfer Mode: <span class="text-success">{{ $baseParams->primary_data_source_types_id == 4 ? 'MQTT Mode' : ' ' }}</span></li>
                    <li class="list-inline-item">At: <span class="text-success">{{ \Carbon\Carbon::parse($base->latest_data_timestamp)->format('d-m-Y h:i:s A') }}</span></li>
                    <li class="list-inline-item">Timezone: <span class="text-success">{{ $base->time_zone }}</span></li>
                </ul>
            </div>
        </div>
        <div class="row">
            @if($is_air_temp)
                <div class="col-md-4">
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
                <div class="col-md-4">
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
                @if($is_solar)
                    <div class="col-md-4">
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
        </div>

        <div class="row mt-5">
            @if($is_rain_fall)
                <div class="col-md-3">
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
                <div class="col-md-3">
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
        </div>
        <div class="row mt-5">
            @if($is_atmospheric_pressure)
                <div class="col-md-3">
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
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h6>Wind Direction: <span v-cloak>@{{ wind_direction }}</span><sup style="font-size: 16px;">°</sup>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="wind-direction-chart" style="width: 100%;height: 300px;"></div>
                        </div>
                    </div>
                </div>
            @endif
                @if($is_strikes || $is_strike_distance)
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Strikes: <span v-cloak>@{{ strikes }}</span></h6>
                                    </div>
                                    <div class="col-md-6 pull-right">
                                        <h6>Strike Distance: <span v-cloak>@{{ strike_distance }}</span> <sub>(Km)</sub>
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
    </div>
@endsection
@push('extra_scripts')
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-ui.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-exports.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-circular-gauge.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-linear-gauge.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
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
        <script src="{{ asset('js/rainfall.js') }}"></script>
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
                air_temp: '0',
                wind_speed: '0',
                gust_speed: '0',
                rain_fall: '0',
                uv: '0',
                atmospheric_pressure: '0',
                relative_humidity: '0',
                wind_direction: '0',
                solar: '0',
                strikes: '0',
                strike_distance: '0',
            },
            beforeMount() {
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
                            }
                            if (_this.is_wind_speed || _this.is_gust_speed) {
                                let windData = response.data.windSpeedData ? response.data.windSpeedData.data : 0;
                                let gustData = response.data.gustSpeedData ? response.data.gustSpeedData.data : 0;
                                gaugeWindGustSpeed.data([windData, gustData]);
                                _this.wind_speed = windData;
                                _this.gust_speed = gustData;
                            }
                            if (_this.is_rain_fall) {
                                let rainData = response.data.rainFallData ? response.data.rainFallData.data : 0;
                                gaugeRainfall.data([rainData]);
                                _this.rain_fall = rainData;
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
                            if (_this.is_solar) {
                                let solarData = response.data.solarData ? response.data.solarData.data : 0;
                                gaugeSolar.data([solarData]);
                                _this.solar = solarData;
                            }
                            if (_this.is_strikes || _this.is_strike_distance) {
                                let strikesData = response.data.strikesData ? response.data.strikesData.data : 0;
                                let strikeDistanceData = response.data.strikeDistanceData ? response.data.strikeDistanceData.data : 0;
                                gaugeStrikes.data([strikesData]);
                                _this.strikes = strikesData;
                                _this.strike_distance = strikeDistanceData;
                            }
                        });
                },
            },
        });
    </script>
@endsection