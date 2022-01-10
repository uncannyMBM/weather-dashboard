var gaugeSolar;
anychart.onDocumentReady(function () {
    gaugeSolar = anychart.gauges.circular();

    gaugeSolar.fill('#fff').stroke(null).padding(0).margin(20);

    var axis = gaugeSolar.axis();

    gaugeSolar.axis(0).startAngle(-140).sweepAngle(290).width(0)
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
    axis.scale().minimum(0).maximum(2000).ticks({interval: 50}).minorTicks({interval: 10});

    axis.labels().position('outside');

    gaugeSolar.needle(0).enabled(true).startRadius("0%").endRadius("90%").middleRadius(0).endWidth("0.1%").fill('#64b5f6').stroke("none").middleWidth(null);

    gaugeSolar.cap().radius('4%').fill('#ffffff').enabled(true).stroke('#1976d2');

    gaugeSolar.container('solar-chart');
    gaugeSolar.draw();
});