jQuery(document).ready(function ($) {
    var lwp_countries_gateways = [
        { "country": "ir", "gateways": ["farazsms", "melipayamak"] },
        { "country": "sa", "gateways": ["taqnyat"] },
        { "country": "in", "gateways": ["mshastra", "textlocal"] }
    ];

    $(window).on('load', function () {
        $("#lwp_idehweb_country_codes_guid").select2();
    });

    $(document).on('click', '#finishWizardIntl', function (e) {
        e.preventDefault();
        var selectedValues = $("#lwp_idehweb_country_codes_guid").val();
        console.log("Selected Countries:", selectedValues);

        var selectedGateways = [];
        selectedValues.forEach(function (selectedCountry) {
            lwp_countries_gateways.forEach(function (item) {
                if (item.country === selectedCountry) {
                    selectedGateways = selectedGateways.concat(item.gateways);
                }
            });
        });

        console.log("Selected Gateways:", selectedGateways,idehweb_lwp.ajaxurl, idehweb_lwp.nonce);

        $.ajax({
            type: "POST",
            dataType: "json",
            url: idehweb_lwp.ajaxurl,
            data: {
                action: "lwp_set_countries",
                nonce: idehweb_lwp.nonce,
                selected_countries: selectedValues,
                selected_gateways: selectedGateways
            },
            success: function (response) {
                console.log("Response from server:", response);
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
                // console.log("Response Text:", xhr.responseText);
            }
        });
    });
});
        //get countries
        //get list of country and gateways
        //return gateways

document.addEventListener("DOMContentLoaded", function () {
    let gatewayOptions = document.querySelectorAll('input[name="selectedGateway"]');
    let finishButton = document.getElementById("finishWizardCustom");

    // Initially disable the finish button
    finishButton.disabled = true;

    // Enable the finish button when a gateway is selected
    gatewayOptions.forEach(function (option) {
        option.addEventListener("change", function () {
            finishButton.disabled = false;
        });
    });

    // Handle finish button click event
    // finishButton.addEventListener("click", function () {
    //     let selectedGateway = document.querySelector('input[name="selectedGateway"]:checked').value;
    //     localStorage.setItem("selectedGateway", selectedGateway);
    //
    //     let gatewayTabs = {
    //         "firebase": "firebase",
    //         "telegram": "telegram",
    //         "whatsapp": "whatsapp"
    //     };
    //
    //     // Redirect to the appropriate settings page with selected gateway
    //     if (gatewayTabs[selectedGateway]) {
    //         let basePath = window.location.origin + window.location.pathname.split("/wp-admin")[0];
    //
    //         let newUrl = `${basePath}/wp-admin/admin.php?page=idehweb-lwp&skip_wizard=1&tab=lwp-tab-gateway-settings&selected_gateway=${gatewayTabs[selectedGateway]}#lwp-tab-gateway-settings`;
    //
    //         console.log("Navigating to:", newUrl);
    //         window.location.href = newUrl; // Use href to properly handle URL fragment navigation
    //     }
    // });

    // Restore previously selected gateway from localStorage
    let savedGateway = localStorage.getItem("selectedGateway");
    if (savedGateway) {
        let selectedGatewayInput = document.querySelector(`input[name="selectedGateway"][value="${savedGateway}"]`);
        if (selectedGatewayInput) {
            selectedGatewayInput.checked = true;
            finishButton.disabled = false;
        }
    }

    // Hide wizard information section
    function hideInfo() {
        document.getElementById("wizardInfo").classList.add("hidden");
    }

    // Navigation from Page 1 to Page 2
    document.getElementById("nextToPage2").addEventListener("click", function () {
        document.getElementById("wizardPage1").style.display = "none";
        document.getElementById("wizardPage2").style.display = "block";
        hideInfo();
    });

    // Enable Next button on Page 2 when an option is selected
    document.querySelectorAll('input[name="option_select"]').forEach(function (radio) {
        radio.addEventListener("change", function () {
            document.getElementById("nextToPage3").disabled = false;
        });
    });

    // Navigation from Page 2 to Page 3 based on selected option
    document.getElementById("nextToPage3").addEventListener("click", function () {
        let selectedOption = document.querySelector('input[name="option_select"]:checked').value;
        document.getElementById("wizardPage2").style.display = "none";

        if (selectedOption === "international") {
            document.getElementById("wizardPage3International").style.display = "block";
        } else {
            document.getElementById("wizardPage3Custom").style.display = "block";
        }
    });

    // Handle country selection for the "Custom" setup
    let selectedCountries = [];
    // document.getElementById("countrySelectIntl").addEventListener("change", function () {
    //     let container = document.getElementById("selectedCountriesContainer");
    //     let selectedOptions = Array.from(this.selectedOptions);
    //
    //     selectedOptions.forEach(option => {
    //         if (!selectedCountries.includes(option.value)) {
    //             selectedCountries.push(option.value);
    //             let div = document.createElement("div");
    //             div.classList.add("selected-country");
    //             div.innerHTML = `${option.text} <button class="remove-country">×</button>`;
    //
    //             // Remove country from the list when the remove button is clicked
    //             div.querySelector(".remove-country").addEventListener("click", function () {
    //                 option.selected = false;
    //                 selectedCountries = selectedCountries.filter(c => c !== option.value);
    //                 div.remove();
    //
    //                 if (container.children.length === 0) {
    //                     container.style.display = "none";
    //                 }
    //             });
    //
    //             container.appendChild(div);
    //         }
    //     });
    //
    //     // Show or hide the selected countries container
    //     container.style.display = selectedCountries.length > 0 ? "block" : "none";
    // });

    // Back button functionality to navigate between pages
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

    // Close wizard and skip setup
    function closeWizard() {
        window.location.href = window.location.href + "&skip_wizard=1";
    }

    document.getElementById("closeWizard").addEventListener("click", closeWizard);
    document.getElementById("installManually").addEventListener("click", closeWizard);
});

// Function to filter country list based on search input
function filterCountries(inputId, selectId) {
    // document.getElementById(inputId).addEventListener("input", function () {
    //     let searchValue = this.value.toLowerCase();
    //     let options = document.getElementById(selectId).options;
    //     for (let option of options) {
    //         option.style.display = option.text.toLowerCase().includes(searchValue) ? "block" : "none";
    //     }
    // });
}

// Apply search filtering to country selection
filterCountries("searchIntl", "countrySelectIntl");
// filterCountries("searchCustom", "countrySelectCustom");









