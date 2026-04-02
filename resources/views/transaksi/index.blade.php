@extends('template.app')
@section('title', 'Daftar Transaksi')
@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Transaksi / Nota</h5>
                <a href="{{ route('transaksi.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Buat Transaksi Baru
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover" id="tableTransaksi">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Transaksi -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Transaksi #<span id="detailId"></span></h5>
                    <div id="detailStamp" style="position: absolute; right: 50px; top: 15px; transform: rotate(-15deg); font-weight: 900; font-size: 24px; padding: 5px 15px; border: 4px double; border-radius: 10px; opacity: 0.6; z-index: 10; display: none;"></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="mb-1 text-muted">Pelanggan:</h6>
                            <p class="fw-bold mb-0" id="detailCustomer"></p>
                            <p class="mb-0" id="detailWA"></p>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <h6 class="mb-1 text-muted">Tanggal:</h6>
                            <p class="mb-0" id="detailTanggal"></p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Tipe</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="detailItems">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total</th>
                                    <th id="detailTotal"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="printNota()">Cetak Nota</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Pelunasan -->
    <div class="modal fade" id="modalBayar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pelunasan Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formPelunasan">
                    @csrf
                    <input type="hidden" id="pay_transaksi_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Total Tagihan</label>
                            <input type="text" id="pay_total_display" class="form-control fw-bold" readonly>
                            <input type="hidden" id="pay_total_actual">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah Bayar (Rp)</label>
                            <input type="text" id="pay_bayar_display" class="form-control" placeholder="0" required>
                            <input type="hidden" name="bayar" id="pay_bayar_actual" value="0">
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="text-muted">Kembalian:</span>
                            <h4 class="fw-bold text-success mb-0" id="pay_labelKembali">Rp 0</h4>
                            <input type="hidden" name="kembali" id="pay_kembali_actual" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        var currentTransaksiData = null;

        $(function () {
            var table = $('#tableTransaksi').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('transaksi.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'tanggal', name: 'created_at' },
                    { data: 'customer', name: 'nama_pelanggan' },
                    { data: 'total_formatted', name: 'total_harga' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });

        function viewDetail(id) {
            $.get("{{ route('transaksi.index') }}/" + id, function (data) {
                currentTransaksiData = data;
                $('#detailId').text(id);
                $('#detailCustomer').text(data.header.pelanggan_id ? data.header.nama_pelanggan_ref : data.header.nama_pelanggan);
                $('#detailWA').text(data.header.pelanggan_id ? data.header.no_wa_ref : data.header.no_wa);
                $('#detailTanggal').text(new Date(data.header.created_at).toLocaleString('id-ID'));

                // Handle status stamp
                if (data.header.status == 'lunas') {
                    $('#detailStamp').text('LUNAS').css({'color': '#28a745', 'border-color': '#28a745', 'display': 'block'});
                } else {
                    $('#detailStamp').text('BELUM LUNAS').css({'color': '#dc3545', 'border-color': '#dc3545', 'display': 'block'});
                }

                var html = '';
                data.details.forEach(function (item) {
                    html += `<tr>
                                                <td>${item.nama_item}</td>
                                                <td><span class="badge ${item.tipe == 'layanan' ? 'bg-label-info' : 'bg-label-warning'} text-capitalize">${item.tipe}</span></td>
                                                <td>Rp ${new Number(item.harga).toLocaleString('id-ID')}</td>
                                                <td>${item.jumlah}</td>
                                                <td>Rp ${new Number(item.subtotal).toLocaleString('id-ID')}</td>
                                            </tr>`;
                });
                $('#detailItems').html(html);
                $('#detailTotal').text('Rp ' + new Number(data.header.total_harga).toLocaleString('id-ID'));
                $('#modalDetail').modal('show');
            });
        }

        function deleteTransaksi(id) {
            Swal.fire({
                title: 'Batalkan Transaksi?',
                text: "Stok sparepart yang terjual akan dikembalikan secara otomatis!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('transaksi.index') }}/" + id,
                        data: { "_token": "{{ csrf_token() }}" },
                        success: function (data) {
                            $('#tableTransaksi').DataTable().draw();
                            Swal.fire('Berhasil!', data.success, 'success');
                        }
                    });
                }
            });
        }

        function openPaymentModal(id, total) {
            $('#formPelunasan').trigger('reset');
            $('#pay_transaksi_id').val(id);
            $('#pay_total_actual').val(total);
            $('#pay_total_display').val('Rp ' + new Number(total).toLocaleString('id-ID'));
            $('#modalBayar').modal('show');
        }

        $('#pay_bayar_display').on('keyup', function () {
            let val = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(formatRupiah(val));
            let bayar = parseInt(val) || 0;
            let total = parseInt($('#pay_total_actual').val());
            let kembali = bayar - total;
            $('#pay_bayar_actual').val(bayar);
            $('#pay_kembali_actual').val(kembali);
            $('#pay_labelKembali').text('Rp ' + (kembali < 0 ? 0 : kembali).toLocaleString('id-ID'));
        });

        $('#formPelunasan').on('submit', function (e) {
            e.preventDefault();
            let id = $('#pay_transaksi_id').val();
            $.ajax({
                url: `/transaksi/${id}/pembayaran`,
                type: "POST",
                data: $(this).serialize(),
                success: function (res) {
                    $('#modalBayar').modal('hide');
                    $('#tableTransaksi').DataTable().draw();
                    Swal.fire('Berhasil!', res.success, 'success');
                }
            });
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
            return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
        }

        function printNota() {
            // Placeholder untuk cetak nota
            Swal.fire('Fitur Cetak', 'Segera hadir di update berikutnya!', 'info');
        }
    </script>
@endpush