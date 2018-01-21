wCashFlow = {
    options: {
        url: null,
        container: null,
        title: 'Cash Flow',
        startDate: null,
        endDate: null
    },

    init: function(options) {
        $.extend(this.options, options);
        this.initDates();
        this.refresh();
    },

    initDates: function() {
        this.options.startDate = new Date();
        this.options.startDate.setDate(1);

        this.options.endDate = new Date();

        var monthName = this.getMonthName(this.options.startDate);
        this.options.title = 'Cash Flow (' + monthName + ')';
    },

    refresh: function() {
        var self = this;

        $.ajax({
            url: self.options.url,
            method: 'GET',
            data: self.getFormData(),
            dataType: 'json'
        }).done(function(data) {
            self.setData(data.rows);
        })
        .fail(function() {
            alert( "error" );
        });
    },
    getFormData: function() {
        return {
            start_date: this.formatDate(this.options.startDate),
            end_date: this.formatDate(this.options.endDate)
        };
    },

    formatDate: function(dateObj) {
        return dateObj.toISOString().slice(0, 10);
    },

    getMonthName: function(dateObj) {
        var monthNames = [
            "January", "February", "March",
            "April", "May", "June", "July",
            "August", "September", "October",
            "November", "December"
        ];

        return monthNames[dateObj.getMonth()];
    },

    setData: function(rows) {
        var list = '';
        for (i = 0; i < rows.length; i++) {
            list += this.getRowHtml(rows[i]);
        }

        var html = '<div class="card-header bg-secondary text-white">' + this.options.title + '</div>';
        html += '<div class="card-body">';
        html += '<ul class="list-group">' + list + '</ul>';
        html += '</div>';

        this.options.container.html(html);
    },
    getRowHtml: function (row) {
        var balance = parseFloat(row.amount);
        var balanceClass = balance > 0 ? 'badge-primary' : 'badge-info';
        return '<li class="list-group-item d-flex justify-content-between align-items-center">' +
            row.categoryName +
            '<span class="badge ' + balanceClass + ' badge-pill">$' + balance.toFixed(2) + '</span>' +
            '</li>';
    }
};