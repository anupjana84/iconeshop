"use strict";

function searchCustomer(phone, prefix) {

    let cleanPhone = String(phone || "")
        .replace(/\D/g, "")
        .slice(0, 10);

    const phoneInput =
        document.getElementById(prefix + "_phone");

    if (phoneInput) {
        phoneInput.value = cleanPhone;
    }

    if (cleanPhone.length !== 10) {
        return;
    }

    const customer =
        customerDatabase[cleanPhone];

    if (!customer) {
        return;
    }

    const name =
        document.getElementById(prefix + "_name");

    const address =
        document.getElementById(prefix + "_address");

    const pincode =
        document.getElementById(prefix + "_pincode");

    if (name) {
        name.value = customer.name || "";
    }

    if (address) {
        address.value = customer.address || "";
    }

    if (pincode) {
        pincode.value = customer.pincode || "";
    }
}


function saveCustomerToDatabase(
    phone,
    name,
    address,
    pincode
) {

    phone = String(phone || "")
        .replace(/\D/g, "");

    if (phone.length !== 10) {
        return;
    }

    customerDatabase[phone] = {
        name: name || "",
        address: address || "",
        pincode: pincode || ""
    };

    saveCustomerDatabase();
}


// Automatically search after 10th digit
document.addEventListener("input", function (event) {

    const input = event.target;

    if (!input) return;

    const id = input.id || "";

    if (!id.endsWith("_phone")) {
        return;
    }

    const prefix = id.replace("_phone", "");

    const phone = input.value
        .replace(/\D/g, "")
        .slice(0, 10);

    input.value = phone;

    if (phone.length === 10) {
        searchCustomer(phone, prefix);
    }
});