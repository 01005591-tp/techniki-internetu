const DatePickerComponent = (function () {
    function DatePickerComponent(datePickerInputId, valueInputId) {
        logger.trace("Instantiating DatePickerComponent with params " +
            "(datePickerInputId=" + datePickerInputId +
            ", valueInputId=" + valueInputId + ")");
        this._datePicker = document.getElementById(datePickerInputId);
        this._valueInput = document.getElementById(valueInputId);
        let self = this;

        $(this._datePicker).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "yy-mm-dd",
            onSelect: function (dateText, inst) {
                self.onDatePickerSelect(dateText, inst);
            }
        });
        $(this._valueInput).on("change", this.onValueInputChange);

        this.onValueInputChange = function (event) {
            self.setDatePickerDate(self._valueInput.value);
        }
        this.onDatePickerSelect = function (dateText, datepicker) {
            $(self._valueInput).attr("value", dateText);
        }
        self.setDatePickerDate($(self._valueInput).attr("value"));
    }

    DatePickerComponent.prototype.setDatePickerDate = function (date) {
        logger.trace("DatePickerComponent.setDatePickerDate() date=" + date);
        if (date !== undefined && date) {
            $(this._datePicker).datepicker("setDate", date);
        } else {
            $(this._datePicker).datepicker("setDate", "");
        }
    }

    return DatePickerComponent;
}());