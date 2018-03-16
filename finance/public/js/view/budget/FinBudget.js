FinBudget = {
    options: {
        chartsOptions: {
            incomeCtx: null,
            incomeReceived: 0,
            incomeBudgeted: 0,
            expenseCtx: null,
            expenseSpent: 0,
            expenseBudgeted: 0
        }
    },

    init: function(options) {
        $.extend(this.options, options);
        FinBudgetCharts.init(this.options.chartsOptions);

        this.options.chartsOptions.incomeReceived = 100;
        this.options.chartsOptions.incomeBudgeted = 1000;
        this.options.chartsOptions.expenseSpent = 300;
        this.options.chartsOptions.expenseBudgeted = 900;

        var self = this;
        setTimeout(function() {
            FinBudgetCharts.reload(self.options.chartsOptions);
        }, 3000);
    }
};