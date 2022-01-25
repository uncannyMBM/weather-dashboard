var aqiPM10Gauge;
anychart.onDocumentReady(function () {
    var good = '#04cc63';
    var moderate = '#93cc65';
    var unhealthy1 = '#eeef63';
    var unhealthy2 = '#efc747';
    var unhealthy3 = '#ef9900';
    var danger1 = '#ef5e33';
    var danger2 = '#ef251c';

   let ticks = [0, 51, 101, 151, 201, 301, 401, 500];

    var stage = anychart.graphics.create('aqi-pm10-chart');

    aqiPM10Gauge = anychart.gauges.linear();

    aqiPM10Gauge.scaleBar(0)
        .width('8%')
        .from(ticks[0])
        .to(ticks[1])
        .fill({
            keys: [good],
            angle: 90
        });

    aqiPM10Gauge.scaleBar(1)
        .width('8%')
        .from(ticks[1])
        .to(ticks[2])
        .fill(moderate);

    aqiPM10Gauge.scaleBar(2)
        .width('8%')
        .from(ticks[2])
        .to(ticks[3])
        .fill({
            keys: [unhealthy1],
            angle: 90
        });

    aqiPM10Gauge.scaleBar(3)
        .width('8%')
        .from(ticks[3])
        .to(ticks[4])
        .fill({
            keys: [unhealthy2],
            angle: 90
        });

    aqiPM10Gauge.scaleBar(4)
        .width('8%')
        .from(ticks[4])
        .to(ticks[5])
        .fill({
            keys: [unhealthy3],
            angle: 90
        });

    aqiPM10Gauge.scaleBar(5)
        .width('8%')
        .from(ticks[5])
        .to(ticks[6])
        .fill({
            keys: [danger1],
            angle: 90
        });

    aqiPM10Gauge.scaleBar(6)
        .width('8%')
        .from(ticks[6])
        .to(ticks[7])
        .fill({
            keys: [danger2],
            angle: 90
        });

    let scale = aqiPM10Gauge.scale();
    scale.minimum(ticks[0])
        .maximum(ticks[7])
        .ticks(ticks);

    var axis = aqiPM10Gauge.axis();
    axis.width('0.5%')
        .offset('-1%')
        .scale(scale);
    axis.labels()
        .fontSize(16)
        .format('{%Value}');

    var marker = aqiPM10Gauge.marker(0);
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

    aqiPM10Gauge.bounds(0, '5%', '50%', '90%');
    aqiPM10Gauge.container(stage).draw();
});