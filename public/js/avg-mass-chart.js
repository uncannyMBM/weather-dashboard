var avgPmDataSet;
anychart.onDocumentReady(function () {
    avgPmDataSet = anychart.data.set([]);
    var firstSeriesData = avgPmDataSet.mapAs({ x: 0, value: 1 });
    var secondSeriesData = avgPmDataSet.mapAs({ x: 0, value: 2 });
    var thirdSeriesData = avgPmDataSet.mapAs({ x: 0, value: 3 });
    var forthSeriesData = avgPmDataSet.mapAs({ x: 0, value: 4 });

    var chart = anychart.line();

    chart.animation(true);

    chart.padding([10, 20, 5, 20]);

    chart.crosshair().enabled(true).yLabel(false).yStroke(null);

    chart.tooltip().positionMode('point');

    chart.yAxis().title('Avrage Mass Concentration (µg/m³)');
    chart.xAxis().labels().padding(5);

    var firstSeries = chart.line(firstSeriesData);
    firstSeries.name('PM 1');
    firstSeries.hovered().markers().enabled(true).type('circle').size(4);
    firstSeries
        .tooltip()
        .position('right')
        .anchor('left-center')
        .offsetX(5)
        .offsetY(5);

    var secondSeries = chart.line(secondSeriesData);
    secondSeries.name('PM 2.5');
    secondSeries.hovered().markers().enabled(true).type('circle').size(4);
    secondSeries
        .tooltip()
        .position('right')
        .anchor('left-center')
        .offsetX(5)
        .offsetY(5);

    var thirdSeries = chart.line(thirdSeriesData);
    thirdSeries.name('PM 4');
    thirdSeries.hovered().markers().enabled(true).type('circle').size(4);
    thirdSeries
        .tooltip()
        .position('right')
        .anchor('left-center')
        .offsetX(5)
        .offsetY(5);

    var forthSeries = chart.line(forthSeriesData);
    forthSeries.name('PM 10');
    forthSeries.hovered().markers().enabled(true).type('circle').size(4);
    forthSeries
        .tooltip()
        .position('right')
        .anchor('left-center')
        .offsetX(5)
        .offsetY(5);

    chart.legend().enabled(true).fontSize(13).padding([0, 0, 10, 0]);

    chart.container('avg_mass_chart');
    chart.draw();
});

