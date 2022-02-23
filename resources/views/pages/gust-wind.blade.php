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
                                <h6>Gust and Wind</h6>
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
    <script src="https://code.highcharts.com/modules/export-data.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endpush
@section('scripts')
    <script>
        $(function () {
            let timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            $('input[name="single_datepicker"]').val(moment().subtract(7, 'days').format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY'));

            $('input[name="single_datepicker"]').daterangepicker({
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

            $('input[name="single_datepicker"]').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                getSingleChartData();
            });

            function getSingleChartData() {
                Notiflix.Block.dots('.single_chart_loader');
                let dateData = $('input[name="single_datepicker"]').val().split("-").map(item => item.trim());
                $.ajax({
                    url: "{{ route('get.guast.wind.chart.data') }}",
                    method: "GET",
                    data: {dateData: dateData, timeZone: timeZone, id: "{{ $id }}", api_key: "{{ $api_key }}"},
                    success: function (response) {
                        Highcharts.chart('single_chart', {
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
                            yAxis: [{
                                title: {
                                    text: 'gust_wind_speed_(m/s)'
                                },
                            }, {
                                title: {
                                    text: 'wind_speed_(m/s)'
                                },
                                opposite: true
                            }],
                            colors: ['#1abc9c', '#9b59b6'],
                            series: [{
                                yAxis: 0,
                                data: response.gust,
                                lineWidth: 0.5,
                                name: 'gust_wind_speed_(m/s)'
                            }, {
                                yAxis: 1,
                                data: response.wind,
                                lineWidth: 0.5,
                                name: 'wind_speed_(m/s)'
                            }],
                            exporting: {
                                buttons: {
                                    contextButton: {
                                        menuItems: ["printChart",
                                            "separator",
                                            "downloadPNG",
                                            "downloadJPEG",
                                            "downloadPDF",
                                            "downloadSVG",
                                            "separator",
                                            "downloadCSV",
                                            "downloadXLS",
                                            "openInCloud"]
                                    }
                                }
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