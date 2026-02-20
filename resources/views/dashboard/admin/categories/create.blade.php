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
        <div class="pt-5 border-0 card-header">
            <h3 class="card-title align-items-start flex-column">
                <span class="mb-1 card-label fw-bolder fs-3">{{$pageTitle}}</span>
                <span class="mt-1 text-muted fw-bold fs-7">{{$pageTitle}} ( {{ App\Models\Category::count() }} )</span>
            </h3>
        </div>
        <div class="py-3 card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
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
                                                " data-bs-toggle="tab" href="#{{ $key }}">{{ $lang['native'] }}</a>
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
                                    <label for="{{$key}}[name]" class="form-label">{{trans('dashboard/category.name') .
                                        ' / ' . $lang['native']}}</label>
                                    <input type="text" id="{{$key}}[name]" name="{{$key}}[name]"
                                        placeholder="{{trans('dashboard/category.category_name_placeholder') . ' / ' . $lang['native']}}"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="{{$key}}[description]"
                                        class="form-label">{{trans('dashboard/category.description') . ' / ' .
                                        $lang['native']}}</label>
                                    <textarea name="{{$key}}[description]" id="{{$key}}[description]" cols="30"
                                        rows="10" class="form-control"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="{{$key}}[short_description]"
                                        class="form-label">{{trans('dashboard/category.short_description') . ' / ' .
                                        $lang['native']}}</label>
                                    <textarea name="{{$key}}[short_description]" id="{{$key}}[short_description]"
                                        cols="30" rows="10" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label for="status" class="form-label">{{trans('dashboard/category.status')}}</label>
                        <select name="status" id="status" class="form-select">
                            <option value="active">نشط</option>
                            <option value="inactive">غير نشط</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="form-check form-switch form-check-custom form-check-solid mt-4">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" />
                            <label class="form-check-label" for="is_featured">
                                قسم مميز
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for="parent_id">التصنيفات</label>
                        <select id="parent_id" name="parent_id" class="form-control">
                            <option value="">اختر التصنيف</option>
                            @forelse($categories as $category)
                            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                            @empty
                            <option value="">لا توجد تصنيفات متاحة</option>
                            @endforelse
                        </select>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div class="p-3 mb-3 text-center border rounded">
                            <label for="image" class="form-label fw-bold">الصوره</label>
                            <input class="form-control" type="file" name="category" id="categoryInput" accept="image/*">
                            <div class="mt-2">
                                <img id="categoryPreview" src="" alt="" width="100" style="cursor: pointer;"
                                    onclick="openImageModal(this.src, 'الصوره')">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="imageModalLabel">عرض الصورة</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="text-center modal-body">
                                <img id="popupImage" src="" class="rounded img-fluid"
                                    style="max-width: 100%; max-height: 80vh;">
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <button type="submit" class="btn btn-success w-100">حفظ</button>
            </form>
        </div>
    </div>
    @endsection

    @push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        function previewImage(inputId, previewId) {
    let input = document.getElementById(inputId);
    let preview = document.getElementById(previewId);

    input.addEventListener("change", function () {
        let file = input.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = "block";
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = "";
            preview.style.display = "none";
        }
    });
}
function openImageModal(src, title) {
    if (src) {
        let popupImage = document.getElementById("popupImage");
        let modalTitle = document.getElementById("imageModalLabel");
        popupImage.src = src;
        modalTitle.innerText = title;
        let imageModal = new bootstrap.Modal(document.getElementById("imageModal"));
        imageModal.show();
    }
}
previewImage("categoryInput", "categoryPreview");
    </script>
    @endpush

