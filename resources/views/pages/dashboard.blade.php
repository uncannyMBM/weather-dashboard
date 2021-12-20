@extends('layouts.app')
@push('extra_styles')
    <link href="https://cdn.anychart.com/releases/v8/css/anychart-ui.min.css" type="text/css" rel="stylesheet">
    <link href="https://cdn.anychart.com/releases/v8/fonts/css/anychart-font.min.css" type="text/css" rel="stylesheet">
    <style>
        #wind-direction-chart {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
@endpush
@section('content')
    @include('partials.alert')
    <div id="main-charts">
        @if($is_wind_direction)
            <div class="card">
                <div class="card-header">
                    <h4>Wind direction</h4>
                </div>
                <div class="card-body">
                    <div id="wind-direction-chart" style="width: 100%;height: 500px;"></div>
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
@endpush
@section('scripts')
    @if($is_wind_direction)
        <script>
            var gauge;
            anychart.onDocumentReady(function () {
                gauge = anychart.gauges.circular();

                gauge.fill('#fff').stroke(null).padding(0).margin(30).startAngle(0).sweepAngle(360);

                gauge.axis().labels().padding(3).position('outside').format('{%Value}\u00B0');

                gauge.axis().scale().minimum(0).maximum(360).ticks({interval: 30}).minorTicks({interval: 10});

                gauge.axis().fill('#7c868e').startAngle(0).sweepAngle(-360).width(1).ticks({
                    type: 'line',
                    fill: '#7c868e',
                    length: 4,
                    position: 'outside'
                });

                gauge.marker().fill('#64b5f6').stroke(null).size('15%').zIndex(120).radius('97%');

                gauge.needle().fill('#1976d2').stroke(null).axisIndex(1).startRadius('6%').endRadius('38%').startWidth('2%').middleWidth(null).endWidth('0');

                gauge.cap().radius('4%').fill('#1976d2').enabled(true).stroke(null);

                var bigTooltipTitleSettings = {
                    fontFamily: '\'Verdana\', Helvetica, Arial, sans-serif',
                    fontWeight: 'normal',
                    fontSize: '12px',
                    hAlign: 'left',
                    fontColor: '#212121'
                };

                gauge
                    .label()
                    .useHtml(true)
                    .textSettings(bigTooltipTitleSettings);

                gauge.label().hAlign('center').anchor('center-top').offsetY(-20).padding(15, 20)
                    .background({
                        fill: '#fff',
                        stroke: {
                            thickness: 1,
                            color: '#E0F0FD'
                        }
                    });

                // set container id for the chart
                gauge.container('wind-direction-chart');

                // initiate chart drawing
                gauge.draw();
            });
        </script>
    @endif
    <script>
        let newVue = new Vue({
            el: "#main-charts",
            data: {
                is_wind_direction: "{{ $is_wind_direction }}"
            },
            beforeMount() {
                this.getChartData();
            },
            methods: {
                getChartData() {
                    axios.get("{{ route('get.chart.data') }}", {params: {id: "{{ $base->id }}"}}).then((response) => {
                        if (this.is_wind_direction) {
                            if (response.data.windDirection) {
                                gauge.label().text(`<span style="color: #64B5F6; font-size: 13px">Wind Direction: </span>
                                <span style="color: #5AA3DD; font-size: 15px">
                                ${response.data.windDirection.data}
                                \u00B0 (+/- 0.5\u00B0)</span><br>`);
                                gauge.data([response.data.windDirection.data]);
                            } else {
                                gauge.data([0]);
                            }
                        }
                    });
                },
            },
        });
    </script>
@endsection