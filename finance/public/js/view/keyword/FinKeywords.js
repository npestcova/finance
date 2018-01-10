FinKeywords = {
    options: {
        url: '',
        table: null,
        dataTableObj: null,
        form: null,
        columns: [
            { title: "Id" },
            { title: "Keyword" },
            { title: "Category" }
        ]
    },

    init: function(params) {
        this.options.table = params.table;
        this.options.url = params.url;
     //   this.options.form = params.form;

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
        };

        return data;
    },

    reload: function() {
        var self = this;

        self.clear();

        var data = self.getFormData();

        $.ajax({
            url: self.options.url,
            method: 'POST',
            data: data,
            dataType: 'json'
        }).done(function(data) {
            self.setData(data.keywords);
        })
            .fail(function() {
                alert( "error" );
            });
    },

    setData: function (records) {
        for (i in records) {
            row = records[i];
            this.dataTableObj.fnAddData([
                row.id,
                row.keyword,
                row.categoryName
            ]);
        }
    },

    clear: function() {
        this.dataTableObj.fnClearTable();
    }
};
