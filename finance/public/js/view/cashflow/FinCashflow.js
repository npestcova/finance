FinCashflow = {
    options: {
        url: null,
        incomeContainer: null,
        expenseContainer: null,
        totalsContainer: null,
        title: 'Cash Flow',
        period: null
    },

    init: function(options) {
        $.extend(this.options, options);
        this.refreshMonthlyData(null);
        this.refreshTotals();
    },

    refreshMonthlyData: function (period) {
        if (period) {
            this.options.period = period;
        }
        this.loadData(this.options.incomeContainer, {type: 'income'}, 'Income');
        this.loadData(this.options.expenseContainer, {type: 'expense'}, 'Expenses');
    },

    loadData: function(container, params, title) {
        var self = this;

        $.ajax({
            url: self.options.url,
            method: 'GET',
            data: $.extend(self.getFormData(), params),
            dataType: 'json'
        }).done(function(data) {
            self.setData(data.rows, container, title);
        })
            .fail(function() {
                alert( "error" );
            });
    },

    getFormData: function () {
        return {
            period: this.options.period,
            excludeTransfers: 1
        };
    },

    setData: function(rows, container, title) {
        var mainList = '';
        var excludeList = '';

        for (i = 0; i < rows.length; i++) {
            if (rows[i].excludeFromCashFlow) {
                excludeList += this.getRowHtml(rows[i], 'text-secondary');
            } else {
                mainList += this.getRowHtml(rows[i], 'text-dark');
            }
        }

        var html = '';

        html += '<div class="card-header bg-warning text-white">' + title + '</div>';
        html += '<div class="card-body p-2">';
        html += '<ul class="list-group">' + mainList + '</ul>';
        html += '</div>';

        if (excludeList) {
            html += '<div class="card-header bg-light text-secondary">Exclude From Cash Flow:</div>';
            html += '<div class="card-body p-2">';
            html += '<ul class="list-group">' + excludeList + '</ul>';
            html += '</div>';

        }

        container.html(html);
    },
    getRowHtml: function (row, aClass) {
        var balance = parseFloat(row.amount);
        var balanceClass = balance > 0 ? 'badge-primary' : 'badge-info';
        var link = '/transaction?' +
            'date_from=' + row.startDate +
            '&date_to=' + row.endDate +
            '&category_id=' + row.categoryId;

        return '<li class="list-group-item d-flex justify-content-between align-items-center p-1 pl-2 pr-2">' +
            '<a href="' + link + '" class="' + aClass + '">' + row.categoryName + '</a>' +
            '<span class="badge ' + balanceClass + ' badge-pill">$' + balance.toFixed(2) + '</span>' +
            '</li>';
    },

    selectMonth: function(button, month) {
        var year = this.options.period.substring(0, 4);
        var newPeriod = year + '-' + month;
        var $button = $(button);
        $button.parent().find('.period-month').removeClass('btn-primary').addClass('btn-secondary');
        $button.removeClass('btn-secondary').addClass('btn-primary');
        console.log(newPeriod);
        this.refreshMonthlyData(newPeriod);
    },

    refreshTotals: function () {

    }
};
