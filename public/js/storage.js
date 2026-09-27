"use strict";

let companyBookings = JSON.parse(
    localStorage.getItem("icon_company_bookings") || "[]"
);

let externalBookings = JSON.parse(
    localStorage.getItem("icon_external_bookings") || "[]"
);

let inhouseBookings = JSON.parse(
    localStorage.getItem("icon_inhouse_bookings") || "[]"
);

let customerDatabase = JSON.parse(
    localStorage.getItem("icon_customer_database") || "{}"
);

// Default customers
if (!customerDatabase["8597753337"]) {
    customerDatabase["8597753337"] = {
        name: "MS APARNA DEY",
        address: "BETHUADAHARI NADIA",
        pincode: "741126"
    };
}

if (!customerDatabase["9800000000"]) {
    customerDatabase["9800000000"] = {
        name: "RITESH ROY",
        address: "KRISHNANAGAR NADIA",
        pincode: "741101"
    };
}

saveCustomerDatabase();

function saveAllData() {
    localStorage.setItem(
        "icon_company_bookings",
        JSON.stringify(companyBookings)
    );

    localStorage.setItem(
        "icon_external_bookings",
        JSON.stringify(externalBookings)
    );

    localStorage.setItem(
        "icon_inhouse_bookings",
        JSON.stringify(inhouseBookings)
    );
}

function saveCustomerDatabase() {
    localStorage.setItem(
        "icon_customer_database",
        JSON.stringify(customerDatabase)
    );
}