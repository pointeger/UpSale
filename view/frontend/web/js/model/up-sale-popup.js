define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Customer/js/customer-data'
], function ($, modal, customerData) {
    'use strict';

    return {
        modalWindow: null,
        upSaleSku: null,

        /**
         * Create popUp window for provided element
         *
         * @param {HTMLElement} element
         * @param upSaleSku
         */
        createPopUp: function (element, upSaleSku) {
            var options = {
                'type': 'popup',
                'modalClass': 'popup-upsale',
                'responsive': true,
                'innerScroll': true,
                'buttons': []
            };

            this.modalWindow = element;
            this.upSaleSku = upSaleSku;
            modal(options, $(this.modalWindow));
        },

        /** Show login popup window */
        showModal: function () {
            if (this.modalWindow) {
                $(this.modalWindow).modal('openModal').trigger('contentUpdated');
                return true;
            }
            return false;
        },

        /**
         * check if the upsale product already in cart
         * @returns {boolean}
         */
        isAllow: function () {
            if (this.upSaleSku) {
                var cart = customerData.get('cart')();
                var found = cart.items.some(item => item.product_sku === this.upSaleSku);

                if (found) {
                    return false;
                }
            }
            return true;
        },

        closeModal: function () {
            var cart = customerData.get('cart')();
            cart.up_sale_popup_shown = true;
            customerData.set("cart", cart);
            $(this.modalWindow).modal('closeModal');
        }
    };
});
