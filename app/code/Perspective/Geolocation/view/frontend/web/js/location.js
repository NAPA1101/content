define([
    'jquery',
    'mage/url'
], function ($, urlBuilder) {

        function main(config) {
            var name = document.getElementById("show-city").getAttribute("name")
            var showCity = document.querySelector('div#show-city');
            var buttonYes = document.querySelectorAll('button#Yes')
            var arrButtonYes = Array.from(buttonYes);

            arrButtonYes.forEach(function (el) {
                el.addEventListener('click', function buttonYes(event) {
                    target = event.target;
                    var showPopup = document.querySelectorAll('div.ui-dialog.ui-widget.ui-widget-content.ui-corner-all.ui-front.mage-dropdown-dialog');
                    var arrShowPopup = Array.from(showPopup);
                    arrShowPopup.forEach(function (el) {
                        el.style.display = 'none'
                    });
                    showCity.innerHTML = name;
                })
            });

            var buttonNo = document.querySelectorAll('button#No')
            var arrButtonNo = Array.from(buttonNo);

            arrButtonNo.forEach(function (el) {
                el.addEventListener('click', function buttonNo(event) {
                    target = event.target;
                    var showPopupNo = document.querySelectorAll('div.popup-container-geolocation');
                    var arrShowPopupNo = Array.from(showPopupNo);
                    arrShowPopupNo.forEach(function (el) {
                        el.style.display = 'block'
                    });
                })
            });

            var buttonCancel = document.querySelectorAll('.cancel-button-geolocation')
            var arrButtonCancel = Array.from(buttonCancel);

            arrButtonCancel.forEach(function (el) {
                el.addEventListener('click', function buttonCancel(event) {
                    target = event.target;
                    var showPopupCancel = document.querySelectorAll('div.popup-container-geolocation');
                    var arrShowPopupCancel = Array.from(showPopupCancel);
                    arrShowPopupCancel.forEach(function (el) {
                        el.style.display = 'none'
                    });
                })
            });

            var buttonOk = document.querySelectorAll('button.ok-button-geolocation')
            var arrButtonOk = Array.from(buttonOk);

            arrButtonOk.forEach(function (el) {
                el.addEventListener('click', function buttonOk(event) {
                    target = event.target;
                    var inputElement = document.querySelectorAll('input.search-input-popup-geolocation');
                    var arrInp = Array.from(inputElement);
                    var inp = ''
                    arrInp.forEach(function (el) {
                        inp = el.value;
                        showCity.innerHTML = inp;
                    });

                    var showPopupOk = document.querySelectorAll('div.ui-dialog.ui-widget.ui-widget-content.ui-corner-all.ui-front.mage-dropdown-dialog');
                    var arrShowPopupOk = Array.from(showPopupOk);
                    arrShowPopupOk.forEach(function (el) {
                        el.style.display = 'none'
                    });
                })
            });
        }
    return main;
});
