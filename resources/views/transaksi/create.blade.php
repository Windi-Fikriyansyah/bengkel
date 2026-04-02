@extends('template.app')
@section('title', 'Buat Transaksi Baru')
@section('content')
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informasi Pelanggan</h5>
                <small class="text-muted">Pilih pelanggan terdaftar atau input manual</small>
            </div>
            <div class="card-body">
                <form id="formTransaksi">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="pelanggan_id" class="form-label">Cari Pelanggan Terdaftar</label>
                            <select id="pelanggan_id" name="pelanggan_id" class="form-select select2">
                                <option value="manual">-- Pelanggan Baru / Manual --</option>
                                @foreach($pelanggan as $p)
                                    <option value="{{ $p->id }}" data-wa="{{ $p->no_wa }}" data-tipe="{{ $p->tipe_kendaraan }}">
                                        {{ $p->nama_pelanggan }} ({{ $p->no_wa }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="nama_pelanggan" class="form-label">Nama Pelanggan (Manual)</label>
                            <input type="text" id="nama_pelanggan" name="nama_pelanggan" class="form-control"
                                placeholder="Input Nama" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="no_wa" class="form-label">No. WA (Manual)</label>
                            <input type="text" id="no_wa" name="no_wa" class="form-control" placeholder="08xxx">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="tipe_kendaraan" class="form-label">Tipe Kendaraan(Optional)</label>
                            <input type="text" id="tipe_kendaraan" name="tipe_kendaraan" class="form-control"
                                placeholder="Beat / Revo">
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row mb-3">
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Tambah Item (Layanan / Sparepart)</label>
                            <select id="selectItem" class="form-select select2">
                                <option value="">-- Pilih Item --</option>
                                <optgroup label="Layanan / Jasa">
                                    @foreach($layanan as $l)
                                        <option value="{{ $l->id }}" data-tipe="layanan" data-harga="{{ $l->harga }}"
                                            data-nama="{{ $l->nama_layanan }}">💎 {{ $l->nama_layanan }} (Rp
                                            {{ number_format($l->harga, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Sparepart">
                                    @foreach($sparepart as $s)
                                        <option value="{{ $s->id }}" data-tipe="sparepart" data-harga="{{ $s->harga_jual }}"
                                            data-nama="{{ $s->nama_sparepart }}">📦 {{ $s->nama_sparepart }} (Stok:
                                            {{ $s->stok }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" id="jumlahItem" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="button" id="btnAddItem" class="btn btn-info w-100"><i class="bx bx-plus"></i>
                                Tambah</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="tableItems">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Item</th>
                                    <th>Tipe</th>
                                    <th>Harga Satuan</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cartBody">
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="4" class="text-end">Total Bayar</th>
                                    <th id="totalBayarDisplay">Rp 0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label for="catatan" class="form-label">Catatan Transaksi (Keluhan / Keterangan)</label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light border-0 shadow-none">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label d-block text-dark fw-bold">Status Pembayaran</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="statusLunas"
                                                value="lunas" checked>
                                            <label class="form-check-label text-success fw-bold"
                                                for="statusLunas">Lunas</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="statusPending"
                                                value="pending">
                                            <label class="form-check-label text-danger fw-bold" for="statusPending">Belum
                                                Lunas</label>
                                        </div>
                                    </div>

                                    <div id="sectionPembayaran">
                                        <div class="mb-3">
                                            <label for="bayar" class="form-label text-dark fw-bold">Dibayar (Rp)</label>
                                            <input type="text" id="bayar_display" class="form-control" placeholder="0">
                                            <input type="hidden" name="bayar" id="bayar_actual" value="0">
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Total Tagihan:</span>
                                            <span class="fw-bold" id="labelTotalTagihan">Rp 0</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                            <span class="text-muted">Kembalian:</span>
                                            <h4 class="fw-bold text-primary mb-0" id="labelKembali">Rp 0</h4>
                                            <input type="hidden" name="kembali" id="kembali_actual" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12 text-end">
                            <input type="hidden" name="total_harga" id="total_harga_actual" value="0">
                            <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary me-2">Kembali</a>
                            <button type="submit" class="btn btn-primary btn-lg px-5">Selesaikan Transaksi</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let cart = [];

        $(function () {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5'
            });

            // Trigger manual fields visibility
            $('#pelanggan_id').on('change', function () {
                let val = $(this).val();
                if (val !== 'manual') {
                    let selected = $(this).find(':selected');
                    $('#nama_pelanggan').val(selected.text().split('(')[0].trim()).prop('readonly', true);
                    $('#no_wa').val(selected.data('wa')).prop('readonly', true);
                    $('#tipe_kendaraan').val(selected.data('tipe')).prop('readonly', true);
                } else {
                    $('#nama_pelanggan').val('').prop('readonly', false);
                    $('#no_wa').val('').prop('readonly', false);
                    $('#tipe_kendaraan').val('').prop('readonly', false);
                }
            });

            // Add item to cart
            $('#btnAddItem').on('click', function () {
                let selected = $('#selectItem').find(':selected');
                if (selected.val() === '') return Swal.fire('Peringatan', 'Pilih item terlebih dahulu!', 'warning');

                let itemId = selected.val();
                let tipe = selected.data('tipe');
                let nama = selected.data('nama');
                let harga = parseFloat(selected.data('harga'));
                let qty = parseInt($('#jumlahItem').val());

                // Check if exist
                let existing = cart.find(i => i.id == itemId && i.tipe == tipe);
                if (existing) {
                    existing.jumlah += qty;
                    existing.subtotal = existing.jumlah * existing.harga;
                } else {
                    cart.push({
                        id: itemId,
                        tipe: tipe,
                        nama: nama,
                        harga: harga,
                        jumlah: qty,
                        subtotal: harga * qty
                    });
                }

                renderCart();
                $('#selectItem').val(null).trigger('change');
                $('#jumlahItem').val(1);
            });

            // Submit Transaction
            $('#formTransaksi').on('submit', function (e) {
                e.preventDefault();
                if (cart.length === 0) return Swal.fire('Error', 'Belum ada item ditambahkan!', 'error');

                let formData = $(this).serializeArray();
                cart.forEach((item, index) => {
                    formData.push({ name: `items[${index}][id]`, value: item.id });
                    formData.push({ name: `items[${index}][tipe]`, value: item.tipe });
                    formData.push({ name: `items[${index}][nama]`, value: item.nama });
                    formData.push({ name: `items[${index}][harga]`, value: item.harga });
                    formData.push({ name: `items[${index}][jumlah]`, value: item.jumlah });
                    formData.push({ name: `items[${index}][subtotal]`, value: item.subtotal });
                });

                $.ajax({
                    url: "{{ route('transaksi.store') }}",
                    type: "POST",
                    data: formData,
                    success: function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.success,
                            showConfirmButton: true,
                        }).then(() => {
                            window.location.href = "{{ route('transaksi.index') }}";
                        });
                    },
                    error: function (err) {
                        Swal.fire('Error', 'Gagal menyimpan transaksi!', 'error');
                    }
                });
            });
        });

        // Handle Status Payment Toggle
        $('input[name="status"]').on('change', function () {
            if ($(this).val() === 'lunas') {
                $('#sectionPembayaran').slideDown();
                $('#bayar_display').prop('required', true);
            } else {
                $('#sectionPembayaran').slideUp();
                $('#bayar_display').prop('required', false).val('');
                $('#bayar_actual').val(0);
                $('#labelKembali').text('Rp 0');
                $('#kembali_actual').val(0);
            }
        });

        // Calculate Change
        $('#bayar_display').on('keyup', function () {
            let val = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(formatRupiah(val));

            let bayar = parseInt(val) || 0;
            let total = parseInt($('#total_harga_actual').val());
            let kembali = bayar - total;

            $('#bayar_actual').val(bayar);
            $('#kembali_actual').val(kembali);
            $('#labelKembali').text('Rp ' + (kembali < 0 ? 0 : kembali).toLocaleString('id-ID'));
        });

        function renderCart() {
            let html = '';
            let total = 0;
            cart.forEach((item, index) => {
                total += item.subtotal;
                html += `<tr>
                                                                                <td>${item.nama}</td>
                                                                                <td><span class="badge ${item.tipe == 'layanan' ? 'bg-label-info' : 'bg-label-warning'}">${item.tipe}</span></td>
                                                                                <td>Rp ${new Number(item.harga).toLocaleString('id-ID')}</td>
                                                                                <td>${item.jumlah}</td>
                                                                                <td>Rp ${new Number(item.subtotal).toLocaleString('id-ID')}</td>
                                                                                <td>
                                                                                    <button type="button" class="btn btn-sm btn-icon btn-danger" onclick="removeFromCart(${index})"><i class="bx bx-trash"></i></button>
                                                                                </td>
                                                                            </tr>`;
            });
            $('#cartBody').html(html);
            $('#totalBayarDisplay').text('Rp ' + new Number(total).toLocaleString('id-ID'));
            $('#labelTotalTagihan').text('Rp ' + new Number(total).toLocaleString('id-ID'));
            $('#total_harga_actual').val(total);

            // Recalculate change if any
            let bayar = parseInt($('#bayar_actual').val()) || 0;
            let kembali = bayar - total;
            $('#labelKembali').text('Rp ' + (kembali < 0 ? 0 : kembali).toLocaleString('id-ID'));
            $('#kembali_actual').val(kembali);
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            renderCart();
        }

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
    </script>
@endpush
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush