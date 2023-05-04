define([
        'jquery',
        'ko',
        'uiComponent',
        'Pointeger_UpSale/js/model/up-sale-popup'
    ],
    function ($, ko, Component, upSalePopup) {
        'use strict';

        return Component.extend({
            productId: ko.observable(''),
            productName: ko.observable(''),
            image: ko.observable(''),
            price: ko.observable(''),
            submitUrl: ko.observable(''),
            upSaleSku: ko.observable(''),

            modalWindow: null,
            defaults: {
                template: 'Pointeger_UpSale/up-sale-popup'
            },
            initialize: function (config) {
                this.productName(config.productName);
                this.productId(config.productId);
                this.image(config.image);
                this.price(config.price);
                this.submitUrl(config.submitUrl);
                this.upSaleSku(config.upSaleSku);

                this._super();
            },
            setModalElement: function (element) {
                this.modalWindow = element;
                upSalePopup.createPopUp(element, this.upSaleSku);
            },

            continueToCheckout: function () {
                upSalePopup.closeModal();
                $("[data-role='proceed-to-checkout']").trigger('click');
            }
        });
    });
