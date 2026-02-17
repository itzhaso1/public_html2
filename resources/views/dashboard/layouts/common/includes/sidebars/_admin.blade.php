<div class="sidebar">
    <div class="menu-item">
        <div class="pb-2 menu-content">
            <span class="menu-section text-muted text-uppercase fs-8 ls-1">
                {{ 'ADMIN | ' . ($settings?->name ?? '') }}
            </span>
        </div>
    </div>

    <!-- Dashboard -->
    <div class="menu-item">
        <a class="menu-link {{ is_active('admin.dashboard') }}" href="{{ route('admin.dashboard') }}">
            <span class="menu-icon"><i class="bi bi-house fs-2"></i></span>
            <span class="menu-title">لوحة التحكم</span>
        </a>
    </div>

    <!-- Products & Payments & Codes -->
    <div data-kt-menu-trigger="click"
         class="menu-item menu-accordion {{ is_active('admin.products.*') || is_active('admin.manual_payments.*') || is_active('admin.diamond_codes.*') }}">
        <span class="menu-link {{ is_active('admin.products.*') || is_active('admin.manual_payments.*') || is_active('admin.diamond_codes.*') }}">
            <span class="menu-icon"><i class="bi bi-box-seam fs-2"></i></span>
            <span class="menu-title">المتجر</span>
            <span class="menu-arrow"></span>
        </span>
        <div class="menu-sub menu-sub-accordion menu-active-bg">
            <div class="menu-item">
                <a class="menu-link {{ is_active('admin.products.index') }}" href="{{ route('admin.products.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">المنتجات</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ is_active('admin.products.create') }}" href="{{ route('admin.products.create') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">إضافة منتج</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ is_active('admin.sections.*') }}" href="{{ route('admin.sections.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">أقسام الصفحة الرئيسية</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ is_active('admin.categories.*') }}" href="{{ route('admin.categories.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">التصنيفات (الأقسام)</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ is_active('admin.products.create_charge') }}" href="{{ route('admin.products.create_charge') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">إضافة منتج شحن</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ is_active('admin.manual_payments.*') }}" href="{{ route('admin.manual_payments.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">طلبات الدفع اليدوي</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ is_active('admin.diamond_codes.index') }}" href="{{ route('admin.diamond_codes.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">مخزون أكواد ملابس</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ is_active('admin.diamond_codes.create') }}" href="{{ route('admin.diamond_codes.create') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">إضافة أكواد</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Users -->
    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ is_active('admin.user.*') }}">
        <span class="menu-link {{ is_active('admin.user.*') }}">
            <span class="menu-icon"><i class="bi bi-people fs-2"></i></span>
            <span class="menu-title">المستخدمين</span>
            <span class="menu-arrow"></span>
        </span>
        <div class="menu-sub menu-sub-accordion menu-active-bg">
            <div class="menu-item">
                <a class="menu-link {{ is_active('admin.user.index') }}" href="{{ route('admin.user.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">قائمة المستخدمين</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Admins -->
    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ is_active('admin.admins.*') }}">
        <span class="menu-link {{ is_active('admin.admins.*') }}">
            <span class="menu-icon"><i class="bi bi-shield-lock fs-2"></i></span>
            <span class="menu-title">المديرين</span>
            <span class="menu-arrow"></span>
        </span>
        <div class="menu-sub menu-sub-accordion menu-active-bg">
            <div class="menu-item">
                <a class="menu-link {{ is_active('admin.admins.index') }}" href="{{ route('admin.admins.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">قائمة المديرين</span>
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link {{ is_active('admin.admins.create') }}" href="{{ route('admin.admins.create') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">إضافة مدير</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Settings -->
    <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ is_active('admin.mainSettings.*') }}">
        <span class="menu-link {{ is_active('admin.mainSettings.*') }}">
            <span class="menu-icon"><i class="bi bi-gear fs-2"></i></span>
            <span class="menu-title">الإعدادات</span>
            <span class="menu-arrow"></span>
        </span>
        <div class="menu-sub menu-sub-accordion menu-active-bg">
            <div class="menu-item">
                <a class="menu-link {{ is_active('admin.mainSettings.index') }}" href="{{ route('admin.mainSettings.index') }}">
                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                    <span class="menu-title">الإعدادات العامة</span>
                </a>
            </div>
        </div>
    </div>
</div>

