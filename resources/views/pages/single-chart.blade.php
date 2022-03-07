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
                                <h6>{{ ucwords(str_replace('-', ' ', $chart)) }}</h6>
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
                    url: "{{ route('get.single.chart.data') }}",
                    method: "GET",
                    data: {dateData: dateData, timeZone: timeZone, key: "{{ $sensorKey }}", id: "{{ $paramId }}", api_key: "{{ $api_key }}", user_name: "{{ $userName }}"},
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
                            yAxis: [
                                {
                                    title: {
                                        text: "{{ ucwords(str_replace('-', ' ', $chart)) }}"
                                    },
                                }
                            ],
                            colors: ['#1abc9c'],
                            series: [{
                                data: response,
                                lineWidth: 0.5,
                                name: '{{ $singleParam }}'
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
