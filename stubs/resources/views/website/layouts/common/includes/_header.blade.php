<!-- شريط التحذير -->
<div class="bg-red-600 text-white py-2 overflow-hidden relative">
    <div class="marquee flex whitespace-nowrap">
        <span class="mx-4">تحذير: لا يوجد أرقام أو صفحات أو مواقع غير هذا. رقمنا: {{ \App\Support\WhatsApp::display() }} | صفحة انستا:
            KING2GAME.COM | متجرنا: KING2GAME.COM</span>
        <span class="mx-4">تحذير: لا يوجد أرقام أو صفحات أو مواقع غير هذا. رقمنا: {{ \App\Support\WhatsApp::display() }} | صفحة انستا:
            KING2GAME.COM | متجرنا: KING2GAME.COM</span>
    </div>
</div>

<!-- الناف بار -->
<nav class="bg-black text-white sticky top-0 z-50 shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-row-reverse justify-between items-center h-10">
            <div class="flex-shrink-0 flex items-center">
               <a href="{{route('home')}}" >
                <img class="h-8 w-auto" src="{{ $logo}}" alt="{{ $settings?->name }}">
                      <a>
             
             
            </div>
            <div class="hidden md:flex space-x-4 items-center">
                <a href="{{route('home')}}" class="text-white hover:text-yellow-400 font-medium">الرئيسية</a>
                <a href="#" class="text-white hover:text-yellow-400 font-medium">المنتجات</a>
                <a href="#" class="text-white hover:text-yellow-400 font-medium">العروض</a>
                @php($whatsappHref = \App\Support\WhatsApp::href())
                @if($whatsappHref)
                    <a href="{{ $whatsappHref }}" target="_blank" rel="noopener noreferrer" class="text-white hover:text-yellow-400 font-medium">تواصل معنا</a>
                @endif
            </div>
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-button" class="text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div class="md:hidden hidden px-2 pt-2 pb-3 space-y-1" id="mobile-menu">
        <a href="#" class="block text-white px-3 py-2 rounded hover:bg-gray-700">الرئيسية</a>
        <a href="#" class="block text-white px-3 py-2 rounded hover:bg-gray-700">المنتجات</a>
        <a href="#" class="block text-white px-3 py-2 rounded hover:bg-gray-700">العروض</a>
        @php($whatsappHref = \App\Support\WhatsApp::href())
        @if($whatsappHref)
            <a href="{{ $whatsappHref }}" target="_blank" rel="noopener noreferrer" class="block text-white px-3 py-2 rounded hover:bg-gray-700">تواصل معنا</a>
        @endif
    </div>
</nav>
