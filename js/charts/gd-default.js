!(function (NioApp, $) {
    "use strict";

    //////// for developer - User Balance //////// 
    // Avilable options to pass from outside 
    // labels: array,
    // legend: false - boolean,
    // dataUnit: string, (Used in tooltip or other section for display) 
    // datasets: [{label : string, color: string (color code with # or other format), data: array}]
    var profileBalance = {
        labels : ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30"],
        dataUnit : 'BTC',
        lineTension : 0.15,
        datasets : [{
            label : "Total Received",
            color : "#733AEA",
            background : NioApp.hexRGB('#733AEA',.3),
            data: [111, 80, 125, 75, 95, 75, 90, 111, 80, 125, 75, 95, 75, 90, 111, 80, 125, 75, 95, 75, 90, 111, 80, 125, 75, 95, 75, 90, 75, 90]
        }]
    };

    function lineProfileBalance(selector, set_data){
        var $selector = (selector) ? $(selector) : $('.profile-balance-chart');
        $selector.each(function(){
            var $self = $(this), _self_id = $self.attr('id'), _get_data = (typeof set_data === 'undefined') ? eval(_self_id) : set_data;
            var selectCanvas = document.getElementById(_self_id).getContext("2d");

            var chart_data = [];
            for (var i = 0; i < _get_data.datasets.length; i++) {
                chart_data.push({
                    label: _get_data.datasets[i].label,
                    tension:_get_data.lineTension,
                    backgroundColor: _get_data.datasets[i].background,
                    fill: true,
                    borderWidth:2,
                    borderColor: _get_data.datasets[i].color,
                    pointBorderColor: "transparent",
                    pointBackgroundColor: "transparent",
                    pointHoverBackgroundColor: "#fff",
                    pointHoverBorderColor: _get_data.datasets[i].color,
                    pointBorderWidth: 2,
                    pointHoverRadius: 3,
                    pointHoverBorderWidth: 2,
                    pointRadius: 3,
                    pointHitRadius: 3,
                    data: _get_data.datasets[i].data,
                });
            } 
            var chart = new Chart(selectCanvas, {
                type: 'line',
                data: {
                    labels: _get_data.labels,
                    datasets: chart_data,
                },
                options: {
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            enabled: true,
                            rtl: NioApp.State.isRTL,
                            callbacks: {
                                title: function() {
                                    return false;
                                },
                                label: function (context) {
                                    return `${context.parsed.y} ${_get_data.dataUnit}`;
                                },
                            },
                            backgroundColor: '#eff6ff',
                            titleFont:{
                                size: 11,
                            },
                            titleColor: '#6783b8',
                            titleMarginBottom: 4,
                            bodyColor: '#9eaecf',
                            bodyFont:{
                                size:10,
                            },
                            bodySpacing:3,
                            padding: 8,
                            footerMarginTop: 0,
                            displayColors: false
                        },
                    },
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            display: false,
                        },
                        x: {
                            display: false,
                            reverse: NioApp.State.isRTL
                        }
                    }
                }
            });
        })
    }

    // init chart
    NioApp.coms.docReady.push(function(){ lineProfileBalance(); });

    var orderOverview = {
        labels : ["22 Jun", "23 Jun", "24 Jun", "25 Jun", "26 Jun", "27 Jun", "28 Jun", "29 Jun", "30 Jun", "01 Jul"],
        dataUnit : 'USD',
        datasets : [{
            label : "Buy Orders",
            color : "#8feac5",
            data: [1820, 1200, 1600, 2500, 1820, 1200, 1700, 1820, 1400, 2100]
        },{
            label : "Sell Orders",
            color : "#9C73F5",
            data: [3000, 3450, 2450, 1820, 2700, 4870, 2470, 2600, 4000, 2380]
        }]
    };

    function orderOverviewChart(selector, set_data){
        var $selector = (selector) ? $(selector) : $('.order-overview-chart');
        $selector.each(function(){
            var $self = $(this), _self_id = $self.attr('id'), _get_data = (typeof set_data === 'undefined') ? eval(_self_id) : set_data,
            _d_legend = (typeof _get_data.legend === 'undefined') ? false : _get_data.legend;

            var selectCanvas = document.getElementById(_self_id).getContext("2d");
            var chart_data = [];
            for (var i = 0; i < _get_data.datasets.length; i++) {
                chart_data.push({
                    label: _get_data.datasets[i].label,
                    data: _get_data.datasets[i].data,
                    // Styles
                    backgroundColor: _get_data.datasets[i].color,
                    borderWidth:2,
                    borderColor: 'transparent',
                    hoverBorderColor : 'transparent',
                    borderSkipped : 'bottom',
                    barPercentage : NioApp.State.asMobile ? 1 : .7,
                    categoryPercentage : NioApp.State.asMobile ? 1 : .7,
                });
            } 
            var chart = new Chart(selectCanvas, {
                type: 'bar',
                data: {
                    labels: _get_data.labels,
                    datasets: chart_data,
                },
                options: {
                    plugins: {
                        legend: {
                            display: (_get_data.legend) ? _get_data.legend : false,
                            rtl: NioApp.State.isRTL,
                            labels: {
                                boxWidth:30,
                                padding:20,
                                color: '#6783b8',
                            }
                        },
                        tooltip: {
                            enabled: true,
                            rtl: NioApp.State.isRTL,
                            callbacks: {
                                label: function (context) {
                                    return `${context.parsed.y} ${_get_data.dataUnit}`;
                                },
                            },
                            backgroundColor: '#eff6ff',
                            titleFont:{
                                size: 13,
                            },
                            titleColor: '#6783b8',
                            titleMarginBottom: 6,
                            bodyColor: '#9eaecf',
                            bodyFont:{
                                size: 12
                            },
                            bodySpacing:4,
                            padding: 10,
                            footerMarginTop: 0,
                            displayColors: false
                        },
                    },
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            display: true,
                            stacked: (_get_data.stacked) ? _get_data.stacked : false,
                            position : NioApp.State.isRTL ? "right" : "left",
                            ticks: {
                                beginAtZero:true,
                                font:{
                                    size:11,
                                },
                                color:'#9eaecf',
                                padding:10,
                                callback: function(value, index, values) {
                                    return '$ ' + value;
                                },
                                min:100,
                                max:5000,
                                stepSize:1200
                            },
                            grid: { 
                                color: NioApp.hexRGB("#526484",.2),
                                tickLength:0,
                                zeroLineColor: NioApp.hexRGB("#526484",.2),
                                drawTicks:false,
                            },
                            
                        },
                        x: {
                            display: true,
                            stacked: (_get_data.stacked) ? _get_data.stacked : false,
                            ticks: {
                                font:{
                                    size:9,
                                },
                                color:'#9eaecf',
                                source: 'auto',
                                padding:10,
                            },
                            reverse: NioApp.State.isRTL,
                            grid: {
                                color: "transparent",
                                tickLength: 0,
                                zeroLineColor: 'transparent',
                                drawTicks:false,
                            },
                        }
                    }
                }
            });
        })
    }
    // init chart
    NioApp.coms.docReady.push(function(){ orderOverviewChart(); });

    var userActivity = {
        labels : ["01 Nov", "02 Nov", "03 Nov", "04 Nov", "05 Nov", "06 Nov", "07 Nov", "08 Nov", "09 Nov", "10 Nov", "11 Nov", "12 Nov", "13 Nov", "14 Nov", "15 Nov", "16 Nov", "17 Nov", "18 Nov", "19 Nov", "20 Nov", "21 Nov"],
        dataUnit : 'USD',
        stacked : true,
        datasets : [{
            label : "Direct Join",
            color : "#9C73F5",
            data: [110, 80, 125, 55, 95, 75, 90, 110, 80, 125, 55, 95, 75, 90, 110, 80, 125, 55, 95, 75, 90]
        },{
            label : "Referral Join",
            color : NioApp.hexRGB("#9C73F5", .2),
            data: [125, 55, 95, 75, 90, 110, 80, 125, 55, 95, 75, 90, 110, 80, 125, 55, 95, 75, 90, 75, 90]
        }]
    };

    function userActivityChart(selector, set_data){
        var $selector = (selector) ? $(selector) : $('.usera-activity-chart');
        $selector.each(function(){
            var $self = $(this), _self_id = $self.attr('id'), _get_data = (typeof set_data === 'undefined') ? eval(_self_id) : set_data,
            _d_legend = (typeof _get_data.legend === 'undefined') ? false : _get_data.legend;

            var selectCanvas = document.getElementById(_self_id).getContext("2d");
            var chart_data = [];
            for (var i = 0; i < _get_data.datasets.length; i++) {
                chart_data.push({
                    label: _get_data.datasets[i].label,
                    data: _get_data.datasets[i].data,
                    // Styles
                    backgroundColor: _get_data.datasets[i].color,
                    borderWidth:2,
                    borderColor: 'transparent',
                    hoverBorderColor : 'transparent',
                    borderSkipped : 'bottom',
                    barPercentage : .8,
                    categoryPercentage : .9
                });
            } 
            var chart = new Chart(selectCanvas, {
                type: 'bar',
                data: {
                    labels: _get_data.labels,
                    datasets: chart_data,
                },
                options: {
                    plugins: {
                        legend: {
                            display: (_get_data.legend) ? _get_data.legend : false,
                            rtl: NioApp.State.isRTL,
                            labels: {
                                boxWidth:30,
                                padding:20,
                                color: '#6783b8',
                            }
                        },
                        tooltip: {
                            enabled: true,
                            rtl: NioApp.State.isRTL,
                            callbacks: {
                                label: function (context) {
                                    return `${context.parsed.y} ${_get_data.dataUnit}`;
                                },
                            },
                            backgroundColor: '#eff6ff',
                            titleFont:{
                                size: 13,
                            },
                            titleColor: '#6783b8',
                            titleMarginBottom: 6,
                            bodyColor: '#9eaecf',
                            bodyFont:{
                                size: 12
                            },
                            bodySpacing:4,
                            padding: 10,
                            footerMarginTop: 0,
                            displayColors: false
                        },
                    },
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            display: false,
                            stacked: (_get_data.stacked) ? _get_data.stacked : false,
                            ticks: {
                                beginAtZero:true
                            }
                        },
                        x: {
                            display: false,
                            stacked: (_get_data.stacked) ? _get_data.stacked : false,
                            reverse: NioApp.State.isRTL
                        }
                    }
                }
            });
        })
    }
    // init chart
    NioApp.coms.docReady.push(function(){ userActivityChart(); });

    var coinOverview = {
        labels : ["Bitcoin", "Ethereum", "NioCoin", "Litecoin", "Bitcoin"],
        stacked:true,
        datasets : [{
            label : "Buy Orders",
            color : ["#f98c45", "#6baafe", "#8feac5", "#6b79c8", "#79f1dc"],
            data: [1740, 2500, 1820, 1200, 1600, 2500]
        },{
            label : "Sell Orders",
            color : [NioApp.hexRGB('#f98c45',.2),NioApp.hexRGB('#6baafe',.4),NioApp.hexRGB('#8feac5',.4),NioApp.hexRGB('#6b79c8',.4),NioApp.hexRGB('#79f1dc',.4)],
            data: [2420, 1820, 3000, 5000, 2450, 1820]
        }]
    };

    function coinOverviewChart(selector, set_data){
        var $selector = (selector) ? $(selector) : $('.coin-overview-chart');
        $selector.each(function(){
            var $self = $(this), _self_id = $self.attr('id'), _get_data = (typeof set_data === 'undefined') ? eval(_self_id) : set_data,
            _d_legend = (typeof _get_data.legend === 'undefined') ? false : _get_data.legend;

            var selectCanvas = document.getElementById(_self_id).getContext("2d");
            var chart_data = [];
            for (var i = 0; i < _get_data.datasets.length; i++) {
                chart_data.push({
                    label: _get_data.datasets[i].label,
                    data: _get_data.datasets[i].data,
                    // Styles
                    backgroundColor: _get_data.datasets[i].color,
                    borderWidth:2,
                    borderColor: 'transparent',
                    hoverBorderColor : 'transparent',
                    borderSkipped : 'bottom',
                    barThickness:'8',
                    categoryPercentage: 0.5,
                    barPercentage: 1.0
                });
            } 
            var chart = new Chart(selectCanvas, {
                type: 'bar',
                data: {
                    labels: _get_data.labels,
                    datasets: chart_data,
                },
                options: {
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: (_get_data.legend) ? _get_data.legend : false,
                            rtl: NioApp.State.isRTL,
                            labels: {
                                boxWidth:30,
                                padding:20,
                                color: '#6783b8',
                            }
                        },
                        tooltip: {
                            enabled: true,
                            rtl: NioApp.State.isRTL,
                            callbacks: {
                                label: function (context) {
                                    return `${context.parsed.y} ${_get_data.dataUnit}`;
                                },
                            },
                            backgroundColor: '#eff6ff',
                            titleFont:{
                                size: 13,
                            },
                            titleColor: '#6783b8',
                            titleMarginBottom: 6,
                            bodyColor: '#9eaecf',
                            bodyFont:{
                                size: 12
                            },
                            bodySpacing:4,
                            padding: 10,
                            footerMarginTop: 0,
                            displayColors: false
                        },
                    },
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            display: false,
                            stacked: (_get_data.stacked) ? _get_data.stacked : false,
                            ticks: {
                                beginAtZero:true,
                                padding:0,
                            },
                            grid: { 
                                color: NioApp.hexRGB("#526484",.2),
                                tickLength:0,
                                zeroLineColor: NioApp.hexRGB("#526484",.2),
                                drawTicks:false,
                            },
                            
                        },
                        x: {
                            display: false,
                            stacked: (_get_data.stacked) ? _get_data.stacked : false,
                            reverse: NioApp.State.isRTL,
                            ticks: {
                                font:{
                                    size:9,
                                },
                                color:'#9eaecf',
                                source: 'auto',
                                padding:0,
                            },
                            grid: {
                                color: "transparent",
                                tickLength: 0,
                                zeroLineColor: 'transparent',
                                drawTicks:false,
                            },
                        }
                    }
                }
            });
        })
    }
    // init chart
    NioApp.coms.docReady.push(function(){ coinOverviewChart(); });


    var salesRevenue = {
        labels : ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        dataUnit : 'USD',
        stacked : true,
        datasets : [{
            label : "Sales Revenue",
            color : [NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2), NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2),"#733AEA"],
            data: [11000, 8000, 12500, 5500, 9500, 14299, 11000, 8000, 12500, 5500, 9500, 14299]
        }]
    };

    var activeSubscription = {
        labels : ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
        dataUnit : 'USD',
        stacked : true,
        datasets : [{
            label : "Active User",
            color : [NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2),"#733AEA"],
            data: [8200, 7800, 9500, 5500, 9200, 9690]
        }]
    };

    var totalSubscription = {
        labels : ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
        dataUnit : 'USD',
        stacked : true,
        datasets : [{
            label : "Active User",
            color : [NioApp.hexRGB("#aea1ff", .2),NioApp.hexRGB("#aea1ff", .2),NioApp.hexRGB("#aea1ff", .2),NioApp.hexRGB("#aea1ff", .2),NioApp.hexRGB("#aea1ff", .2),"#aea1ff"],
            data: [8200, 7800, 9500, 5500, 9200, 9690]
        }]
    };

    function salesBarChart(selector, set_data){
        var $selector = (selector) ? $(selector) : $('.sales-bar-chart');
        $selector.each(function(){
            var $self = $(this), _self_id = $self.attr('id'), _get_data = (typeof set_data === 'undefined') ? eval(_self_id) : set_data,
            _d_legend = (typeof _get_data.legend === 'undefined') ? false : _get_data.legend;

            var selectCanvas = document.getElementById(_self_id).getContext("2d");
            var chart_data = [];
            for (var i = 0; i < _get_data.datasets.length; i++) {
                chart_data.push({
                    label: _get_data.datasets[i].label,
                    data: _get_data.datasets[i].data,
                    // Styles
                    backgroundColor: _get_data.datasets[i].color,
                    borderWidth:2,
                    borderColor: 'transparent',
                    hoverBorderColor : 'transparent',
                    borderSkipped : 'bottom',
                    barPercentage : .7,
                    categoryPercentage : .7
                });
            } 
            var chart = new Chart(selectCanvas, {
                type: 'bar',
                data: {
                    labels: _get_data.labels,
                    datasets: chart_data,
                },
                options: {
                    plugins: {
                        legend: {
                            display: (_get_data.legend) ? _get_data.legend : false,
                            rtl: NioApp.State.isRTL,
                            labels: {
                                boxWidth:30,
                                padding:20,
                                color: '#6783b8',
                            }
                        },
                        tooltip: {
                            enabled: true,
                            rtl: NioApp.State.isRTL,
                            callbacks: {
                                title: function() {
                                    return false;
                                },
                                label: function (context) {
                                    return `${context.parsed.y} ${_get_data.dataUnit}`;
                                },
                            },
                            backgroundColor: '#eff6ff',
                            titleFont:{
                                size: 11,
                            },
                            titleColor: '#6783b8',
                            titleMarginBottom: 4,
                            bodyColor: '#9eaecf',
                            bodyFont:{
                                size:10,
                            },
                            bodySpacing:3,
                            padding: 8,
                            footerMarginTop: 0,
                            displayColors: false
                        },
                    },
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            display: false,
                            stacked: (_get_data.stacked) ? _get_data.stacked : false,
                            ticks: {
                                beginAtZero:true
                            }
                        },
                        x: {
                            display: false,
                            stacked: (_get_data.stacked) ? _get_data.stacked : false,
                            reverse: NioApp.State.isRTL
                        }
                    }
                }
            });
        })
    }
    // init chart
    NioApp.coms.docReady.push(function(){ salesBarChart(); });

    var salesOverview = {
        labels : ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30"],
        dataUnit : 'BTC',
        lineTension : 0.1,
        datasets : [{
            label : "Sales Overview",
            color : "#733AEA",
            background : NioApp.hexRGB('#733AEA',.3),
            data: [8200, 7800, 9500, 5500, 9200, 9690, 8200, 7800, 9500, 5500, 9200, 9690,8200, 7800, 9500, 5500, 9200, 9690, 8200, 7800, 9500, 5500, 9200, 9690,8200, 7800, 9500, 5500, 9200, 9690]
        }]
    };

    function lineSalesOverview(selector, set_data){
        var $selector = (selector) ? $(selector) : $('.sales-overview-chart');
        $selector.each(function(){
            var $self = $(this), _self_id = $self.attr('id'), _get_data = (typeof set_data === 'undefined') ? eval(_self_id) : set_data;
            var selectCanvas = document.getElementById(_self_id).getContext("2d");

            var chart_data = [];
            for (var i = 0; i < _get_data.datasets.length; i++) {
                chart_data.push({
                    label: _get_data.datasets[i].label,
                    tension:_get_data.lineTension,
                    backgroundColor: _get_data.datasets[i].background,
                    fill: true,
                    borderWidth:2,
                    borderColor: _get_data.datasets[i].color,
                    pointBorderColor: "transparent",
                    pointBackgroundColor: "transparent",
                    pointHoverBackgroundColor: "#fff",
                    pointHoverBorderColor: _get_data.datasets[i].color,
                    pointBorderWidth: 2,
                    pointHoverRadius: 3,
                    pointHoverBorderWidth: 2,
                    pointRadius: 3,
                    pointHitRadius: 3,
                    data: _get_data.datasets[i].data,
                });
            } 
            var chart = new Chart(selectCanvas, {
                type: 'line',
                data: {
                    labels: _get_data.labels,
                    datasets: chart_data,
                },
                options: {
                    plugins: {
                        legend: {
                            display: (_get_data.legend) ? _get_data.legend : false,
                            rtl: NioApp.State.isRTL,
                            labels: {
                                boxWidth:30,
                                padding:20,
                                color: '#6783b8',
                            }
                        },
                        tooltip: {
                            enabled: true,
                            rtl: NioApp.State.isRTL,
                            callbacks: {
                                label: function (context) {
                                    return `${context.parsed.y} ${_get_data.dataUnit}`;
                                },
                            },
                            backgroundColor: '#eff6ff',
                            titleFont:{
                                size: 13,
                            },
                            titleColor: '#6783b8',
                            titleMarginBottom: 6,
                            bodyColor: '#9eaecf',
                            bodyFont:{
                                size: 12
                            },
                            bodySpacing:4,
                            padding: 10,
                            footerMarginTop: 0,
                            displayColors: false
                        },
                    },
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            display: true,
                            stacked: (_get_data.stacked) ? _get_data.stacked : false,
                            position : NioApp.State.isRTL ? "right" : "left",
                            ticks: {
                                beginAtZero:true,
                                font:{
                                    size:11,
                                },
                                color:'#9eaecf',
                                padding:10,
                                callback: function(value, index, values) {
                                    return '$ ' + value;
                                },
                                min:100,
                                stepSize:3000
                            },
                            grid: { 
                                color: NioApp.hexRGB("#526484",.2),
                                tickLength:0,
                                zeroLineColor: NioApp.hexRGB("#526484",.2),
                                drawTicks:false,
                            },
                            
                        },
                        x: {
                            display: true,
                            stacked: (_get_data.stacked) ? _get_data.stacked : false,
                            ticks: {
                                font:{
                                    size:9,
                                },
                                color:'#9eaecf',
                                source: 'auto',
                                padding:10,
                            },
                            reverse: NioApp.State.isRTL,
                            grid: {
                                color: "transparent",
                                tickLength: 0,
                                zeroLineColor: 'transparent',
                                drawTicks:false,
                            },
                        }
                    }
                }
            });
        })
    }

    // init chart
    NioApp.coms.docReady.push(function(){ lineSalesOverview();  });

    var supportStatus = {
        labels : ["Bitcoin", "Ethereum", "NioCoin", "Feature Request", "Bug Fix"],
        stacked:true,
        datasets : [{
            label : "Solved",
            color : ["#f98c45", "#6baafe", "#8feac5", "#6b79c8", "#79f1dc"],
            data: [66, 74, 92, 142, 189]
        },{
            label : "Open",
            color : [NioApp.hexRGB('#f98c45',.4),NioApp.hexRGB('#6baafe',.4),NioApp.hexRGB('#8feac5',.4),NioApp.hexRGB('#6b79c8',.4),NioApp.hexRGB('#79f1dc',.4)],
            data: [66, 74, 92, 32, 26]
        },{
            label : "Pending",
            color : [NioApp.hexRGB('#f98c45',.2),NioApp.hexRGB('#6baafe',.2),NioApp.hexRGB('#8feac5',.2),NioApp.hexRGB('#6b79c8',.2),NioApp.hexRGB('#79f1dc',.2)],
            data: [66, 74, 92, 21, 9]
        }]
    };

    function supportStatusChart(selector, set_data){
        var $selector = (selector) ? $(selector) : $('.support-status-chart');
        $selector.each(function(){
            var $self = $(this), _self_id = $self.attr('id'), _get_data = (typeof set_data === 'undefined') ? eval(_self_id) : set_data,
            _d_legend = (typeof _get_data.legend === 'undefined') ? false : _get_data.legend;

            var selectCanvas = document.getElementById(_self_id).getContext("2d");
            var chart_data = [];
            for (var i = 0; i < _get_data.datasets.length; i++) {
                chart_data.push({
                    label: _get_data.datasets[i].label,
                    data: _get_data.datasets[i].data,
                    // Styles
                    backgroundColor: _get_data.datasets[i].color,
                    borderWidth:2,
                    borderColor: 'transparent',
                    hoverBorderColor : 'transparent',
                    borderSkipped : 'bottom',
                    barThickness:'8',
                    categoryPercentage: 0.5,
                    barPercentage: 1.0
                });
            } 
            var chart = new Chart(selectCanvas, {
                type: 'bar',
                data: {
                    labels: _get_data.labels,
                    datasets: chart_data,
                },
                options: {
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: (_get_data.legend) ? _get_data.legend : false,
                            rtl: NioApp.State.isRTL,
                            labels: {
                                boxWidth:30,
                                padding:20,
                                color: '#6783b8',
                            }
                        },
                        tooltip: {
                            enabled: true,
                            rtl: NioApp.State.isRTL,
                            callbacks: {
                                label: function (context) {
                                    return `${context.parsed.y} ${_get_data.dataUnit}`;
                                },
                            },
                            backgroundColor: '#eff6ff',
                            titleFont:{
                                size: 13,
                            },
                            titleColor: '#6783b8',
                            titleMarginBottom: 6,
                            bodyColor: '#9eaecf',
                            bodyFont:{
                                size: 12
                            },
                            bodySpacing:4,
                            padding: 10,
                            footerMarginTop: 0,
                            displayColors: false
                        },
                    },
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            display: true,
                            stacked: (_get_data.stacked) ? _get_data.stacked : false,
                            position : NioApp.State.isRTL ? "right" : "left",
                            ticks: {
                                beginAtZero:true,
                                padding:16,
                                color: "#8094ae"
                            },
                            grid: { 
                                color: "transparent",
                                tickLength:0,
                                zeroLineColor: 'transparent',
                                drawTicks:false,
                            },
                            
                        },
                        x: {
                            display: false,
                            stacked: (_get_data.stacked) ? _get_data.stacked : false,
                            ticks: {
                                font:{
                                    size:9,
                                },
                                color:'#9eaecf',
                                source: 'auto',
                                padding:0,
                            },
                            reverse: NioApp.State.isRTL,
                            grid: {
                                color: "transparent",
                                tickLength: 0,
                                zeroLineColor: 'transparent',
                                drawTicks:false,
                            },
                        }
                    }
                }
            });
        })
    }
    // init chart
    NioApp.coms.docReady.push(function(){ supportStatusChart(); });


})(NioApp, jQuery);