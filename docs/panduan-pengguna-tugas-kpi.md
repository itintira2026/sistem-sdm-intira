# Panduan Pengguna Sistem Tugas dan KPI

Panduan ini disusun per role. Setiap section berisi halaman/menu, langkah penggunaan, dan FAQ khusus role tersebut. Menu yang terlihat mengikuti role akun dan lingkup aksesnya.

## Daftar Isi

- [FO](#fo)
- [AM](#am)
- [Owner](#owner)
- [Ketua dan Direktur](#ketua-dan-direktur)
- [Kadiv](#kadiv)
- [Korwil](#korwil)
- [Staff](#staff)
- [Superadmin](#superadmin)

## Aturan Ringkas

- **Tugas KPI** diberikan atasan, memiliki bobot, dan entry-nya mengikuti validasi. **Tugas Tambahan** adalah catatan aktivitas pribadi di luar KPI; tidak berbobot dan tidak menambah KPI.
- Mode tugas: **Sekali** memakai satu entry, **Harian** memakai entry pada tanggal yang ditandai sebagai hari kerja saat tugas dibuat, dan **Kumulatif** memakai satu nilai total terhadap target.
- Bobot semua Tugas KPI aktif harus tepat **100%**. Target ditetapkan per penerima dan dapat berbeda untuk tiap orang.
- Entry **Disetujui** belum tentu mencapai target. Status validasi dan capaian target adalah dua hal berbeda.
- Pada Detail Harian, hijau berarti target hari itu tercapai dan entry disetujui; merah berarti target belum tercapai atau entry belum disetujui.
- Tugas beku atau dinonaktifkan tidak dapat menerima entry baru. Jika menu yang disebut tidak terlihat, hubungi Superadmin untuk memeriksa role, divisi, dan cabang.

## FO

### Halaman dan Menu

- **Tugas Saya** (`/tugas-saya`): mengisi tugas KPI yang diberikan atasan dan mencatat Tugas Tambahan.
- **KPI Saya** (`/kpi-saya`): melihat capaian KPI bulanan.
- **FO Dashboard** (`/daily-reports-fo`): mengisi laporan operasional per slot.

### Cara Mengecek dan Mengisi Tugas Hari Ini

1. Buka **Tugas Saya** dari sidebar. Tanggal di bawah judul halaman menunjukkan tanggal hari ini.
2. Mulai dari bagian **Perlu Diperbaiki** jika bagian itu tampil. Bagian ini berisi entry yang ditolak dan tetap tersedia untuk diperbaiki meskipun periode tugas sudah berakhir.
3. Periksa bagian **Perlu Dikerjakan Hari Ini**. Setiap kartu menunjukkan judul tugas, persentase progres, dan status entry hari ini. Jika ada lebih dari lima tugas, klik **Tampilkan lainnya**.
4. Jangan gunakan tombol bulan **Sebelumnya/Berikutnya** untuk mencari tugas hari ini. Navigasi bulan hanya mengubah riwayat di bagian **Tugas Selesai**.
5. Klik judul tugas jika perlu membaca detail atau deskripsi. Untuk mengisi, klik tombol pada kartu: **Isi Sekarang**, **Ubah**, atau **Perbaiki**. Form terbuka sebagai modal.
6. Pastikan tanggal entry di bagian atas modal benar. Entry Harian harus diisi pada tanggal yang sedang dijadwalkan; jika tanggalnya bukan hari ini dan masih pending, entry tidak bisa diubah.
7. Isi kolom **Nilai** untuk tugas Numeric atau centang **Sudah selesai dikerjakan** untuk Checklist. Lihat preview nilai/target di bawah kolom Numeric agar tidak salah satuan.
8. Lampiran bersifat opsional. Form menerima gambar/dokumen sampai 3 MB dan video sampai 20 MB; periksa jenis file yang didukung pada form.
9. Klik **Simpan**. Jika berhasil, halaman dimuat ulang dan status terbaru terlihat pada kartu tugas.

### Memahami Status Setelah Mengirim

- **Belum diisi** atau **Entry tanggal ... belum diisi**: belum ada nilai untuk tanggal tersebut. Klik **Isi Sekarang** jika tombol tersedia.
- **Sudah diisi, menunggu validasi**: entry terkirim dan sedang menunggu pemeriksaan/approval. Jangan kirim entry duplikat.
- **Dicek AM, menunggu approve Korwil**: khusus entry FO yang lolos pemeriksaan awal AM. Tunggu keputusan Korwil.
- **Sudah disetujui**: entry sudah terkunci dan tidak dapat diubah lagi oleh pengisi. KPI hanya naik jika capaian memenuhi aturan target dan bobot.
- **Ditolak, perlu diperbaiki**: baca alasan penolakan, klik **Perbaiki**, koreksi entry tanggal yang sama, lalu klik **Simpan** untuk mengirim ulang.
- **Dibekukan/Non-aktif**: tugas tidak menerima entry baru. Hubungi atasan jika status ini tidak sesuai.

Entry Harian pending hanya bisa diisi tepat pada tanggalnya. Entry yang ditolak tetap dapat diperbaiki setelah tanggal lewat selama tugas belum dibekukan atau dinonaktifkan.

### Langkah Mengisi Laporan Operasional FO

1. Buka **FO Dashboard**.
2. Pilih shift jika diminta.
3. Buka slot laporan yang perlu diisi.
4. Isi data dan lampiran sesuai kolom pada form, lalu simpan.
5. Buka **History** untuk melihat status. Jika ditolak, edit dan kirim ulang dalam batas waktu yang ditampilkan.

Laporan operasional FO berbeda dari entry KPI. Laporan diproses melalui **Validasi FO** dan dapat digunakan dalam Rekonsiliasi; entry KPI ada di Tugas Saya.

### Langkah Mencatat Tugas Tambahan

1. Di **Tugas Saya**, buka bagian **Tugas Tambahan**.
2. Klik **+ Tambah**, isi judul dan deskripsi.
3. Pilih cara catat **Selesai / Tidak** atau **Angka**, masukkan hasil, lalu simpan.

Catatan langsung tersimpan tanpa approval dan tidak masuk KPI. Batas edit/hapus mengikuti waktu yang ditampilkan aplikasi.

### FAQ FO

**Saya mau isi laporan FO, apakah dari Tugas Saya?** Tidak. Laporan operasional diisi lewat FO Dashboard; Tugas Saya untuk Tugas KPI dan Tugas Tambahan.

**Entry Harian kemarin tidak bisa diisi, kenapa?** Entry pending hanya bisa diisi pada tanggalnya. Jika tanggal lewat, hari tersebut dihitung belum tercapai. Entry yang ditolak masih dapat diperbaiki selama tugas aktif.

**Entry sudah disetujui, kenapa progres belum penuh?** Approval hanya mengesahkan entry. Progres numeric tetap dibandingkan dengan target.

**Apa arti merah di Detail Harian?** Target hari itu belum tercapai atau entry belum disetujui.

## AM

AM adalah user dengan role dasar `manager` tanpa role KPI `korwil`. Jika akun memiliki kedua role tersebut, ikuti section Korwil.

### Halaman dan Menu

- **Tugas Saya** (`/tugas-saya`) dan **KPI Saya** (`/kpi-saya`): tugas pribadi dan capaian sendiri.
- **Perlu Saya Cek** (`/task-check-queue`): memeriksa entry FO sesuai kewenangan.
- **Validasi FO**: laporan operasional FO.
- Sidebar mungkin menampilkan **Perlu Saya Approve** karena menu bersama, tetapi AM tidak berwenang menyetujui entry KPI.
- AM tidak mendapat menu KPI Tim dan tidak membuat tugas KPI untuk anggota lain.

### Jika AM Menerima Tugas KPI Pribadi

1. Buka **Tugas Saya** dan cek **Perlu Diperbaiki** terlebih dahulu.
2. Lihat kartu pada **Perlu Dikerjakan Hari Ini** untuk mengetahui entry yang belum diisi, menunggu validasi, atau sudah disetujui.
3. Klik **Isi Sekarang**, **Ubah**, atau **Perbaiki**. Isi form pada tanggal yang tertera lalu klik **Simpan**.
4. Entry pribadi AM menunggu keputusan Korwil. Jika ditolak, alasan muncul di **Perlu Diperbaiki**; jika disetujui, entry terkunci dan progres hanya dihitung sesuai target.

### Langkah Memeriksa Entry FO

1. Buka **Tugas Tim > Perlu Saya Cek**.
2. Tinjau pengisi, judul tugas, tanggal, nilai, target, dan lampiran.
3. Pilih **Lolos Cek** untuk meneruskan entry ke Korwil, atau **Tolak** dan isi alasan.
4. Klik **Proses**. Jika ditolak, FO dapat memperbaiki dan mengirim ulang.

Setelah memilih **Lolos Cek**, status entry FO menjadi **Dicek** dan menunggu approve Korwil; ini belum berarti target tercapai. Setelah Korwil menyetujui, entry menjadi **Disetujui** dan terkunci. Jika AM memilih **Tolak**, entry kembali ke pengisi dengan alasan dan muncul pada bagian **Perlu Diperbaiki** di Tugas Saya.

Untuk entry non-FO tidak ada tahap cek terpisah. **Lolos Cek** hanya dapat langsung menyetujui entry jika pengguna yang sedang login memang approver berwenang atas entry tersebut. AM biasa tidak mendapat kewenangan approve entry AM.

### FAQ AM

**Kenapa antrian Perlu Saya Approve kosong?** AM memeriksa entry FO; keputusan akhir entry FO berada pada Korwil. Tautan approve dapat tampil di sidebar, tetapi itu tidak memberi AM kewenangan approve.

**Apa yang dilakukan setelah Lolos Cek?** Entry FO berubah menjadi Dicek dan diteruskan ke Korwil untuk keputusan akhir. Nilai KPI belum dihitung sebagai entry disetujui sebelum Korwil memprosesnya.

**Kenapa saya tidak punya KPI Tim?** KPI Tim tidak disediakan untuk role AM saja. Untuk KPI sendiri, buka KPI Saya.

**Apa yang terjadi jika saya menolak entry FO?** Isi alasan sebelum klik Proses. FO akan melihat alasan tersebut, memperbaiki entry, lalu mengirim ulang.

## Owner

### Halaman dan Menu

- **KPI Tim** (`/kpi-tim`): memantau KPI sesuai lingkup yang tersedia.
- **Rekonsiliasi** (`/rekonsiliasi`): membaca perbandingan laporan FO yang disetujui dengan data resmi.
- Panduan Sistem (`/panduan-sistem`) dan laporan lain yang diberikan sebagai akses baca.

### Cara Memantau KPI dan Rekonsiliasi

1. Buka **KPI Tim**, pilih bulan, dan cari anggota.
2. Klik nama anggota untuk membaca detail KPI. Halaman detail anggota bersifat baca saja.
3. Buka **Rekonsiliasi** bila perlu membandingkan laporan FO dan data resmi.
4. Jika menemukan masalah, sampaikan kepada role yang berwenang.

**Owner hanya membaca dan memantau.** Jangan membuat, mengubah, menyetujui, menghapus, atau mengoreksi data dari akun Owner.

### FAQ Owner

**Bolehkah Owner mengubah atau menyetujui data?** Tidak. Peran Owner pada panduan ini hanya baca dan pantau.

**Kenapa suatu halaman tidak memiliki tombol aksi?** Halaman pantauan memang tidak menyediakan aksi perubahan untuk Owner.

**Apa yang dilakukan jika menemukan selisih?** Catat informasi yang relevan dan teruskan kepada role yang bertanggung jawab atas data tersebut.

## Ketua dan Direktur

Bagian ini berlaku untuk Ketua, Direktur Utama, Direktur Bisnis, dan Direktur Operasional.

### Halaman dan Menu

- **Bikin Tugas KPI** (`/tasks/create`) dan **Tugas yang Diberikan** (`/tugas-diberikan`).
- **Perlu Saya Cek** (`/task-check-queue`) dan **Perlu Saya Approve** (`/task-approve-queue`).
- **KPI Tim** (`/kpi-tim`) dan Detail KPI anggota (`/kpi-tim/{user}`).
- **Set Hari Kerja** (`/period-working-days`) serta **Rekonsiliasi** (`/rekonsiliasi`).

### Langkah Membuat Tugas KPI

1. Untuk tugas mode Harian, buka **Set Hari Kerja** lebih dahulu dan tandai tanggal kerja. Menandai tanggal setelah tugas dibuat tidak akan membuat entry baru untuk tugas tersebut.
2. Buka **Tugas Tim > Bikin Tugas KPI**.
3. Opsional: cari tugas sebelumnya pada kolom **Salin dari** dengan mengetik minimal dua huruf awal judul, lalu pilih hasil. Template hanya menyalin judul, deskripsi, tracking, satuan, mode, dan divisi; periode, penerima, target, bobot, dan Tugas Referensi tidak ikut disalin.
4. Isi judul, deskripsi bila perlu, cara tracking, satuan, mode, tanggal mulai, dan tanggal selesai.
5. Pilih penerima. Ketua/Direktur dapat memilih Kadiv atau Korwil.
6. Atur target per orang bila diperlukan. Target persen maksimal 100%.
7. Klik **Simpan & Lanjut Atur Bobot**.
8. Dari **Tugas yang Diberikan**, atur bobot seluruh tugas aktif masing-masing penerima hingga total tepat 100%.

### Langkah Menyetujui Entry dan Memantau

1. Buka **Tugas Tim > Perlu Saya Approve**.
2. Tinjau entry Kadiv/Korwil, lampiran, nilai, dan target.
3. Pilih **Setujui** atau **Tolak**. Penolakan wajib disertai alasan.
4. Klik **Proses**.
5. Buka **KPI Tim**, pilih bulan, lalu klik nama untuk melihat rincian anggota.

Jika memilih **Setujui**, entry Kadiv/Korwil menjadi terkunci dan pengisi melihat status **Sudah disetujui**. Progres KPI dihitung ulang, tetapi hanya naik sesuai nilai terhadap target, mode tugas, dan bobot. Jika memilih **Tolak**, isi alasan; pengisi melihat alasan tersebut di Tugas Saya pada bagian **Perlu Diperbaiki**, lalu dapat memperbaiki dan mengirim ulang.

Jika Anda juga menerima tugas KPI pribadi, buka **Tugas Saya**. Periksa **Perlu Diperbaiki**, lalu **Perlu Dikerjakan Hari Ini**; gunakan tombol pada kartu untuk mengisi atau memperbaiki entry. Setelah **Simpan**, cek status terbaru pada kartu tersebut.

### FAQ Ketua dan Direktur

**Kenapa nama tidak muncul pada Assign Ke?** Role penerima harus Kadiv atau Korwil. Pastikan status role KPI user sudah ditetapkan.

**Kapan hari kerja harus ditandai?** Sebelum membuat tugas Harian. Perubahan kalender setelah tugas dibuat tidak menambah entry untuk tugas lama.

**Approve berarti target tercapai?** Tidak. Approve hanya menyatakan entry lolos validasi; target tetap dibandingkan dengan nilai input.

## Kadiv

### Halaman dan Menu

- **Bikin Tugas KPI** (`/tasks/create`) dan **Tugas yang Diberikan** (`/tugas-diberikan`).
- **Perlu Saya Approve** (`/task-approve-queue`).
- **KPI Tim** (`/kpi-tim`), **Upload Data Resmi** (`/data-resmi`), dan **Rekonsiliasi** (`/rekonsiliasi`).

Kadiv dapat mengelola Staff di divisi yang sama. Staff pada divisi lain tidak muncul sebagai penerima tugas. Grup Lapangan di KPI Tim tampil sebagai Preview.

### Langkah Kerja

1. Buka **Tugas Tim > Bikin Tugas KPI**, isi rincian tugas, lalu pilih Staff di divisi Anda.
2. Isi target setiap Staff bila diperlukan dan simpan tugas.
3. Buka **Tugas yang Diberikan > Atur Bobot** untuk setiap Staff. Pastikan semua tugas aktif memiliki total bobot tepat 100%.
4. Buka **Perlu Saya Approve**, tinjau entry Staff, lalu setujui atau tolak dengan alasan.
5. Gunakan **KPI Tim** untuk memantau Staff sendiri. Data Lapangan berlabel Preview hanya untuk dilihat.
6. Jika diberi kewenangan mengunggah data resmi, buka **Data Resmi & Rekonsiliasi > Upload Data Resmi**, unduh template, isi, lalu unggah. Periksa daftar baris yang gagal dan perbaiki file.

Jika entry Staff **Disetujui**, entry terkunci dan status pada Tugas Saya berubah menjadi **Sudah disetujui**. Jika ditolak, isi alasan wajib; Staff melihat alasan itu pada **Perlu Diperbaiki**, membuka **Perbaiki**, lalu mengirim ulang entry. Persetujuan tidak otomatis membuat target tercapai.

Jika Anda juga menjadi penerima tugas KPI, cek tugas pribadi lewat **Tugas Saya**: buka **Perlu Diperbaiki** lebih dahulu, lanjutkan ke **Perlu Dikerjakan Hari Ini**, isi atau perbaiki entry dari kartu, lalu pantau status setelah menyimpan.

### FAQ Kadiv

**Kenapa Staff dari divisi lain tidak muncul?** Kadiv hanya dapat memberi tugas kepada Staff di divisinya sendiri. Periksa divisi pada akun Staff.

**Apakah entry Staff perlu dicek AM?** Tidak. Entry Staff diproses oleh Kadiv melalui Perlu Saya Approve.

**Apa arti Preview di grup Lapangan?** Anda boleh membaca KPI yang ditampilkan, tetapi tidak mengelola tugas anggota tersebut.

## Korwil

Korwil adalah user dengan role dasar Manager dan role KPI Korwil. Jika kedua role ada, panduan dan badge mengikuti Korwil, bukan AM.

### Halaman dan Menu

- **Bikin Tugas KPI** (`/tasks/create`) dan **Tugas yang Diberikan** (`/tugas-diberikan`).
- **Perlu Saya Cek** (`/task-check-queue`) dan **Perlu Saya Approve** (`/task-approve-queue`).
- **KPI Tim** (`/kpi-tim`) dan **Validasi FO**.

Korwil mengelola AM/FO pada cabang yang dikelolanya. Cabang Korwil dan anggota harus aktif dan cocok.

### Langkah Kerja

1. Buka **Tugas Tim > Bikin Tugas KPI**, isi tugas, lalu pilih AM/FO di cabang kelolaan.
2. Atur target bila diperlukan dan simpan tugas.
3. Buka **Tugas yang Diberikan > Atur Bobot** untuk tiap anggota. Total bobot tugas aktif harus tepat 100%.
4. Buka **Perlu Saya Approve** untuk menyetujui atau menolak entry AM/FO. Entry FO dapat diproses langsung tanpa menunggu cek AM.
5. Buka **KPI Tim** untuk memantau anggota pada cabang kelolaan.
6. Untuk laporan operasional FO, buka **Laporan FO & Bisnis > Validasi FO**. Proses ini berbeda dari approval entry KPI.

Setelah approve, pengisi melihat **Sudah disetujui** dan entry terkunci; progres KPI dihitung dari capaian terhadap target, bukan dari approval saja. Jika Korwil menolak, alasan tampil pada **Perlu Diperbaiki** di akun pengisi. Pengisi memperbaiki entry tanggal yang sama dan mengirim ulang. Untuk FO, keputusan Korwil dapat diberikan langsung meski statusnya belum **Dicek AM**.

Jika Anda juga menerima tugas KPI pribadi, gunakan **Tugas Saya**. Periksa **Perlu Diperbaiki** terlebih dahulu, lalu kartu **Perlu Dikerjakan Hari Ini**; isi atau perbaiki entry pada tanggal yang ditampilkan dan cek kembali status setelah **Simpan**.

### FAQ Korwil

**Kenapa AM atau FO tidak muncul?** Periksa role dasar (`manager` untuk AM atau `fo`), penempatan cabang aktif anggota, dan cabang kelolaan Korwil. Keduanya harus memiliki cabang yang sama.

**Perlukah menunggu AM mengecek entry FO?** Tidak. Korwil dapat menyetujui atau menolak entry FO secara langsung.

**Apakah laporan operasional FO sama dengan entry KPI FO?** Tidak. Laporan operasional diproses pada Validasi FO; entry KPI berada pada antrean tugas KPI.

## Staff

### Halaman dan Menu

- **Tugas Saya** (`/tugas-saya`), **KPI Saya** (`/kpi-saya`), dan **Tugas Tambahan**.
- **KPI Tim** (`/kpi-tim`) untuk melihat grup Lapangan sebagai Preview.
- **Rekonsiliasi** (`/rekonsiliasi`) bila tersedia pada akun.

### Langkah Kerja

1. Buka **Tugas Saya**. Tanggal pada header menunjukkan hari ini; navigasi bulan hanya berlaku untuk riwayat **Tugas Selesai**.
2. Cek **Perlu Diperbaiki** terlebih dahulu. Jika ada entry ditolak, baca alasan, klik **Perbaiki**, koreksi tanggal entry yang sama, lalu klik **Simpan**.
3. Cek **Perlu Dikerjakan Hari Ini**. Kartu tugas menampilkan progres dan status. Klik **Tampilkan lainnya** jika tugas lebih dari lima.
4. Klik **Isi Sekarang** pada tugas yang belum diisi. Di modal, pastikan tanggal benar, isi Nilai atau Checklist, tambahkan lampiran bila perlu, lalu klik **Simpan**.
5. Setelah halaman dimuat ulang, baca status pada kartu. **Sudah diisi, menunggu validasi** berarti tunggu Kadiv; **Sudah disetujui** berarti entry terkunci; **Ditolak** berarti perbaiki lagi dari bagian **Perlu Diperbaiki**.
6. Buka **KPI Saya** untuk melihat dampak ke progres bulanan. Entry yang disetujui hanya menaikkan KPI bila target dan aturan tugas terpenuhi.
7. Untuk aktivitas di luar tugas KPI, buka bagian **Tugas Tambahan**, klik **+ Tambah**, isi catatan, lalu simpan.
8. Jika perlu memantau KPI Lapangan, buka **KPI Tim** dan cari nama. Label Preview berarti data hanya bisa dibaca.

Staff tidak membuat tugas untuk anggota lain, tidak mengatur bobot tim, dan tidak menyetujui entry.

### FAQ Staff

**Kenapa anggota Lapangan hanya bisa dilihat?** Akses tersebut adalah Preview; hak kelola tugas tetap pada atasan yang berwenang.

**Apakah Tugas Tambahan menambah KPI?** Tidak. Catatan hanya untuk aktivitas tambahan dan tetap dapat dilihat atasan.

**Kenapa tugas tidak bisa diisi?** Periksa tanggal entry Harian, status validasi, serta apakah tugas sudah dibekukan atau dinonaktifkan.

## Superadmin

### Halaman dan Menu

- Seluruh menu administrasi yang tersedia, termasuk **Tugas Tim**, **Laporan & Pengaturan KPI**, **HR Management**, **Pengaturan**, dan **Pembersihan Data**.
- **Set Role KPI** (`/kpi-roles`), **Kelola Divisi** (`/divisions`), dan **Panel Koreksi Data** (`/superadmin/koreksi-data`).
- Menu tugas, antrean validasi, KPI Tim, data resmi, dan rekonsiliasi.

### Langkah Mengatur Role KPI

1. Buka **Laporan & Pengaturan KPI > Set Role KPI**.
2. Cari user berdasarkan nama atau username, lalu pilih **Atur Role KPI**.
3. Pilih satu role struktural. Divisi wajib diisi untuk Kadiv dan Staff.
4. Centang **Korwil** jika Manager juga menjalankan fungsi Korwil.
5. Simpan. Perubahan role KPI, divisi, atau status Korwil dapat membekukan tugas KPI aktif dan mengunci progresnya.

### Langkah Mengatur User dan Cabang

1. Buka **HR Management > User** untuk membuat atau memperbarui akun.
2. Pastikan role dasar seperti Manager/AM atau FO sesuai fungsi pengguna.
3. Atur penempatan cabang aktif pada pengelolaan user/cabang.
4. Untuk Korwil, tandai cabang yang dikelolanya.
5. Atur role KPI struktural dan status Korwil secara terpisah melalui Set Role KPI.

Role dasar dan role KPI disimpan melalui alur berbeda. Perubahan role dasar atau cabang tidak selalu membekukan tugas otomatis; nonaktifkan tugas yang tidak lagi diperlukan sebelum mutasi.

### Langkah Koreksi Data

1. Buka **Panel Koreksi Data**.
2. Cari entry berdasarkan user, judul tugas, status, atau tanggal.
3. Pilih **Koreksi**, perbarui nilai/status yang diizinkan, lalu isi alasan koreksi.
4. Untuk tugas terhapus, periksa detailnya sebelum memilih **Pulihkan**.

Untuk memantau tugas pribadi bila ada penugasan, buka **Tugas Saya**, cek **Perlu Diperbaiki**, lalu **Perlu Dikerjakan Hari Ini**. Superadmin tetap mengikuti tanggal entry dan status validasi; gunakan Panel Koreksi Data hanya untuk koreksi administratif yang beralasan, bukan untuk menggantikan alur pengisian biasa.

### FAQ Superadmin

**Kenapa Korwil tidak muncul sebagai penerima tugas Direktur?** Pastikan user memiliki role KPI Korwil pada Set Role KPI. Role dasar Manager saja akan dikenali sebagai AM.

**Kenapa AM/FO tidak muncul untuk Korwil?** Periksa role dasar, cabang aktif anggota, dan cabang kelolaan Korwil.

**Apa dampak perubahan role KPI?** Tugas KPI aktif dapat dibekukan dan progres dikunci. Periksa penugasan sebelum menyimpan mutasi.

**Apakah koreksi data perlu alasan?** Ya. Alasan diperlukan agar perubahan tercatat untuk audit.

---

Nama menu dapat berubah mengikuti pembaruan aplikasi. Jika menu atau akses tidak sesuai, periksa role, divisi, dan cabang akun sebelum mengubah data.
