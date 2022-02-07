var gaugeUV;
anychart.onDocumentReady(function () {

    gaugeUV = anychart.gauges.linear();

    gaugeUV.layout('vertical');

    var marker = gaugeUV.marker(0);

    marker.offset('12%');

    marker.type('triangle-left');

    marker.zIndex(10);

    var scale = gaugeUV.scale();
    scale.minimum(0);
    scale.maximum(14);
    scale.ticks().interval(2);

    var axis = gaugeUV.axis();
    axis.minorTicks(true)
    axis.minorTicks().stroke('#cecece');
    axis.width('1%');
    axis.offset('8%');
    axis.orientation('left');

    gaugeUV.container('uv-chart');
    gaugeUV.draw();
});