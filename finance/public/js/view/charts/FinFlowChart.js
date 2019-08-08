FinFlowChart = {
    options: {
        url: null,
        form: null,
        canvas: null,
    },
    chartColors: {
        red: 'rgb(255, 99, 132)',
        orange: 'rgb(255, 159, 64)',
        yellow: 'rgb(255, 205, 86)',
        green: 'rgb(75, 192, 192)',
        blue: 'rgb(54, 162, 235)',
        purple: 'rgb(153, 102, 255)',
        grey: 'rgb(201, 203, 207)'
    },
    labels: {},
    dataSets: {},
    chartOptions: {
        responsive: true,
        title: {
            display: true,
            text: 'Cash Flow Chart'
        },
        tooltips: {
            mode: 'index',
            intersect: false,
        },
        hover: {
            mode: 'nearest',
            intersect: true
        },
        scales: {
            xAxes: [{
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: 'Month'
                }
            }],
            yAxes: [{
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: 'Value'
                }
            }]
        }
    },

    init: function (options) {
        $.extend(this.options, options);
        this.bindEvents();
    },

    bindEvents: function () {
        // on month or category change - reload flow
    },

    reloadFlow: function () {

    }
};