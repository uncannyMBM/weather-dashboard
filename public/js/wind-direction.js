var gaugeWindDirection;
anychart.onDocumentReady(function () {
    gaugeWindDirection = anychart.gauges.circular();

    gaugeWindDirection.fill('#fff').stroke(null).padding(0).margin(20).startAngle(0).sweepAngle(360);

    gaugeWindDirection.axis().labels().padding(3).position('outside').format('{%Value}\u00B0');

    gaugeWindDirection.axis().scale().minimum(0).maximum(360).ticks({interval: 30}).minorTicks({interval: 10});

    gaugeWindDirection.axis().fill('#7c868e').startAngle(0).sweepAngle(-360).width(1).ticks({
        type: 'line',
        fill: '#7c868e',
        length: 4,
        position: 'outside'
    });

    gaugeWindDirection.marker().fill('#64b5f6').stroke(null).size('15%').zIndex(120).radius('97%');

    gaugeWindDirection.bar(0)
        .position("inside")
        .fill("#F0673B 1")
        .stroke("#F0673B")
        .radius(60);

    gaugeWindDirection.needle().fill('#1976d2').stroke(null).axisIndex(1).startRadius('6%').endRadius('38%').startWidth('2%').middleWidth(null).endWidth('0');

    gaugeWindDirection.cap().radius('4%').fill('#1976d2').enabled(true).stroke(null);

    gaugeWindDirection.label(null);

    gaugeWindDirection.container('wind-direction-chart');

    gaugeWindDirection.draw();
});