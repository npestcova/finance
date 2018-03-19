jQuery.widget ("custom.FinTransactions", {
    options: {
        url: '',
		saveUrl: '',
        table: null,
        dataTable: null,
        dataTableObj: null,
        filterForm: null,
        form: null,
        columns: [
            { 
				title: '<input type="checkbox" class="editor-active" id="select_all_transactions" value="1"/>',
				render: function ( data, type, row ) {
                    if ( type === 'display' ) {
                        return '<input type="checkbox" class="editor-active" name="id[' + data + ']" value="1">';
                    }
                    return data;
                },
				searchable: false,
				orderable: false
			},
            { title: "Date" },
            { title: "Account" },
            { title: "Category" },
            { title: "Description" },
            { title: "Amount" }
        ]
    },

    _create: function(options) {
        // this.options.table = params.table;
        // this.options.url = params.url;
        // this.options.saveUrl = params.saveUrl;
        // this.options.filterForm = params.filterForm;
        // this.options.form = params.form;

        this.initDataTable();
		this.bindEvents();
        this.reload();
    },

    initDataTable: function() {
        this.options.dataTable = this.options.table.DataTable( {
            data: [],
            columns: this.options.columns
        } );
        this.options.dataTableObj = this.options.table.dataTable();
    },

	bindEvents: function() {
		var self = this;
		
		$('#select_all_transactions').on('click', function() {
		  
		  var rows = self.options.dataTable.rows({ 'search': 'applied' }).nodes();		  
		  $('input[type="checkbox"]', rows).prop('checked', this.checked);
	   });
	   
	}, 
	
    getFilterFormData: function() {
        var data = {
            date_from: this.options.filterForm.find('#date_from').val(),
            date_to: this.options.filterForm.find('#date_to').val(),
            account_id: this.options.filterForm.find('#account_id').val(),
            category_id: this.options.filterForm.find('#category_id').val(),
            description: this.options.filterForm.find('#description').val()
        };

        return data;
    },

    reload: function() {
        var self = this;

        self.clear();

        var data = self.getFilterFormData();

        $.ajax({
            url: self.options.url,
            method: 'POST',
            data: data,
            dataType: 'json'
        }).done(function(data) {
            self.setTransactions(data.transactions);
			self.updateTotal(data.total);
        })
            .fail(function() {
                alert( "error" );
            });
    },
	
	applyChanges: function() {		
        var self = this;
		
		var filterData = $.param(self.getFilterFormData());
		var formData = this.options.form.serialize();		

        self.clear();
		
		$.ajax({
            url: self.options.saveUrl,
            method: 'POST',
            data: filterData + '&' + formData,
            dataType: 'json'
        }).done(function(data) {
            self.setTransactions(data.transactions);
			self.updateTotal(data.total);
        })
            .fail(function() {
                alert( "error" );
            });
	},

    setTransactions: function (transactions) {
        for (i in transactions) {
            row = transactions[i];
            this.options.dataTableObj.fnAddData([
				row.id,
                row.date,
                row.accountName,
                row.categoryName,
                row.description,
                row.amount
            ]);
        }
    },
	
	updateTotal: function(total) {
		$('#transaction_total').html(total);
	},	

    clear: function() {
        this.options.dataTableObj.fnClearTable();
    }
});
