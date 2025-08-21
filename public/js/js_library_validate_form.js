// Mẫu gọi hàm khởi tạo

/*
// Validate the form #form_1
        Validator({
            form: '#form_1',
            formGroupSelector: '.form-group',
            errorSelector: '.form_message',
            rules: [
                Validator.isRequired('#shipping_name'),

                Validator.isRequired('#shipping_country'),

                Validator.isRequired('#shipping_street'),

                Validator.maxLength('#shipping_street', 35),

                Validator.maxLength('#shipping_address1', 35),

                Validator.maxLength('#shipping_address2', 35),

                Validator.isRequired('#shipping_province'),

                Validator.isRequired('#shipping_city'),

                Validator.isRequired('#shipping_zip'),

                Validator.isRequired('#package_height'),

                Validator.isRequired('#package_length'),

                Validator.isRequired('#package_width'),

                Validator.isRequired('#package_weight'),
            ],
        });
*/

// Hàm khởi tạo Validator
function Validator(options) {
    let selectorRules = {};

    // Hàm thực hiện validate
    function validate(inputElement, rule) {
        // Lấy phần tử hiển thị message lỗi
        let errorElement = inputElement
            .closest(options.formGroupSelector)
            .querySelector(options.errorSelector);

        let errorMessage;

        // Lấy ra các rules của selector
        let rules = selectorRules[rule.selector];

        // Lặp qua từng rule để check, nếu có lỗi thì dừng vòng lặp
        for (let i = 0; i < rules.length; i++) {
            switch (inputElement.type) {
                case "checkbox":
                case "radio":
                    errorMessage = rules[i](
                        formElement.querySelector(rule.selector + ":checked")
                    );
                    break;
                default:
                    errorMessage = rules[i](inputElement.value);
            }

            if (errorMessage) {
                break;
            }
        }

        if (errorMessage) {
            // Nếu có lỗi thì hiển thị lỗi
            errorElement.innerText = errorMessage;
            inputElement
                .closest(options.formGroupSelector)
                .classList.add("invalid");
        } else {
            // Nếu không có lỗi thì xóa thông báo
            errorElement.innerText = "";
            inputElement
                .closest(options.formGroupSelector)
                .classList.remove("invalid");
        }

        return !errorMessage; // Có lỗi trả về false, không có lỗi trả về true (không lỗi thì errorMessage = undefined)
    }

    // Lấy phần tử form element
    let formElement = document.querySelector(options.form);

    if (formElement) {
        // Khi submit form
        formElement.onsubmit = function (e) {
            e.preventDefault();

            let isFormValid = true;

            // Lặp qua các rules và thực thi validate
            options.rules.forEach(function (rule) {
                // Lấy phần tử input element
                let inputElement = formElement.querySelector(rule.selector);
                let isValid = validate(inputElement, rule);

                if (!isValid) {
                    isFormValid = false;
                }
            });

            if (isFormValid) {
                // Nếu validate không có lỗi
                if (typeof options.onSubmit === "function") {
                    // Trường hợp submit form bằng js
                    let enableInputs = formElement.querySelectorAll("[name]");
                    let dataInput = Array.from(enableInputs).reduce(function (
                        result,
                        input
                    ) {
                        switch (input.type) {
                            case "checkbox":
                                if (!input.matches(":checked")) {
                                    result[input.name] = "";
                                    return result;
                                }

                                if (!Array.isArray(result[input.name])) {
                                    result[input.name] = [];
                                }

                                result[input.name].push(input.value);
                                break;
                            case "radio":
                                result[input.name] = formElement.querySelector(
                                    'input[name="' + input.name + '"]:checked'
                                ).value;
                                break;
                            case "file":
                                result[input.name] = input.files;
                                break;
                            default:
                                result[input.name] = input.value;
                        }
                        return result;
                    },
                    {});

                    options.onSubmit(dataInput);
                } else {
                    // trường hợp submit form bằng hành vi mặc định của form html
                    formElement.submit();
                }
            }
        };

        // Lặp qua các rules và thực thi validate
        options.rules.forEach(function (rule) {
            // Lưu lại các rules cho mỗi input
            if (Array.isArray(selectorRules[rule.selector])) {
                selectorRules[rule.selector].push(rule.test);
            } else {
                selectorRules[rule.selector] = [rule.test];
            }

            // Lấy phần tử input element
            let inputElements = formElement.querySelectorAll(rule.selector);
            Array.from(inputElements).forEach(function (inputElement) {
                let errorElement = inputElement
                    .closest(options.formGroupSelector)
                    .querySelector(options.errorSelector);

                // Mỗi khi blur ra khỏi input element thì thực thi validate giá trị nhập vào ô input đó
                inputElement.onblur = function () {
                    validate(inputElement, rule);
                };

                // Khi người dùng gõ vào ô input thì xóa thông báo lỗi đi
                inputElement.oninput = function () {
                    errorElement.innerText = "";
                    inputElement
                        .closest(options.formGroupSelector)
                        .classList.remove("invalid");
                };
            });
        });
    }
}

// Định nghĩa các rules
/** Nguyên tắc chung các rules:
 * 1. Khi có lỗi => trả error message
 * 2. Khi hợp lệ => không trả gì cả (undefined)
 */

// Rule required
/**
 * @param selector: id của ô input
 * @param message: custom message
 */
Validator.isRequired = function (selector, message = null) {
    return {
        selector,
        test: function (value) {
            console.log(value);
            return value ? undefined : message || "Vui lòng nhập trường này!";
        },
    };
};

// Rule check email
/**
 * @param selector: id của ô input
 * @param message: custom message
 */
Validator.isEmail = function (selector, message = null) {
    return {
        selector,
        test: function (value) {
            let regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

            return regex.test(value)
                ? undefined
                : message || "Vui lòng nhập đúng định dạng email!";
        },
    };
};

// Rule kiểm tra độ dài tối thiểu
/**
 * @param selector: id của ô input
 * @param minLength: độ dài tối thiểu mong muốn
 * @param message: custom message
 */
Validator.minLength = function (selector, minLength, message = null) {
    return {
        selector,
        test: function (value) {
            return value.length >= minLength
                ? undefined
                : message || `Vui lòng nhập tối thiểu ${minLength} ký tự!`;
        },
    };
};

// Rule kiểm tra độ dài tối đa
/**
 * @param selector: id của ô input
 * @param maxLength: độ dài tối đa mong muốn
 * @param message: custom message
 */
Validator.maxLength = function (selector, maxLength, message = null) {
    return {
        selector,
        test: function (value) {
            return value.length <= maxLength
                ? undefined
                : message || `Vui lòng nhập tối đa ${maxLength} ký tự!`;
        },
    };
};

// Rule kiểm tra giá trị 2 ô input có giống nhau không (ví dụ password và password confirm)
/**
 * @param selector: id của ô input confirm
 * @param getConfirmValue: function callback, hàm này sẽ trả về của ô input đang mong muốn confirm
 * @param message: custom message
 */
Validator.isConfirmed = function (selector, getConfirmValue, message = null) {
    return {
        selector,
        test: function (value) {
            return value === getConfirmValue()
                ? undefined
                : message || "Giá trị nhập vào chưa chính xác!";
        },
    };
};
