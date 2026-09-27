<!-- Print Money Receipt Modal -->
<div id="printModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4 z-50">
    <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl p-6 relative">
        <button onclick="closePrintModal()" class="absolute top-3 right-3 text-gray-500 hover:text-black font-bold text-xl">&times;</button>

        <!-- Printable Area -->
        <div id="printableReceipt" class="p-4 border rounded-lg bg-white text-black font-sans">
            <div class="text-center border-b pb-3 mb-3">
                <h2 class="text-xl font-bold tracking-wide">ICON COMPUTER</h2>
                <p class="text-xs">Bethuadahari, Nadia | Ph: 8597753337</p>
                <p class="text-xs font-semibold text-gray-600 mt-1">SERVICE & REPAIR RECEIPT</p>
            </div>
            <div class="text-xs space-y-1.5 mb-4">
                <div class="flex justify-between"><span><strong>Receipt No:</strong> <span id="pr_id"></span></span><span><strong>Date:</strong> <span id="pr_date"></span></span></div>
                <p><strong>Customer Name:</strong> <span id="pr_name"></span></p>
                <p><strong>Phone:</strong> <span id="pr_phone"></span></p>
                <p><strong>Address:</strong> <span id="pr_address"></span></p>
                <p><strong>Product/Issue:</strong> <span id="pr_product"></span></p>
                <p><strong>Serial No:</strong> <span id="pr_serial"></span></p>
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
            <button onclick="closePrintModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-xs font-bold rounded-lg">বন্ধ করুন</button>
            <button onclick="triggerPrint()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow">🖨️ প্রিন্ট আউট</button>
        </div>
    </div>
</div>
