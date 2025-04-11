@extends('layout.body')

@section('css')
<link href="{{ asset('assets/master/lib/datatables.net-dt/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/datatables.net-responsive-dt/css/responsive.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/master/lib/select2/css/select2.min.css') }}" rel="stylesheet">
<style>
    /* Custom Styling */
    .table-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Efek shadow untuk kartu */
        border: 1px solid black; /* Menambahkan border hitam */
        width: 100%; /* Agar kartu selebar mungkin */
    }
    
    .card-title {
        background: white;
        padding: 6px 10px;
        border-radius: 10px;
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
        border: 1px solid black;
        width: 100%;
        display: flex;
        align-items: center; /* Menyesuaikan tinggi sesuai teks */
    }

    #example2 {
        width: 100% !important; /* Paksa tabel menjadi 100% */
    }
    .custom-link {
        text-decoration: none;  /* Hilangkan garis bawah */
        color: rgb(64,64,64);           /* Warna default hitam */
    }

    .custom-link:hover, .custom-link:focus {
        color: blue;            /* Warna biru saat hover atau klik */
        text-decoration: underline;
    }

</style>
@endsection

@section('content')

<div class="az-content-breadcrumb">
    <span>Reference</span>
    <span>Company</span>
    <span>List of Employee</span>
</div>
<div class="card-title">
    <h2 class="az-content-title">Daftar Karyawan {{$company->name}}</h2>
</div>

    <div class="table-card">
        <div class="row grid-margin">
            <div class="d-sm-flex align-items-center wd-sm-400">
            </div>
        </div>
       <table id="example2" class="table">
            <thead>
               <tr>
                <th class="wd-20p">No</th>
                <th class="wd-25p">Nama</th>
                <th class="wd-25p">Kontak</th>
                <th class="wd-25p">Email</th>
                <th class="wd-25p">Status</th>
                <th class="wd-25p">Action</th>
              </tr>
            </thead>
            <tbody></tbody>
       </table>
    </div>
@endsection

@section('script')
<script src="{{ asset('assets/master/lib/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/datatables.net-dt/js/dataTables.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/master/lib/datatables.net-responsive-dt/js/responsive.dataTables.min.js') }}"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    var datatable = $('#example2').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ordering: true,
            ajax: {
                url: '{!! url()->current() !!}'
            },
            columns: [{
                "data": 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'name',
                name: 'name',
                render: function(data, type, row) {
                    return `<a href="/employee/${row.id}/edit" class="custom-link">${data}</a>`;
                }
            },
            {
                data: 'phone',
                name: 'phone'
            },
            {
                data: 'email',
                name: 'email'
            },
            {
                data: 'isactive',
                name: 'isactive',
                render: function(data, type, row) {
                    let statusClass = data ? 'text-success' : 'text-danger'; // Hijau jika aktif, merah jika non-aktif
                    let statusText = data ? 'Aktif' : 'Non-Aktif';

                    return `<a href="javascript:void(0);" class="toggle-status ${statusClass}" data-id="${row.id}" data-status="${data}">
                                ${statusText}
                            </a>`;
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                width: '15%'
            }
        ]
    });

    // Toggle status aktif/non-aktif
    $('body').on('click', '.toggle-status', function() {
        let employeeId = $(this).data('id');
        let currentStatus = $(this).data('status');

        $.ajax({
            url: `/employee/${employeeId}/toggle-status`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: !currentStatus
            },
            success: function(response) {
                Swal.fire("Berhasil!", response.message, "success");
                $('#example2').DataTable().ajax.reload(); // Refresh tabel
            },
            error: function(xhr) {
                Swal.fire("Error!", "Gagal mengubah status.", "error");
            }
        });
    });
</script>

    @if(session('success'))
        <script>
            Swal.fire({
            title: "Berhasil!",
            text: "{{ session('success') }}",
            icon: "success"
            });
       </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
            title: "Error!",
            text: "{{ session('error') }}",
            icon: "error"
            });
        </script>
    @endif
@endsection