@extends('layouts.app')
@push('extra_styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn3.devexpress.com/jslib/21.2.5/css/dx.common.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn3.devexpress.com/jslib/21.2.5/css/dx.light.css"/>
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
                                <h6>Wind Rose</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        @php
                                            $months = [date('Y'), date('Y') - 1];
                                        @endphp
                                        <select class="form-control-sm float-right" name="from_date">
                                            @for ($i = 0; $i < 12; $i++)
                                                @php
                                                    $months[] = date("F Y", strtotime( date( 'Y-m-01' )." - $i months"));
                                                @endphp
                                                <option value="{{ date("Y-m-d", strtotime( date( 'Y-m-01' )." - $i months")) }}">{{ date("F Y", strtotime( date( 'Y-m-01' )." - $i months")) }}</option>
                                            @endfor
                                        </select>
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
    <script src="https://cdn3.devexpress.com/jslib/21.2.5/js/dx.all.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endpush
@section('scripts')
    <script>
        $(function () {
            let timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

            $(document).on('change', 'select[name="from_date"]', function () {
                getSingleChartData();
            });

            function getSingleChartData() {
                Notiflix.Block.dots('.single_chart_loader');
                let fromDate = $('select[name="from_date"] option:selected').val();
                $.ajax({
                    url: "{{ route('get.wind.rose.chart.data') }}",
                    method: "GET",
                    data: {
                        dateData: fromDate,
                        timeZone: timeZone,
                        id: "{{ $id }}",
                        api_key: "{{ $api_key }}"
                    },
                    success: function (response) {
                        historical_zoomed_chart_instance = {
                            palette: "Bright",
                            dataSource: response.values,
                            title: "Wind Rose",
                            commonSeriesSettings: {
                                type: "stackedbar"
                            },
                            margin: {
                                bottom: 50,
                                left: 100
                            },
                            onLegendClick: function (e) {
                                var series = e.target;
                                if (series.isVisible()) {
                                    series.hide();
                                } else {
                                    series.show();
                                }
                            },
                            tooltip: {
                                enabled: true
                            },
                            argumentAxis: {
                                discreteAxisDivisionMode: "crossLabels",
                                firstPointOnStartAngle: true
                            },

                            valueAxis: {
                                valueMarginsEnabled: false
                            },

                            export: {
                                enabled: true
                            },
                            series: [
                                {valueField: "val1", name: "Calm"},
                                {valueField: "val2", name: "0.5-2 m/s"},
                                {valueField: "val3", name: "2-4 m/s"},
                                {valueField: "val4", name: "4-6 m/s"},
                                {valueField: "val5", name: "6-8 m/s"},
                                {valueField: "val6", name: "8-10 m/s"},
                                {valueField: "val7", name: "10-12 m/s"},
                                {valueField: "val8", name: "12-14 m/s"},
                                {valueField: "val9", name: " 14 > m/s"}
                            ]
                        };

                        var radar = $("#single_chart").dxPolarChart(historical_zoomed_chart_instance).dxPolarChart("instance");
                        Notiflix.Block.remove('.single_chart_loader');
                    }
                });
            }

            getSingleChartData();
        });
    </script>
@endsection