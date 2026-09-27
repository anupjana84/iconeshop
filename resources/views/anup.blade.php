@include('layouts.guestLayout.headder')

@include('layouts.guestLayout.navbar')

<div class="mt-16">

    <form>
        @csrf

        <label for="phone">Phone Number</label>

        <input
            id="phone"
            name="phone"
            type="text"
            maxlength="10"
            inputmode="numeric"
            autocomplete="tel"
            placeholder="Enter 10 digit phone number"
        />

        <p id="phoneMessage" class="mt-2"></p>
    </form>

</div>

<script>
    const phoneInput = document.getElementById('phone');
    const phoneMessage = document.getElementById('phoneMessage');

    phoneInput.addEventListener('input', async function () {

        // Only allow numbers
        this.value = this.value.replace(/\D/g, '');

        const phone = this.value;

        // Clear message if less than 10 digits
        if (phone.length < 10) {
            phoneMessage.textContent = '';
            return;
        }

        // Only check when exactly 10 digits
        if (phone.length === 10) {

            phoneMessage.textContent = 'Checking...';

            try {

                const response = await fetch("{{ route('guest.checkPhone') }}", {
                    method: "POST",

                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },

                    body: JSON.stringify({
                        phone: phone
                    })
                });

                const data = await response.json();
                // console.log(data)

                if (data.exists) {

                    phoneMessage.textContent =
                        "Phone number already exists.";

                    phoneMessage.classList.add('text-red-500');

                } else {

                    phoneMessage.textContent =
                        "Phone number is available.";

                    phoneMessage.classList.remove('text-red-500');
                    phoneMessage.classList.add('text-green-500');
                }

            } catch (error) {

                console.error(error);

                phoneMessage.textContent =
                    "Unable to check phone number.";

            }
        }
    });
</script>

@yield('content_page')

@stack('style_link')
@stack('extra_style')
@stack('page_title')
@stack('extra_js')

@include('layouts.guestLayout.footer')