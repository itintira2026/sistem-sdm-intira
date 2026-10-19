{{-- Tampil untuk semua. Jawaban singkat, tautkan ke bagian terkait. --}}
<h4>Kenapa KPI saya 0%?</h4>
<p>KPI dihitung dari kontribusi Tugas KPI aktif pada bulan yang dipilih. Total bisa 0% jika belum ada tugas di bulan
    itu, entry belum disetujui sehingga progresnya 0, atau bobot belum diatur. Pastikan bulan sesuai tanggal selesai
    tugas dan minta atasan memeriksa bobot; tugas yang dibekukan atau dinonaktifkan tidak masuk total. Lihat <a
        href="#kpi-saya" @click.prevent="go('kpi-saya')" class="text-teal-700 underline">KPI Saya</a> untuk rincian per
    tugas.</p>
<h4>Kenapa entry tidak bisa diisi?</h4>
<p>Entry Harian yang masih pending hanya dapat diisi tepat pada tanggalnya; tanggal Harian yang belum ditandai sebagai
    hari kerja tidak memiliki entry. Entry yang sudah dicek/disetujui atau tugas yang dinonaktifkan/dibekukan tidak bisa
    diubah. Entry yang ditolak dapat diperbaiki dan dikirim ulang. Lihat <a href="#isi-entry"
        @click.prevent="go('isi-entry')" class="text-teal-700 underline">Mengisi Entry</a>.</p>
<h4>Kenapa tugas saya hilang dari Perlu Dikerjakan?</h4>
<p>Setelah tanggal selesai lewat, tugas berpindah ke bagian <strong>Tugas Selesai</strong>. Bagian itu mengikuti bulan
    yang dipilih di halaman Tugas Saya; pilih bulan yang memuat tanggal selesai tugas.</p>
<h4>Kenapa bobot saya belum pas 100%?</h4>
<p>Tugas KPI baru belum menyumbang bobot sampai atasan mengaturnya. Minta atasan meninjau semua tugas KPI aktif Anda
    pada bulan terkait dan menyesuaikan totalnya menjadi 100%.</p>
<h4>Kenapa lampiran ditolak?</h4>
<p>Periksa tipe file dan batas yang tertera pada form: gambar/dokumen maksimal 3 MB dan video maksimal 20 MB. Tipe yang
    didukung meliputi JPG, JPEG, PNG, PDF, XLSX, XLS, CSV, MP4, MOV, dan AVI. Lihat bagian <a href="#isi-entry"
        @click.prevent="go('isi-entry')" class="text-teal-700 underline">Lampiran pada Mengisi Entry</a>.</p>
<h4>Saya dipindah jabatan, bagaimana tugas lama?</h4>
<p>Perubahan role KPI, divisi, atau status Korwil akan membekukan Tugas KPI aktif dan mengunci progresnya; tugas beku
    tidak masuk total KPI berikutnya. Perubahan role dasar atau cabang saja tidak memicu freeze otomatis, jadi minta
    atasan menonaktifkan tugas lama sebelum mutasi. Tugas untuk jabatan baru perlu ditetapkan dan diberi bobot kembali.
    Lihat <a href="#pengantar" @click.prevent="go('pengantar')" class="text-teal-700 underline">Pengantar &amp;
        Istilah</a>.</p>
