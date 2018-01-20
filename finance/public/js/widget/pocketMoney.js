PocketMoney = {
    options: {
        url: null,
        container: null,
        title: 'Pocket Money'
    },

    init: function(options) {
        $.extend(this.options, options);
        this.refresh();
    },

    refresh: function() {
        var self = this;

        $.ajax({
            url: self.options.url,
            method: 'GET',
            data: {},
            dataType: 'json'
        }).done(function(data) {
            self.setData(data.rows);
        })
        .fail(function() {
            alert( "error" );
        });
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
        var balanceClass = balance > 0 ? 'badge-primary' : 'badge-warning';
        return '<li class="list-group-item d-flex justify-content-between align-items-center">' +
            row.categoryName +
            '<span class="badge ' + balanceClass + ' badge-pill">$' + balance.toFixed(2) + '</span>' +
            '</li>';
    }
};