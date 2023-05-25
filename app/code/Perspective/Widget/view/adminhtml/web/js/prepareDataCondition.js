define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Ui/js/form/element/abstract',
    'mage/translate'
], function (
    $,
    ko,
    Component,
    Abstract
) {
    'use strict';
    return {
        prepareCondition: function (cuttedName, name, requestUrl) {
            jQuery(document).on('click', "#rule_button-save-submit-" + cuttedName + "", function () {
                $('.loading-mask').show();
                var serArr = JSON.stringify($("#edit_form_modal").serializeArray());
                $.ajax({
                    url: requestUrl,
                    data: {form_key: window.FORM_KEY, serializedArray: serArr},
                    type: 'post',
                    success: function (response) {
                        console.log(response.data);
                        $("input[name='" + name + "']")[0].value = response.data;
                        $("label[data-param-name='" + name + "']").text($.mage.__('Save and Continue to proceed correctly'));
                        $('.loading-mask').hide();
                    },
                    error: function (response) {
                        console.log(response.message);
                        alert(response.message);
                        $('.loading-mask').hide();
                    }
                });
            });
        }
    };
});
