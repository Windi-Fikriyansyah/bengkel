@extends('template.app')

@section('title', 'Pengaturan Profil Bengkel')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4 shadow-sm border-0">
            <h5 class="card-header bg-white border-bottom fw-bold text-primary">
                <i class="bx bx-user me-2"></i> Profil & Informasi Bengkel
            </h5>
            <!-- Account -->
            <div class="card-body pt-4">
                <form id="formAccountSettings" method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-bold">NAMA LENGKAP</label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" autofocus />
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-bold">ALAMAT EMAIL</label>
                            <input class="form-control @error('email') is-invalid @enderror" type="text" id="email" name="email" value="{{ old('email', $user->email) }}" />
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="nama_bengkel" class="form-label fw-bold">NAMA BENGKEL</label>
                            <input class="form-control @error('nama_bengkel') is-invalid @enderror" type="text" id="nama_bengkel" name="nama_bengkel" value="{{ old('nama_bengkel', $user->nama_bengkel) }}" />
                            @error('nama_bengkel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="no_whatsapp" class="form-label fw-bold">NOMOR WHATSAPP</label>
                            <input class="form-control @error('no_whatsapp') is-invalid @enderror" type="text" id="no_whatsapp" name="no_whatsapp" value="{{ old('no_whatsapp', $user->no_whatsapp) }}" />
                            @error('no_whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-12">
                            <label for="alamat_bengkel" class="form-label fw-bold">ALAMAT LENGKAP BENGKEL</label>
                            <textarea class="form-control @error('alamat_bengkel') is-invalid @enderror" id="alamat_bengkel" name="alamat_bengkel" rows="3">{{ old('alamat_bengkel', $user->alamat_bengkel) }}</textarea>
                            @error('alamat_bengkel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <div>
                            @if (session('status') === 'profile-updated')
                                <span class="text-success small fw-bold me-2"><i class="bx bx-check-circle me-1"></i> Profil Berhasil Diperbarui</span>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bx bx-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card mb-4 shadow-sm border-0">
            <h5 class="card-header bg-white border-bottom fw-bold text-dark">
                <i class="bx bx-lock-alt me-2"></i> Keamanan (Ganti Password)
            </h5>
            <div class="card-body pt-4">
                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="current_password" class="form-label fw-bold">PASSWORD SAAT INI</label>
                            <input class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" type="password" name="current_password" id="current_password" autocomplete="current-password" />
                            @error('current_password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="password" class="form-label fw-bold">PASSWORD BARU</label>
                            <input class="form-control @error('password', 'updatePassword') is-invalid @enderror" type="password" name="password" id="password" autocomplete="new-password" />
                            @error('password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="password_confirmation" class="form-label fw-bold">KONFIRMASI PASSWORD BARU</label>
                            <input class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" />
                            @error('password_confirmation', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <div>
                            @if (session('status') === 'password-updated')
                                <span class="text-success small fw-bold"><i class="bx bx-check-circle me-1"></i> Password Berhasil Diperbarui</span>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-dark px-4">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Account -->
        <div class="card shadow-sm border-0 card-label-danger border-start border-danger border-3">
            <h5 class="card-header bg-white border-bottom fw-bold text-danger">
                Hapus Akun
            </h5>
            <div class="card-body pt-4 text-center py-5">
                 <div class="mb-3 text-muted">
                    Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. <br>
                    Harap pastikan ini adalah tindakan yang benar.
                 </div>
                 <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    Hapus Akun & Data Bengkel
                 </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">Konfirmasi Penghapusan</h5>
                    <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#deleteAccountModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus akun? Masukkan password Anda untuk konfirmasi.</p>
                    <div class="mt-3">
                        <label for="password_confirm" class="form-label">Password Konfirmasi</label>
                        <input type="password" name="password" id="password_confirm" class="form-control" placeholder="Masukkan password Anda" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus Permanen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
