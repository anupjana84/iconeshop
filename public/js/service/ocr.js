// ================================
// ocr.js — পারচেজ বিল/ইনভয়েস অটো-স্ক্যান (Tesseract.js + pdf.js)
// ================================

async function scanBill(evt) {
    var fileInput = document.getElementById('invoiceFile');
    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        alert("অনুগ্রহ করে পারচেজ বিলের একটি ছবি বা PDF নির্বাচন করুন!");
        return;
    }

    var file = fileInput.files[0];
    var scanBtn = evt ? evt.target : null;
    var originalBtnText = scanBtn ? scanBtn.innerText : null;
    if (scanBtn) {
        scanBtn.innerText = "⏳ স্ক্যান করা হচ্ছে...";
        scanBtn.disabled = true;
    }

    try {
        var imageSource = file;

        // Handle PDF files by rendering the first page to a canvas
        if (file.type === "application/pdf") {
            var arrayBuffer = await file.arrayBuffer();
            var pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
            var page = await pdf.getPage(1);
            var viewport = page.getViewport({ scale: 2.0 });

            var canvas = document.createElement('canvas');
            var context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            await page.render({ canvasContext: context, viewport: viewport }).promise;
            imageSource = canvas;
        }

        // Run Tesseract OCR
        var result = await Tesseract.recognize(imageSource, 'eng');
        var text = result.data.text;

        if (text && text.trim().length > 0) {
            parseAndFillInvoiceData(text);
            alert("✅ বিল সফলভাবে স্ক্যান করা হয়েছে এবং ফর্ম ফিল-আপ হয়েছে!");
        } else {
            alert("⚠️ ফাইল থেকে কোনো টেক্সট পড়া সম্ভব হয়নি। পরিষ্কার ছবি ব্যবহার করুন।");
        }

    } catch (err) {
        console.error(err);
        alert("❌ স্ক্যান করার সময় সমস্যা হয়েছে।");
    } finally {
        if (scanBtn) {
            scanBtn.innerText = originalBtnText;
            scanBtn.disabled = false;
        }
    }
}

function parseAndFillInvoiceData(text) {
    // ১. ফোন নম্বর
    var phoneMatch = text.match(/(?:Mobile\s*No|WA\s*No|Reward\s*Mobile)\s*:\s*(\d{10})/i) || text.match(/\b[6-9]\d{9}\b/);
    if (phoneMatch && document.getElementById('comp_phone')) {
        document.getElementById('comp_phone').value = phoneMatch[1] || phoneMatch[0];
    }

    // ২. কাস্টমারের নাম
    var nameMatch = text.match(/Bill\s*To[\s\S]*?[•\-]\s*(?:IC\s+)?([A-Z\s]{3,30})/i);
    if (nameMatch && document.getElementById('comp_name')) {
        var cleanName = nameMatch[1].replace(/Address|Mobile|Pin|Reward/gi, '').trim();
        document.getElementById('comp_name').value = cleanName;
    }

    // ৩. কাস্টমারের অ্যাড্রেস
    var addressMatch = text.match(/Address\s*:\s*([^,\n]+(?:,[^,\n]+)*)/i);
    if (addressMatch && document.getElementById('comp_address')) {
        document.getElementById('comp_address').value = addressMatch[1].trim();
    }

    // ৪. পিন কোড
    var pinMatch = text.match(/Pin\s*-\s*(\d{6})/i) || text.match(/\b7\d{5}\b/);
    if (pinMatch && document.getElementById('comp_pincode')) {
        document.getElementById('comp_pincode').value = pinMatch[1] || pinMatch[0];
    }

    // ৫. বিল / সেল ডেট
    var dateMatch = text.match(/(?:Sale\s*Date|Date)\s*[:\-]\s*(\d{1,2}-[A-Za-z]{3}-\d{4})/i);
    if (dateMatch && document.getElementById('comp_bill_date')) {
        var d = new Date(dateMatch[1]);
        if (!isNaN(d.getTime())) {
            document.getElementById('comp_bill_date').value = d.toISOString().split('T')[0];
        }
    }

    // ৬. প্রোডাক্ট নাম ও মডেল
    var productMatch = text.match(/(HAIER|HITACHI|CANON|HP|LENV|EPSON)\s*AC/i) || text.match(/Product[\s\S]*?([A-Z0-9\s]+AC)/i);
    var modelMatch = text.match(/Model:\s*([A-Z0-9\.\(\)]+)/i);
    if (document.getElementById('comp_product')) {
        var prodStr = "";
        if (productMatch) prodStr += productMatch[1].trim();
        if (modelMatch) prodStr += " (" + modelMatch[1].trim() + ")";
        if (prodStr) document.getElementById('comp_product').value = prodStr;
    }

    // ৭. সিরিয়াল নম্বর (একাধিক হতে পারে)
    var serialMatches = [].concat(
        [...text.matchAll(/Sl\s*no\s*[:\-]\s*([A-Za-z0-9]+)/gi)],
        [...text.matchAll(/S\/N\s*:\s*([A-Za-z0-9]+)/gi)]
    );
    if (serialMatches.length > 0 && document.getElementById('comp_serial')) {
        var serials = serialMatches.map(function (m) { return m[1]; }).join(', ');
        document.getElementById('comp_serial').value = serials;
    }
}
