define([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, customerData) {
    'use strict';

    return function (config, element) {
        let autoDismissTime = (config.autoDismissSeconds || 5) * 1000;
        let hideTimeout = null;
        let $popup = $(element);
        let cartData = customerData.get('cart');
        let prevCount = cartData() && cartData().summary_count ? cartData().summary_count : 0;

        function showNotification(item) {
            if (!item) {
                return;
            }

            $popup.find('#cart-popup-name').text(item.product_name);
            $popup.find('#cart-popup-img').attr('src', item.product_image ? item.product_image.src : '');
            $popup.find('#cart-popup-price').html(item.product_price);

            $popup.stop(true, true).fadeIn(250);

            if (hideTimeout) {
                clearTimeout(hideTimeout);
            }

            hideTimeout = setTimeout(function () {
                $popup.fadeOut(300);
            }, autoDismissTime);
        }

        cartData.subscribe(function (updatedCart) {
            if (updatedCart && updatedCart.items && updatedCart.items.length > 0) {
                let currentCount = updatedCart.summary_count || 0;

                if (currentCount > prevCount) {
                    showNotification(updatedCart.items[0]);
                }
                prevCount = currentCount;
            }
        });

        $popup.find('#close-cart-popup').on('click', function () {
            $popup.fadeOut(200);
            if (hideTimeout) {
                clearTimeout(hideTimeout);
            }
        });
    };
});
