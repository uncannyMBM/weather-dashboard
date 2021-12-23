var gaugeAirTemp;
anychart.onDocumentReady(function () {
    gaugeAirTemp = anychart.gauges.circular();

    gaugeAirTemp.fill('#fff').stroke(null).padding(0).margin(20);

    var axis = gaugeAirTemp.axis();

    gaugeAirTemp.axis(0)
        .startAngle(-140)
        .sweepAngle(280)
        .width(0)
        .ticks(
            {
                type: 'trapezium',
                length: 9,
                position: 'inside',
                // fill: '#fff'
            }
        )
        .minorTicks(
            {
                type: 'line',
                length: 3,
                position: 'inside'
            }
        );
    axis.scale().minimum(-20).maximum(50).ticks({interval: 10}).minorTicks({interval: 5});

    axis.labels().position('outside');

    gaugeAirTemp.needle(0).enabled(true).startRadius("0%").endRadius("90%").middleRadius(0).endWidth("0.1%").fill('#64b5f6').stroke("none").middleWidth(null);

    gaugeAirTemp.cap().radius('4%').fill('#ffffff').enabled(true).stroke('#1976d2');

    gaugeAirTemp.range({
        from: -20,
        to: 50,
        fill: {keys: ["green", "yellow", "orange" , "red"]},
        position: "inside",
        radius: 100,
        endSize: "3%",
        startSize:"3%",
        zIndex: 10
    });

    gaugeAirTemp.container('air-temp-chart');
    gaugeAirTemp.draw();
});