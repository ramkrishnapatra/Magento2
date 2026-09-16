define([
    'ko',
    'jquery',
    'uiComponent',
    'moment',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/url-builder',
    'mage/storage',
    'mage/translate'
], function (ko, $, Component, moment, quote, urlBuilder, storage, $t) {
    'use strict';

    var CARRIER_CODE = 'zonedelivery',
        METHOD_CODE = 'standard';

    var selectedDate = ko.observable(''),
        selectedSlotId = ko.observable(''),
        availableSlots = ko.observableArray([]),
        isLoading = ko.observable(false),
        errorMessage = ko.observable(''),
        minDate = ko.observable(''),
        leadTimeNotice = ko.observable('');

    function isZoneDeliverySelected() {
        var method = quote.shippingMethod();

        return !!method &&
            method.carrier_code === CARRIER_CODE &&
            method.method_code === METHOD_CODE;
    }

    function resetSelection() {
        selectedDate('');
        selectedSlotId('');
        availableSlots([]);
        errorMessage('');
    }

    /**
     * BR-02: Enforce remote zone minimum lead time (Day + 2)
     */
    function updateLeadTimeRules(postcode) {
        var pin = postcode ? postcode.trim() : '';
        // Known remote pincodes designated under BRD-04
        var isRemote = (pin === '797001' || pin === '799001');

        var targetMoment = moment();

        if (isRemote) {
            targetMoment.add(2, 'days');
            leadTimeNotice($t('Remote postal codes require at least 2 business days lead time. Next-day delivery is not available.'));
        } else {
            targetMoment.add(1, 'days');
            leadTimeNotice('');
        }

        var minDateFormatted = targetMoment.format('YYYY-MM-DD');
        minDate(minDateFormatted);

        // Auto-correct selected date if it violates minDate
        if (selectedDate() && selectedDate() < minDateFormatted) {
            selectedDate(minDateFormatted);
            fetchAvailableSlots();
        }
    }

    function fetchAvailableSlots() {
        var address = quote.shippingAddress(),
            pincode = address && address.postcode ? address.postcode.trim() : '',
            date = selectedDate(),
            serviceUrl,
            queryParams;

        selectedSlotId('');
        availableSlots([]);
        errorMessage('');

        if (!pincode) {
            errorMessage($t('A valid shipping address with a pincode is required to fetch delivery slots.'));
            return;
        }

        if (!date) {
            return;
        }

        isLoading(true);

        queryParams = $.param({
            pincode: pincode,
            date: date
        });

        serviceUrl = urlBuilder.createUrl('/delivery/slots/available', {}) + '?' + queryParams;

        storage.get(serviceUrl, false)
            .done(function (response) {
                var slots = response || [];

                slots = slots.filter(function (slot) {
                    return !!slot.is_available;
                });

                availableSlots(slots);

                if (!slots.length) {
                    errorMessage($t('No delivery slots are available for the selected date. Please choose another date.'));
                }
            })
            .fail(function () {
                availableSlots([]);
                errorMessage($t('Unable to retrieve delivery slots at this time. Please try again.'));
            })
            .always(function () {
                isLoading(false);
            });
    }

    function validate() {
        if (!isZoneDeliverySelected()) {
            errorMessage('');
            return true;
        }

        if (!selectedDate()) {
            errorMessage($t('Please select a delivery date to continue.'));
            return false;
        }

        if (!selectedSlotId()) {
            errorMessage($t('Please select a delivery slot to continue.'));
            return false;
        }

        errorMessage('');
        return true;
    }

    var DeliverySlot = Component.extend({
        defaults: {
            template: 'Codilar_ZoneDelivery/delivery-slot'
        },

        selectedDate: selectedDate,
        selectedSlotId: selectedSlotId,
        availableSlots: availableSlots,
        isLoading: isLoading,
        errorMessage: errorMessage,
        minDate: minDate,
        leadTimeNotice: leadTimeNotice,

        initialize: function () {
            this._super();

            this.isSlotSelectorVisible = ko.observable(isZoneDeliverySelected());

            // Initialize min date based on current address
            var initAddress = quote.shippingAddress();
            updateLeadTimeRules(initAddress && initAddress.postcode ? initAddress.postcode : '');

            // Subscribe to address changes (BR-03)
            quote.shippingAddress.subscribe(function (address) {
                updateLeadTimeRules(address && address.postcode ? address.postcode : '');
            }, this);

            // Subscribe to shipping method changes
            quote.shippingMethod.subscribe(function (method) {
                var isVisible = !!method &&
                    method.carrier_code === CARRIER_CODE &&
                    method.method_code === METHOD_CODE;

                this.isSlotSelectorVisible(isVisible);

                if (!isVisible) {
                    resetSelection();
                } else {
                    var curAddress = quote.shippingAddress();
                    updateLeadTimeRules(curAddress && curAddress.postcode ? curAddress.postcode : '');
                }
            }, this);

            return this;
        },

        onDateChange: function () {
            fetchAvailableSlots();
            return true;
        },

        hasNoSlots: function () {
            return !this.isLoading() && !!this.selectedDate() && !this.availableSlots().length;
        },

        validate: function () {
            return validate();
        }
    });

    DeliverySlot.selectedDate = selectedDate;
    DeliverySlot.selectedSlotId = selectedSlotId;
    DeliverySlot.availableSlots = availableSlots;
    DeliverySlot.errorMessage = errorMessage;
    DeliverySlot.isZoneDeliverySelected = isZoneDeliverySelected;
    DeliverySlot.validate = validate;

    return DeliverySlot;
});
