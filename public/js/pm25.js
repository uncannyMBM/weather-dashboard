var aqiPM25Gauge;
anychart.onDocumentReady(function () {
    var success_color = '#04cc63';
    var warning_color = '#eeef63';

    let ticks = [0, 50, 100];

    var stage = anychart.graphics.create('aqi-pm25-chart');

    aqiPM25Gauge = anychart.gauges.linear();
    aqiPM25Gauge.title().enabled(true).text('').margin([-15, 0 ,0 ,0]).fontColor('#212121').fontSize(18);
    aqiPM25Gauge.scaleBar(0)
        .width('8%')
        .from(ticks[0])
        .to(ticks[1])
        .fill({
            keys: [success_color],
            angle: 90
        });

    aqiPM25Gauge.scaleBar(1)
        .width('8%')
        .from(ticks[1])
        .to(ticks[2])
        .fill(warning_color);

    let scale = aqiPM25Gauge.scale();
    scale.minimum(ticks[0])
        .maximum(ticks[2])
        .ticks(ticks);

    var axis = aqiPM25Gauge.axis();
    axis.width('0.5%')
        .offset('-1%')
        .scale(scale);
    axis.labels()
        .fontSize(16)
        .format('{%Value}');

    var marker = aqiPM25Gauge.marker(0);
    marker.color('#7c868e')
        .offset('8.5%')
        .type('triangle-left');

    marker.labels()
        .enabled(true)
        .position('right-center')
        .offsetX(0)
        .anchor('left-center')
        .fontSize(18)
        .fontColor('#212121');

    aqiPM25Gauge.bounds(0, '5%', '50%', '90%');
    aqiPM25Gauge.container(stage).draw();
});