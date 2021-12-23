var gaugeWindGustSpeed;
anychart.onDocumentReady(function () {
    gaugeWindGustSpeed = anychart.gauges.circular();

    gaugeWindGustSpeed.fill('#fff').stroke(null).padding(0).margin(20);

    var axis = gaugeWindGustSpeed.axis();

    gaugeWindGustSpeed.axis(0).startAngle(-140).sweepAngle(280).width(0)
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
    axis.scale().minimum(0).maximum(30).ticks({interval: 5}).minorTicks({interval: 1});

    axis.labels().position('outside');

    gaugeWindGustSpeed.needle(0).enabled(true).startRadius("0%").endRadius("90%").middleRadius(0).endWidth("0.1%").fill('#64b5f6').stroke("none").middleWidth(null);

    gaugeWindGustSpeed.needle(1).enabled(true).startRadius("0%").endRadius("90%").middleRadius(0).endWidth("0.1%").fill("#e74c3c").stroke("none").middleWidth(null);

    gaugeWindGustSpeed.cap().radius('4%').fill('#ffffff').enabled(true).stroke('#1976d2');

    gaugeWindGustSpeed.container('wind-gust-speed-chart');
    gaugeWindGustSpeed.draw();
});