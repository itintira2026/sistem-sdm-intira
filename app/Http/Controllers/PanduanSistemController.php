<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * BARU — Halaman Panduan Sistem (/panduan-sistem), satu link untuk semua
 * peran KPI. Isi halaman disesuaikan otomatis dengan peran user yang login.
 *
 * ATURAN:
 * - Peran utama = peran KPI tertinggi yang dimiliki user (urutan di
 *   ROLE_PRIORITY). Role dasar non-KPI (finance, hr, marketing) TIDAK
 *   pernah ditampilkan atau dipakai.
 * - Atasan mewarisi modul bawahan. Modul yang bukan tugasnya tapi perlu
 *   dipahami alurnya ditandai 'context'.
 * - FAQ tampil untuk semua.
 *
 * KALAU ADA PERAN KPI BARU: tambah di ROLE_PRIORITY dan PROFILE_MODULES.
 * Dua konstanta ini satu-satunya tempat pemetaannya.
 */
class PanduanSistemController extends Controller
{
    // Urutan = prioritas (atas = tertinggi). Korwil harus di atas manager,
    // karena Korwil di sistem ini juga punya role dasar manager (AM).
    private const ROLE_PRIORITY = [
        'superadmin' => ['profile' => 'superadmin', 'label' => 'Superadmin'],
        'owner' => ['profile' => 'owner', 'label' => 'Owner'],
        'ketua' => ['profile' => 'direktur', 'label' => 'Ketua'],
        'direktur_utama' => ['profile' => 'direktur', 'label' => 'Direktur Utama'],
        'direktur_bisnis' => ['profile' => 'direktur', 'label' => 'Direktur Bisnis'],
        'direktur_operasional' => ['profile' => 'direktur', 'label' => 'Direktur Operasional'],
        'kadiv' => ['profile' => 'kadiv', 'label' => 'Kadiv'],
        'korwil' => ['profile' => 'korwil', 'label' => 'Korwil'],
        'staff' => ['profile' => 'staff', 'label' => 'Staff'],
        'manager' => ['profile' => 'am', 'label' => 'AM'],
        'fo' => ['profile' => 'fo', 'label' => 'FO'],
    ];

    // slug => judul. Urutan di sini = urutan tampil di halaman.
    // Isi tiap modul: resources/views/panduan-sistem/modul/{slug}.blade.php
    private const MODULES = [
        'pengantar' => 'Pengantar & Istilah',
        'isi-entry' => 'Mengisi Entry (Tugas Saya)',
        'kpi-saya' => 'KPI Saya',
        'tugas-tambahan' => 'Tugas Tambahan',
        'laporan-fo' => 'Laporan FO',
        'cek-entry' => 'Mengecek Entry',
        'approve-entry' => 'Approve Entry',
        'membuat-tugas' => 'Membuat Tugas KPI',
        'atur-bobot' => 'Atur Bobot, Target & Nonaktifkan',
        'kpi-tim' => 'KPI Tim & Detail per Orang',
        'rekonsiliasi' => 'Rekonsiliasi',
        'upload-data-resmi' => 'Upload Data Resmi',
        'set-hari-kerja' => 'Set Hari Kerja',
        'admin-role-kpi' => 'Set Role KPI & Mutasi Jabatan',
        'admin-koreksi-data' => 'Koreksi Data',
        'admin-kelola-lampiran' => 'Kelola Lampiran',
        'faq' => 'FAQ',
    ];

    // 'duty' = tugas perannya, 'context' = bukan tugasnya, tapi perlu paham alurnya.
    private const PROFILE_MODULES = [
        'fo' => [
            'pengantar' => 'duty',
            'isi-entry' => 'duty',
            'kpi-saya' => 'duty',
            'tugas-tambahan' => 'duty',
            'laporan-fo' => 'duty',
            'faq' => 'duty',
        ],
        'am' => [
            'pengantar' => 'duty',
            'isi-entry' => 'duty',
            'kpi-saya' => 'duty',
            'tugas-tambahan' => 'duty',
            'laporan-fo' => 'duty',
            'cek-entry' => 'duty',
            'faq' => 'duty',
        ],
        'staff' => [
            'pengantar' => 'duty',
            'isi-entry' => 'duty',
            'kpi-saya' => 'duty',
            'tugas-tambahan' => 'duty',
            'kpi-tim' => 'duty',
            'rekonsiliasi' => 'duty',
            'faq' => 'duty',
        ],
        'korwil' => [
            'pengantar' => 'duty',
            'isi-entry' => 'duty',
            'kpi-saya' => 'duty',
            'tugas-tambahan' => 'duty',
            'laporan-fo' => 'duty',
            'cek-entry' => 'duty',
            'approve-entry' => 'duty',
            'membuat-tugas' => 'duty',
            'atur-bobot' => 'duty',
            'kpi-tim' => 'duty',
            'faq' => 'duty',
        ],
        'kadiv' => [
            'pengantar' => 'duty',
            'isi-entry' => 'duty',
            'kpi-saya' => 'duty',
            'tugas-tambahan' => 'duty',
            'approve-entry' => 'duty',
            'membuat-tugas' => 'duty',
            'atur-bobot' => 'duty',
            'kpi-tim' => 'duty',
            'rekonsiliasi' => 'duty',
            'upload-data-resmi' => 'duty',
            'faq' => 'duty',
        ],
        'direktur' => [
            'pengantar' => 'duty',
            'isi-entry' => 'context',
            'kpi-saya' => 'context',
            'tugas-tambahan' => 'context',
            'cek-entry' => 'context',
            'approve-entry' => 'duty',
            'membuat-tugas' => 'duty',
            'atur-bobot' => 'duty',
            'kpi-tim' => 'duty',
            'rekonsiliasi' => 'duty',
            'set-hari-kerja' => 'duty',
            'faq' => 'duty',
        ],
        'owner' => [
            'pengantar' => 'duty',
            'isi-entry' => 'context',
            'kpi-saya' => 'context',
            'tugas-tambahan' => 'context',
            'cek-entry' => 'context',
            'approve-entry' => 'context',
            'membuat-tugas' => 'context',
            'atur-bobot' => 'context',
            'kpi-tim' => 'duty',
            'rekonsiliasi' => 'duty',
            'faq' => 'duty',
        ],
    ];

    public function index()
    {
        $user = Auth::user();
        $resolved = $this->resolveRole($user);

        // Peran tanpa KPI (HR, marketing, dst) -> pesan ramah, bukan 403.
        if (! $resolved) {
            return view('panduan-sistem.index', [
                'noAccess' => true,
                'roleLabel' => null,
                'sections' => collect(),
            ]);
        }

        $sections = collect(self::MODULES)
            ->map(fn($title, $slug) => [
                'slug' => $slug,
                'title' => $title,
                'mode' => $this->modeFor($resolved['profile'], $slug),
                'view' => 'panduan-sistem.modul.' . $slug,
            ])
            ->filter(fn($section) => $section['mode'] !== null)
            ->values();

        return view('panduan-sistem.index', [
            'noAccess' => false,
            'roleLabel' => $resolved['label'],
            'sections' => $sections,
        ]);
    }

    private function resolveRole(User $user): ?array
    {
        foreach (self::ROLE_PRIORITY as $role => $info) {
            if ($user->hasRole($role)) {
                return $info;
            }
        }

        return null;
    }

    private function modeFor(string $profile, string $slug): ?string
    {
        // Superadmin: semua modul, semuanya dianggap tugasnya.
        if ($profile === 'superadmin') {
            return 'duty';
        }

        return self::PROFILE_MODULES[$profile][$slug] ?? null;
    }
}
