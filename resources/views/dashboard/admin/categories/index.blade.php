@extends('dashboard.layouts.master')

@section('css')
    <!-- Add any custom CSS here -->
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
                    <span class="mt-1 text-muted fw-bold fs-7">{{$pageTitle}} ( {{ App\Models\Category::count() }} )</span>
                    <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover">
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-light btn-active-primary">
                            <span class="svg-icon svg-icon-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="black" />
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="black" />
                                </svg>
                            </span>
                            اضافه تصنيف جديد
                        </a>
                        <button type="button" class="btn btn-sm btn-light btn-active-success" data-bs-toggle="modal" data-bs-target="#importModal">
                            <span class="svg-icon svg-icon-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)"
                                        fill="black" />
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="black" />
                                </svg>
                            </span>
                            استيراد تصنيفات من Excel
                        </button>
                    </div>
                </h3>
            </div>
            <!--end::Header-->

            <!--begin::Body-->
            <div class="py-3 card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-row-bordered gy-5 gs-7">
                        {!! $dataTable->table() !!}
                    </table>
                </div>

                <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('admin.categories.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="importModalLabel">استيراد تصنيفات</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="excelFile" class="form-label">ملف Excel</label>
                                        <input type="file" class="form-control" name="file" id="excelFile" required accept=".xlsx,.xls">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">استيراد</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--begin::Body-->
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
{!! $dataTable->scripts() !!}
@endpush

