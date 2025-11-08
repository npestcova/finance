jQuery.widget ("custom.FinTransactions", {
    options: {
        url: '',
		    saveUrl: '',
        updateOneUrl: '',
        table: null,
        dataTable: null,
        dataTableObj: null,
        filterForm: null,
        form: null,
        editMode: false, // track whether grid is currently in edit mode
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
        this._initInlineEditSaving(); // new extraction for inline edit save logic
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

    // New: initialize blur saving handler separately
    _initInlineEditSaving: function() {
        var self = this;
        if (this._inlineSaveBound) { return; }
        this._inlineSaveBound = true;
        this.options.table.on('blur', '.edit-date, .edit-description, .edit-amount', function() {
            var $input = $(this);
            var original = $input.data('original');
            var current = $input.val().trim();

            // Skip if no change
            if (original === current) { return; }

            var $row = $input.closest('tr');
            var rowId = $row.attr('data-row-id');

            // Validate row ID exists
            if (!rowId) {
                console.error('No row ID found for transaction');
                return;
            }

            var data = self._collectRowEditData($row);
            if (!data || !data.id) {
                console.error('Failed to collect row data');
                return;
            }

            // Validate and normalize amount field
            if ($input.hasClass('edit-amount')) {
                var numValue = parseFloat(data.amount);
                if (data.amount !== '' && !isNaN(numValue)) {
                    data.amount = numValue.toFixed(2);
                    $input.val(data.amount);
                } else if (data.amount !== '') {
                    // Invalid amount - revert to original and show error
                    $input.val(original);
                    self._markError($input);
                    alert('Invalid amount format. Please enter a valid number.');
                    return;
                }
            }

            self._markSaving($input);

            $.ajax({
                url: self.options.updateOneUrl,
                method: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify(data),
                timeout: 10000 // 10 second timeout
            }).done(function(resp) {
                if (resp && resp.success === true) {
                    // Update original value only for the changed input
                    $input.data('original', $input.val());
                    self._markSuccess($input);
                } else {
                    var message = (resp && resp.error) ? resp.error : 'Error saving value';
                    self._markError($input);
                    alert(message);
                }
            }).fail(function(xhr, status, error) {
                self._markError($input);
                var message = 'Network error saving value';
                if (status === 'timeout') {
                    message = 'Request timed out. Please try again.';
                } else if (xhr.status) {
                    message = 'Server error (' + xhr.status + '). Please try again.';
                }
                alert(message);
            });
        });
    },

    // Helper: collect row data into JSON for saving
    _collectRowEditData: function($row) {
        if (!$row || !$row.length) { return null; }

        var id = $row.attr('data-row-id');
        if (!id) { return null; }

        // Get values from inputs, fallback to cell text if input doesn't exist
        var dateInput = $row.find('.edit-date');
        var descInput = $row.find('.edit-description');
        var amountInput = $row.find('.edit-amount');

        var dateVal = dateInput.length ? dateInput.val().trim() : $row.find('td').eq(1).text().trim();
        var descVal = descInput.length ? descInput.val().trim() : $row.find('td').eq(4).text().trim();
        var amountVal = amountInput.length ? amountInput.val().trim() : $row.find('td').eq(5).text().trim();

        return {
            id: id,
            date: dateVal || '',
            description: descVal || '',
            amount: amountVal || ''
        };
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
        for (var i = 0; i < transactions.length; i++) {
            var row = transactions[i];
            this.options.dataTableObj.fnAddData([
                row.id,
                row.date,
                row.accountName,
                row.categoryName,
                row.description,
                row.amount
            ]);
            // Tag the last inserted DOM row with its transaction id for later lookup
            var $lastRow = this.options.table.find('tbody tr').last();
            $lastRow.attr('data-row-id', row.id);
        }
    },

    // Helper: escape HTML to prevent injection (e.g. description containing tags)
    _escapeHtml: function(value) {
        if (value === null || value === undefined) { return ''; }
        return $('<div/>').text(value).html();
    },

    // Helper: build input element markup
    _buildInput: function(className, value, type) {
        var raw = (value === null || value === undefined) ? '' : String(value).trim();

        // For number inputs, normalize the value
        if (type === 'number' && raw !== '') {
            var numVal = parseFloat(raw.replace(/[^\d.-]/g, ''));
            if (!isNaN(numVal)) {
                raw = numVal.toString();
            }
        }

        // Basic escaping for quotes inside attribute
        var escaped = raw.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        var inputType = type || 'text';
        var stepAttr = (type === 'number') ? ' step="0.01"' : '';
        return '<input name="' + className + '" type="' + inputType + '" class="' + className + '" value="' + escaped + '" data-original="' + escaped + '"' + stepAttr + '>';
    },

    switchToEditMode: function() {
        if (this.options.editMode) { return; } // already in edit mode
        this.options.editMode = true;

        var rows = this.options.dataTable.rows({ 'search': 'applied' }).nodes();
        var self = this;

        $(rows).each(function(index, row) {
            var $row = $(row);
            var cells = $row.find('td');

            // Date (index 1)
            var dateCell = $(cells[1]);
            if (!dateCell.find('input').length) {
                var dateValue = dateCell.text();
                // Use type=date if value looks like YYYY-MM-DD
                var dateType = /^\d{4}-\d{2}-\d{2}$/.test(dateValue) ? 'date' : 'text';
                dateCell.html(self._buildInput('edit-date', dateValue, dateType));
            }

            // Description (index 4)
            var descCell = $(cells[4]);
            if (!descCell.find('input').length) {
                var descValue = descCell.text();
                descCell.html(self._buildInput('edit-description', descValue, 'text'));
            }

            // Amount (index 5)
            var amountCell = $(cells[5]);
            if (!amountCell.find('input').length) {
                var amountValue = amountCell.text();
                amountCell.html(self._buildInput('edit-amount', amountValue, 'number'));
            }
        });
    },

    switchToViewMode: function() {
        if (!this.options.editMode) { return; }
        this.options.editMode = false;

        var rows = this.options.dataTable.rows({ 'search': 'applied' }).nodes();

        $(rows).each(function(index, row) {
            var $row = $(row);
            var cells = $row.find('td');

            var dateCell = $(cells[1]);
            var dateInput = dateCell.find('.edit-date');
            if (dateInput.length) {
                dateCell.text(dateInput.val());
            }

            var descCell = $(cells[4]);
            var descInput = descCell.find('.edit-description');
            if (descInput.length) {
                descCell.text(descInput.val());
            }

            var amountCell = $(cells[5]);
            var amountInput = amountCell.find('.edit-amount');
            if (amountInput.length) {
                amountCell.text(amountInput.val());
            }
        });
    },

	updateTotal: function(total) {
		$('#transaction_total').html(total);
	},	

    clear: function() {
        this.options.dataTableObj.fnClearTable();
    },

    _markSaving: function($input) {
        $input.removeClass('edit-success edit-error').addClass('edit-saving');
    },
    _markSuccess: function($input) {
        $input.removeClass('edit-error edit-saving').addClass('edit-success');
        // Optionally remove success highlight after a short delay
        setTimeout(function(){ $input.removeClass('edit-success'); }, 2000);
    },
    _markError: function($input) {
        $input.removeClass('edit-success edit-saving').addClass('edit-error');
    }
});
