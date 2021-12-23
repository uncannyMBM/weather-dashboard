var gaugeRelativeHumidity;
anychart.onDocumentReady(function () {
    gaugeRelativeHumidity = anychart.gauges.circular();

    gaugeRelativeHumidity.fill('#fff').stroke(null).padding(0).margin(20);

    var axis = gaugeRelativeHumidity.axis();

    gaugeRelativeHumidity.axis(0).startAngle(-140).sweepAngle(280).width(0)
        .ticks(
            {
                type: 'line',
                length: 5,
                position: 'outside'
            }
        )
        .minorTicks(
            {
                type: 'line',
                length: 3,
                position: 'outside'
            }
        );
    axis.scale().minimum(0).maximum(100).ticks({interval: 10}).minorTicks({interval: 5});

    axis.labels().position('outside');

    gaugeRelativeHumidity.needle(0).enabled(true).startRadius("0%").endRadius("90%").middleRadius(0).endWidth("0.1%").fill('#64b5f6').stroke("none").middleWidth(null);

    gaugeRelativeHumidity.cap().radius('4%').fill('#ffffff').enabled(true).stroke('#1976d2');

    gaugeRelativeHumidity.container('relative-humidity-chart');
    gaugeRelativeHumidity.draw();
});