"use strict";


// =====================================================
// FILE SELECT
// =====================================================

function handleInvoiceFile(input) {

    const button =
        document.getElementById(
            "scanBillBtn"
        );

    const fileName =
        document.getElementById(
            "ocrFileName"
        );


    if (!input.files || !input.files.length) {

        if (button) {

            button.disabled = true;

            button.innerText =
                "🔍 আপলোড ও অটো-স্ক্যান";

            button.classList.remove(
                "bg-blue-600",
                "hover:bg-blue-700",
                "cursor-pointer"
            );

            button.classList.add(
                "bg-gray-400",
                "cursor-not-allowed"
            );
        }

        if (fileName) {
            fileName.innerText = "";
        }

        return;
    }


    const file =
        input.files[0];


    if (fileName) {

        fileName.innerText =
            "📎 নির্বাচিত ফাইল: " +
            file.name;
    }


    if (button) {

        button.disabled = false;

        button.innerText =
            "🔍 আপলোড ও অটো-স্ক্যান";

        button.classList.remove(
            "bg-gray-400",
            "cursor-not-allowed"
        );

        button.classList.add(
            "bg-blue-600",
            "hover:bg-blue-700",
            "cursor-pointer"
        );
    }
}


// =====================================================
// SCAN
// =====================================================

async function scanBill() {

    const input =
        document.getElementById(
            "invoiceFile"
        );

    if (
        !input ||
        !input.files ||
        !input.files.length
    ) {

        alert(
            "অনুগ্রহ করে প্রথমে একটি PDF অথবা ছবি নির্বাচন করুন।"
        );

        return;
    }


    const file =
        input.files[0];

    const button =
        document.getElementById(
            "scanBillBtn"
        );

    const progress =
        document.getElementById(
            "ocrProgress"
        );


    try {

        button.disabled = true;

        button.innerText =
            "⏳ OCR চলছে...";


        if (progress) {
            progress.classList.remove(
                "hidden"
            );
        }


        updateOCRProgress(
            0,
            "ফাইল প্রস্তুত করা হচ্ছে..."
        );


        let fullText = "";


        if (
            file.type ===
            "application/pdf" ||
            file.name
                .toLowerCase()
                .endsWith(".pdf")
        ) {

            fullText =
                await ocrPdf(
                    file,
                    updateOCRProgress
                );

        } else {

            fullText =
                await ocrImage(
                    file,
                    updateOCRProgress
                );
        }


        if (!fullText.trim()) {

            throw new Error(
                "কোনো লেখা পাওয়া যায়নি।"
            );
        }


        // console.log(
        //     "OCR TEXT:",
        //     fullText
        // );


        updateOCRProgress(
            100,
            "OCR সম্পন্ন হয়েছে।"
        );


        parseAndFillInvoiceData(
            fullText
        );


        alert(
            "✅ বিল স্ক্যান সম্পন্ন হয়েছে এবং তথ্য ফর্মে বসানো হয়েছে।"
        );


    } catch (error) {

        console.error(
            "OCR Error:",
            error
        );

        alert(
            "❌ OCR করতে সমস্যা হয়েছে।\n\n" +
            error.message
        );


    } finally {

        button.disabled = false;

        button.innerText =
            "🔍 আবার স্ক্যান করুন";

        button.classList.remove(
            "bg-gray-400"
        );

        button.classList.add(
            "bg-blue-600",
            "hover:bg-blue-700"
        );
    }
}


// =====================================================
// IMAGE OCR
// =====================================================

async function ocrImage(
    file,
    progressCallback
) {

    if (
        typeof Tesseract ===
        "undefined"
    ) {

        throw new Error(
            "Tesseract.js লোড হয়নি।"
        );
    }


    const result =
        await Tesseract.recognize(
            file,
            "eng",
            {

                logger: function (info) {

                    if (
                        info.status ===
                        "recognizing text"
                    ) {

                        const percent =
                            Math.round(
                                (info.progress || 0) *
                                100
                            );

                        progressCallback(
                            percent,
                            "ছবি থেকে লেখা পড়া হচ্ছে..."
                        );
                    }
                }
            }
        );


    return result.data.text || "";
}


// =====================================================
// PDF OCR
// =====================================================

async function ocrPdf(
    file,
    progressCallback
) {

    const pdfjs =
        window.pdfjsLib;


    if (!pdfjs) {

        throw new Error(
            "PDF.js লোড হয়নি।"
        );
    }


    const arrayBuffer =
        await file.arrayBuffer();


    const pdf =
        await pdfjs
            .getDocument({
                data: arrayBuffer
            })
            .promise;


    let completeText = "";

    const totalPages =
        pdf.numPages;


    for (
        let pageNumber = 1;
        pageNumber <= totalPages;
        pageNumber++
    ) {

        const page =
            await pdf.getPage(
                pageNumber
            );


        const viewport =
            page.getViewport({
                scale: 2
            });


        const canvas =
            document.createElement(
                "canvas"
            );


        const context =
            canvas.getContext(
                "2d"
            );


        canvas.width =
            viewport.width;

        canvas.height =
            viewport.height;


        await page.render({

            canvasContext:
                context,

            viewport:
                viewport

        }).promise;


        const imageData =
            canvas.toDataURL(
                "image/png"
            );


        const result =
            await Tesseract.recognize(
                imageData,
                "eng",
                {

                    logger:
                        function (info) {

                            if (
                                info.status ===
                                "recognizing text"
                            ) {

                                const pageProgress =
                                    info.progress || 0;


                                const overallProgress =
                                    (
                                        (
                                            pageNumber - 1
                                        ) +
                                        pageProgress
                                    ) /
                                    totalPages;


                                progressCallback(

                                    Math.round(
                                        overallProgress *
                                        100
                                    ),

                                    "PDF থেকে OCR করা হচ্ছে..."
                                );
                            }
                        }
                }
            );


        completeText +=
            "\n\n" +
            result.data.text;
    }


    return completeText;
}


// =====================================================
// PROGRESS
// =====================================================

function updateOCRProgress(
    percent,
    message
) {

    const status =
        document.getElementById(
            "ocrStatus"
        );

    const percentText =
        document.getElementById(
            "ocrPercent"
        );

    const bar =
        document.getElementById(
            "ocrProgressBar"
        );


    if (status) {
        status.innerText = message;
    }

    if (percentText) {
        percentText.innerText =
            percent + "%";
    }

    if (bar) {
        bar.style.width =
            percent + "%";
    }
}


// =====================================================
// OCR PARSER
// =====================================================

function parseAndFillInvoiceData(text) {

    if (!text) return;


    const cleanText =
        text
            .replace(/\r/g, "\n")
            .replace(/[ \t]+/g, " ");


    // console.log(
    //     "NORMALIZED OCR:",
    //     cleanText
    // );


    // PHONE
    const phoneMatches =
        cleanText.match(
            /\b[6-9]\d{9}\b/g
        );


    if (
        phoneMatches &&
        phoneMatches.length
    ) {

        const phone =
            phoneMatches[0];


        const phoneInput =
            document.getElementById(
                "comp_phone"
            );


        if (phoneInput) {

            phoneInput.value =
                phone;

            searchCustomer(
                phone,
                "comp"
            );
        }
    }


    // PINCODE
    const pinMatches =
        cleanText.match(
            /\b\d{6}\b/g
        );


    if (
        pinMatches &&
        pinMatches.length
    ) {

        const pin =
            pinMatches.find(
                value =>
                    value.startsWith("7") ||
                    value.startsWith("6")
            ) ||
            pinMatches[0];


        const pinInput =
            document.getElementById(
                "comp_pincode"
            );


        if (pinInput) {
            pinInput.value = pin;
        }
    }


    // NAME
    const namePatterns = [

        /Bill\s*To\s*[:\-]?\s*([A-Za-z][A-Za-z .&]{2,50})/i,

        /Customer\s*Name\s*[:\-]?\s*([A-Za-z][A-Za-z .&]{2,50})/i,

        /Name\s*[:\-]?\s*([A-Za-z][A-Za-z .&]{2,50})/i

    ];


    let foundName = "";


    for (
        const pattern of namePatterns
    ) {

        const match =
            cleanText.match(
                pattern
            );


        if (match) {

            foundName =
                match[1]
                    .replace(
                        /Address|Mobile|Phone|Pin|GST|Invoice/gi,
                        ""
                    )
                    .trim();


            if (
                foundName.length >= 3
            ) {
                break;
            }
        }
    }


    if (foundName) {

        const nameInput =
            document.getElementById(
                "comp_name"
            );


        if (nameInput) {
            nameInput.value =
                foundName;
        }
    }


    // ADDRESS
    const addressMatch =
        cleanText.match(
            /Address\s*[:\-]?\s*([\s\S]{5,150}?)(?=\s+(?:Pin|Mobile|Phone|GST|Invoice|Date)\b)/i
        );


    if (addressMatch) {

        const address =
            addressMatch[1]
                .replace(/\n+/g, " ")
                .replace(/\s+/g, " ")
                .trim();


        const addressInput =
            document.getElementById(
                "comp_address"
            );


        if (addressInput) {
            addressInput.value =
                address;
        }
    }


    // DATE
    const dateMatch =
        cleanText.match(
            /\b(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})\b/
        );


    if (dateMatch) {

        const day =
            dateMatch[1].padStart(2, "0");

        const month =
            dateMatch[2].padStart(2, "0");

        const year =
            dateMatch[3];


        const billDate =
            document.getElementById(
                "comp_bill_date"
            );


        if (billDate) {

            billDate.value =
                `${year}-${month}-${day}`;
        }
    }


    // SERIAL NUMBER
    const serialMatch =
        cleanText.match(
            /(?:S\/N|SN|Serial\s*(?:No|Number)?)\s*[:\-]?\s*([A-Za-z0-9\/._-]+)/i
        );


    if (serialMatch) {

        const serialInput =
            document.getElementById(
                "comp_serial"
            );


        if (serialInput) {

            serialInput.value =
                serialMatch[1];
        }
    }


    // SAVE CUSTOMER
    const phone =
        document.getElementById(
            "comp_phone"
        )?.value.trim();


    const name =
        document.getElementById(
            "comp_name"
        )?.value.trim();


    const address =
        document.getElementById(
            "comp_address"
        )?.value.trim();


    const pincode =
        document.getElementById(
            "comp_pincode"
        )?.value.trim();


    if (
        phone &&
        phone.length === 10
    ) {

        saveCustomerToDatabase(
            phone,
            name,
            address,
            pincode
        );
    }
}