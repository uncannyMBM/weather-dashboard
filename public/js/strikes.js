var gaugeStrikes;
anychart.onDocumentReady(function () {
    gaugeStrikes = anychart.gauges.circular();

    gaugeStrikes.fill('#fff').stroke(null).padding(0).margin(20);

    var axis = gaugeStrikes.axis();

    gaugeStrikes.axis(0).startAngle(-140).sweepAngle(280).width(0)
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
    axis.scale().minimum(0).maximum(250).ticks({interval: 25}).minorTicks({interval: 5});

    axis.labels().position('outside');

    gaugeStrikes.needle(0).enabled(true).startRadius("0%").endRadius("90%").middleRadius(0).endWidth("0.1%").fill('#64b5f6').stroke("none").middleWidth(null);

    gaugeStrikes.cap().radius('4%').fill('#ffffff').enabled(true).stroke('#1976d2');

    gaugeStrikes.container('strikes-chart');
    gaugeStrikes.draw();
});