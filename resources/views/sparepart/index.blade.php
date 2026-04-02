@extends('template.app')
@section('title', 'Manajemen Sparepart')
@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Sparepart Bengkel</h5>
                <button type="button" class="btn btn-primary" id="btnTambahSparepart" data-bs-toggle="modal"
                    data-bs-target="#modalSparepart">
                    <i class="bx bx-plus me-1"></i> Tambah Sparepart
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover" id="tableSparepart">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Sparepart</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Sparepart -->
    <div class="modal fade" id="modalSparepart" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Sparepart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formSparepart">
                    @csrf
                    <input type="hidden" name="id" id="sparepart_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="nama_sparepart" class="form-label">Nama Sparepart</label>
                                <input type="text" id="nama_sparepart" name="nama_sparepart" class="form-control"
                                    placeholder="Contoh: Kampas Rem Depan" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="harga_beli" class="form-label">Harga Beli (Rp)</label>
                                <input type="text" id="harga_beli" name="harga_beli_display" class="form-control rupiah"
                                    placeholder="0" required>
                                <input type="hidden" name="harga_beli" id="harga_beli_actual">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="harga_jual" class="form-label">Harga Jual (Rp)</label>
                                <input type="text" id="harga_jual" name="harga_jual_display" class="form-control rupiah"
                                    placeholder="0" required>
                                <input type="hidden" name="harga_jual" id="harga_jual_actual">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="stok" class="form-label">Stok</label>
                                <input type="number" id="stok" name="stok" class="form-control" placeholder="0" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"
                                    placeholder="Keterangan tambahan"></textarea>
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
            var table = $('#tableSparepart').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('sparepart.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_sparepart', name: 'nama_sparepart' },
                    { data: 'harga_beli_formatted', name: 'harga_beli' },
                    { data: 'harga_jual_formatted', name: 'harga_jual' },
                    { data: 'stok', name: 'stok' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            $('#btnTambahSparepart').click(function () {
                $('#formSparepart').trigger("reset");
                $('#modalTitle').html("Tambah Sparepart");
                $('#sparepart_id').val('');
            });

            $('body').on('click', '.editSparepart', function () {
                var id = $(this).data('id');
                $.get("{{ route('sparepart.index') }}" + '/' + id, function (data) {
                    $('#modalTitle').html("Edit Sparepart");
                    $('#modalSparepart').modal('show');
                    $('#sparepart_id').val(data.id);
                    $('#nama_sparepart').val(data.nama_sparepart);

                    var hBeli = Math.floor(data.harga_beli);
                    var hJual = Math.floor(data.harga_jual);

                    $('#harga_beli').val(formatRupiah(hBeli.toString()));
                    $('#harga_beli_actual').val(hBeli);
                    $('#harga_jual').val(formatRupiah(hJual.toString()));
                    $('#harga_jual_actual').val(hJual);

                    $('#stok').val(data.stok);
                    $('#deskripsi').val(data.deskripsi);
                })
            });

            $('.rupiah').on('keyup', function () {
                var value = $(this).val();
                $(this).val(formatRupiah(value));

                var actualId = $(this).attr('id') + '_actual';
                $('#' + actualId).val(value.replace(/[^0-9]/g, ''));
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

            $('#formSparepart').submit(function (e) {
                e.preventDefault();

                // Sync actual values
                $('#harga_beli_actual').val($('#harga_beli').val().replace(/[^0-9]/g, ''));
                $('#harga_jual_actual').val($('#harga_jual').val().replace(/[^0-9]/g, ''));

                var id = $('#sparepart_id').val();
                var url = id ? "{{ route('sparepart.index') }}/" + id : "{{ route('sparepart.store') }}";
                var type = id ? "PUT" : "POST";

                $.ajax({
                    data: $('#formSparepart').serialize(),
                    url: url,
                    type: type,
                    dataType: 'json',
                    success: function (data) {
                        $('#formSparepart').trigger("reset");
                        $('#modalSparepart').modal('hide');
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

            $('body').on('click', '.deleteSparepart', function () {
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
                            url: "{{ route('sparepart.index') }}/" + id,
                            data: {
                                "_token": "{{ csrf_token() }}",
                            },
                            success: function (data) {
                                table.draw();
                                Swal.fire('Terhapus!', data.success, 'success');
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