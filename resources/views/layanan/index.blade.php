@extends('template.app')
@section('title', 'Manajemen Layanan')
@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Layanan Bengkel</h5>
                <button type="button" class="btn btn-primary" id="btnTambahLayanan" data-bs-toggle="modal"
                    data-bs-target="#modalLayanan">
                    <i class="bx bx-plus me-1"></i> Tambah Layanan
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover" id="tableLayanan">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Layanan</th>
                                <th>Harga</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Layanan -->
    <div class="modal fade" id="modalLayanan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Layanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formLayanan">
                    @csrf
                    <input type="hidden" name="id" id="layanan_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama_layanan" class="form-label">Nama Layanan</label>
                                <input type="text" id="nama_layanan" name="nama_layanan" class="form-control"
                                    placeholder="Contoh: Service Ringan" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="harga" class="form-label">Harga (Rp)</label>
                                <input type="text" id="harga" name="harga_display" class="form-control"
                                    placeholder="Contoh: 50.000" required>
                                <input type="hidden" name="harga" id="harga_actual">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"
                                    placeholder="Penjelasan singkat mengenai layanan"></textarea>
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

@endsection
@push('js')
    <script>
        $(function () {
            var table = $('#tableLayanan').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('layanan.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_layanan', name: 'nama_layanan' },
                    { data: 'harga_formatted', name: 'harga' },
                    { data: 'deskripsi', name: 'deskripsi' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            $('#btnTambahLayanan').click(function () {
                $('#formLayanan').trigger("reset");
                $('#modalTitle').html("Tambah Layanan");
                $('#layanan_id').val('');
            });

            $('body').on('click', '.editLayanan', function () {
                var id = $(this).data('id');
                $.get("{{ route('layanan.index') }}" + '/' + id, function (data) {
                    $('#modalTitle').html("Edit Layanan");
                    $('#modalLayanan').modal('show');
                    $('#layanan_id').val(data.id);
                    $('#nama_layanan').val(data.nama_layanan);
                    
                    // Hilangkan desimal .00 agar tidak terbaca 5.000.000
                    var harga_clean = Math.floor(data.harga);
                    $('#harga').val(formatRupiah(harga_clean.toString()));
                    $('#harga_actual').val(harga_clean);
                    $('#deskripsi').val(data.deskripsi);
                })
            });

            // Format Rupiah saat diketik
            $('#harga').on('keyup', function() {
                var value = $(this).val();
                $(this).val(formatRupiah(value));
                
                // Simpan nilai asli (angka saja) ke hidden input
                var actualValue = value.replace(/[^0-9]/g, '');
                $('#harga_actual').val(actualValue);
            });

            function formatRupiah(angka, prefix) {
                var number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
            }

            $('#formLayanan').submit(function (e) {
                e.preventDefault();
                
                // Pastikan nilai actual terisi sebelum submit
                var rawHarga = $('#harga').val().replace(/[^0-9]/g, '');
                $('#harga_actual').val(rawHarga);

                var id = $('#layanan_id').val();
                var url = id ? "{{ route('layanan.index') }}/" + id : "{{ route('layanan.store') }}";
                var type = id ? "PUT" : "POST";

                $.ajax({
                    data: $('#formLayanan').serialize(),
                    url: url,
                    type: type,
                    dataType: 'json',
                    success: function (data) {
                        $('#formLayanan').trigger("reset");
                        $('#modalLayanan').modal('hide');
                        table.draw();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.success,
                            timer: 1500
                        });
                    },
                    error: function (data) {
                        console.log('Error:', data);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi kesalahan saat menyimpan data!',
                        });
                    }
                });
            });

            $('body').on('click', '.deleteLayanan', function () {
                var id = $(this).data("id");
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
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
                            url: "{{ route('layanan.index') }}/" + id,
                            data: {
                                "_token": "{{ csrf_token() }}",
                            },
                            success: function (data) {
                                table.draw();
                                Swal.fire(
                                    'Terhapus!',
                                    data.success,
                                    'success'
                                )
                            },
                            error: function (data) {
                                console.log('Error:', data);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush