// ================================
// print.js — মানি রিসিপ্ট প্রিন্ট মোডাল লজিক
// ================================

function printReceipt(id) {
    var item = null;
    var list = (currentTab === 'company') ? companyBookings : (currentTab === 'external') ? externalBookings : inhouseBookings;
    for (var i = 0; i < list.length; i++) {
        if (list[i].id === id) { item = list[i]; break; }
    }

    if (!item) return;

    document.getElementById('pr_id').innerText = item.id;
    document.getElementById('pr_date').innerText = item.created_at || new Date().toISOString().split('T')[0];
    document.getElementById('pr_name').innerText = item.name;
    document.getElementById('pr_phone').innerText = item.phone;
    document.getElementById('pr_address').innerText = item.address || 'N/A';
    document.getElementById('pr_product').innerText = item.product;
    document.getElementById('pr_serial').innerText = item.serial || 'N/A';
    document.getElementById('pr_amount').innerText = item.final_amount || item.final_cost || item.estimate || item.budget || '0';

    document.getElementById('printModal').classList.remove('hidden');
}

function closePrintModal() {
    document.getElementById('printModal').classList.add('hidden');
}

function triggerPrint() {
    var printContents = document.getElementById('printableReceipt').innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = '<div style="width: 350px; margin: 0 auto; font-family: sans-serif;">' + printContents + '</div>';
    window.print();
    document.body.innerHTML = originalContents;
    window.location.reload();
}
