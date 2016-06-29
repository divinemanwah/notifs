$(function () {

	$('div.easy-pie-chart.percentage').each(function(){
		var $box = $(this).closest('.infobox');
		var barColor = $(this).data('color') || (!$box.hasClass('infobox-dark') ? $box.css('color') : 'rgba(255,255,255,0.95)');
		var trackColor = barColor == 'rgba(255,255,255,0.95)' ? 'rgba(255,255,255,0.25)' : '#E2E2E2';
		var size = parseInt($(this).data('size')) || 50;
		$(this).easyPieChart({
			barColor: barColor,
			trackColor: trackColor,
			scaleColor: false,
			lineCap: 'butt',
			lineWidth: parseInt(size/10),
			animate: /msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase()) ? false : 1000,
			size: size
		});
	});
	
	$.resize.throttleWindow = false;
	
	var placeholder = $('div#piechart-placeholder').css({'width':'90%' , 'min-height':'150px'});
	
	var $tooltip = $("<div class='tooltip top in'><div class='tooltip-inner'></div></div>").hide().appendTo('body');
	var previousPoint = null;

	if(cite_pie_data.length) {

		$.plot(placeholder, cite_pie_data, {
			series: {
				pie: {
					show: true,
					tilt:0.8,
					highlight: {
						opacity: 0.25
					},
					stroke: {
						color: '#fff',
						width: 2
					},
					startAngle: 2
				}
			},
			legend: {
				show: true,
				position: 'ne',
				labelBoxBorderColor: null,
				margin:[-30, 15],
				//noColumns: 2
				container: 'div.pie-legend'
			}
			,
			grid: {
				hoverable: true,
				clickable: true
			}
		});

		placeholder.on('plothover', function (event, pos, item) {
			if(item) {
				if (previousPoint != item.seriesIndex) {
					previousPoint = item.seriesIndex;
					var tip = item.series['label'] + " : " + (Math.floor(item.series['percent'] * 100) / 100)+'%';
					$tooltip.show().children(0).text(tip);
				}
				$tooltip.css({top:pos.pageY + 10, left:pos.pageX + 10});
			} else {
				$tooltip.hide();
				previousPoint = null;
			}

		});
	}
	else
		placeholder.html('<span class="text-muted">No offense recorded for this month</span>');

	$('div#offenses-increase-decrease').tooltip();
	
	var __data = {};
	
	$.getJSON(
		base_url + 'employees/getKPI/0/1',
		function (data) {
			
			var _data = new Array();
			
			$.each(data, function (i, val) {
			
				// var ___data = $.map(val, function (i2, val2) { return [[val2 - 1, i2]]; });
				var ___data = $.map(val, function (i2, val2) { return [[val2 - 1, 20]]; });
				
				_data[_data.length] = {
						label: i,
						data: ___data
					};
			});

			var months_abbr = moment.monthsShort(),
				months_tick = new Array();
			
			$.each(months_abbr, function (i, val) {
				
				months_tick[months_tick.length] = [i, val];
				
			});

			var dept_kpi_stats = $('div#dept-kpi-stats').css({'width':'100%' , 'height':'450px'}).empty();

			var _dept_kpi_stats = $.plot(dept_kpi_stats, _data, {
					shadowSize: 0,
					series: {
							lines: { show: true },
							points: { show: true }
						},
					xaxis: {
							ticks: months_tick,
							min: 0,
							max: 11,
						},
					yaxis: {
							min: 0,
							max: base_hr_score,
							tickDecimals: 0
						},
					grid: {
							backgroundColor: { colors: [ "#fff", "#fff" ] },
							borderWidth: 1,
							borderColor:'#ccc',
							hoverable: true
						},
					legend: {
							backgroundOpacity: .5,
							sorted: true,
							position: moment().format('M') > 9 ? 'sw' : 'se'
						}
				});
			
			dept_kpi_stats.on('plothover', function (event, pos, item) {
				if(item) {
					if (previousPoint != item.seriesIndex) {
						previousPoint = item.seriesIndex;
						$tooltip.show().children(0).text(item.series.label + ' : ' + item.datapoint[1]);
					}
					$tooltip.css({top:pos.pageY + 10, left:pos.pageX + 10});
				} else {
					$tooltip.hide();
					previousPoint = null;
				}

			});
			
			$.each(_dept_kpi_stats.getData(), function (i, val) {
				
				__data[val.label] = val;
				
			});
			
			$('div.legend tr', dept_kpi_stats[0]).hover(
				function () {
				
						_dept_kpi_stats.setData([__data[$('td.legendLabel', this).text()]]);

						_dept_kpi_stats.draw();
					},
				function () {
						
						_dept_kpi_stats.setData(_data);
						
						_dept_kpi_stats.draw();
					}
			);
			
		}
	);
	
});