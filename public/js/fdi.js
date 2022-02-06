var fdiGauge;
anychart.onDocumentReady(function () {
    var green_color = '#04cc63';
    var yello_color = '#eeef63';
    var orange_color = '#efc747';
    var deep_orange_color = '#ef9900';
    var light_red_color = '#ef5e33';
    var red_color = '#ef251c';

   let ticks = [0, 10, 25, 50, 75, 100, 120];

    // var stage = anychart.graphics.create('fdi-chart');

    fdiGauge = anychart.gauges.linear();

    fdiGauge.title().enabled(true).text('').margin([-20, 0 ,0 ,0]).fontColor('#212121').fontSize(18);
    fdiGauge.scaleBar(0)
        .width('5%')
        .from(ticks[0])
        .to(ticks[1])
        .fill({
            keys: [green_color],
            angle: 90
        });

    fdiGauge.scaleBar(1)
        .width('5%')
        .from(ticks[1])
        .to(ticks[2])
        .fill(yello_color);

    fdiGauge.scaleBar(2)
        .width('5%')
        .from(ticks[2])
        .to(ticks[3])
        .fill(orange_color);

    fdiGauge.scaleBar(3)
        .width('5%')
        .from(ticks[3])
        .to(ticks[4])
        .fill(deep_orange_color);

    fdiGauge.scaleBar(4)
        .width('5%')
        .from(ticks[4])
        .to(ticks[5])
        .fill(light_red_color);

    fdiGauge.scaleBar(5)
        .width('5%')
        .from(ticks[5])
        .to(ticks[6])
        .fill(red_color);

    let scale = fdiGauge.scale();
    scale.minimum(0)
        .maximum(120)
        .ticks([0, 20, 40, 60, 80, 100, 120]);

    var axis = fdiGauge.axis();
    axis.width('0.5%')
        .offset('-1%')
        .scale(scale);
    axis.labels()
        .fontSize(16)
        .format('{%Value}');

    var marker = fdiGauge.marker(0);
    marker.color('#7c868e')
        .offset('8.5%')
        .type('triangle-left');

    marker.labels()
        .enabled(true)
        .position('right-center')
        .offsetX(0)
        .anchor('left-center')
        .fontSize(18)
        .fontColor('#212121');

    fdiGauge.bounds(0, '2%', '100%', '95%');
    fdiGauge.container('fdi-chart');
    fdiGauge.draw();
});