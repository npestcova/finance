FinBudgetCharts = {
    options: {
        incomeCtx: null,
        incomeChart: null,
        incomeReceived: 0,
        incomeBudgeted: 0,
        expenseCtx: null,
        expenseChart: null,
        expenseSpent: 0,
        expenseBudgeted: 0
    },
    incomeConfig: {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [
                    0,
                    0
                ],
                backgroundColor: [
                    '#007bff',
                    '#efefef'
                ],
                label: 'Income'
            }],
            labels: [
                '',
                ''
            ]
        },
        options: {
            responsive: true,
            legend: {
                position: 'top'
            },
            title: {
                display: true,
                text: 'Income'
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    },
    expenseConfig: {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [
                    0,
                    0
                ],
                backgroundColor: [
                    '#ffc107',
                    '#efefef'
                ],
                label: 'Expenses'
            }],
            labels: [
                '',
                ''
            ]
        },
        options: {
            responsive: true,
            legend: {
                position: 'top'
            },
            title: {
                display: true,
                text: 'Expenses'
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    },

    init: function (options) {
        $.extend(this.options, options);
        this.updateIncome();
        this.updateExpenses();
    },

    reload: function(options) {
        $.extend(this.options, options);
        this.updateIncome();
        this.updateExpenses();
    },

    updateIncome: function() {
        var moneyLeft = this.options.incomeBudgeted - this.options.incomeReceived;
        var dataset = this.incomeConfig.data.datasets[0];
        dataset.data = [this.options.incomeReceived.toFixed(2), moneyLeft.toFixed(2)];
        this.incomeConfig.data.datasets = [dataset];
        this.incomeConfig.data.labels = [
            this.options.incomeReceived.toFixed(2) + ' Received',
            moneyLeft.toFixed(2) + ' Left'
        ];

        this.reloadIncomeChart();
    },

    reloadIncomeChart: function() {
        if (!this.options.incomeChart) {
            this.options.incomeChart = new Chart(this.options.incomeCtx, this.incomeConfig);
        } else {
            this.options.incomeChart.update();
        }
    },

    updateExpenses: function() {
        var moneyLeft = this.options.expenseBudgeted - this.options.expenseSpent;
        var dataset = this.expenseConfig.data.datasets[0];
        dataset.data = [this.options.expenseSpent.toFixed(2), moneyLeft.toFixed(2)];
        this.expenseConfig.data.datasets = [dataset];
        this.expenseConfig.data.labels = [
            this.options.expenseSpent.toFixed(2) + ' Spent',
            moneyLeft.toFixed(2) + ' Left'
        ];

        this.reloadExpenseChart();
    },

    reloadExpenseChart: function() {
        if (!this.options.expenseChart) {
            this.options.expenseChart = new Chart(this.options.expenseCtx, this.expenseConfig);
        } else {
            this.options.expenseChart.update();
        }
    }

};