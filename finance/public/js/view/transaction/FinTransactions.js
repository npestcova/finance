jQuery.widget ("custom.FinTransactions", {
    // Constants for better maintainability
    COLUMN_INDICES: {
        ID: 0,
        DATE: 1,
        ACCOUNT: 2,
        CATEGORY: 3,
        DESCRIPTION: 4,
        AMOUNT: 5
    },

    CSS_CLASSES: {
        EDIT_DATE: 'edit-date',
        EDIT_DESCRIPTION: 'edit-description',
        EDIT_AMOUNT: 'edit-amount',
        SPLIT_BUTTON: 'btn-split-transaction',
        SAVING: 'edit-saving',
        SUCCESS: 'edit-success',
        ERROR: 'edit-error'
    },

    AJAX_TIMEOUT: 10000,
    SUCCESS_HIGHLIGHT_DURATION: 2000,

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
        // Split transaction button handler
        this.options.table.on('click', '.btn-split-transaction', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $button = $(this);
            var $amountInput = $button.siblings('.edit-amount');
            var currentAmount = $amountInput.val() || '0';
            self._showSplitModal(currentAmount, $amountInput);
        });

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
            if ($input.hasClass(self.CSS_CLASSES.EDIT_AMOUNT)) {
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

            self._makeAjaxRequest(data,
                function(resp) {
                    // Success callback
                    $input.data('original', $input.val());
                    self._markSuccess($input);
                },
                function(message, isNetworkError) {
                    // Error callback
                    self._markError($input);
                    alert(message);
                }
            );
        });
    },

    // Helper: collect row data into JSON for saving
    _collectRowEditData: function($row) {
        if (!$row || !$row.length) { return null; }

        var id = $row.attr('data-row-id');
        if (!id) { return null; }

        // Get values from inputs, fallback to cell text if input doesn't exist
        var dateInput = $row.find('.' + this.CSS_CLASSES.EDIT_DATE);
        var descInput = $row.find('.' + this.CSS_CLASSES.EDIT_DESCRIPTION);
        var amountInput = $row.find('.' + this.CSS_CLASSES.EDIT_AMOUNT);

        var dateVal = dateInput.length ? dateInput.val().trim() : this._getCellValue($row, this.COLUMN_INDICES.DATE);
        var descVal = descInput.length ? descInput.val().trim() : this._getCellValue($row, this.COLUMN_INDICES.DESCRIPTION);
        var amountVal = amountInput.length ? amountInput.val().trim() : this._getCellValue($row, this.COLUMN_INDICES.AMOUNT);

        return {
            id: id,
            date: dateVal || '',
            description: descVal || '',
            amount: amountVal || '',
            accountId: $row.attr('data-account-id') || '',
            categoryId: $row.attr('data-category-id') || '',
            accountName: this._getCellValue($row, this.COLUMN_INDICES.ACCOUNT),
            categoryName: this._getCellValue($row, this.COLUMN_INDICES.CATEGORY)
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

            var $lastRow = this.options.table.find('tbody tr').last();
            this._setRowDataAttributes($lastRow, row.id, row.accountId, row.categoryId);
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
        var input = '<input name="' + className + '" type="' + inputType + '" class="' + className + '" value="' + escaped + '" data-original="' + escaped + '"' + stepAttr + '>';

        // Add split button for amount inputs
        if (className === this.CSS_CLASSES.EDIT_AMOUNT) {
            input += '<button type="button" class="' + this.CSS_CLASSES.SPLIT_BUTTON + '" title="Split Transaction">//' + '</button>';
        }

        return input;
    },

    switchToEditMode: function() {
        if (this.options.editMode) { return; }
        this.options.editMode = true;

        var rows = this.options.dataTable.rows({ 'search': 'applied' }).nodes();
        var self = this;

        $(rows).each(function(index, row) {
            self._convertCellToEditMode($(row), self.COLUMN_INDICES.DATE, self.CSS_CLASSES.EDIT_DATE, 'date');
            self._convertCellToEditMode($(row), self.COLUMN_INDICES.DESCRIPTION, self.CSS_CLASSES.EDIT_DESCRIPTION, 'text');
            self._convertCellToEditMode($(row), self.COLUMN_INDICES.AMOUNT, self.CSS_CLASSES.EDIT_AMOUNT, 'number');
        });
    },

    switchToViewMode: function() {
        if (!this.options.editMode) { return; }
        this.options.editMode = false;

        var rows = this.options.dataTable.rows({ 'search': 'applied' }).nodes();
        var self = this;

        $(rows).each(function(index, row) {
            self._convertCellToViewMode($(row), self.COLUMN_INDICES.DATE, self.CSS_CLASSES.EDIT_DATE);
            self._convertCellToViewMode($(row), self.COLUMN_INDICES.DESCRIPTION, self.CSS_CLASSES.EDIT_DESCRIPTION);
            self._convertCellToViewMode($(row), self.COLUMN_INDICES.AMOUNT, self.CSS_CLASSES.EDIT_AMOUNT);
        });
    },

    // Helper: Convert cell to edit mode
    _convertCellToEditMode: function($row, columnIndex, cssClass, inputType) {
        var cell = $row.find('td').eq(columnIndex);
        if (!cell.find('input').length) {
            var value = cell.text();
            // Use appropriate input type based on content
            var actualType = inputType;
            if (inputType === 'date' && !/^\d{4}-\d{2}-\d{2}$/.test(value)) {
                actualType = 'text';
            }
            cell.html(this._buildInput(cssClass, value, actualType));
        }
    },

    // Helper: Convert cell to view mode
    _convertCellToViewMode: function($row, columnIndex, cssClass) {
        var cell = $row.find('td').eq(columnIndex);
        var input = cell.find('.' + cssClass);
        if (input.length) {
            cell.text(input.val());
        }
    },

	updateTotal: function(total) {
		$('#transaction_total').html(total);
	},	

    clear: function() {
        this.options.dataTableObj.fnClearTable();
    },

    _markSaving: function($input) {
        $input.removeClass(this.CSS_CLASSES.SUCCESS + ' ' + this.CSS_CLASSES.ERROR).addClass(this.CSS_CLASSES.SAVING);
    },
    _markSuccess: function($input) {
        var self = this;
        $input.removeClass(this.CSS_CLASSES.ERROR + ' ' + this.CSS_CLASSES.SAVING).addClass(this.CSS_CLASSES.SUCCESS);
        // Remove success highlight after configured delay
        setTimeout(function(){
            $input.removeClass(self.CSS_CLASSES.SUCCESS);
        }, this.SUCCESS_HIGHLIGHT_DURATION);
    },
    _markError: function($input) {
        $input.removeClass(this.CSS_CLASSES.SUCCESS + ' ' + this.CSS_CLASSES.SAVING).addClass(this.CSS_CLASSES.ERROR);
    },

    // Helper: Create standardized AJAX request
    _makeAjaxRequest: function(data, successCallback, errorCallback) {
        var self = this;
        return $.ajax({
            url: self.options.updateOneUrl,
            method: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify(data),
            timeout: self.AJAX_TIMEOUT
        }).done(function(resp) {
            if (resp && resp.success === true) {
                if (successCallback) successCallback(resp);
            } else {
                var message = (resp && resp.error) ? resp.error : 'Operation failed';
                if (errorCallback) errorCallback(message, false);
            }
        }).fail(function(xhr, status, error) {
            var message = self._getAjaxErrorMessage(status, xhr.status);
            if (errorCallback) errorCallback(message, true);
        });
    },

    // Helper: Get standardized AJAX error message
    _getAjaxErrorMessage: function(status, httpStatus) {
        if (status === 'timeout') {
            return 'Request timed out. Please try again.';
        } else if (httpStatus) {
            return 'Server error (' + httpStatus + '). Please try again.';
        }
        return 'Network error. Please try again.';
    },

    // Helper: Get cell value by column index
    _getCellValue: function($row, columnIndex) {
        return $row.find('td').eq(columnIndex).text().trim();
    },

    // Helper: Set row data attributes
    _setRowDataAttributes: function($row, id, accountId, categoryId) {
        $row.attr({
            'data-row-id': id,
            'data-account-id': accountId || '',
            'data-category-id': categoryId || ''
        });
    },

    // Show split transaction modal
    _showSplitModal: function(currentAmount, $amountInput) {
        var self = this;

        // Create modal if it doesn't exist
        if (!$('#splitTransactionModal').length) {
            self._createSplitModal();
        }

        var originalAmount = parseFloat(currentAmount) || 0;

        // Set initial values
        $('#split_amount_1').val(currentAmount);
        $('#split_amount_2').val('0');

        // Store references
        $('#splitTransactionModal').data('original-input', $amountInput);
        $('#splitTransactionModal').data('original-amount', originalAmount);

        // Update total display
        self._updateSplitTotal();

        // Show modal
        $('#splitTransactionModal').modal('show');

        // Focus first input
        setTimeout(function() {
            $('#split_amount_1').focus().select();
        }, 300);
    },

    // Create split transaction modal
    _createSplitModal: function() {
        var modalHtml = '<div class="modal fade" id="splitTransactionModal" tabindex="-1" role="dialog">' +
            '<div class="modal-dialog" role="document">' +
            '<div class="modal-content">' +
            '<div class="modal-header">' +
            '<h5 class="modal-title">Split Transaction</h5>' +
            '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>' +
            '<div class="modal-body">' +
            '<div class="form-group">' +
            '<label for="split_amount_1">Amount 1:</label>' +
            '<input type="number" class="form-control" id="split_amount_1" step="0.01">' +
            '</div>' +
            '<div class="form-group">' +
            '<label for="split_amount_2">Amount 2:</label>' +
            '<input type="number" class="form-control" id="split_amount_2" step="0.01">' +
            '</div>' +
            '<div class="form-group">' +
            '<small class="text-muted">Total: <span id="split_total">0.00</span></small>' +
            '</div>' +
            '</div>' +
            '<div class="modal-footer">' +
            '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>' +
            '<button type="button" class="btn btn-primary" id="btn-apply-split">Apply Split</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';

        $('body').append(modalHtml);

        var self = this;

        // When amount 1 changes, calculate remaining balance for amount 2
        $('#split_amount_1').on('input', function() {
            self._updateRemainingBalance();
        });

        // Update total display when amount 2 changes
        $('#split_amount_2').on('input', function() {
            self._updateSplitTotal();
        });

        // Apply split button handler
        $('#btn-apply-split').on('click', function() {
            self._applySplit();
        });
    },

    // Update remaining balance in amount 2 when amount 1 changes
    _updateRemainingBalance: function() {
        var originalAmount = parseFloat($('#splitTransactionModal').data('original-amount')) || 0;
        var amount1 = parseFloat($('#split_amount_1').val()) || 0;
        var remaining = originalAmount - amount1;
        $('#split_amount_2').val(remaining.toFixed(2));
        this._updateSplitTotal();
    },

    // Update split total display
    _updateSplitTotal: function() {
        var amount1 = parseFloat($('#split_amount_1').val()) || 0;
        var amount2 = parseFloat($('#split_amount_2').val()) || 0;
        var total = amount1 + amount2;
        $('#split_total').text(total.toFixed(2));
    },

    // Apply the split - update existing row and create new row
    _applySplit: function() {
        var self = this;
        var $originalInput = $('#splitTransactionModal').data('original-input');
        var amount1 = parseFloat($('#split_amount_1').val()) || 0;
        var amount2 = parseFloat($('#split_amount_2').val()) || 0;

        if (!$originalInput || !$originalInput.length || amount2 === 0) {
            $('#splitTransactionModal').modal('hide');
            return;
        }

        var $originalRow = $originalInput.closest('tr');
        var originalData = self._collectRowEditData($originalRow);

        // Step 1: Update the existing row with amount1
        $originalInput.val(amount1.toFixed(2));
        self._markSaving($originalInput);

        // Update existing transaction
        var updateData = {
            id: originalData.id,
            date: originalData.date,
            description: originalData.description,
            amount: amount1.toFixed(2)
        };

        self._makeAjaxRequest(updateData,
            function(resp) {
                // Success callback
                $originalInput.data('original', amount1.toFixed(2));
                self._markSuccess($originalInput);

                // Step 2: Create new transaction with amount2
                self._createSplitTransaction(originalData, amount2, $originalRow);
            },
            function(message, isNetworkError) {
                // Error callback
                self._markError($originalInput);
                alert(message);
            }
        );

        $('#splitTransactionModal').modal('hide');
    },

    // Create new split transaction
    _createSplitTransaction: function(originalData, amount2, $originalRow) {
        var self = this;

        // Prepare new transaction data for creation (no ID since it's new)
        var newTransactionData = {
            date: originalData.date,
            description: originalData.description,
            amount: amount2.toFixed(2),
            accountId: originalData.accountId,
            categoryId: originalData.categoryId
        };

        self._makeAjaxRequest(newTransactionData,
            function(resp) {
                // Success callback - create new row
                if (resp.id) {
                    self._addNewSplitRow(resp, originalData, amount2, $originalRow);
                    console.log('Split transaction created successfully with ID:', resp.id);
                } else {
                    alert('Error creating split transaction - no ID returned');
                }
            },
            function(message, isNetworkError) {
                // Error callback
                alert(message);
            }
        );
    },

    // Helper: Add new split row to table
    _addNewSplitRow: function(resp, originalData, amount2, $originalRow) {
        var self = this;

        // Create new row data array
        var newRowData = [
            resp.id,
            originalData.date,
            originalData.accountName,
            originalData.categoryName,
            originalData.description,
            amount2.toFixed(2)
        ];

        // Add row to DataTable
        var newRow = self.options.dataTable.row.add(newRowData);
        var $newRowNode = $(newRow.node());

        // Set data attributes using helper method
        self._setRowDataAttributes($newRowNode, resp.id, originalData.accountId, originalData.categoryId);

        // Redraw and position the new row
        self.options.dataTable.draw(false);
        self._positionSplitRow(resp.id, originalData.id);
    },

    // Helper: Position split row after original row
    _positionSplitRow: function(newRowId, originalRowId) {
        var $allRows = this.options.table.find('tbody tr');
        var $newRow = $allRows.filter('[data-row-id="' + newRowId + '"]');
        var $originalRow = $allRows.filter('[data-row-id="' + originalRowId + '"]');

        if ($newRow.length && $originalRow.length) {
            $newRow.detach().insertAfter($originalRow);
        }
    }
});
