<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin — Panel Koreksi Data</title>
    <style>
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: #F4F6F8;
            color: #1E2430;
        }

        * {
            box-sizing: border-box;
        }
    </style>
</head>

<body>

    <div style="padding:28px 32px;max-width:1100px;margin:0 auto;">

        <div style="margin-bottom:20px;">
            <h1 style="font-size:20px;margin:0 0 4px 0;">Panel Koreksi Data</h1>
            <p style="margin:0;color:#6B7280;font-size:13px;">Akses penuh ke semua board — cari user, buka board-nya,
                reassign / hapus card yang salah input</p>
        </div>

        <!-- Pencarian user target -->
        <div style="display:flex;gap:10px;margin-bottom:20px;">
            <input type="text" placeholder="Cari user untuk dibuka board-nya..."
                style="flex:1;padding:9px 12px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;background:#fff;">
            <button
                style="background:#1B4D3E;color:#fff;border:none;padding:9px 18px;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;">Buka
                Board</button>
        </div>

        <!-- Contoh board yang sedang dibuka (reuse tampilan kanban + tombol admin override) -->
        <div style="background:#fff;border:1px solid #E2E5E9;border-radius:10px;padding:16px;margin-bottom:16px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                <div>
                    <div style="font-size:13.5px;font-weight:700;">Board: Andi Nugraha (FO — Cabang Kemang)</div>
                    <div style="font-size:12px;color:#6B7280;">Mode admin — semua card bisa direassign / dihapus /
                        direset statusnya</div>
                </div>
                <span
                    style="padding:2px 8px;border-radius:5px;font-size:11px;font-weight:600;background:#FBE9E7;color:#C6493C;">Mode
                    Override Aktif</span>
            </div>

            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;">

                <div style="background:#EEF1F0;border-radius:10px;padding:12px;">
                    <h4
                        style="margin:0 0 10px 4px;font-size:12.5px;color:#6B7280;text-transform:uppercase;letter-spacing:0.4px;">
                        To Do</h4>
                    <div
                        style="background:#fff;border-radius:9px;padding:12px 13px;margin-bottom:10px;border-left:3px solid #C98A3D;box-shadow:0 1px 2px rgba(20,30,25,0.05);">
                        <div style="font-weight:600;font-size:13.5px;margin-bottom:8px;">Rekrut Anggota Baru</div>
                        <div style="font-size:11px;color:#6B7280;margin-bottom:10px;">Target 5/hari · Dari: AM Kemang
                        </div>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <button
                                style="background:#fff;color:#1B4D3E;border:1px solid #1B4D3E;padding:4px 8px;border-radius:5px;font-size:10.5px;cursor:pointer;">Reassign</button>
                            <button
                                style="background:#fff;color:#D68A2B;border:1px solid #D68A2B;padding:4px 8px;border-radius:5px;font-size:10.5px;cursor:pointer;">Reset
                                Status</button>
                            <button
                                style="background:#fff;color:#C6493C;border:1px solid #C6493C;padding:4px 8px;border-radius:5px;font-size:10.5px;cursor:pointer;">Hapus</button>
                        </div>
                    </div>
                </div>

                <div style="background:#EEF1F0;border-radius:10px;padding:12px;">
                    <h4
                        style="margin:0 0 10px 4px;font-size:12.5px;color:#6B7280;text-transform:uppercase;letter-spacing:0.4px;">
                        In Progress</h4>
                    <div
                        style="background:#fff;border-radius:9px;padding:12px 13px;border-left:3px solid #1B4D3E;box-shadow:0 1px 2px rgba(20,30,25,0.05);">
                        <div style="font-weight:600;font-size:13.5px;margin-bottom:8px;">Follow up member trial</div>
                        <div style="font-size:11px;color:#6B7280;margin-bottom:10px;">Dari: AM Kemang · Due 5 Sep</div>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <button
                                style="background:#fff;color:#1B4D3E;border:1px solid #1B4D3E;padding:4px 8px;border-radius:5px;font-size:10.5px;cursor:pointer;">Reassign</button>
                            <button
                                style="background:#fff;color:#C6493C;border:1px solid #C6493C;padding:4px 8px;border-radius:5px;font-size:10.5px;cursor:pointer;">Hapus</button>
                        </div>
                    </div>
                </div>

                <div style="background:#EEF1F0;border-radius:10px;padding:12px;">
                    <h4
                        style="margin:0 0 10px 4px;font-size:12.5px;color:#6B7280;text-transform:uppercase;letter-spacing:0.4px;">
                        Review</h4>
                </div>

                <div style="background:#EEF1F0;border-radius:10px;padding:12px;">
                    <h4
                        style="margin:0 0 10px 4px;font-size:12.5px;color:#6B7280;text-transform:uppercase;letter-spacing:0.4px;">
                        Done</h4>
                    <div
                        style="background:#fff;border-radius:9px;padding:12px 13px;border-left:3px solid #C98A3D;box-shadow:0 1px 2px rgba(20,30,25,0.05);">
                        <div style="font-weight:600;font-size:13.5px;margin-bottom:8px;">Rekrut Anggota Baru — Kemarin
                        </div>
                        <div style="font-size:11px;color:#6B7280;margin-bottom:10px;">5/5 tercapai · 31 Agu</div>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <button
                                style="background:#fff;color:#D68A2B;border:1px solid #D68A2B;padding:4px 8px;border-radius:5px;font-size:10.5px;cursor:pointer;">Reset
                                Status</button>
                            <button
                                style="background:#fff;color:#C6493C;border:1px solid #C6493C;padding:4px 8px;border-radius:5px;font-size:10.5px;cursor:pointer;">Hapus</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div
            style="background:#fff;border:1px dashed #E2E5E9;border-radius:10px;padding:30px 20px;text-align:center;color:#6B7280;font-size:13px;">
            <strong style="display:block;color:#1E2430;margin-bottom:4px;font-size:13.5px;">Catatan
                implementasi</strong>
            Panel ini murni reuse komponen board dari role lain (Kanban Column) — bedanya tiap card dapat tombol
            tambahan (Reassign / Reset Status / Hapus) yang hanya tampil kalau user login sebagai Superadmin.
        </div>

        <p style="font-size:12px;color:#9AA1A9;margin-top:16px;">
            ← <a href="superadmin-1-manajemen-role" style="color:#1B4D3E;">Kembali ke Halaman 1: Manajemen Role</a>
        </p>

    </div>

</body>

</html>
