{{-- Kadiv (dan Superadmin). Fakta: ada template Excel; file xlsx/xls/csv maksimal 5MB; hasil upload per baris (berhasil/gagal). --}}
<h4>Mengunduh template</h4>
<p>Di halaman <strong>Upload Data Resmi</strong>, klik <strong>Download Template</strong>. Isi kolom wajib
    <code>username</code>, <code>tanggal</code>, <code>omset</code>, dan <code>revenue</code>. Kolom
    <code>jumlah_akad</code>, <code>nasabah_baru</code>, dan <code>branch_code</code> bersifat opsional. Gunakan tanggal
    berformat <code>YYYY-MM-DD</code>. Jika <code>branch_code</code> dikosongkan, sistem menggunakan cabang FO yang
    terdaftar.</p>
<h4>Mengunggah</h4>
<p>Pilih file <code>.xlsx</code>, <code>.xls</code>, atau <code>.csv</code> dengan ukuran maksimal 5 MB, lalu klik
    <strong>Upload &amp; Proses</strong>. Username harus milik FO yang terdaftar; omset dan revenue harus berupa angka.
    Upload ulang untuk username dan tanggal yang sama akan memperbarui data sebelumnya, bukan membuat baris duplikat.
</p>
<h4>Membaca hasil impor</h4>
<p>Pesan hasil menampilkan jumlah baris yang berhasil dan gagal diproses. Jika ada kegagalan, tabel <strong>Baris yang
        Gagal Diproses</strong> menunjukkan nomor baris Excel dan penyebabnya, seperti username tidak ditemukan/bukan
    FO, tanggal kosong atau tidak valid, nilai omset/revenue bukan angka, atau cabang tidak dapat ditentukan. Perbaiki
    baris tersebut lalu unggah kembali.</p>
