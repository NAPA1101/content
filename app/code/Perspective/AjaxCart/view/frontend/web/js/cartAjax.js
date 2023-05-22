define([
    'jquery',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/cart/cache',
    'Magento_Checkout/js/model/cart/totals-processor/default',
    'Magento_Checkout/js/model/quote'
], function ($, getTotalsAction, customerData, cartCache, totalsProcessor, quote) {

    'use strict';

    function ajaxCart(url, data, method = 'post')
    {
        $.ajax({
            url: url,
            data: data,
            method: method,
            showLoader: true,
            success: function (res) {
                let parsedResponse = $.parseHTML(res);
                let result = $(parsedResponse).find("#form-validate");
                let sections = ['cart'];
                $("#form-validate").replaceWith(result);
                customerData.reload(sections, true);
                let deferred = $.Deferred();
                getTotalsAction([], deferred);
            },
            error: function (xhr, status, error) {
                let err = eval("(" + xhr.responseText + ")");
                console.log(err.Message);
            }
        });
    }

    $(document).ready(function () {
        $(document).on('change', 'input[name="qty"] , select', function () {
            let config = $(this).data('params');
            let data = {};
            if (config) {
                let prod = $('#productId' + config.product_id + ' select');
                for (let i = 0; i < prod.length; i++) {
                    data[prod.eq(i).attr('name')] =
                        prod.eq(i).val();
                }
                data['form_key'] = $('input[name="form_key"]').val();
                data['qty'] = $('#cart-' + config.item_id + '-qty').val();
                data['product'] = config.product_id;
                let url = '/checkout/cart/updateItemOptions/id/' + config.item_id + '/';
                ajaxCart(url, data);
            }
        });
        $(document).on("click", ".main-delete", function (event) {
            // event.preventDefault();
            //  event.stopPropagation();
            let data = $(this).prev().data("post");
            let param = {};
            param['id'] = data.data.id;
            param['form_key'] = $('input[name="form_key"]').val();
            console.log(param);
            ajaxCart(data.action, param);
            return false;
        });

        $(document).on('change', 'input[name=coupon_code]', function () {
            let form = $('#discount-coupon-form');
            $.ajax(
                {
                    type: "POST",
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function (response) {
                        console.log(response);
                        cartCache.clear('cartVersion');
                        totalsProcessor.estimateTotals(quote.shippingAddress());
                    }
                }
            );
        });
    });
});