class LogLevel {
    static TRACE = 4;
    static DEBUG = 3;
    static INFO = 2;
    static WARN = 1;
    static ERROR = 0;
    static NONE = -1;
}

const Logger = (function () {
    function Logger(level) {
        let self = this;
        self._level = level !== undefined && level ? level : LogLevel.INFO;
    }

    Logger.prototype.trace = function (message) {
        if (this._level >= LogLevel.TRACE) {
            console.log('[TRACE]: ' + message);
        }
    }

    Logger.prototype.debug = function (message) {
        if (this._level >= LogLevel.DEBUG) {
            console.log('[DEBUG]: ' + message);
        }
    }

    Logger.prototype.info = function (message) {
        if (this._level >= LogLevel.INFO) {
            console.log('[INFO ]: ' + message);
        }
    }

    Logger.prototype.warn = function (message) {
        if (this._level >= LogLevel.WARN) {
            console.log('[WARN ]: ' + message);
        }
    }

    Logger.prototype.error = function (message) {
        if (this._level >= LogLevel.ERROR) {
            console.log('[ERROR]: ' + message);
        }
    }

    Logger.prototype.setLogLevel = function (level) {
        let self = this;
        self._level = level !== undefined && level ? level : self._level;
    }

    Logger.prototype.getLogLevel = function () {
        return this._level;
    }

    return Logger;
}());
const logger = new Logger(LogLevel.INFO);

const InputUtils = (function () {
    function InputUtils() {
    }

    InputUtils.prototype.clearInput = function (id) {
        let element = document.getElementById(id);
        element.value = '';
        element.removeAttribute('value');
    };
    InputUtils.prototype.clearInputs = function (inputs) {
        if (inputs !== undefined && inputs) {
            inputs.forEach(it => inputUtils.clearInput(it));
        }
    };
    return InputUtils;
}());

const MultipleSelectComponent = (function () {
    function MultipleSelectComponent(displayInputId, dropdownId, valueHolderId, optionsHolderId) {
        logger.debug('Instantiating MultipleSelectComponent with params (displayInputId=' + displayInputId
            + ', dropdownId=' + dropdownId
            + ', valueHolderId=' + valueHolderId + '' +
            ', optionsHolderId = ' + optionsHolderId + ')')
        this._displayInputId = document.getElementById(displayInputId);
        this._dropdown = document.getElementById(dropdownId);
        this._valueHolder = document.getElementById(valueHolderId);
        this._optionsHolder = document.getElementById(optionsHolderId);
        let multipleSelectComponent = this;

        this.findSelectedOptions = function () {
            let selectedInputs = [];
            $(multipleSelectComponent._dropdown).find('input.form-check-input:checked')
                .each(function (index) {
                    let element = this;
                    let elementId = element.id;
                    let elementValue = element.value;
                    let elementLabel = multipleSelectComponent.findCorrespondingLabel(elementId);
                    let elementLabelText = elementLabel.text();
                    let elementDisplayName = elementLabelText !== undefined && elementLabelText
                        ? elementLabelText.trim()
                        : null;
                    selectedInputs.push({
                        "id": elementId,
                        "value": elementValue,
                        "displayName": elementDisplayName
                    });
                    logger.trace('findSelectedOptions() [' + index + ']: SelectedInput(id=' + elementId
                        + ', value=' + elementValue
                        + ', displayName=' + elementDisplayName + ')');
                });
            return selectedInputs;
        }

        this.findCorrespondingLabel = function (inputId) {
            return $(multipleSelectComponent._dropdown).find('label[for=' + inputId + ']').first();
        }

        this.storeOptionsToValueHolder = function (selectedOptions) {
            let selectedValuesString = selectedOptions.map(it => it.value).join(',');
            logger.trace('storeOptionsToValueHolder(): ' + selectedValuesString);
            multipleSelectComponent._valueHolder.value = selectedValuesString;
        }

        this.storeOptionsToOptionsHolder = function (selectedOptions) {
            let selectedIds = selectedOptions.map(it => it.id)
            logger.trace('storeOptionsToOptionsHolder(): ' + selectedIds);
            multipleSelectComponent._optionsHolder.value = selectedIds;
            logger.trace('storeOptionsToOptionsHolder(): ids = ' + multipleSelectComponent._optionsHolder.value);
        }

        this.displayOptions = function (selectedOptions) {
            let selectedValuesString = selectedOptions.map(it => it.displayName)
                .filter(it => it !== undefined && it)
                .map(it => it.trim())
                .join(',');
            logger.trace('displayOptions(): ' + selectedValuesString);
            multipleSelectComponent._displayInputId.value = selectedValuesString;
        }

        this.findLastSelectedOptions = function () {
            let selectedIdsString = multipleSelectComponent._optionsHolder.value;
            logger.trace('findLastSelectedOptions(): selectedIdsString=' + selectedIdsString);
            let selectedInputs = [];
            if (selectedIdsString === undefined || !selectedIdsString) {
                $(multipleSelectComponent._dropdown).find('input.form-check-input')
                    .each(function (index) {
                        this.checked = false;
                    });
                return selectedInputs;
            }
            let selectedIds = selectedIdsString.split(',');
            $(multipleSelectComponent._dropdown).find('input.form-check-input')
                .each(function (index) {
                    let element = this;
                    let elementId = element.id;
                    logger.trace('findLastSelectedOptions(): Element(id=' + elementId
                        + ', checked=' + element.checked
                        + ', selected=' + selectedIds.some(it => it === elementId) + ')')
                    if (selectedIds.some(it => it === elementId)) {
                        let elementValue = element.value;
                        let elementLabel = multipleSelectComponent.findCorrespondingLabel(elementId);
                        let elementDisplayName = elementLabel.text();
                        selectedInputs.push({
                            "id": elementId,
                            "value": elementValue,
                            "displayName": elementDisplayName
                        });
                        element.checked = true;
                    } else {
                        element.checked = false;
                    }
                });
            return selectedInputs;
        }

        this.onDropdownHide = function (event) {
            let selectedOptions = multipleSelectComponent.findSelectedOptions();
            multipleSelectComponent.storeOptionsToValueHolder(selectedOptions);
            multipleSelectComponent.storeOptionsToOptionsHolder(selectedOptions)
            multipleSelectComponent.displayOptions(selectedOptions);
        }

        this.onDropdownShow = function (event) {
            let selectedOptions = multipleSelectComponent.findLastSelectedOptions();
            multipleSelectComponent.storeOptionsToValueHolder(selectedOptions);
            multipleSelectComponent.displayOptions(selectedOptions);
        }

        this._dropdown.addEventListener('hide.bs.dropdown', event => {
            multipleSelectComponent.onDropdownHide(event);
        })
        this._dropdown.addEventListener('show.bs.dropdown', event => {
            multipleSelectComponent.onDropdownShow(event);
        })
    }


    return MultipleSelectComponent;
}());

const inputUtils = new InputUtils();