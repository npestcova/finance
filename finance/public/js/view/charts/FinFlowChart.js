var FinFlowChart = {};

(function($) {
    FinFlowChart = {
        options: {
            url: null,
            form: null,
            canvasId: null
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
        labels: [],
        dataSets: [],
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
        config: null,
        chart: null,

        init: function (options) {
            $.extend(this.options, options);
            this.initChart();
            this.bindEvents();
        },

        initChart: function() {
            this.initConfig();
            var ctx = document.getElementById(this.options.canvasId).getContext('2d');
            this.chart = new Chart(ctx, this.config);
        },

        initConfig: function() {
            this.config = {
                type: 'line',
                data: {
                    labels: this.labels,
                    datasets: this.dataSets
                },
                options: this.chartOptions
            };
        },

        bindEvents: function () {
            var self = this;
            this.options.form.find('select').on('change', function() {
                self.reloadFlow();
            });
        },

        reloadFlow: function () {
            var self = this;

            $.ajax({
                url: self.options.url,
                method: 'GET',
                data: self.getFormData(),
                dataType: 'json'
            }).done(function(data) {
                self.setData(data);
                self.updateChart();

            })
                .fail(function() {
                    alert( "error" );
                });
        },

        getFormData: function () {
            return this.options.form.serialize();
        },

        setData: function(data) {
            this.setLabels(data.periods);
            this.setDatasets(data.categories);
        },

        setLabels: function(labels) {
            this.labels = [];
            var self = this;
            $.each(labels, function(ym, title) {
                self.labels.push(title);
            });
        },

        setDatasets: function(dataSets) {
            this.dataSets = [
                {
                    label: 'My First dataset',
                    backgroundColor: this.chartColors.red,
                    borderColor: this.chartColors.red,
                    data: [
                        100,
                        230.24,
                        54.56,
                        -234,
                        0,
                        6,
                        7
                    ],
                    fill: false,
                }, {
                    label: 'My Second dataset',
                    fill: false,
                    backgroundColor: this.chartColors.blue,
                    borderColor: this.chartColors.blue,
                    data: [
                        7,
                        6,
                        5,
                        4,
                        3,
                        2,
                        1
                    ],
                }
            ]
        },

        updateChart: function () {
            this.initConfig();
            this.chart.data = this.config.data;
            this.chart.update();
        }


    };
})(jQuery);