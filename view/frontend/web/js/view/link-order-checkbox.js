// File: view/frontend/web/js/view/link-order-checkbox.js
define([
    'jquery',
    'uiComponent',
    'ko',
    'Magento_Customer/js/model/customer',
    'mage/cookies'
], function ($, Component, ko, customer) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Teknio_LinkGuestOrder/link-order-checkbox',
            isLinkOrderChecked: ko.observable(false), // Track checkbox state
            isEnabledLinkOrder: ko.observable(window.checkoutConfig.isEnabledLinkOrder ?? false)
        },

        initObservable: function () {
            this._super().observe(['isLinkOrderChecked', 'isEnabledLinkOrder']);
            
            this.isEnabledLinkOrder(window.checkoutConfig.isEnabledLinkOrder);
            console.log(window.checkoutConfig.isEnabledLinkOrder);
            // Initialize the checkbox state from the cookie
            const cookieValue = $.mage.cookies.get('link_order_value'); // Get cookie value

            // Check if cookieValue is 'true', otherwise default to false
            this.isLinkOrderChecked(cookieValue === 'true' ? true : false); // Track checkbox state

            // Update the cookie whenever the checkbox state changes
            this.isLinkOrderChecked.subscribe(function (newValue) {
                if (newValue === false) {
                    // Remove the cookie if the checkbox is unchecked
                    // Check if the cookie exists before clearing it
                    if ($.mage.cookies.get('link_order_value')) {
                        // Remove the cookie if it exists
                        $.mage.cookies.clear('link_order_value');
                    }
                } else {
                    // Set the cookie with 1-hour expiration if the checkbox is checked
                    const now = new Date();
                    const expirationTime = now.getTime() + 3600 * 1000;
                    now.setTime(expirationTime);
            
                    // Set the cookie with the new expiration date
                    $.mage.cookies.set('link_order_value', newValue, { expires: now });
                }
            });

            return this;
        },

        isCustomerLoggedIn: function () {
            return customer.isLoggedIn();
        }
    });
});
