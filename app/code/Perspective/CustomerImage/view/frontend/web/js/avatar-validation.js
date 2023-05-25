require([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'
], function ($) {
    function validateSize(fileInput, size) {
        var fileObj, oSize;
        if (typeof ActiveXObject == "function") { // IE
            fileObj = (new
            ActiveXObject("Scripting.FileSystemObject")).getFile(fileInput.value);
        } else {
            fileObj = fileInput.files[0];
        }
        oSize = fileObj.size;
        if (oSize > size * 1024 * 1024) {
            return false
        }
        return true;
    }

    var inp = document.querySelector("#customer_image");
    $('#customer_image').change(function () {
        $file_type = this.value.split('.').pop();
        if ($file_type != "jpeg" && $file_type != "jpg" && $file_type != "png") {
            alert("Допускаются файлы только jpeg, jpg, png");
            inp.value = ""
        }
        if (!validateSize(this, 1)) {
            alert("Файл должен быть не более 1 мегабайта");
            inp.value = ""
        }
    });
    $('#customer_image').on('change',function(){
        var elem = this;
        var _URL = window.URL;
        var file, img;
        if ((file = elem.files[0])) {
            img = new Image();
            img.onload = function () {
                elem.dataset['imageWidth'] = this.width;
                elem.dataset['imageHeight'] = this.height;
            };
            img.src = _URL.createObjectURL(file);
        }
    });
    $.validator.addMethod("data-rule-customerImage", function (value, elem, attrValue) {
        var width = parseInt(elem.dataset['width']);
        var height = parseInt(elem.dataset['height']);

        if (width < 520 && height < 290) {
            return false;
        }
    });
});
