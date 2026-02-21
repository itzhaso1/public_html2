<footer class="bg-black text-white py-6">
    <div
        class="max-w-7xl mx-auto flex flex-col md:flex-row justify-center items-center gap-6 md:gap-12 text-center px-2">
        <div>
            <h2 class="font-bold mb-1">طرق الدفع</h2>
            <div class="payment-icons flex justify-center gap-2">
                <img src="https://placehold.co/50x30/ffffff/000000/png?text=Visa" alt="Visa">
                <img src="https://placehold.co/50x30/ffffff/000000/png?text=MasterCard" alt="MasterCard">
                <img src="https://placehold.co/50x30/ffffff/000000/png?text=PayPal" alt="PayPal">
            </div>
        </div>
        <div>
            <h2 class="font-bold mb-1">التواصل معنا</h2>
            <ul>
                @php($whatsappHref = \App\Support\WhatsApp::href())
                @if($whatsappHref)
                    <li>📞 <a href="{{ $whatsappHref }}" target="_blank" rel="noopener noreferrer" class="underline">واتساب</a></li>
                @endif
                <li>📸 <a href="{{ config('contact.instagram_url') }}" target="_blank" rel="noopener noreferrer" class="underline">إنستغرام</a></li>
            </ul>
        </div>
    </div>
</footer>
