@extends('template.app')
@section('title', 'Riwayat Stok Masuk')
@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Riwayat Stok Masuk</h5>
                <button type="button" class="btn btn-primary" id="btnTambahStok" data-bs-toggle="modal"
                    data-bs-target="#modalStokMasuk">
                    <i class="bx bx-plus me-1"></i> Input Stok Masuk
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover" id="tableStokMasuk">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Sparepart</th>
                                <th>Jumlah Masuk</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Stok Masuk -->
    <div class="modal fade" id="modalStokMasuk" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Stok Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formStokMasuk">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="sparepart_id" class="form-label">Pilih Sparepart</label>
                                <select id="sparepart_id" name="sparepart_id" class="form-select select2" required>
                                    <option value="">-- Pilih Sparepart --</option>
                                    @foreach($spareparts as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama_sparepart }} (Stok Saat Ini:
                                            {{ $item->stok }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="jumlah" class="form-label">Jumlah Masuk</label>
                                <input type="number" id="jumlah" name="jumlah" class="form-control" placeholder="0" min="1"
                                    required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                                <input type="text" id="keterangan" name="keterangan" class="form-control"
                                    placeholder="Contoh: Pembelian dari Supplier A">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah Stok</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function () {
            // Initialize Select2
            $('.select2').select2({
                dropdownParent: $('#modalStokMasuk'),
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Sparepart --'
            });

            var table = $('#tableStokMasuk').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('stok-masuk.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'tanggal', name: 'created_at' },
                    { data: 'nama_sparepart', name: 'spareparts.nama_sparepart' },
                    { data: 'jumlah', name: 'jumlah' },
                    { data: 'keterangan', name: 'keterangan' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            $('#formStokMasuk').submit(function (e) {
                e.preventDefault();
                $.ajax({
                    data: $('#formStokMasuk').serialize(),
                    url: "{{ route('stok-masuk.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#formStokMasuk').trigger("reset");
                        $('#sparepart_id').val(null).trigger('change');
                        $('#modalStokMasuk').modal('hide');
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

            $('body').on('click', '.deleteStokMasuk', function () {
                var id = $(this).data("id");
                Swal.fire({
                    title: 'Batalkan Stok Masuk?',
                    text: "Stok pada master sparepart akan berkurang kembali!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, batalkan!',
                    cancelButtonText: 'Tutup'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('stok-masuk.index') }}/" + id,
                            data: {
                                "_token": "{{ csrf_token() }}",
                            },
                            success: function (data) {
                                table.draw();
                                Swal.fire('Berhasil!', data.success, 'success');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
@push('style')
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush