@extends('dashboard.layouts.master')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('pageTitle')
{{$pageTitle}}
@endsection

@section('content')
@include('dashboard.layouts.common._partial.messages')
<div id="kt_content_container" class="container-xxl">
    <div class="mb-5 card card-xxl-stretch mb-xl-8">
        <!--begin::Header-->
        <div class="pt-5 border-0 card-header">
            <h3 class="card-title align-items-start flex-column">
                <span class="mb-1 card-label fw-bolder fs-3">{{$pageTitle}}</span>
                <span class="mt-1 text-muted fw-bold fs-7">{{$pageTitle}}</span>
            </h3>
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="py-3 card-body">
            <form action="{{ route('admin.sections.store') }}" method="POST">
                @csrf
                <div class="container p-4 mt-2 bg-white rounded shadow">
                    <div class="row">
                        <div class="mb-5 hover-scroll-x">
                            <div class="d-grid">
                                <ul class="nav nav-tabs flex-nowrap text-nowrap">
                                    @foreach(config('laravellocalization.supportedLocales') as $key=>$lang)
                                    <li class="nav-item">
                                        <a class="nav-link
                                                                        @if(app()->getLocale() == $key)
                                                                            btn btn-active-light btn-color-gray-600 btn-active-color-success rounded-bottom-0 active
                                                                        @endif
                                                                    " data-bs-toggle="tab" href="#{{ $key }}">{{
                                            $lang['native'] }}</a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content" id="myTabContent">
                            @foreach(config('laravellocalization.supportedLocales') as $key=>$lang)
                            <div class="tab-pane fade @if($loop->index == 0) show active @endif" id="{{$key}}"
                                role="tabpanel" aria-labelledby="{{$key}}-tab">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="{{$key}}[name]"
                                            class="form-label">{{trans('dashboard/category.name') .
                                            ' / ' . $lang['native']}}</label>
                                        <input type="text" id="{{$key}}[name]" name="{{$key}}[name]"
                                            placeholder="{{trans('dashboard/category.category_name_placeholder') . ' / ' . $lang['native']}}"
                                            class="form-control" required>
                                    </div>
                                    <div class="col-md-8">
                                        <label for="{{$key}}[description]"
                                            class="form-label">{{trans('dashboard/category.description') . '
                                            / ' .
                                            $lang['native']}}</label>
                                        <textarea name="{{$key}}[description]" id="{{$key}}[description]" cols="30"
                                            rows="10" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    </br>
                    <div class="mt-4 row">
                        <div class="col-md-4">
                            <label for="product_ids" class="form-label">اختر المنتجات</label>
                            <select name="product_ids[]" id="product_ids" class="form-select" multiple>
                                @foreach(App\Models\Product::all() as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="category_ids" class="form-label">اختر التصنيفات</label>
                            <select name="category_ids[]" id="category_ids" class="form-select" multiple>
                                <option value="0">-- بدون تصنيف --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="order" class="form-label">ترتيب القسم</label>
                            <select class="form-select" name="order" id="order" required>
                                <option value="0">-- افتراضي --</option>
                                @foreach($orders as $order)
                                <option value="{{ $order }}">{{ $order }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="design_type" class="form-label">نوع التصميم</label>
                        <select name="design_type" id="design_type" class="form-select" required>
                            <option value="layout1">تصميم 1</option>
                            <option value="layout2">تصميم 2</option>
                            <option value="layout3">تصميم 3</option>
                            <option value="layout4">تصميم 4</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100">حفظ</button>
            </form>
        </div>
        <!--begin::Body-->
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

