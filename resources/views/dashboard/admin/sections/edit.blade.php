@extends('dashboard.layouts.master')

@section('pageTitle')
تعديل القسم:
@endsection

@section('content')
<div id="kt_content_container" class="container-xxl">
    <div class="mb-5 card card-xxl-stretch mb-xl-8">
        <!--begin::Header-->
        <div class="pt-5 border-0 card-header">
            <h3 class="card-title align-items-start flex-column">
                <span class="mb-1 card-label fw-bolder fs-3">{{'تعديل القسم: ' . $section?->name}}</span>
            </h3>
        </div>
        <!--end::Header-->

        <!--begin::Form-->
        <div class="py-3 card-body">
            <form action="{{ route('admin.sections.update', $section->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="container p-4 mt-2 bg-white rounded shadow">
                    <div class="row">
                        <div class="mb-5 hover-scroll-x">
                            <div class="d-grid">
                                <ul class="nav nav-tabs flex-nowrap text-nowrap">
                                    @foreach(config('laravellocalization.supportedLocales') as $key => $lang)
                                    <li class="nav-item">
                                        <a class="nav-link @if(app()->getLocale() == $key) active @endif" data-bs-toggle="tab"
                                            href="#{{ $key }}">
                                            {{ $lang['native'] }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content">
                            @foreach(config('laravellocalization.supportedLocales') as $key => $lang)
                            <div class="tab-pane fade @if($loop->first) show active @endif" id="{{ $key }}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="{{ $key }}[name]" class="form-label">
                                            {{ trans('dashboard/category.name') . ' / ' . $lang['native'] }}
                                        </label>
                                        <input type="text" name="{{ $key }}[name]" class="form-control"
                                            value="{{ $section->translate($key)->name ?? '' }}" required>
                                    </div>
                                    <div class="col-md-8">
                                        <label for="{{ $key }}[description]" class="form-label">
                                            {{ trans('dashboard/category.description') . ' / ' . $lang['native'] }}
                                        </label>
                                        <textarea name="{{ $key }}[description]" class="form-control" rows="5">
                                                {{ $section->translate($key)->description ?? '' }}
                                            </textarea>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <label for="product_ids" class="form-label">اختر المنتجات</label>
                            <select name="product_ids[]" id="product_ids" class="form-select" multiple>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ $section->products->contains($product->id) ? 'selected' : ''
                                    }}>
                                    {{ $product->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="category_ids" class="form-label">اختر التصنيفات</label>
                            <select name="category_ids[]" id="category_ids" class="form-select" multiple>
                                <option value="">-- بدون تصنيف --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ in_array($category->id, $section->categories->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="order" class="form-label">ترتيب القسم</label>
                            <select class="form-select" name="order" id="order">
                                <option value="0">-- افتراضي --</option>
                                @foreach($orders as $order)
                                <option value="{{ $order }}" {{ $section->order == $order ? 'selected' : '' }}>
                                    {{ $order }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3 w-100">تحديث</button>
            </form>
        </div>
        <!--end::Form-->
    </div>
</div>
@endsection
@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
            $('#product_ids').select2({
                placeholder: "اختر المنتجات",
                allowClear: true,
                dropdownParent: $('#product_ids').parent(),
                width: '100%'
            });
            $('#category_ids').select2({
                placeholder: "اختر التصنيف",
                allowClear: true,
                dropdownParent: $('#category_ids').parent(),
                width: '100%'
            });
        });
</script>
@endpush

