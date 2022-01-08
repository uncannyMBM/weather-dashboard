var rainfall;
anychart.onDocumentReady(function () {

    rainfall = anychart.gauges.linear();

    rainfall.layout('vertical');

    var marker = rainfall.marker(0);

    marker.offset('35%');

    marker.type('triangle-left');

    marker.zIndex(10);

    var scale = rainfall.scale();
    scale.minimum(0);
    scale.maximum(16);
    scale.ticks().interval(2);

    var axis = rainfall.axis();
    axis.minorTicks(true)
    axis.minorTicks().stroke('#cecece');
    axis.width('1%');
    axis.offset('29.5%');
    axis.orientation('left');

    rainfall.padding([20, 50]);

    rainfall.container('rainfall-chart');

    rainfall.draw();
});