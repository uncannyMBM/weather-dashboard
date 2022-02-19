var gaugeWindDirection;
var sigmaTheta;
anychart.onDocumentReady(function () {
    gaugeWindDirection = anychart.gauges.circular();

    gaugeWindDirection.fill('#fff').stroke(null).padding(0).margin(20).startAngle(0).sweepAngle(360);

    gaugeWindDirection.axis().labels().padding(3).position('outside').format('{%Value}\u00B0');

    gaugeWindDirection.axis().scale().minimum(0).maximum(360).ticks({interval: 30}).minorTicks({interval: 10});

    gaugeWindDirection.axis().fill('#7c868e').startAngle(0).sweepAngle(360).width(1).ticks({
        type: 'line',
        fill: '#7c868e',
        length: 4,
        position: 'outside'
    });

    gaugeWindDirection.needle(0).enabled(true).startRadius("0%").middleRadius("40%").endRadius("90%").startWidth(".9").middleWidth("0.6%").endWidth("0.3%").fill('#64b5f6').stroke("none");

    sigmaTheta = gaugeWindDirection.range();
    sigmaTheta.from(0);
    sigmaTheta.to(0);
    sigmaTheta.fill("#F0673B 1");
    sigmaTheta.radius(60);
    sigmaTheta.startSize(3);
    sigmaTheta.endSize(3);

    gaugeWindDirection.cap().radius('4%').fill('#1976d2').enabled(true).stroke(null);

    gaugeWindDirection.label(null);

    gaugeWindDirection.container('wind-direction-chart');

    gaugeWindDirection.draw();
});