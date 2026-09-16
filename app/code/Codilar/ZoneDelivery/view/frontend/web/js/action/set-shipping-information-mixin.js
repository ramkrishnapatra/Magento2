/**
 * Codilar ZoneDelivery
 *
 * Mixin for Magento_Checkout/js/action/set-shipping-information.
 *
 * WHY A FULL OVERRIDE INSTEAD OF A THIN pre/post WRAP:
 * The core action builds its REST payload as a function-local variable
 * ("payload") that is never exposed on "this" or returned before the
 * network call is made, so there is no seam to hook into with
 * mage/utils/wrapper for the purpose of mutating that specific object.
 * The documented, supported approach for this exact scenario (adding
 * extension_attributes to the shipping-information payload) is for the
 * mixin to reproduce the target function's contract while injecting
 * the additional data - which is what this file does. The mixin still
 * fully honours the original function's public contract: it accepts
 * the same messageContainer argument and returns a jQuery promise.
 *
 * This mixin also enforces requirement #3: when ZoneDelivery is the
 * active shipping method, the checkout is not allowed to proceed
 * (the underlying REST call is never made, so the shipping.js
 * ".done()" handler that advances to the payment step never fires)
 * unless both a delivery date and a delivery slot have been chosen.
 */
define([
    'jquery',
    'mage/storage',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/model/resource-url-manager',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Ui/js/model/messageList',
    'Magento_Checkout/js/model/full-screen-loader',
    'Codilar_ZoneDelivery/js/view/delivery-slot',
    'mage/translate'
], function (
    $,
    storage,
    quote,
    paymentService,
    methodConverter,
    resourceUrlManager,
    errorProcessor,
    globalMessageList,
    fullScreenLoader,
    deliverySlot,
    $t
) {
    'use strict';

    var CARRIER_CODE = 'zonedelivery';

    return function (originalAction) {

        /**
         * @param {Object} messageContainer
         * @return {jQuery.Deferred}
         */
        return function (messageContainer) {
            var shippingMethod = quote.shippingMethod(),
                isZoneDelivery = !!shippingMethod && shippingMethod.carrier_code === CARRIER_CODE,
                payload;

            if (isZoneDelivery && !deliverySlot.validate()) {
                globalMessageList.addErrorMessage({
                    message: deliverySlot.errorMessage() || $t('Please select a delivery date and slot to continue.')
                });

                return $.Deferred().reject({
                    responseText: deliverySlot.errorMessage()
                }).promise();
            }

            if (!isZoneDelivery) {
                return originalAction(messageContainer);
            }

            fullScreenLoader.startLoader();

            payload = {
                addressInformation: {
                    shipping_address: quote.shippingAddress(),
                    billing_address: quote.billingAddress(),
                    shipping_method_code: quote.shippingMethod().method_code,
                    shipping_carrier_code: quote.shippingMethod().carrier_code,
                    extension_attributes: {
                        delivery_slot_id: parseInt(deliverySlot.selectedSlotId(), 10),
                        delivery_date: deliverySlot.selectedDate()
                    }
                }
            };

            return storage.post(
                resourceUrlManager.getUrlForSetShippingInformation(quote),
                JSON.stringify(payload)
            ).done(function (response) {
                quote.setTotals(response.totals);
                paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                quote.guestEmail = quote.guestEmail || payload.addressInformation.shipping_address.email;
            }).fail(function (response) {
                errorProcessor.process(response, messageContainer);
            }).always(function () {
                fullScreenLoader.stopLoader();
            });
        };
    };
});
