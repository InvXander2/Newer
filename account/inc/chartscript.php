<script>
    // Investment Overview Chart (ana_dash_1)
    var invests = [<?php echo $invests; ?>];
    var capital = [<?php echo $capital; ?>];
    var options = {
        chart: { height: 320, type: "area", stacked: !0, toolbar: { show: !1, autoSelected: "zoom" } },
        colors: ["#a5c2f1", "#2a77f4"],
        dataLabels: { enabled: !1 },
        stroke: { curve: "smooth", width: [1.5, 1.5], dashArray: [0, 4], lineCap: "round" },
        grid: { padding: { left: 0, right: 0 }, strokeDashArray: 3 },
        markers: { size: 0, hover: { size: 0 } },
        series: [
            { name: "Investment", data: invests },
            { name: "Profit", data: capital },
        ],
        xaxis: { 
            type: "category", 
            categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], 
            axisBorder: { show: !0 }, 
            axisTicks: { show: !0 } 
        },
        fill: { type: "gradient", gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.3, stops: [0, 90, 100] } },
        tooltip: { x: { format: "MMM yyyy" } },
        legend: { position: "top", horizontalAlign: "right" },
    };
    var chart = new ApexCharts(document.querySelector("#ana_dash_1"), options);
    chart.render();

    // Earnings Summary Chart (ana_device)
    var p1 = <?php echo $percent_plan1 ?: 0; ?>;
    var p2 = <?php echo $percent_plan2 ?: 0; ?>;
    var p3 = <?php echo $percent_plan3 ?: 0; ?>;
    var options = {
        chart: { height: 270, type: "donut" },
        plotOptions: { pie: { donut: { size: "85%" } } },
        dataLabels: { enabled: !1 },
        stroke: { show: !0, width: 2, colors: ["transparent"] },
        series: [p1, p2, p3].filter(val => val > 0), // Filter out zero values
        legend: { 
            show: !0, 
            position: "bottom", 
            horizontalAlign: "center", 
            verticalAlign: "middle", 
            floating: !1, 
            fontSize: "13px", 
            offsetX: 0, 
            offsetY: 0 
        },
        labels: [
            <?php 
            $labels = [];
            foreach ($stmt1 as $pinv) {
                $labels[] = "'" . htmlspecialchars($pinv['name']) . "'";
            }
            echo implode(',', $labels) ?: "'Plan 1', 'Plan 2', 'Plan 3'";
            ?>
        ],
        colors: ["#2a76f4", "rgba(42, 118, 244, .5)", "rgba(42, 118, 244, .18)"],
        responsive: [
            { 
                breakpoint: 600, 
                options: { 
                    plotOptions: { donut: { customScale: 0.2 } }, 
                    chart: { height: 240 }, 
                    legend: { show: !1 } 
                } 
            }
        ],
        tooltip: {
            y: {
                formatter: function (o) {
                    return o + " %";
                },
            },
        },
    };
    var chart = new ApexCharts(document.querySelector("#ana_device"), options);
    chart.render();

    // Earnings by Channel Chart (barchart)
    var colors = ["#98e7df", "#b8c4d0", "#bec7fa"];
    var options = {
        series: [{
            name: "Earnings",
            data: [
                <?php echo $total_plan1 ?: 0; ?>,
                <?php echo $total_plan2 ?: 0; ?>,
                <?php echo $total_plan3 ?: 0; ?>
            ].filter(val => val > 0) // Filter out zero values
        }],
        chart: { height: 355, type: "bar", toolbar: { show: !1 } },
        plotOptions: { 
            bar: { 
                dataLabels: { position: "top" }, 
                columnWidth: "20", 
                distributed: true 
            } 
        },
        dataLabels: {
            enabled: !0,
            formatter: function (o) {
                return "$" + o.toFixed(2);
            },
            offsetY: -20,
            style: { fontSize: "12px", colors: ["#000"] },
        },
        colors: colors,
        xaxis: {
            categories: [
                <?php 
                $labels = [];
                foreach ($stmt1 as $pinv) {
                    $labels[] = "'" . htmlspecialchars($pinv['name']) . "'";
                }
                echo implode(',', $labels) ?: "'Plan 1', 'Plan 2', 'Plan 3'";
                ?>
            ],
            position: "top",
            axisBorder: { show: !1 },
            axisTicks: { show: !1 },
            crosshairs: { 
                fill: { 
                    type: "gradient", 
                    gradient: { 
                        colorFrom: "#D8E3F0", 
                        colorTo: "#BED1E6", 
                        stops: [0, 100], 
                        opacityFrom: 0.4, 
                        opacityTo: 0.5 
                    } 
                } 
            },
            tooltip: { enabled: !0 },
        },
        grid: { padding: { left: 0, right: 0 }, strokeDashArray: 3 },
        yaxis: {
            axisBorder: { show: !1 },
            axisTicks: { show: !1 },
            labels: {
                show: !0,
                formatter: function (o) {
                    return "$" + o.toFixed(2);
                },
            },
        },
    };
    var chart = new ApexCharts(document.querySelector("#barchart"), options);
    chart.render();
</script>
