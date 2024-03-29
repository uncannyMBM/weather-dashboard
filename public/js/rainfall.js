var gaugeRainfall;
anychart.onDocumentReady(function () {

    gaugeRainfall = anychart.gauges.linear();

    gaugeRainfall.layout('vertical');

    var marker = gaugeRainfall.marker(0);

    marker.offset('12%');

    marker.type('triangle-left');

    marker.zIndex(10);

    var scale = gaugeRainfall.scale();
    scale.minimum(0);
    scale.maximum(16);
    scale.ticks().interval(2);

    var axis = gaugeRainfall.axis();
    axis.minorTicks(true)
    axis.minorTicks().stroke('#cecece');
    axis.width('1%');
    axis.offset('8%');
    axis.orientation('left');

    gaugeRainfall.container('rainfall-chart');
    gaugeRainfall.draw();
});