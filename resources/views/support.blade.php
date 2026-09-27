@php
    $page_title = 'Service Settings';
@endphp

@extends('layouts.main')

@section('content_page')

<div class="max-w-7xl mx-auto bg-white p-6 rounded-xl shadow-lg">

    <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-3 flex justify-between items-center">
        <span>🛠️ সার্ভিস সেটিং (Service Settings Hub)</span>
        <span class="text-xs font-normal bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
            Icon Computer Management
        </span>
    </h2>

    {{-- Service Tabs --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        <div
            onclick="selectService('company')"
            id="btn-company"
            class="service-tab-btn cursor-pointer border-2 border-blue-500 bg-blue-50 hover:bg-blue-100 p-5 rounded-xl shadow-sm hover:shadow-md transition text-center"
        >
            <div class="text-3xl text-blue-600 mb-2">📞</div>
            <h3 class="font-bold text-gray-800 text-lg mb-1">
                ১) কোম্পানি কল বুকিং
            </h3>
            <p class="text-xs text-gray-600">
                অফিসিয়াল কোম্পানি কল বুকিং ও কেস আইডি ট্র্যাকিং
            </p>
        </div>

        <div
            onclick="selectService('external')"
            id="btn-external"
            class="service-tab-btn cursor-pointer border-2 border-orange-400 bg-orange-50 hover:bg-orange-100 p-5 rounded-xl shadow-sm hover:shadow-md transition text-center"
        >
            <div class="text-3xl text-orange-500 mb-2">🚚</div>
            <h3 class="font-bold text-gray-800 text-lg mb-1">
                ২) বাইরে থেকে সার্ভিস
            </h3>
            <p class="text-xs text-gray-600">
                থার্ডপার্টি / ভেন্ডর আউটসোর্স সার্ভিস
            </p>
        </div>

        <div
            onclick="selectService('inhouse')"
            id="btn-inhouse"
            class="service-tab-btn cursor-pointer border-2 border-green-500 bg-green-50 hover:bg-green-100 p-5 rounded-xl shadow-sm hover:shadow-md transition text-center"
        >
            <div class="text-3xl text-green-600 mb-2">🛠️</div>
            <h3 class="font-bold text-gray-800 text-lg mb-1">
                ৩) নিজের সার্ভিস
            </h3>
            <p class="text-xs text-gray-600">
                নিজস্ব শপ বা ইন-হাউস রিপেয়ারিং ম্যানেজমেন্ট
            </p>
        </div>

    </div>

    {{-- Dynamic Form Area --}}
    <div
        id="formArea"
        class="border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50 min-h-[250px]"
    >
        <div class="text-center text-gray-500 font-medium my-10">
            👆 উপরে যেকোনো একটি সার্ভিস অপশন নির্বাচন করুন।
        </div>
    </div>

    {{-- Form Templates Loaded into DOM (Hidden by default or managed via JS) --}}
    <div id="companyFormWrapper" class="hidden">
        @include('support.company-form')
    </div>

    <div id="externalFormWrapper" class="hidden">
        @include('support.external-form')
    </div>

    <div id="inhouseFormWrapper" class="hidden">
        @include('support.inhouse-form')
    </div>

    {{-- Universal Table --}}
    <div id="tableSectionArea" class="mt-8 hidden">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 border-b pb-4 mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span>📋 রেকর্ড ও ট্র্যাকিং তালিকা</span>
                    <span
                        id="activeFilterBadge"
                        class="text-xs bg-gray-200 text-gray-700 font-semibold px-2.5 py-0.5 rounded-full"
                    >
                        সব দেখুন
                    </span>
                </h3>

                <div class="w-full md:w-80 relative">
                    <input
                        type="text"
                        id="globalSearchInput"
                        oninput="handleSearch(this.value)"
                        placeholder="🔍 ফোন নম্বর, নাম বা প্রোডাক্ট দিয়ে খুঁজুন..."
                        class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                    >
                    <span class="absolute left-3 top-2.5 text-gray-400 text-xs">
                        🔍
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead id="mainTableHead"></thead>
                    <tbody id="mainTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Print Receipt Modal --}}
<div
    id="printModal"
    class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center p-4 z-50"
>
    <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl p-6 relative">
        <button
            onclick="closePrintModal()"
            class="absolute top-3 right-3 text-gray-500 hover:text-black font-bold text-xl"
        >
            &times;
        </button>

        <div
            id="printableReceipt"
            class="p-4 border rounded-lg bg-white text-black font-sans"
        >
            <div class="text-center border-b pb-3 mb-3">
                <h2 class="text-xl font-bold tracking-wide">ICON COMPUTER</h2>
                <p class="text-xs">Bethuadahari, Nadia | Ph: 8597753337</p>
                <p class="text-xs font-semibold text-gray-600 mt-1">SERVICE & REPAIR RECEIPT</p>
            </div>

            <div class="text-xs space-y-1.5 mb-4">
                <div class="flex justify-between">
                    <span>
                        <strong>Receipt No:</strong>
                        <span id="pr_id"></span>
                    </span>
                    <span>
                        <strong>Date:</strong>
                        <span id="pr_date"></span>
                    </span>
                </div>

                <p>
                    <strong>Customer Name:</strong>
                    <span id="pr_name"></span>
                </p>

                <p>
                    <strong>Phone:</strong>
                    <span id="pr_phone"></span>
                </p>

                <p>
                    <strong>Address:</strong>
                    <span id="pr_address"></span>
                </p>

                <p>
                    <strong>Product/Issue:</strong>
                    <span id="pr_product"></span>
                </p>

                <p>
                    <strong>Serial No:</strong>
                    <span id="pr_serial"></span>
                </p>

                <div class="flex justify-between pt-2 border-t font-bold text-sm">
                    <span>Final Amount / Est:</span>
                    <span>₹<span id="pr_amount"></span></span>
                </div>
            </div>

            <div class="text-[10px] text-gray-500 text-center border-t pt-2">
                * Please bring this receipt during product delivery. Thank you!
            </div>
        </div>

        <div class="mt-4 flex justify-end gap-3">
            <button
                onclick="closePrintModal()"
                class="px-4 py-2 bg-gray-200 text-gray-700 text-xs font-bold rounded-lg"
            >
                বন্ধ করুন
            </button>
            <button
                onclick="triggerPrint()"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow"
            >
                🖨️ প্রিন্ট আউট
            </button>
        </div>
    </div>
</div>

{{-- OCR & PDF Libraries (UMD Standard) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>

<script>
    if (window.pdfjsLib) {
        pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
    }

    /* =========================================================
       SERVICE TAB CONTROLLER
    ========================================================= */
    function selectService(type) {
        const formArea = document.getElementById("formArea");
        const tableArea = document.getElementById("tableSectionArea");
        
        let sourceElement = null;
        if (type === 'company') sourceElement = document.getElementById("companyFormWrapper");
        if (type === 'external') sourceElement = document.getElementById("externalFormWrapper");
        if (type === 'inhouse') sourceElement = document.getElementById("inhouseFormWrapper");

        if (sourceElement && formArea) {
            formArea.innerHTML = sourceElement.innerHTML;
            formArea.classList.remove('min-h-[250px]', 'border-dashed');
        }

        if (tableArea) {
            tableArea.classList.remove("hidden");
        }
    }

    /* =========================================================
       OCR FILE SELECT & UI STATES
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

    function updateOCRProgress(percent, message) {
        const status = document.getElementById("ocrStatus");
        const percentText = document.getElementById("ocrPercent");
        const bar = document.getElementById("ocrProgressBar");

        if (status) status.innerText = message;
        if (percentText) percentText.innerText = `${percent}%`;
        if (bar) bar.style.width = `${percent}%`;
    }

    /* =========================================================
       OCR ENGINE (IMAGE & PDF)
    ========================================================= */
    function ensureTesseractLoaded() {
        if (typeof Tesseract === "undefined") {
            throw new Error("Tesseract.js লোড হয়নি। ইন্টারনেট সংযোগ যাচাই করুন।");
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
                throw new Error("কোনো লেখা সনাক্ত করা যায়নি। ফাইলের স্পষ্টতা যাচাই করুন।");
            }

            updateOCRProgress(100, "OCR সম্পন্ন হয়েছে।");
            parseAndFillInvoiceData(text);

        } catch (error) {
            alert("❌ OCR করতে সমস্যা হয়েছে:\n\n" + error.message);
        } finally {
            setOCRRunning(false);
        }
    }

    /* =========================================================
       DATA EXTRACTION & FORM POPULATION
    ========================================================= */
    function normalizeOCRText(text) {
        return String(text)
            .replace(/\r/g, "\n")
            .replace(/[ \t]+/g, " ")
            .replace(/\n{3,}/g, "\n\n")
            .trim();
    }

    function getBillToBlock(text) {
        const match = text.match(/Bill\s*To[\s\S]*?(?=(?:Product|HSN|Price|Rate|Tax\s*Invoice|Sub\s*Total|Qty|\n\s*#))/i);
        return match ? match[0] : "";
    }

    function extractCustomerPhone(billToBlock, fullText) {
        const target = billToBlock || fullText;
        const labeledMatch = target.match(/(?:Mobile\s*(?:No)?|WA\s*No|Reward\s*Mobile)[\s\S]{0,15}?([6-9]\d{9})/i);
        
        if (labeledMatch) {
            return labeledMatch[1];
        }

        if (billToBlock && billToBlock !== fullText) {
            const matches = billToBlock.match(/\b[6-9]\d{9}\b/g);
            if (matches?.length) return matches[0];
        }

        const globalMatch = fullText.match(/\b[6-9]\d{9}\b/);
        return globalMatch ? globalMatch[0] : "";
    }

    function extractCustomerName(billToBlock, fullText) {
        const targetText = billToBlock || fullText;
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
        const pinMatch = target.match(/Pin\s*[-:]?\s*(\d{6})/i);
        if (pinMatch) return pinMatch[1];

        const matches = target.match(/\b\d{6}\b/g);
        if (!matches?.length) return "";

        return matches.find(pin => pin.startsWith("7") || pin.startsWith("6")) || matches[0];
    }

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
                const day = parseInt(match[1], 10);
                const month = parseInt(match[2], 10);
                // Standard DD/MM/YYYY formatting detection
                if (day <= 31 && month <= 12) {
                    return `${match[3]}-${match[2].padStart(2, "0")}-${match[1].padStart(2, "0")}`;
                }
            }
            if (pattern === patterns[2]) {
                return `${match[1]}-${match[2].padStart(2, "0")}-${match[3].padStart(2, "0")}`;
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

        const serials = new Set();

        for (const pattern of patterns) {
            pattern.lastIndex = 0;
            let match;
            while ((match = pattern.exec(text)) !== null) {
                if (match[1] && match[1].trim().length > 3) {
                    serials.add(match[1].trim());
                }
            }
        }

        return Array.from(serials).join(", ");
    }

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

    function parseAndFillInvoiceData(text) {
        if (!text?.trim()) return;

        const cleanText = normalizeOCRText(text);
        const data = extractInvoiceData(cleanText);

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

    /* =========================================================
       HELPERS & MODAL HANDLERS
    ========================================================= */
    function fillInput(id, value) {
        if (!value) return;
        const input = document.getElementById(id);
        if (input) input.value = value;
    }

    function closePrintModal() {
        const modal = document.getElementById("printModal");
        if (modal) {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }
    }

    function triggerPrint() {
        const printContents = document.getElementById("printableReceipt")?.innerHTML;
        if (!printContents) return;

        const originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
    }

    function handleSearch(query) {
        // console.log("Global search triggered for:", query);
    }
</script>

@endsection