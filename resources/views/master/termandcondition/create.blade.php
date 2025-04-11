@extends('layout.body')
@section('css')
<link href="{{ asset('assets/master/lib/line-awesome/css/line-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/quill/quill.snow.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/quill/quill.bubble.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="az-content-breadcrumb">
    <span>Master</span>
    <span>Term And Condition</span>
</div>

<!-- Form -->
<form id="quillForm">
    @csrf
    @method('PUT')
    <div class="wd-xl-90p ht-400">
        <div id="scrolling-container" class="ql-scrolling-demo">
            <div id="quillInline"></div>
        </div>
    </div>
    <!-- Input Hidden untuk menyimpan isi Quill -->
    <input type="hidden" name="text" id="quill_content">
    <button type="submit" class="btn btn-primary mt-3">Simpan</button>
</form>

<!-- Notifikasi -->
<div id="successMessage" class="alert alert-success mt-3 d-none">Data berhasil disimpan!</div>
<div id="errorMessage" class="alert alert-danger mt-3 d-none">Gagal menyimpan data.</div>

@endsection

@section('script')
<script src="{{ asset('assets/master/lib/quill/quill.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        'use strict';

        var toolbarInlineOptions = [
            ['bold', 'italic', 'underline'],
            [{ 'header': 1 }, { 'header': 2 }, 'blockquote'],
        ];

        var quillInline = new Quill('#quillInline', {
            modules: {
                toolbar: toolbarInlineOptions
            },
            bounds: '#quillInline',
            scrollingContainer: '#scrolling-container',
            placeholder: 'Write something...',
            theme: 'bubble'
        });

        new PerfectScrollbar('#scrolling-container', {
            suppressScrollX: true
        });

        // **Set Isi Awal Quill dari Database**
        quillInline.root.innerHTML = `{!! $term ? addslashes($term->text) : '' !!}`;

        // **AJAX Submit**
        $('#quillForm').on('submit', function (e) {
            e.preventDefault();

            let content = quillInline.root.innerHTML;
            let token = $('input[name="_token"]').val();

            $.ajax({
                url: "{{ route('termandcondition.store') }}",
                type: "PUT",
                data: {
                    _token: token,
                    text: content
                },
                success: function (response) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat menyimpan data.',
                        icon: 'error',
                        confirmButtonText: 'Coba Lagi'
                    });
                }
            });
        });
    });
</script>

@endsection
