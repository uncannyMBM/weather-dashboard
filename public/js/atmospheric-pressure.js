var gaugeAtmosphericPressure;
anychart.onDocumentReady(function () {
    gaugeAtmosphericPressure = anychart.gauges.circular();

    gaugeAtmosphericPressure.fill('#fff').stroke(null).padding(0).margin(20);

    var axis = gaugeAtmosphericPressure.axis();

    gaugeAtmosphericPressure.axis(0).startAngle(-140).sweepAngle(280).width(0)
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
    axis.scale().minimum(860).maximum(1100).ticks({interval: 20}).minorTicks({interval: 5});

    axis.labels().position('outside');

    gaugeAtmosphericPressure.needle(0).enabled(true).startRadius("0%").endRadius("90%").middleRadius(0).endWidth("0.1%").fill('#64b5f6').stroke("none").middleWidth(null);

    gaugeAtmosphericPressure.cap().radius('4%').fill('#ffffff').enabled(true).stroke('#1976d2');

    gaugeAtmosphericPressure.container('atmospheric-pressure-chart');
    gaugeAtmosphericPressure.draw();
});