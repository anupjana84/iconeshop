/* =========================================================
   OCR FILE SELECT
========================================================= */

function handleInvoiceFile(input) {
    const fileName = document.getElementById("ocrFileName");
    const file = input?.files?.[0];

    if (!file) {
        setScanButton(false);
        if (fileName) fileName.innerText = "";
        return;
    }

    if (fileName) {
        fileName.innerText = `📎 নির্বাচিত ফাইল: ${file.name}`;
    }

    setScanButton(true);
}


/* =========================================================
   OCR BUTTON & RUNNING STATE
========================================================= */

function setScanButton(active) {
    const button = document.getElementById("scanBillBtn");
    if (!button) return;

    button.disabled = !active;
    button.classList.toggle("bg-blue-600", active);
    button.classList.toggle("hover:bg-blue-700", active);
    button.classList.toggle("cursor-pointer", active);

    button.classList.toggle("bg-gray-400", !active);
    button.classList.toggle("cursor-not-allowed", !active);

    button.innerText = active
        ? "🔍 আপলোড ও অটো-স্ক্যান"
        : "🔍 ফাইল নির্বাচন করুন";
}

function setOCRRunning(running) {
    const button = document.getElementById("scanBillBtn");
    if (!button) return;

    button.disabled = running;
    button.innerText = running
        ? "⏳ OCR চলছে..."
        : "🔍 আবার স্ক্যান করুন";

    button.classList.toggle("bg-gray-400", running);
    button.classList.toggle("bg-blue-600", !running);
    button.classList.toggle("hover:bg-blue-700", !running);
}


/* =========================================================
   OCR MAIN
========================================================= */

async function scanBill() {
    const input = document.getElementById("invoiceFile");
    const file = input?.files?.[0];

    if (!file) {
        alert("অনুগ্রহ করে প্রথমে একটি PDF অথবা ছবি নির্বাচন করুন।");
        return;
    }

    const progress = document.getElementById("ocrProgress");

    try {
        setOCRRunning(true);
        progress?.classList.remove("hidden");
        updateOCRProgress(0, "ফাইল প্রস্তুত করা হচ্ছে...");

        const isPDF =
            file.type === "application/pdf" ||
            file.name.toLowerCase().endsWith(".pdf");

        const text = isPDF
            ? await ocrPdf(file)
            : await ocrImage(file);

        if (!text.trim()) {
            throw new Error("কোনো লেখা পাওয়া যায়নি।");
        }

        updateOCRProgress(100, "OCR সম্পন্ন হয়েছে।");
        parseAndFillInvoiceData(text);

      

    } catch (error) {
        alert("❌ OCR করতে সমস্যা হয়েছে.\n\n" + error.message);
    } finally {
        setOCRRunning(false);
    }
}


/* =========================================================
   OCR ENGINE (IMAGE & PDF)
========================================================= */

function ensureTesseractLoaded() {
    if (typeof Tesseract === "undefined") {
        throw new Error("Tesseract.js লোড হয়নি।");
    }
}

async function ocrImage(file) {
    ensureTesseractLoaded();

    const result = await Tesseract.recognize(
        file,
        "eng",
        { logger: ocrLogger }
    );

    return result.data.text || "";
}

function ocrLogger(info) {
    if (info.status !== "recognizing text") return;

    const percent = Math.round((info.progress || 0) * 100);
    updateOCRProgress(percent, "ছবি থেকে লেখা পড়া হচ্ছে...");
}

async function ocrPdf(file) {
    if (!window.pdfjsLib) {
        throw new Error("PDF.js লোড হয়নি।");
    }
    ensureTesseractLoaded();

    const pdf = await pdfjsLib.getDocument({
        data: await file.arrayBuffer()
    }).promise;

    let fullText = "";

    for (let pageNo = 1; pageNo <= pdf.numPages; pageNo++) {
        const page = await pdf.getPage(pageNo);
        const viewport = page.getViewport({ scale: 2 });
        const canvas = document.createElement("canvas");

        canvas.width = viewport.width;
        canvas.height = viewport.height;

        await page.render({
            canvasContext: canvas.getContext("2d"),
            viewport: viewport
        }).promise;

        const result = await Tesseract.recognize(
            canvas,
            "eng",
            {
                logger: info => {
                    if (info.status !== "recognizing text") return;
                    const pageProgress = info.progress || 0;
                    const overallProgress = ((pageNo - 1) + pageProgress) / pdf.numPages;
                    updateOCRProgress(
                        Math.round(overallProgress * 100),
                        `PDF page ${pageNo}/${pdf.numPages} OCR হচ্ছে...`
                    );
                }
            }
        );

        fullText += "\n\n" + result.data.text;
    }

    return fullText;
}

function updateOCRProgress(percent, message) {
    const status = document.getElementById("ocrStatus");
    const percentText = document.getElementById("ocrPercent");
    const bar = document.getElementById("ocrProgressBar");

    if (status) status.innerText = message;
    if (percentText) percentText.innerText = `${percent}%`;
    if (bar) bar.style.width = `${percent}%`;
}


/* =========================================================
   PARSE AND FILL DATA
========================================================= */

function parseAndFillInvoiceData(text) {
    if (!text?.trim()) return;

    const cleanText = normalizeOCRText(text);
    const data = extractInvoiceData(cleanText);
    // console.log(data,"data")

    fillInput("comp_phone", data.phone);
    fillInput("comp_name", data.name);
    fillInput("comp_address", data.address);
    fillInput("comp_pincode", data.pincode);
    fillInput("comp_bill_date", data.date);
    fillInput("comp_product", data.product);
    fillInput("comp_serial", data.serial);

    if (data.phone) {
        if (typeof searchCustomer === "function") {
            searchCustomer(data.phone, "comp");
        }
        if (typeof saveCustomerToDatabase === "function") {
            saveCustomerToDatabase(data.phone, data.name, data.address, data.pincode);
        }
    }
}

function normalizeOCRText(text) {
    return String(text)
        .replace(/\r/g, "\n")
        .replace(/[ \t]+/g, " ")
        .replace(/\n{3,}/g, "\n\n")
        .trim();
}


/* =========================================================
   EXTRACT ALL DATA
========================================================= */

function extractInvoiceData(text) {
    const billToBlock = getBillToBlock(text);

    return {
        phone: extractCustomerPhone(billToBlock, text),
        pincode: extractCustomerPincode(billToBlock, text),
        name: extractCustomerName(billToBlock, text),
        address: extractCustomerAddress(billToBlock),
        date: extractDate(text),
        product: extractProduct(text),
        serial: extractSerial(text)
    };
}

function getBillToBlock(text) {
    // Isolates the "Bill To" area before the products/pricing table begins
    const match = text.match(/Bill\s*To[\s\S]*?(?=(?:Product|HSN|Price|Rate|Tax\s*Invoice|Sub\s*Total|Qty|\n\s*#))/i);
    return match ? match[0] : "";
}


/* =========================================================
   TARGETED CUSTOMER EXTRACTORS
========================================================= */

function extractCustomerPhone(billToBlock, fullText) {
    const pp =fullText.split("Bill To")
    // console.log(pp, "madan")
    // 1. Check for labeled mobile numbers in Bill-To or full document
    const target = billToBlock || fullText;
    const labeledMatch = target.match(/(?:Mobile\s*(?:No)?|WA\s*No|Reward\s*Mobile)[\s\S]{0,15}?([6-9]\d{9})/i);
    
    if (labeledMatch) {
        return labeledMatch[1];
    }

    // 2. Search for any standard 10-digit number inside the Bill To section
    if (billToBlock && billToBlock !== fullText) {
        const matches = billToBlock.match(/\b[6-9]\d{9}\b/g);
        if (matches?.length) return matches[0];
    }

    return "";
}

function extractCustomerName(billToBlock, fullText) {
    const targetText = billToBlock || fullText;

    // Handles formats like: "• FULCHUND SHAIKH" right after "Bill To"
    const patterns = [
        /Bill\s*To[\s\S]*?[•\-\*]?\s*([A-Za-z\s]{3,40})(?=\n|\s*•|\s*Address)/i,
        /(?:Customer\s*)?Name\s*[:\-]?\s*([A-Za-z\s]{3,40})/i
    ];

    for (const pattern of patterns) {
        const match = targetText.match(pattern);
        if (!match) continue;

        let name = match[1]
            .replace(/\b(Bill\s*To|Address|Mobile|Phone|WA|Pin|GST|Reward|Model|Date)\b.*/gi, "")
            .replace(/[^A-Za-z\s]/g, "")
            .trim();

        if (name.length >= 3) return name;
    }

    return "";
}

function extractCustomerAddress(billToBlock) {
    if (!billToBlock) return "";

    // Extracts text following "Address :" up until Pin/Phone/bullet points
    const match = billToBlock.match(/Address\s*[:\-]?\s*([\s\S]*?)(?=\s*(?:Mobile|Phone|WA|Pin\s*[-:]|Reward|\n\s*•|\n\s*-|$))/i);
    if (!match) return "";

    const address = match[1]
        .replace(/\n+/g, " ")
        .replace(/\s+/g, " ")
        .replace(/Pin\s*[-:]?\s*\d{6}/gi, "")
        .replace(/[,.-]+$/, "")
        .trim();

    return address.length >= 4 ? address : "";
}

function extractCustomerPincode(billToBlock, fullText) {
    const target = billToBlock || fullText;

    // Matches explicit "Pin- 741162" or "Pin : 741162"
    const pinMatch = target.match(/Pin\s*[-:]?\s*(\d{6})/i);
    if (pinMatch) return pinMatch[1];

    const matches = target.match(/\b\d{6}\b/g);
    if (!matches?.length) return "";

    return matches.find(pin => pin.startsWith("7") || pin.startsWith("6")) || matches[0];
}


/* =========================================================
   DATE, PRODUCT & SERIAL EXTRACTORS
========================================================= */

function extractDate(text) {
    const patterns = [
        /(\d{1,2})[\/-](\d{1,2})[\/-](\d{4})/,
        /(\d{1,2})[\s-](Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*[\s-](\d{4})/i,
        /(\d{4})[\/-](\d{1,2})[\/-](\d{1,2})/
    ];

    for (const pattern of patterns) {
        const match = text.match(pattern);
        if (!match) continue;

        if (pattern === patterns[0]) {
            return [match[3], match[2].padStart(2, "0"), match[1].padStart(2, "0")].join("-");
        }
        if (pattern === patterns[2]) {
            return [match[1], match[2].padStart(2, "0"), match[3].padStart(2, "0")].join("-");
        }

        const date = new Date(`${match[2]} ${match[1]}, ${match[3]}`);
        if (!isNaN(date.getTime())) {
            return date.toISOString().split("T")[0];
        }
    }

    return "";
}

function extractProduct(text) {
    const brands = [
        "HAIER", "HITACHI", "CANON", "HP", "LENOVO", "EPSON", "LG",
        "SAMSUNG", "VOLTAS", "DAIKIN", "PANASONIC", "WHIRLPOOL",
        "ACER", "DELL", "ASUS", "BROTHER"
    ];

    const brandRegex = new RegExp(`\\b(${brands.join("|")})\\b.{0,80}`, "i");
    const brandMatch = text.match(brandRegex);

    let product = brandMatch ? brandMatch[0].replace(/[\n\r]+/g, " ").trim() : "";

    const modelMatch = text.match(/Model\s*(?:No|Number)?\s*[:\-]?\s*([A-Za-z0-9._()\/-]+)/i);
    if (modelMatch) {
        product = product ? `${product} ${modelMatch[1]}` : modelMatch[1];
    }

    return product;
}

function extractSerial(text) {
    const patterns = [
        /(?:S\/N|SN|Serial\s*(?:No|Number)?)\s*[:\-]?\s*([A-Za-z0-9\/._-]+)/gi,
        /(?:Sl\.?\s*No\.?)\s*[:\-]?\s*([A-Za-z0-9\/._-]+)/gi
    ];

    const serials = [];

    for (const pattern of patterns) {
        let match;
        while ((match = pattern.exec(text)) !== null) {
            const value = match[1];
            if (value && !serials.includes(value)) {
                serials.push(value);
            }
        }
    }

    return serials.join(", ");
}


/* =========================================================
   DOM HELPERS
========================================================= */

function fillInput(id, value) {
    if (!value) return;
    const input = document.getElementById(id);
    if (input) input.value = value;
}

function escapeHtml(value) {
    return String(value ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}