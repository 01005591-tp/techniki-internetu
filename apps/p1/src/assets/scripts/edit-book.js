const DatePickerComponent = (function () {
    function DatePickerComponent(datePickerInputId, valueInputId) {
        logger.trace("Instantiating DatePickerComponent with params " +
            "(datePickerInputId=" + datePickerInputId +
            ", valueInputId=" + valueInputId + ")");
        this._datePicker = document.getElementById(datePickerInputId);
        this._valueInput = document.getElementById(valueInputId);
        const self = this;

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

const ImagePreviewComponent = (function () {
    function ImagePreviewComponent(inputId, imageId) {
        logger.trace("Instantiating ImagePreviewComponent with params " +
            "(inputId=" + inputId +
            ", imageId=" + imageId + ")");
        this._input = document.getElementById(inputId);
        this._image = document.getElementById(imageId);
        const self = this;

        $(this._input).on("blur", event => {
            self.onInputChange(event);
        });

        this.onInputChange = function (event) {
            const newValue = this._input.value;
            console.log(event);
            logger.trace("ImagePreviewComponent.onInputChange() " + newValue);
            self._image.src = newValue;
        }
    }

    return ImagePreviewComponent;
}());