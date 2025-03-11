document.addEventListener("DOMContentLoaded", function () {
    let gatewayOptions = document.querySelectorAll('input[name="selectedGateway"]');
    let finishButton = document.getElementById("finishWizardCustom");

    //  ابتدا دکمه‌ی Finish را غیرفعال کن
    finishButton.disabled = true;

    //  فعال‌سازی دکمه‌ی Finish بعد از انتخاب Gateway
    gatewayOptions.forEach(function (option) {
        option.addEventListener("change", function () {
            finishButton.disabled = false;
        });
    });

    //  وقتی روی Finish کلیک شد، ویزارد بسته شده و به صفحه‌ی تنظیمات Gateway هدایت شود
    finishButton.addEventListener("click", function () {
        let selectedGateway = document.querySelector('input[name="selectedGateway"]:checked').value;
        localStorage.setItem("selectedGateway", selectedGateway); // ذخیره در Local Storage

        // مسیر هدایت به تنظیمات Gateway
        let gatewayTabs = {
            "firebase": "firebase",
            "telegram": "telegram",
            "whatsapp": "whatsapp"
        };

        if (gatewayTabs[selectedGateway]) {
            console.log("url", window.location.href.split("?")[0] + "?page=idehweb-lwp&skip_wizard=1&tab=" + gatewayTabs[selectedGateway]+'#lwp-tab-gateway-settings');

            // window.location.href = window.location.href.split("?")[0] + "?page=idehweb-lwp&skip_wizard=1&tab=" + gatewayTabs[selectedGateway]+'#lwp-tab-gateway-settings';

            window.location.assign(
                window.location.href.split("?")[0] + "?page=idehweb-lwp&skip_wizard=1&tab=" + gatewayTabs[selectedGateway] + "#lwp-tab-gateway-settings"
            );
        }
    });

    let savedGateway = localStorage.getItem("selectedGateway");
    if (savedGateway) {
        let selectedGatewayInput = document.querySelector(`input[name="selectedGateway"][value="${savedGateway}"]`);
        if (selectedGatewayInput) {
            selectedGatewayInput.checked = true;
            finishButton.disabled = false;
        }
    }










    function hideInfo() {
        document.getElementById("wizardInfo").classList.add("hidden");
    }

    document.getElementById("nextToPage2").addEventListener("click", function () {
        document.getElementById("wizardPage1").style.display = "none";
        document.getElementById("wizardPage2").style.display = "block";
        hideInfo();
    });

    document.querySelectorAll('input[name="option_select"]').forEach(function (radio) {
        radio.addEventListener("change", function () {
            document.getElementById("nextToPage3").disabled = false;
        });
    });

    document.getElementById("nextToPage3").addEventListener("click", function () {
        let selectedOption = document.querySelector('input[name="option_select"]:checked').value;
        document.getElementById("wizardPage2").style.display = "none";

        if (selectedOption === "international") {
            document.getElementById("wizardPage3International").style.display = "block";
        } else {
            document.getElementById("wizardPage3Custom").style.display = "block";
        }
    });

    let selectedCountries = [];
    document.getElementById("countrySelectIntl").addEventListener("change", function () {
        let container = document.getElementById("selectedCountriesContainer");
        let selectedOptions = Array.from(this.selectedOptions);

        selectedOptions.forEach(option => {
            if (!selectedCountries.includes(option.value)) {
                selectedCountries.push(option.value);
                let div = document.createElement("div");
                div.classList.add("selected-country");
                div.innerHTML = `${option.text} <button class="remove-country">×</button>`;

                div.querySelector(".remove-country").addEventListener("click", function () {
                    option.selected = false;
                    selectedCountries = selectedCountries.filter(c => c !== option.value);
                    div.remove();

                    if (container.children.length === 0) {
                        container.style.display = "none";
                    }
                });

                container.appendChild(div);
            }
        });

        if (selectedCountries.length > 0) {
            container.style.display = "block";
        } else {
            container.style.display = "none";
        }
    });

    document.getElementById("backToPage1").addEventListener("click", function () {
        document.getElementById("wizardPage2").style.display = "none";
        document.getElementById("wizardPage1").style.display = "block";
        document.getElementById("wizardInfo").classList.remove("hidden");
    });

    document.getElementById("backToPage2FromIntl").addEventListener("click", function () {
        document.getElementById("wizardPage3International").style.display = "none";
        document.getElementById("wizardPage2").style.display = "block";
    });

    document.getElementById("backToPage2FromCustom").addEventListener("click", function () {
        document.getElementById("wizardPage3Custom").style.display = "none";
        document.getElementById("wizardPage2").style.display = "block";
    });

    function closeWizard() {
        window.location.href = window.location.href + "&skip_wizard=1";
    }

    document.getElementById("closeWizard").addEventListener("click", closeWizard);
    document.getElementById("installManually").addEventListener("click", closeWizard);
});
function filterCountries(inputId, selectId) {
    document.getElementById(inputId).addEventListener("input", function () {
        let searchValue = this.value.toLowerCase();
        let options = document.getElementById(selectId).options;
        for (let option of options) {
            option.style.display = option.text.toLowerCase().includes(searchValue) ? "block" : "none";
        }
    });
}

// search box for International and Custom
filterCountries("searchIntl", "countrySelectIntl");
// filterCountries("searchCustom", "countrySelectCustom");