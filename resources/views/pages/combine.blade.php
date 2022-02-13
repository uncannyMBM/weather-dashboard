@extends('layouts.app')
@push('extra_styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <style>
        .highcharts-credits {
            display: none !important;
        }
    </style>
@endpush
@section('content')
    @include('partials.alert')
    <div id="main-charts">
        <div class="row mt-0 mt-lg-5 mb-5">
            <div class="col-md-12">
                <div class="card single_chart_loader">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Combine Chart</h6>
                            </div>
                            <div class="col-md-6">
                                <input class="float-right" type="text" name="single_datepicker" value=""
                                       readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="single_chart" style="width: 100%;height: 500px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('extra_scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/boost.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/offline-exporting.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endpush
@section('scripts')
    <script>
        $(function () {
            let rainfallSeries = "{{ $is_rain_fall }}";
            let airTempSeries = "{{ $is_air_temp }}";
            let pressureSeries = "{{ $is_atmospheric_pressure }}";
            let humiditySeries = "{{ $is_relative_humidity }}";
            let windDirectionSeries = "{{ $is_wind_direction }}";
            let windSpeedSeries = "{{ $is_wind_speed }}";
            let gustSpeedSeries = "{{ $is_gust_speed }}";
            let pm1Series = "{{ $is_avg_pm1 }}";
            let pm25Series = "{{ $is_avg_pm25 }}";
            let pm4Series = "{{ $is_avg_pm4 }}";
            let pm10Series = "{{ $is_avg_pm10 }}";

            let timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            $('input[name="single_datepicker"]').val(moment().subtract(30, 'days').format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY'));

            $('input[name="single_datepicker"]').daterangepicker({
                autoUpdateInput: false,
                startDate: moment().subtract(30, 'days'),
                endDate: moment(),
                minDate: moment().subtract(1, 'year'),
                maxDate: moment(),
                showDropdowns: true,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('input[name="single_datepicker"]').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                getSingleChartData();
            });

            function getSingleChartData() {
                Notiflix.Block.dots('.single_chart_loader');
                let dateData = $('input[name="single_datepicker"]').val().split("-").map(item => item.trim());
                $.ajax({
                    url: "{{ route('get.combine.chart.data') }}",
                    method: "GET",
                    data: {dateData: dateData, timeZone: timeZone, id: "{{ $id }}"},
                    success: function (response) {
                        let chartSeries = [];
                        let xSeries = [];
                        let chartColors = [];
                        if (rainfallSeries) {
                            chartColors.push('#27ae60');
                            chartSeries.push({
                                yAxis: 0,
                                data: response.rainfall,
                                lineWidth: 0.5,
                                name: 'Rainfall'
                            });
                            xSeries.push({
                                title: {
                                    text: 'Rainfall'
                                },
                            });
                        }
                        if (airTempSeries) {
                            chartColors.push('#2980b9');
                            chartSeries.push({
                                yAxis: 1,
                                data: response.airTemp,
                                lineWidth: 0.5,
                                name: 'Air Temperature'
                            });
                            xSeries.push({
                                title: {
                                    text: 'Air Temperature'
                                },
                                opposite: true
                            });
                        }
                        if (pressureSeries) {
                            chartColors.push('#f1c40f');
                            chartSeries.push({
                                yAxis: 2,
                                data: response.pressure,
                                lineWidth: 0.5,
                                name: 'Atmospheric Pressure'
                            });
                            xSeries.push({
                                title: {
                                    text: 'Atmospheric Pressure'
                                },
                            });
                        }
                        if (humiditySeries) {
                            chartColors.push('#34495e');
                            chartSeries.push({
                                yAxis: 3,
                                data: response.humidity,
                                lineWidth: 0.5,
                                name: 'Relative Humidity'
                            });
                            xSeries.push({
                                title: {
                                    text: 'Relative Humidity'
                                },
                                opposite: true
                            });
                        }
                        if (windDirectionSeries) {
                            chartColors.push('#7f8c8d');
                            chartSeries.push({
                                yAxis: 4,
                                data: response.windDirection,
                                lineWidth: 0.5,
                                name: 'Wind Direction'
                            });
                            xSeries.push({
                                title: {
                                    text: 'Wind Direction'
                                },
                            });
                        }
                        if (windSpeedSeries) {
                            chartColors.push('#e84393');
                            chartSeries.push({
                                yAxis: 5,
                                data: response.windSpeed,
                                lineWidth: 0.5,
                                name: 'Wind Speed'
                            });
                            xSeries.push({
                                title: {
                                    text: 'Wind Speed'
                                },
                                opposite: true
                            });
                        }
                        if (gustSpeedSeries) {
                            chartColors.push('#e17055');
                            chartSeries.push({
                                yAxis: 6,
                                data: response.gustSpeed,
                                lineWidth: 0.5,
                                name: 'Gust Speed'
                            });
                            xSeries.push({
                                title: {
                                    text: 'Gust Speed'
                                },
                            });
                        }
                        if (pm1Series) {
                            chartColors.push('#1abc9c');
                            chartSeries.push({
                                yAxis: 7,
                                data: response.pm1,
                                lineWidth: 0.5,
                                name: 'PM 1.0'
                            });
                            xSeries.push({
                                title: {
                                    text: 'Avg Mass Concentration'
                                },
                                opposite: true
                            });
                        }
                        if (pm25Series) {
                            chartColors.push('#9b59b6');
                            chartSeries.push({
                                yAxis: 7,
                                data: response.pm25,
                                lineWidth: 0.5,
                                name: 'PM 2.5'
                            });
                            xSeries.push({
                                title: {
                                    text: 'Avg Mass Concentration'
                                },
                                opposite: true
                            });
                        }
                        if (pm4Series) {
                            chartColors.push('#f39c12');
                            chartSeries.push({
                                yAxis: 7,
                                data: response.pm4,
                                lineWidth: 0.5,
                                name: 'PM 4.0'
                            });
                            xSeries.push({
                                title: {
                                    text: 'Avg Mass Concentration'
                                },
                                opposite: true
                            });
                        }
                        if (pm10Series) {
                            chartColors.push('#e74c3c');
                            chartSeries.push({
                                yAxis: 7,
                                data: response.pm10,
                                lineWidth: 0.5,
                                name: 'PM 10.0'
                            });
                            xSeries.push({
                                title: {
                                    text: 'Avg Mass Concentration'
                                },
                                opposite: true
                            });
                        }
                        Highcharts.chart('single_chart', {
                            chart: {
                                zoomType: 'x',
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
                            yAxis: xSeries,
                            colors: chartColors,
                            series: chartSeries,
                            exporting: {
                                width: 2000
                            }
                        });

                        Notiflix.Block.remove('.single_chart_loader');
                    }
                });
            }

            getSingleChartData();
        });
    </script>
@endsection