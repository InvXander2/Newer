'use strict';

// Menu options custom affix
var fixed_top = $(".header");
$(window).on("scroll", function(){
    if( $(window).scrollTop() > 50){  
        fixed_top.addClass("animated fadeInDown menu-fixed");
    }
    else{
        fixed_top.removeClass("animated fadeInDown menu-fixed");
    }
});

// Mobile menu js
$(".navbar-collapse>ul>li>a, .navbar-collapse ul.sub-menu>li>a").on("click", function() {
  const element = $(this).parent("li");
  if (element.hasClass("open")) {
    element.removeClass("open");
    element.find("li").removeClass("open");
  }
  else {
    element.addClass("open");
    element.siblings("li").removeClass("open");
    element.siblings("li").find("li").removeClass("open");
  }
});

let img=$('.bg_img');
img.css('background-image', function () {
	let bg = ('url(' + $(this).data('background') + ')');
	return bg;
});

// Show or hide the sticky footer button
$(window).on("scroll", function() {
	if ($(this).scrollTop() > 200) {
			$(".scroll-to-top").fadeIn(200);
	} else {
			$(".scroll-to-top").fadeOut(200);
	}
});

// Animate the scroll to top
$(".scroll-to-top").on("click", function(event) {
	event.preventDefault();
	$("html, body").animate({scrollTop: 0}, 300);
});

// Preloader js code
$(".preloader").delay(300).animate({
	"opacity" : "0"
	}, 300, function() {
	$(".preloader").css("display","none");
});

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
});

/* ==============================
					Slider area
================================= */

// Testimonial slider 
$('.testimonial-slider').slick({
  dots: true,
  infinite: true,
  speed: 300,
  slidesToShow: 3,
  slidesToScroll: 1,
  arrows: false,
  // autoplay: true,
  prevArrow: '<div class="prev"><i class="las la-angle-left"></i></div>',
  nextArrow: '<div class="next"><i class="las la-angle-right"></i></div>',
  responsive: [
    {
      breakpoint: 1200,
      settings: {
        slidesToShow: 2
      }
    },
    {
      breakpoint: 768,
      settings: {
        slidesToShow: 1
      }
    },
    {
      breakpoint: 576,
      settings: {
        slidesToShow: 1
      }
    }
  ]
});

$('.payment-slider').slick({
  dots: false,
  infinite: true,
  speed: 300,
  slidesToShow: 6,
  slidesToScroll: 1,
  arrows: false,
  autoplay: false,
  prevArrow: '<div class="prev"><i class="las la-angle-left"></i></div>',
  nextArrow: '<div class="next"><i class="las la-angle-right"></i></div>',
  responsive: [
    {
      breakpoint: 1200,
      settings: {
        slidesToShow: 4,
        slidesToScroll: 1,
      }
    },
    {
      breakpoint: 768,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 2
      }
    },
    {
      breakpoint: 576,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1
      }
    }
  ]
});

/*------- Calculator --------*/
(function () {
    if (typeof $.fn.databinder == 'undefined') {
        return;
    }

    $('.profit-calculator').on('changed.bs.select', function () {
        $(this).trigger('recalculate');
    }).on('input', ':input', function () {
        $(this).trigger('input');
    }).databinder({
        money: {
            decimals: 2,
            separator: '.',
            thousands: ' ',
            cutzero: false
        },
        calculate: function (data, callback) {
            try {
                // Example: minimal cost
                data.min = 100;

                data.total_profit = data.plan * data.amount * data.duration / 100;

                var profit = data.total_profit;

                profit = Math.max(profit, data.min);

                data.profit = profit ? profit : null;

                callback(data);

            } catch (error) {
                console && console.log(error.message);
            }
        }
    });
})();

// ApexCharts initialization for charts
$(document).ready(function() {
    // Investment Overview Chart (ana_dash_1)
    var invests = [<?php echo isset($invests) ? $invests : '0,0,0,0,0,0,0,0,0,0,0,0'; ?>];
    var capital = [<?php echo isset($capital) ? $capital : '0,0,0,0,0,0,0,0,0,0,0,0'; ?>];

    var options = {
        chart: {
            type: 'area',
            height: 350,
            toolbar: { show: false }
        },
        series: [
            {
                name: 'Net Returns',
                data: invests
            },
            {
                name: 'Capital Invested',
                data: capital
            }
        ],
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },
        colors: ['#556ee6', '#34c38f'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3
            }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 }
    };

    if (document.querySelector("#ana_dash_1")) {
        var chart = new ApexCharts(document.querySelector("#ana_dash_1"), options);
        chart.render();
    }

    // Earnings By Channel Chart (barchart)
    <?php
    $plan_earnings = [];
    $plan_names = [];
    $stmt = $conn->prepare("SELECT ip.name, SUM(i.returns) as total_earned 
                            FROM investment i 
                            LEFT JOIN investment_plans ip ON ip.id = i.invest_plan_id 
                            WHERE i.user_id = :id AND i.status = 'completed' 
                            GROUP BY i.invest_plan_id");
    $stmt->execute(['id' => isset($id) ? $id : 0]);
    foreach ($stmt as $row) {
        $plan_earnings[] = round($row['total_earned'], 2);
        $plan_names[] = "'" . htmlspecialchars($row['name']) . "'";
    }
    $plan_earnings = implode(',', $plan_earnings) ?: '0';
    $plan_names = implode(',', $plan_names) ?: "'No Plans'";
    ?>

    var barOptions = {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false }
        },
        series: [{
            name: 'Earnings',
            data: [<?php echo $plan_earnings; ?>]
        }],
        xaxis: {
            categories: [<?php echo $plan_names; ?>]
        },
        colors: ['#556ee6'],
        plotOptions: {
            bar: { horizontal: false, columnWidth: '55%' }
        },
        dataLabels: { enabled: false },
        yaxis: {
            title: { text: 'Earnings ($)' }
        }
    };

    if (document.querySelector("#barchart")) {
        var barChart = new ApexCharts(document.querySelector("#barchart"), barOptions);
        barChart.render();
    }
});
