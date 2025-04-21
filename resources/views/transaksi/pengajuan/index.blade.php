@extends('layout.body')
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">
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
    #tabungan {
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
    .ui-datepicker {
        z-index: 1051 !important;
    }
    .product-title {
        font-style: bold;
    }
</style>
@endsection
@section('content')
<div class="az-content-breadcrumb">
    <span>Tabungan</span>
    <span>Data Peserta Tabungan</span>
</div>

<div class="az-content-label mg-b-5">Daftar Seluruh Tabungan Peserta Yayasan Persada Hati</div>
<br>
<div class="table-card">
<div class="row grid-margin">
    <div class="col-12">
        <button class="btn btn-secondary" type="button" data-toggle="collapse" data-target="#collapseMigrasi" 
            aria-expanded="false" aria-controls="collapseMigrasi"><i class="typcn typcn-upload"></i>
            Migrasi Data
        </button>
        <div class="collapse mt-2" id="collapseMigrasi">
            <div class="card card-body">
                <a class="custom-link" id="migratedataanak"><i class="typcn typcn-upload"></i> Peserta Tabungan</a>
                <a class="custom-link" id="migratesaldoanak"><i class="typcn typcn-upload"></i> Saldo Peserta Tabungan</a>
            </div>
        </div>
        <button class="btn btn-secondary" id="generateSemester" type="button"><i class="typcn typcn-input-checked"></i>
            Tambah Saldo Per Semester
        </button>
    </div>
</div>

    <table id="tabungan" class="table table-bordered mg-b-0">
        <thead>
            <th class="wd-10p text-center">No. </th>
            <th class="wd-15p text-left">Nama Anak</th>
            <th class="wd-15p text-left">Nama Orangtua</th>
            <th class="wd-20p text-left">Company</th>
            <th class="wd-20p text-left">Jenjang Pendidikan</th>
            <th class="wd-20p text-left">Nama Sekolah</th>
            <th class="wd-20p text-right">Saldo</th>
        </thead>
    </table>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Data Diri <span id="namaanak"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row row-xs">
                    <div class="col-md-6">
                        <label for="nama_anak">Nama Anak</label>
                        <input type="text" name="nama_anak" id="nama_anak" class="form-control" placeholder="Nama Anak" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="namasekolah">Nama Sekolah</label>
                        <input type="text" name="namasekolah" id="namasekolah" class="form-control" placeholder="Nama Sekolah" readonly>
                    </div>
                </div>
                <br>
                <div class="row row-xs">
                    <div class="col-md-6">
                        <label for="tempatlahir">Tempat Lahir</label>
                        <input type="text" name="tempatlahir" id="tempatlahir" class="form-control" placeholder="Tempat Lahir" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="tgllahir">Tanggal Lahir</label>
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                            </div>
                                <input type="text" name="tgllahir" id="tgllahir" class="form-control fc-datepicker" placeholder="Tanggal Lahir" readonly>
                        </div>
                    </div>
                </div>
                <hr>
                <br>
                <div class="col-lg-12">
                    <div class="row">
                        <!-- Surat Keterangan Sekolah -->
                        <div class="col-md-6 col-sm-6 text-center">
                            <div class="card">
                                <div class="card-body">
                                    <a href="" id="link_surat" download>
                                        <div id="file_surat"></div>
                                    </a>
                                    <br>
                                    <p class="product-title">Surat Keterangan Sekolah</p>
                                </div>
                            </div>
                        </div>

                        <!-- Fotocopy KTP -->
                        <div class="col-md-6 col-sm-6 text-center">
                            <div class="card">
                                <div class="card-body">
                                    <a href="" id="link_fcktp" download>
                                        <div id="file_fcktp"></div>
                                    </a>
                                    <br>
                                    <p class="product-title">Fotocopy KTP Orangtua</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Raport -->
                        <div class="col-md-6 col-sm-6 text-center">
                            <div class="card">
                                <div class="card-body">
                                    <a href="" id="link_raport" download>
                                        <div id="file_raport"></div>
                                    </a>
                                    <br>
                                    <p class="product-title">Raport</p>
                                </div>
                            </div>
                        </div>

                        <!-- Rekening -->
                        <div class="col-md-6 col-sm-6 text-center">
                            <div class="card">
                                <div class="card-body">
                                    <a href="" id="link_rekening" download>
                                        <div id="file_rekening"></div>
                                    </a>
                                    <br>
                                    <p class="product-title">Rekening</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <br>
                <h5 class="modal-title bg-success text-white p-2 rounded" id="editModalLabel">Detail Data Tabungan</h5>
                <br>
                <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                    <table id="tablesaldo" class="table table-bordered">
                        <thead class="thead-dark" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th class="text-right">Saldo Awal</th>
                                <th class="text-right">Credit</th>
                                <th class="text-right">Saldo Berjalan</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Saldo Akhir</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <br>
                <h5 class="modal-title bg-success text-white p-2 rounded" id="editModalLabel">Riwayat Pencairan</h5>
                <br>
                <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                    <table id="tablelog" class="table table-bordered">
                        <thead style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th>No.</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Nominal Yang Diajukan</th>
                                <th>Alasan Pencairan</th>
                                <th>Status</th>
                                <th>Tipe Pencairan</th>
                                <th>Dokumen Pencairan</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="addbalance" class="modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" role="document">
            <div class="modal-header">
                <h6 class="modal-title">Tambah Saldo Semester untuk <span id="namechild"></span></h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="tambahsaldo">
                    @csrf
                 <div class="modal-body">
                    <input type="hidden" name="id_anak" id="id_anak">
                    <div class="form-group">
                        <div class="row row-sm">
                            <div class="col-sm-7 col-md-6 col-lg">
                                <label for="nominal_input">Masukan Nominal</label>
                                <input type="text" class="form-control" name="nominal_input" id="nominal_input">
                            </div>
                        </div>
                        <br>
                        <div class="row row-sm">
                            <div class="col-sm-7 col-md-6 col-lg">
                                <label for="note_input">Notes</label>
                                <input type="text" class="form-control" name="note_input" id="note_input">
                            </div>
                        </div>
                    </div>
                 </div>
                <div class="modal-footer justify-content-center">
                    <button type="submit" class="btn btn-indigo">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- modal untuk migrasi data -->
<div class="modal fade" id="migrateData" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editMigrate"></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form id="addMigrate">
                    <div class="form-group">
                        <label class="font-weight-bold" for="new_excel_file">
                            Klik <a id="exportLink" class="text-primary font-weight-bold">di sini</a> untuk mendapat format migrasi 
                            <span id="varmigrate" class="text-danger"></span>
                        </label>
                        <div class="custom-file mt-3">
                            <input type="file" class="custom-file-input" id="addFileMigrate" name="addFileMigrate">
                            <label class="custom-file-label" for="addFileMigrate">Pilih file</label>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button id="migrateDataAnakBtn" class="btn btn-success px-4" style="display: none;">
                            <i class="fas fa-check"></i> Simpan
                        </button>
                        <button id="migrateDataSaldo" class="btn btn-success px-4" style="display: none;">
                            <i class="fas fa-check"></i> Simpan
                        </button>
                        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ asset('assets/master/lib/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js"></script>
<script src="{{ asset('assets/master/lib/jquery/jquery.masknumber.js') }}"></script>
<script src="{{ asset('assets/master/lib/jquery/jquery.masknumber.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/master/lib/jquery-ui/ui/widgets/datepicker.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>

<script>
    var datatable = $('#tabungan').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ordering: true,
        ajax: {
            url: '{!! url()->current() !!}'
        },
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function () {
                    return ''; // Akan diisi manual di rowCallback
                }
            },
            {
                data: 'nama',
                name: 'nama',
                render: function(data, type, row) {
                    return `<a id="show" data-id="${row.id}" class="custom-link">${data}</a>`;
                }
            },
            {
                data: 'karyawan.name',
                name: 'karyawan.name',
                className: 'dt-left'
            },
            {
                data: 'karyawan.company.name',
                name: 'karyawan.company.name',
                className: 'dt-left'
            },
            {
                data: 'program.level',
                name: 'program.level'
            },
            {
                data: 'nama_sekolah',
                name: 'nama_sekolah'
            },
            {
                data: 'running_balance',
                name: 'running_balance',
                className: 'dt-right',
                render: function(data, type, row) {
                    let balance = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2 }).format(data);
                    return `<a id="balance" data-id="${row.id}" class="custom-link">${balance}</a>`;
                }
            }
        ],
        rowCallback: function(row, data, displayIndex) {
            var pageInfo = datatable.page.info();
            var nomor = pageInfo.start + displayIndex + 1;
            $('td:eq(0)', row).html(nomor); // Isi nomor di kolom pertama
        },
        dom: '<"d-flex justify-content-between"<"d-flex"B><"ml-auto"f>>rtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Daftar Peserta Tabungan Yayasan Persada Hati',
            },
            'copy',
            'pdf',
            'print'
        ]
    });

</script>
<script>

    function formatNumber(value) {
        if (!value) return "0"; // Jika tidak ada nilai, kembalikan "0"
        return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
    }

    function formatTanggal(dateString) {
        let date = new Date(dateString);
        return new Intl.DateTimeFormat('id-ID', {
            year: 'numeric', 
            month: 'long', 
            day: 'numeric'
        }).format(date);
    }

    $('body').on('click', '#show', function (){
        var id = $(this).data('id')

        $.ajax({
            url: "/get/pengajuan/" + id,
            type: 'GET',
            success: function (response) {
                $('#namaanak').text(response.nama);
                $('#nama_anak').val(response.nama)
                $('#namasekolah').val(response.nama_sekolah)
                $('#tempatlahir').val(response.tempat_lahir)
                // Format ulang tanggal sebelum ditampilkan di datepicker
                let formattedDate = $.datepicker.formatDate('dd-mm-yy', new Date(response.tgl_lahir));
                $('#tgllahir').val(formattedDate);  // Isi input field
                $('#tgllahir').datepicker("setDate", formattedDate); // Set tanggal ke datepicker
                let basePath = "/upload/";
                
                function displayFile(filePath, containerId, linkId) {
                    if (!filePath) {
                        $(containerId).html('<p class="text-muted">Tidak ada file</p>');
                        $(linkId).attr('href', "#");
                        return;
                    }

                    let fullPath = basePath + filePath;
                    let fileExtension = filePath.split('.').pop().toLowerCase();

                    $(linkId).attr('href', fullPath); // Set link download

                    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                        $(containerId).html(`<img src="${fullPath}" class="img-fluid rounded shadow" alt="File">`);
                    } else if (fileExtension === 'pdf'){
                        $(containerId).html(`<embed src="${fullPath}" type="application/pdf" width="100%" height="200px">`);
                    }else{
                        $(containerId).html('<p class="text-muted">Format tidak didukung</p>');
                    }
                }

                // Set file untuk masing-masing dokumen
                displayFile(response.surat_sekolah, "#file_surat", "#link_surat");
                displayFile(response.fc_ktp, "#file_fcktp", "#link_fcktp");
                displayFile(response.fc_raport, "#file_raport", "#link_raport");
                displayFile(response.fc_rek_sekolah, "#file_rekening", "#link_rekening");


                // Bersihkan tabel saldo sebelum menambahkan data baru
                $('#tablesaldo tbody').empty();

                if (response.transaction.length > 0) {
                    $.each(response.transaction, function (index, transaction) {
                        $('#tablesaldo tbody').append(`
                            <tr>
                                <td class="text-right">${transaction.previous_balance.toLocaleString()}</td>
                                <td class="text-right">${transaction.credit.toLocaleString()}</td>
                                <td class="text-right">${transaction.running_balance.toLocaleString()}</td>
                                <td class="text-right">${transaction.debit.toLocaleString()}</td>
                                <td class="text-right">${transaction.final_balance.toLocaleString()}</td>
                                <td>${transaction.notes}</td>
                            </tr>
                        `)
                    })
                }else {
                    $('#tablesaldo tbody').append(`
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada transaksi</td>
                        </tr>
                    `);
                }

                $('#tablelog tbody').empty();

                let statusText = ''

                if (response.reqpproval.length > 0) {
                    $.each(response.reqpproval, function (index, reqpproval) {
                        let typeText = '';
                        let statusText = '';
                        let statusClass = '';
                        let btndwnload = '';

                        if (reqpproval.status == 0) {
                            statusText = 'New';
                            statusClass = 'badge-primary'; // Biru
                        } else if (reqpproval.status == 1) {
                            statusText = 'Approved';
                            statusClass = 'badge-success'; // Hijau
                        } else if (reqpproval.status == 2) {
                            statusText = 'Rejected';
                            statusClass = 'badge-danger'; // Merah
                        }

                        if (reqpproval.isreimburst == 0) {
                            typeText = 'Cash Advance'
                        }else{
                            typeText = 'Reimburse'
                        }

                        if (reqpproval.file != null && reqpproval.file != '') {
                            btndwnload = `<a href="#" class="show-file" data-file="${reqpproval.file}" target="_blank">Lihat File</a>`;
                        } else {
                            btndwnload = 'Tidak ada file';
                        }

                        $('#tablelog tbody').append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${formatTanggal(reqpproval.created_at)}</td>
                                <td>${reqpproval.nominal.toLocaleString()}</td>
                                <td>${reqpproval.reason}</td>
                                <td><span class="badge ${statusClass} p-2 rounded">${statusText}</span></td>
                                <td>${typeText}</td>
                                <td>${btndwnload}</td>
                                <td>${reqpproval.notes}</td>
                            </tr>
                        `)
                    })
                }else{
                    $('#tablelog tbody').append(`
                        <tr>
                            <td colspan="8" class="text-center">Belum Mengajukan Pencairan</td>
                        </tr>
                    `)
                }

                $('#editModal').modal('show');
            }
        })
    })

    $('body').on('click', '.show-file', function (e) {
        e.preventDefault(); // Mencegah link membuka halaman kosong
        let filePath = $(this).data('file');
        let basePath = "/upload/";
        
        if (filePath) {
            window.open(basePath + filePath, '_blank'); // Buka file di tab baru
        } else {
            alert("File tidak tersedia");
        }
    });


    $('body').on('click', '#balance', function () {
        var id = $(this).data('id')

        $.ajax({
            url: "/get/pengajuan/" + id,
            type: 'GET',
            success: function (response){
                $('#id_anak').val(id);    
                $('#nominal_input').val(response.program.total.toLocaleString());    
                $('#namechild').text(response.nama);    
            }
        })

        $('#addbalance').modal('show');
    })

    $('#nominal_input').maskNumber({integer: true});

    function cleanNumber(value) {
        if (!value) return "0"; // Jika kosong, kembalikan "0"
        return value.replace(/[^\d]/g, ''); // Hapus semua karakter selain angka
    }
    
    $('#tambahsaldo').submit(function(e) {
        e.preventDefault();
        var anak_id = $('#id_anak').val();
        var nominal = cleanNumber($('#nominal_input').val());
        var notes = $('#note_input').val();

        $.ajax({
            url: "/get/pengajuan/" + anak_id,
            type: "PUT",
            data: {
                _token: "{{ csrf_token() }}",
                _method: "PUT",
                nominaltotal: nominal,
                notesdescript: notes
            },
            success: function(response) {
                Swal.fire({
                    title: "Berhasil!",
                    text: response.message || "Data berhasil diupdate!",
                    icon: "success"
                }).then(() => {
                    // Tutup modal sebelum reload DataTable
                    $('#addbalance').modal('hide');

                    setTimeout(function() {
                        // Cek apakah DataTable sudah diinisialisasi sebelum reload
                        if ($.fn.DataTable.isDataTable('#tabungan')) {
                            $('#tabungan').DataTable().ajax.reload();
                        }
                        if ($.fn.DataTable.isDataTable('#tablesaldo')) {
                            $('#tablesaldo').DataTable().ajax.reload();
                        }
                        if ($.fn.DataTable.isDataTable('#tablelog')) {
                            $('#tablelog').DataTable().ajax.reload();
                        }
                    }, 500); // Beri jeda agar modal tertutup dengan sempurna
                });
            },
            error: function(xhr) {
                let errorMessage = "Terjadi kesalahan pada server!";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    title: "Error!",
                    text: errorMessage,
                    icon: "error"
                });

                console.log(xhr.responseText); // Debugging
            }
        });
    });

    $('body').on('click', '#migratedataanak', function () {
        $('#editMigrate').text('Migrasi Data Peserta Tabungan'); // Ubah teks modal-title
        $('#varmigrate').text('Migrasi Data Peserta Tabungan'); // Ubah teks modal-title
        $('#migrateDataAnakBtn').show()
        $('#migrateDataSaldo').hide()
        $('a#exportLink').attr('href', '/get-anak-format');
        $('#migrateData').modal('show'); // Tampilkan modal
    });

    $('body').on('click', '#migratesaldoanak', function () {
        $('#editMigrate').text('Migrasi Saldo Peserta Tabungan'); // Ubah teks modal-title
        $('#varmigrate').text('Migrasi Saldo Peserta Tabungan'); // Ubah teks modal-title
        $('#migrateDataAnakBtn').hide()
        $('#migrateDataSaldo').show()
        $('a#exportLink').attr('href', '/get-saldo-format');
        $('#migrateData').modal('show'); // Tampilkan modal
    });

    $('#migrateDataAnakBtn').on('click', function(e){
        e.preventDefault(); 
        var formData = new FormData();
        formData.append('file', $('#addFileMigrate')[0].files[0]); // Ambil file dari input
        formData.append('_token', '{{ csrf_token() }}'); // Tambahkan token CSRF
        $.ajax({
            url : "/post-anak-format",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire("Berhasil!", response.message, "success"); // Notifikasi sukses
                $('#migrateData').modal('hide'); // Tutup modal
                $('#tabungan').DataTable().ajax.reload(); // Refresh DataTable
            },
            error: function(xhr) {
                Swal.fire("Gagal!", xhr.responseJSON?.message || "Terjadi kesalahan saat mengupload file.", "error");
                console.log(xhr.responseText);
            }
        })
    })
    
    $('#migrateDataSaldo').on('click', function(e){
        e.preventDefault(); 
        var formData = new FormData();
        formData.append('file', $('#addFileMigrate')[0].files[0]); // Ambil file dari input
        formData.append('_token', '{{ csrf_token() }}'); // Tambahkan token CSRF
        $.ajax({
            url : "post-saldo-anak-format",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire("Berhasil!", response.message, "success"); // Notifikasi sukses
                $('#migrateData').modal('hide'); // Tutup modal
                $('#tabungan').DataTable().ajax.reload(); // Refresh DataTable
            },
            error: function(xhr) {
                Swal.fire("Gagal!", xhr.responseJSON?.message || "Terjadi kesalahan saat mengupload file.", "error");
                console.log(xhr.responseText);
            }
        })
    })

    $('#generateSemester').on('click', function(e){
        e.preventDefault();
        let today = new Date();
        var formattedDate = today.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });

        Swal.fire({
            title: 'Apa Kamu Yakin?',
            text: "Untuk mengenerate skor kredit semester semua anak per tanggal " + formattedDate + " ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, lanjutkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("transactions.generate") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        Swal.fire('Berhasil!',response.message,'success')
                        $('#tabungan').DataTable().ajax.reload(); // Refresh DataTable
                    },
                    error: function (xhr) {
                        Swal.fire( 'Gagal!', 'Terjadi kesalahan saat membuat transaksi.', 'error' )
                        console.log(xhr.responseText);
                    }
                })
            }
        })
    })

</script>
<script>
    $(function(){'use strict'});
</script>
@endsection