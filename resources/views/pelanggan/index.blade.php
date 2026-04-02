@extends('template.app')
@section('title', 'Data Pelanggan')
@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Pelanggan</h5>
                <button type="button" class="btn btn-primary" id="btnTambahPelanggan" data-bs-toggle="modal"
                    data-bs-target="#modalPelanggan">
                    <i class="bx bx-plus me-1"></i> Tambah Pelanggan
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover" id="tablePelanggan">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pelanggan</th>
                                <th>No. WA</th>
                                <th>No. Polisi</th>
                                <th>Kendaraan</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pelanggan -->
    <div class="modal fade" id="modalPelanggan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formPelanggan">
                    @csrf
                    <input type="hidden" name="id" id="pelanggan_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
                                <input type="text" id="nama_pelanggan" name="nama_pelanggan" class="form-control"
                                    placeholder="Contoh: Budi Santoso" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="no_wa" class="form-label">Nomor WhatsApp</label>
                                <input type="text" id="no_wa" name="no_wa" class="form-control" placeholder="08xxxxxxxxx"
                                    required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="no_polisi" class="form-label">No. Polisi</label>
                                <input type="text" id="no_polisi" name="no_polisi" class="form-control"
                                    placeholder="B 1234 ABC">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tipe_kendaraan" class="form-label">Tipe Kendaraan</label>
                                <input type="text" id="tipe_kendaraan" name="tipe_kendaraan" class="form-control"
                                    placeholder="Vario 150 / Avanza">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea id="alamat" name="alamat" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpan">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal History -->
    <div class="modal fade" id="modalHistory" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">History Service: <span id="historyPelangganName" class="fw-bold"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>#ID Nota</th>
                                    <th>Total Transaksi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody">
                                <!-- Data history akan diisi via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div id="noHistoryMessage" class="text-center py-3 d-none">
                        <i class="bx bx-info-circle fs-4 text-info mb-2"></i>
                        <p class="mb-0">Belum ada riwayat service untuk pelanggan ini.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(function () {
            var table = $('#tablePelanggan').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('pelanggan.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_pelanggan', name: 'nama_pelanggan' },
                    { data: 'no_wa', name: 'no_wa' },
                    { data: 'no_polisi', name: 'no_polisi' },
                    { data: 'tipe_kendaraan', name: 'tipe_kendaraan' },
                    { data: 'alamat', name: 'alamat' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            $('#btnTambahPelanggan').click(function () {
                $('#formPelanggan').trigger("reset");
                $('#modalTitle').html("Tambah Pelanggan");
                $('#pelanggan_id').val('');
            });

            $('body').on('click', '.editPelanggan', function () {
                var id = $(this).data('id');
                $.get("{{ route('pelanggan.index') }}" + '/' + id, function (data) {
                    $('#modalTitle').html("Edit Pelanggan");
                    $('#modalPelanggan').modal('show');
                    $('#pelanggan_id').val(data.pelanggan.id);
                    $('#nama_pelanggan').val(data.pelanggan.nama_pelanggan);
                    $('#no_wa').val(data.pelanggan.no_wa);
                    $('#no_polisi').val(data.pelanggan.no_polisi);
                    $('#tipe_kendaraan').val(data.pelanggan.tipe_kendaraan);
                    $('#alamat').val(data.pelanggan.alamat);
                })
            });

            $('body').on('click', '.viewHistory', function () {
                var id = $(this).data('id');
                $('#historyTableBody').empty();
                $('#noHistoryMessage').addClass('d-none');

                $.get("{{ route('pelanggan.index') }}" + '/' + id, function (data) {
                    $('#historyPelangganName').text(data.pelanggan.nama_pelanggan);
                    $('#modalHistory').modal('show');

                    if (data.history.length > 0) {
                        var html = '';
                        data.history.forEach(function (item) {
                            var statusBadge = item.status == 'lunas'
                                ? '<span class="badge bg-label-success">Lunas</span>'
                                : '<span class="badge bg-label-danger">Belum Lunas</span>';

                            html += `<tr>
                                <td>${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
                                <td>#${item.id}</td>
                                <td>Rp ${new Number(item.total_harga).toLocaleString('id-ID')}</td>
                                <td>${statusBadge}</td>
                            </tr>`;
                        });
                        $('#historyTableBody').html(html);
                    } else {
                        $('#noHistoryMessage').removeClass('d-none');
                    }
                })
            });

            $('#formPelanggan').submit(function (e) {
                e.preventDefault();
                var id = $('#pelanggan_id').val();
                var url = id ? "{{ route('pelanggan.index') }}/" + id : "{{ route('pelanggan.store') }}";
                var type = id ? "PUT" : "POST";

                $.ajax({
                    data: $('#formPelanggan').serialize(),
                    url: url,
                    type: type,
                    dataType: 'json',
                    success: function (data) {
                        $('#formPelanggan').trigger("reset");
                        $('#modalPelanggan').modal('hide');
                        table.draw();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.success,
                            timer: 1500
                        });
                    },
                    error: function (data) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi kesalahan!',
                        });
                    }
                });
            });

            $('body').on('click', '.deletePelanggan', function () {
                var id = $(this).data("id");
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Semua data terkait pelanggan ini akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('pelanggan.index') }}/" + id,
                            data: {
                                "_token": "{{ csrf_token() }}",
                            },
                            success: function (data) {
                                table.draw();
                                Swal.fire('Terhapus!', data.success, 'success');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush