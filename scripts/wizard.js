jQuery(document).ready(function ($) {
    // jQuery(document).ready(function ($) {
    //     if (window.location.search.includes("skip_wizard=1")) {
    //         let newUrl = window.location.origin + window.location.pathname + "?page=idehweb-lwp&wizard";
    //         window.history.replaceState({}, document.title, newUrl);
    //     }
    // });

    var lwp_countries_gateways = [
        { "country": "ir", "gateways": ["farazsms", "mellipayamak"] },
        { "country": "sa", "gateways": ["taqnyat"] },
        { "country": "in", "gateways": ["mshastra", "textlocal"] }
    ];


    $(window).on('load', function () {
        $("#lwp_idehweb_country_codes_guid").select2();
    });

    $(document).on('click', '#finishWizardIntl', function (e) {
        e.preventDefault();
        var basePath = window.location.origin + window.location.pathname.split("/wp-admin")[0];
        var newUrl = basePath + "/wp-admin/admin.php?page=idehweb-lwp";
        // window.location.href = newUrl;

        var selectedValues = $("#lwp_idehweb_country_codes_guid").val();
        console.log("Selected Countries:", selectedValues);

        var selectedGateways = [];
        $.each(selectedValues, function (index, selectedCountry) {
            $.each(lwp_countries_gateways, function (i, item) {
                if (item.country === selectedCountry) {
                    selectedGateways = selectedGateways.concat(item.gateways);
                }
            });
        });

        console.log("Selected Gateways:", selectedGateways, idehweb_lwp.ajaxurl, idehweb_lwp.nonce);

        $.ajax({
            type: "POST",
            dataType: "json",
            url: idehweb_lwp.ajaxurl,
            data: {
                action: "lwp_set_countries",
                nonce: idehweb_lwp.nonce,
                selected_countries: selectedValues,
                selected_gateways: (selectedGateways?.length>0) ? selectedGateways : ["firebase","twilio"]
            },
            success: function (response) {
                console.log("Response from server:", response);
                // &tab=lwp-tab-gateway-settings&selected_gateway=firebase#lwp-tab-gateway-settings
                // selectedGateways
                // let newUrl = window.location.origin + window.location.pathname + "?page=idehweb-lwp&tab=lwp-tab-gateway-settings&selected_gateway="+selectedGateways?.join(", ")+"#lwp-tab-gateway-settings";
                let newUrl = window.location.origin + window.location.pathname + "?page=idehweb-lwp&tab=lwp-tab-gateway-settings#lwp-tab-gateway-settings";
                window.history.replaceState({}, document.title, newUrl);
                window.location.href = newUrl;
                window.location.reload();
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    });

    // Get selected_gateway from URL
    function getQueryParam(param) {
        let urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    var selectedGateway = getQueryParam("selected_gateway") || localStorage.getItem("selectedGateway");

    var default_tab = selectedGateway ? "lwp-tab-gateway-settings" : (window.location.hash ? window.location.hash.substring(1) : "lwp-tab-general-settings");

    console.log("Default Tab:", default_tab);

    if (selectedGateway) {
        console.log("Selected Gateway:", selectedGateway);
        $('input[name="selectedGateway"][value="' + selectedGateway + '"]').prop("checked", true);
        $("#finishWizardCustom").prop("disabled", false);
    }

    // Initially disable the finish button
    $("#finishWizardCustom").prop("disabled", true);

    // Enable the finish button when a gateway is selected
    $('input[name="selectedGateway"]').on("change", function () {
        $("#finishWizardCustom").prop("disabled", false);
    });

    // Handle finish button click event
    $("body").on("click","#finishWizardCustom", function () {
        var selectedGateway = $('input[name="selectedGateway"]:checked').val();
        localStorage.setItem("selectedGateway", selectedGateway);

        var gatewayTabs = {
            "firebase": "firebase",
            "telegram": "telegram",
            "whatsapp": "whatsapp"
        };

        if (gatewayTabs[selectedGateway]) {
            var basePath = window.location.origin + window.location.pathname.split("/wp-admin")[0];
            var newUrl = basePath + "/wp-admin/admin.php?page=idehweb-lwp&skip_wizard=1&tab=lwp-tab-gateway-settings&selected_gateway=" + gatewayTabs[selectedGateway] + "#lwp-tab-gateway-settings";

            console.log("Navigating to:", newUrl);
            window.location.href = newUrl;
        }
    });

    // Restore previously selected gateway from localStorage
    var savedGateway = localStorage.getItem("selectedGateway");
    if (savedGateway) {
        $('input[name="selectedGateway"][value="' + savedGateway + '"]').prop("checked", true);
        $("#finishWizardCustom").prop("disabled", false);
    }

    function hideInfo() {
        $("#wizardInfo").addClass("hidden");
    }

    // Navigation from Page 1 to Page 2
    $("#nextToPage2").on("click", function () {
        $("#wizardPage1").hide();
        $("#wizardPage2").show();
        hideInfo();
    });

    // Enable Next button on Page 2 when an option is selected
    $('input[name="option_select"]').on("change", function () {
        $("#nextToPage3").prop("disabled", false);
    });

    // Navigation from Page 2 to Page 3 based on selected option
    $("#nextToPage3").on("click", function () {
        var selectedOption = $('input[name="option_select"]:checked').val();
        $("#wizardPage2").hide();

        if (selectedOption === "international") {
            $("#wizardPage3International").show();
        } else {
            $("#wizardPage3Custom").show();
        }
    });

    // Back button functionality to navigate between pages
    $("#backToPage1").on("click", function () {
        $("#wizardPage2").hide();
        $("#wizardPage1").show();
        $("#wizardInfo").removeClass("hidden");
    });

    $("#backToPage2FromIntl").on("click", function () {
        $("#wizardPage3International").hide();
        $("#wizardPage2").show();
    });

    $("#backToPage2FromCustom").on("click", function () {
        $("#wizardPage3Custom").hide();
        $("#wizardPage2").show();
    });

    // Close wizard and skip setup
    function closeWizard() {
        localStorage.setItem('ldwtutshow', 1);

        let newUrl = window.location.origin + window.location.pathname + "?page=idehweb-lwp";
                window.history.replaceState({}, document.title, newUrl);
        window.location.href = newUrl;

    }

    $("#closeWizard, #installManually").on("click", closeWizard);

    // Function to filter country list based on search input
    function filterCountries(inputId, selectId) {
        $("#" + inputId).on("input", function () {
            var searchValue = $(this).val().toLowerCase();
            $("#" + selectId + " option").each(function () {
                var optionText = $(this).text().toLowerCase();
                $(this).toggle(optionText.includes(searchValue));
            });
        });
    }

    // Apply search filtering to country selection
    filterCountries("searchIntl", "countrySelectIntl");
});
