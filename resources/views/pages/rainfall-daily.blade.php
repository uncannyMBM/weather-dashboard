@extends('layouts.app')
@push('extra_styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <style>
        .highcharts-credits {
            display: none !important;
        }

        .table-condensed thead tr:nth-child(2),
        .table-condensed tbody,
        .drp-selected {
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
                                <h6>Rainfall Daily</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-8">
                                        @php
                                            $months = [];
                                        @endphp
                                        <select class="form-control-sm float-right" name="from_date">
                                            @for ($i = 0; $i < 6; $i++)
                                                @php
                                                    $months[] = date("F Y", strtotime( date( 'Y-m-01' )." - $i months"));
                                                @endphp
                                                <option value="{{ date("F Y", strtotime( date( 'Y-m-01' )." - $i months")) }}">{{ date("F Y", strtotime( date( 'Y-m-01' )." - $i months")) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-control-sm float-right" name="to_date">
                                            @for ($i = 0; $i < 1; $i++)
                                                <option value="{{ date("F Y", strtotime( date( 'Y-m-01' )." - $i months")) }}">{{ date("F Y", strtotime( date( 'Y-m-01' )." - $i months")) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-primary apply-btn">Apply</button>
                                    </div>
                                </div>
                                <input class="float-right" type="text" name="single_datepicker" value=""
                                       readonly style="display: none"/>
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

            $(document).on('change', 'select[name="from_date"]', function () {
                let monthsList = $.parseJSON(<?php print json_encode(json_encode($months)); ?>);
                let selectedIndex = $(this)[0].selectedIndex;
                $('select[name="to_date"]').html("");
                for (let i = 0; i <= selectedIndex; i++) {
                    $('select[name="to_date"]').append(`<option value="${monthsList[i]}">${monthsList[i]}</option>`)
                }
            })

            $(document).on('click', '.apply-btn', function () {
                getSingleChartData();
            });

            function getSingleChartData() {
                Notiflix.Block.dots('.single_chart_loader');
                let fromDate = $('select[name="from_date"] option:selected').val()
                let toDate = $('select[name="to_date"] option:selected').val()
                $.ajax({
                    url: "{{ route('get.rainfall.daily.chart.data') }}",
                    method: "GET",
                    data: {
                        dateData: {fromDate, toDate},
                        timeZone: timeZone,
                        key: "{{ $sensorKey }}",
                        id: "{{ $paramId }}",
                        api_key: "{{ $api_key }}",
                        user_name: "{{ $userName }}"
                    },
                    success: function (response) {
                        Highcharts.chart('single_chart', {
                            chart: {
                                type: 'column',
                                zoomType: 'x'
                            },
                            title: {
                                text: ''
                            },
                            tooltip: {
                                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                                '<td style="padding:0"><b>{point.y:.2f} mm</b></td></tr>',
                                footerFormat: '</table>',
                                shared: true,
                                useHTML: true
                            },
                            xAxis: {
                                categories: response.categories,
                                crosshair: true
                            },
                            yAxis: {
                                min: 0,
                                title: {
                                    text: 'Rainfall (mm)'
                                }
                            },
                            plotOptions: {
                                column: {
                                    pointPadding: 0.4,
                                    borderWidth: 0
                                }
                            },
                            colors: ['#1abc9c'],
                            series: [{
                                data: response.rainFall,
                                name: 'Rainfall Daily'
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
