<!-- EDIT SERVICE RECORD MODAL -->
<div id="editRecordModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-2xl w-full p-6 relative animate-fade-in my-8">
        
        <!-- Modal Header -->
        <div class="flex justify-between items-center border-b pb-4 mb-4">
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span>✏️ সার্ভিস রেকর্ড সম্পাদনা করুন</span>
                <span id="editModalRecordId" class="text-xs bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full font-mono font-bold"></span>
            </h3>
            <button onclick="closeEditModal()" type="button" class="text-gray-400 hover:text-gray-600 text-2xl font-bold p-1 rounded-lg hover:bg-gray-100 leading-none">&times;</button>
        </div>

        <!-- Modal Form -->
        <form id="editRecordForm" onsubmit="saveEditedRecord(event)" class="space-y-4">
            <input type="hidden" id="edit_record_id">
            <input type="hidden" id="edit_record_type">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- Phone -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">📞 ফোন নম্বর</label>
                    <input type="text" id="edit_phone" required class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <!-- Customer Name -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">👤 কাস্টমারের নাম</label>
                    <input type="text" id="edit_name" required class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <!-- Product / Model -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">💻 প্রোডাক্ট ও মডেল</label>
                    <input type="text" id="edit_product" required class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <!-- Serial Number -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">🏷️ সিরিয়াল নম্বর (S/N)</label>
                    <input type="text" id="edit_serial" class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <!-- Address -->
                <div id="edit_address_container">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">📍 ঠিকানা (Address)</label>
                    <input type="text" id="edit_address" class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <!-- Pincode -->
                <div id="edit_pincode_container">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">📮 পিন কোড (Pincode)</label>
                    <input type="text" id="edit_pincode" class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <!-- Vendor -->
                <div id="edit_vendor_container" class="hidden">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">🚚 ভেন্ডর / সেন্টার নাম</label>
                    <input type="text" id="edit_vendor" class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <!-- Cost / Final Amount -->
                <div id="edit_cost_container">
                    <label id="edit_cost_label" class="block text-xs font-semibold text-gray-700 mb-1">💰 ফাইনাল অ্যামাউন্ট (₹)</label>
                    <input type="text" id="edit_cost" class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <!-- Date -->
                <div>
                    <label id="edit_date_label" class="block text-xs font-semibold text-gray-700 mb-1">📅 তারিখ</label>
                    <input type="date" id="edit_date" class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">📊 স্ট্যাটাস</label>
                    <select id="edit_status" class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none bg-white">
                        <option value="Pending">পেন্ডিং (Pending)</option>
                        <option value="Engineer Visited">ইঞ্জিনিয়ার ভিজিটেড</option>
                        <option value="Completed">কমপ্লিট (Completed)</option>
                        <option value="Canceled">ক্যান্সেল (Canceled)</option>
                    </select>
                </div>

            </div>

            <!-- Remarks -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">📝 রিমার্কস (Remarks)</label>
                <textarea id="edit_remarks" rows="2" class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-400 outline-none" placeholder="অতিরিক্ত মন্তব্য..."></textarea>
            </div>

            <!-- Modal Buttons -->
            <div class="flex justify-end gap-3 pt-3 border-t">
                <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 rounded-xl border border-gray-300 font-semibold text-gray-700 hover:bg-gray-100 text-sm transition">
                    বাতিল
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md transition flex items-center gap-2">
                    💾 আপডেট করুন
                </button>
            </div>
        </form>
    </div>
</div>
