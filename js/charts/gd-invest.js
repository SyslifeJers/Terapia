!(function (NioApp, $) {
    "use strict";
    
    var refBarChart = {
            labels : ["01 Nov", "02 Nov", "03 Nov", "04 Nov", "05 Nov", "06 Nov", "07 Nov", "08 Nov", "09 Nov", "10 Nov", "11 Nov", "12 Nov", "13 Nov", "14 Nov", "15 Nov", "16 Nov", "17 Nov", "18 Nov", "19 Nov", "20 Nov", "21 Nov", "22 Nov", "23 Nov", "24 Nov", "25 Nov", "26 Nov", "27 Nov", "28 Nov", "29 Nov", "30 Nov"],
            dataUnit : 'People',
            datasets : [{
                label : "Join",
                color : "#6baafe",
                data: [110, 80, 125, 55, 95, 75, 90, 110, 80, 125, 55, 95, 75, 90, 110, 80, 125, 55, 95, 75, 90, 110, 80, 125, 55, 95, 75, 90, 75, 90]
            }]
        };

    var profitCM = {
            labels : ["01 Nov", "02 Nov", "03 Nov", "04 Nov", "05 Nov", "06 Nov", "07 Nov", "08 Nov", "09 Nov", "10 Nov", "11 Nov", "12 Nov", "13 Nov", "14 Nov", "15 Nov", "16 Nov", "17 Nov", "18 Nov", "19 Nov", "20 Nov", "21 Nov", "22 Nov", "23 Nov", "24 Nov", "25 Nov", "26 Nov", "27 Nov", "28 Nov", "29 Nov", "30 Nov"],
            dataUnit : 'USD',
            datasets : [{
                label : "Send",
                color : "#5d7ce0",
                data: [0, 80, 125, 55, 95, 75, 90, 110, 80, 125, 55, 95, 75, 90, 110, 80, 125, 55, 95, 75, 90, 110, 80, 125, 55, 95, 75, 90, 75, 0]
            }]
        };

    function referStats(elem, set_data){
        var $elem = (elem) ? $(elem) : $('.chart-refer-stats');
        $elem.each(function(){
            var $self = $(this), _self_id = $self.attr('id'), _get_data = (typeof set_data === 'undefined') ? eval(_self_id) : set_data;

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
                    categoryPercentage : .8
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
                            backgroundColor: '#fff',
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
                            ticks: {
                                beginAtZero: true
                            },
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
    NioApp.coms.docReady.push(function(){ referStats(); });


    function investProfit(elem, set_data){
        var $elem = (elem) ? $(elem) : $('.chart-profit');
        $elem.each(function(){
            var $self = $(this), _self_id = $self.attr('id'), _get_data = (typeof set_data === 'undefined') ? eval(_self_id) : set_data;
            var selectCanvas = document.getElementById(_self_id).getContext("2d");

            var chart_data = [];
            for (var i = 0; i < _get_data.datasets.length; i++) {
                chart_data.push({
                    label: _get_data.datasets[i].label,
                    tension:.4,
                    backgroundColor: NioApp.hexRGB(_get_data.datasets[i].color,.3),
                    fill: true,
                    borderWidth:2,
                    borderColor: _get_data.datasets[i].color,
                    pointBorderColor: 'transparent',
                    pointBackgroundColor: 'transparent',
                    pointHoverBackgroundColor: "#fff",
                    pointHoverBorderColor: _get_data.datasets[i].color,
                    pointBorderWidth: 2,
                    pointHoverRadius: 4,
                    pointHoverBorderWidth: 2,
                    pointRadius: 4,
                    pointHitRadius: 4,
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
                            backgroundColor: '#fff',
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
                            ticks: {
                                beginAtZero: true
                            }
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
    NioApp.coms.docReady.push(function(){ investProfit(); });

    //////////////// ADMIN PANEL /////////////
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


    var totalDeposit = {
        labels : ["01 Jan", "02 Jan", "03 Jan", "04 Jan", "05 Jan", "06 Jan", "07 Jan"],
        dataUnit : 'USD',
        stacked : true,
        datasets : [{
            label : "Active User",
            color : [NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2),NioApp.hexRGB("#733AEA", .2),"#733AEA"],
            data: [7200, 8200, 7800, 9500, 5500, 9200, 9690]
        }]
    };

    var totalWithdraw = {
        labels : ["01 Jan", "02 Jan", "03 Jan", "04 Jan", "05 Jan", "06 Jan", "07 Jan"],
        dataUnit : 'USD',
        stacked : true,
        datasets : [{
            label : "Active User",
            color : [NioApp.hexRGB("#816bff", .2),NioApp.hexRGB("#816bff", .2),NioApp.hexRGB("#816bff", .2),NioApp.hexRGB("#816bff", .2),NioApp.hexRGB("#816bff", .2),NioApp.hexRGB("#816bff", .2),"#816bff"],
            data: [7200, 8200, 7800, 9500, 5500, 9200, 9690]
        }]
    };

    var totalBalance = {
        labels : ["01 Jan", "02 Jan", "03 Jan", "04 Jan", "05 Jan", "06 Jan", "07 Jan"],
        dataUnit : 'USD',
        stacked : true,
        datasets : [{
            label : "Active User",
            color : [NioApp.hexRGB("#AB89F2", .2),NioApp.hexRGB("#AB89F2", .2),NioApp.hexRGB("#AB89F2", .2),NioApp.hexRGB("#AB89F2", .2),NioApp.hexRGB("#AB89F2", .2),NioApp.hexRGB("#AB89F2", .2),"#AB89F2"],
            data: [6000,8200, 7800, 9500, 5500, 9200, 9690]
        }]
    };

    function ivDataChart(selector, set_data){
        var $selector = (selector) ? $(selector) : $('.iv-data-chart');
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
                    barPercentage : .85,
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
    NioApp.coms.docReady.push(function(){ ivDataChart(); });

    var planPurchase = {
        labels : ["01 Jan", "02 Jan", "03 Jan", "04 Jan", "05 Jan", "06 Jan", "07 Jan", "01 Jan", "02 Jan", "03 Jan", "04 Jan", "05 Jan", "06 Jan", "07 Jan", "01 Jan", "02 Jan", "03 Jan", "04 Jan", "05 Jan", "06 Jan", "07 Jan"],
        dataUnit : 'USD',
        stacked : true,
        datasets : [{
            label : "Active User",
            color : NioApp.hexRGB("#733AEA", .3),
            colorHover : "#733AEA",
            data: [6000,8200, 7800, 9500, 5500, 9200, 9690, 6000,8200, 7800, 9500, 5500, 9200, 9690, 6000,8200, 7800, 9500, 5500, 9200, 9690]
        }]
    };

    function ivPlanPurchase(selector, set_data){
        var $selector = (selector) ? $(selector) : $('.iv-plan-purchase');
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
                    hoverBackgroundColor: _get_data.datasets[i].colorHover,
                    borderWidth:2,
                    borderColor: 'transparent',
                    hoverBorderColor : 'transparent',
                    borderSkipped : 'bottom',
                    barPercentage : .75,
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
    NioApp.coms.docReady.push(function(){ ivPlanPurchase(); });


    var userActivity = {
        labels : ["01 Nov", "02 Nov", "03 Nov", "04 Nov", "05 Nov", "06 Nov", "07 Nov", "08 Nov", "09 Nov", "10 Nov", "11 Nov", "12 Nov", "13 Nov", "14 Nov", "15 Nov", "16 Nov", "17 Nov", "18 Nov", "19 Nov", "20 Nov", "21 Nov"],
        dataUnit : 'Person',
        stacked : true,
        datasets : [{
            label : "Direct Join",
            color : "#6baafe",
            data: [110, 80, 125, 55, 95, 75, 90, 110, 80, 125, 55, 95, 75, 90, 110, 80, 125, 55, 95, 75, 90]
        },{
            label : "Referral Join",
            color : "#ccd4ff",
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



})(NioApp, jQuery);