FinTransactions = {
    options: {
        url: '',
        table: null,
        dataTableObj: null,
        form: null,
        columns: [
            { title: "Date" },
            { title: "Account" },
            { title: "Category" },
            { title: "Description" },
            { title: "Amount" }
        ]
    },

    init: function(params) {
        this.options.table = params.table;
        this.options.url = params.url;
        this.options.form = params.form;

        this.initDataTable();
        this.reload();
    },

    initDataTable: function() {
        this.options.table.DataTable( {
            data: [],
            columns: this.options.columns
        } );
        this.dataTableObj = this.options.table.dataTable();
    },

    getFormData: function() {
        var data = {
            date_from: this.options.form.find('#date_from').val(),
            date_to: this.options.form.find('#date_to').val(),
            account_id: this.options.form.find('#account_id').val(),
            category_id: this.options.form.find('#category_id').val(),
            description: this.options.form.find('#description').val()
        };

        return data;
    },

    reload: function() {
        var self = this;

        self.clear();

        var data = self.getFormData();
        console.log(data);

        $.ajax({
            url: self.options.url,
            method: 'POST',
            data: data,
            dataType: 'json'
        }).done(function(data) {
            self.setTransactions(data.transactions);
        })
            .fail(function() {
                alert( "error" );
            });
    },

    setTransactions: function (transactions) {
        for (i in transactions) {
            row = transactions[i];
            this.dataTableObj.fnAddData([
                row.date,
                row.accountName,
                row.categoryName,
                row.description,
                row.amount
            ]);
        }
    },

    clear: function() {
        this.dataTableObj.fnClearTable();
    }
};
