FinLogin = {
    options: {
        url: null,
        form: null,
        submitBtn: null,
        redirect_url: ''
    },

    init: function(options) {
        $.extend(this.options, options);
        this.bindEvents();
    },

    bindEvents: function () {
        if (!this.options.submitBtn) {
            alert('Submit button not found');
        }
        
        var self = this;
        this.options.submitBtn.on('click', function() {
            self.submitForm();
        });
    },
    
    submitForm: function () {
        var data = this.getFormData();
        var self = this;

        $.ajax({
            url: self.options.url,
            method: 'POST',
            data: data,
            dataType: 'json'
        }).done(function(data) {
            if (data.message) {
                self.showErrorMessage(data.message);
            }
            if (data.result=1 && data.redirect) {
                self.redirect(data.redirect);
            }
        })
            .fail(function() {
                alert( "error" );
            });
    },

    getFormData: function() {
        return this.options.form.serialize();
    },

    showErrorMessage: function (message) {
        var $alert = this.options.form.find('.alert');
        $alert.html(message);
        $alert.show();
    },

    redirect: function (url) {
        window.location.href=url;
    }

};