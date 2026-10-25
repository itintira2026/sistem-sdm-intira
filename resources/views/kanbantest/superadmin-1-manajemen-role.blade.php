<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin — Manajemen Role</title>
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

        <!-- Header halaman -->
        <div
            style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px;flex-wrap:wrap;gap:12px;">
            <div>
                <h1 style="font-size:20px;margin:0 0 4px 0;">Manajemen Role</h1>
                <p style="margin:0;color:#6B7280;font-size:13px;">Cari user, lihat role saat ini, dan atur role baru</p>
            </div>
            <button
                style="background:#FFFFFF;color:#1B4D3E;border:1px solid #1B4D3E;padding:9px 16px;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;">
                + Tambah User
            </button>
        </div>

        <!-- Search & filter -->
        <div style="display:flex;gap:10px;margin:20px 0 16px 0;flex-wrap:wrap;">
            <input type="text" placeholder="Cari nama / username / email..."
                style="flex:1;min-width:220px;padding:9px 12px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;background:#fff;">
            <select style="padding:9px 12px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;background:#fff;">
                <option>Semua Role</option>
                <option>Owner</option>
                <option>Ketua</option>
                <option>Direktur Utama</option>
                <option>Direktur Bisnis</option>
                <option>Direktur Operasional</option>
                <option>SPI</option>
                <option>Kadiv</option>
                <option>Staff</option>
                <option>Korea</option>
                <option>Manager (AM)</option>
                <option>FO</option>
            </select>
            <select style="padding:9px 12px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;background:#fff;">
                <option>Semua Status</option>
                <option>Aktif</option>
                <option>Nonaktif</option>
            </select>
        </div>

        <!-- Tabel user -->
        <table
            style="width:100%;border-collapse:collapse;background:#FFFFFF;border-radius:10px;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,0.04);">
            <thead>
                <tr>
                    <th
                        style="text-align:left;font-size:11.5px;text-transform:uppercase;letter-spacing:0.4px;color:#6B7280;background:#FAFBFC;padding:11px 16px;border-bottom:1px solid #E2E5E9;">
                        Nama</th>
                    <th
                        style="text-align:left;font-size:11.5px;text-transform:uppercase;letter-spacing:0.4px;color:#6B7280;background:#FAFBFC;padding:11px 16px;border-bottom:1px solid #E2E5E9;">
                        Role Saat Ini</th>
                    <th
                        style="text-align:left;font-size:11.5px;text-transform:uppercase;letter-spacing:0.4px;color:#6B7280;background:#FAFBFC;padding:11px 16px;border-bottom:1px solid #E2E5E9;">
                        Status</th>
                    <th
                        style="text-align:left;font-size:11.5px;text-transform:uppercase;letter-spacing:0.4px;color:#6B7280;background:#FAFBFC;padding:11px 16px;border-bottom:1px solid #E2E5E9;">
                        Aksi</th>
                </tr>
            </thead>
            <tbody>

                <!-- Row 1 -->
                <tr>
                    <td style="padding:12px 16px;border-bottom:1px solid #E2E5E9;font-size:13.5px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div
                                style="width:32px;height:32px;border-radius:50%;background:#E4EEEA;color:#123328;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;flex-shrink:0;">
                                RH</div>
                            Rina Hartono <span style="color:#6B7280;">· rina.hr</span>
                        </div>
                    </td>
                    <td style="padding:12px 16px;border-bottom:1px solid #E2E5E9;">
                        <div style="display:flex;gap:5px;flex-wrap:wrap;">
                            <span
                                style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#E4EEEA;color:#123328;">HR</span>
                            <span
                                style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#F6E9D8;color:#8A5A1E;">Kadiv</span>
                        </div>
                    </td>
                    <td style="padding:12px 16px;border-bottom:1px solid #E2E5E9;">
                        <span
                            style="padding:2px 8px;border-radius:5px;font-size:11px;font-weight:600;background:#E4F4EB;color:#2F9E68;">Aktif</span>
                    </td>
                    <td style="padding:12px 16px;border-bottom:1px solid #E2E5E9;">
                        <button onclick="document.getElementById('modalRole').style.display='flex'"
                            style="background:none;color:#6B7280;border:1px solid #E2E5E9;padding:7px 14px;border-radius:7px;font-size:12.5px;cursor:pointer;">Atur
                            Role</button>
                    </td>
                </tr>

                <!-- Row 2 -->
                <tr>
                    <td style="padding:12px 16px;border-bottom:1px solid #E2E5E9;font-size:13.5px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div
                                style="width:32px;height:32px;border-radius:50%;background:#E4EEEA;color:#123328;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;flex-shrink:0;">
                                BS</div>
                            Budi Santoso <span style="color:#6B7280;">· budi.s</span>
                        </div>
                    </td>
                    <td style="padding:12px 16px;border-bottom:1px solid #E2E5E9;">
                        <div style="display:flex;gap:5px;flex-wrap:wrap;">
                            <span
                                style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#E4EEEA;color:#123328;">Manager</span>
                            <span
                                style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#F6E9D8;color:#8A5A1E;">Korea</span>
                        </div>
                    </td>
                    <td style="padding:12px 16px;border-bottom:1px solid #E2E5E9;">
                        <span
                            style="padding:2px 8px;border-radius:5px;font-size:11px;font-weight:600;background:#E4F4EB;color:#2F9E68;">Aktif</span>
                    </td>
                    <td style="padding:12px 16px;border-bottom:1px solid #E2E5E9;">
                        <button onclick="document.getElementById('modalRole').style.display='flex'"
                            style="background:none;color:#6B7280;border:1px solid #E2E5E9;padding:7px 14px;border-radius:7px;font-size:12.5px;cursor:pointer;">Atur
                            Role</button>
                    </td>
                </tr>

                <!-- Row 3 -->
                <tr>
                    <td style="padding:12px 16px;border-bottom:1px solid #E2E5E9;font-size:13.5px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div
                                style="width:32px;height:32px;border-radius:50%;background:#E4EEEA;color:#123328;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;flex-shrink:0;">
                                AN</div>
                            Andi Nugraha <span style="color:#6B7280;">· andi.n</span>
                        </div>
                    </td>
                    <td style="padding:12px 16px;border-bottom:1px solid #E2E5E9;">
                        <span
                            style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#E4EEEA;color:#123328;">FO</span>
                    </td>
                    <td style="padding:12px 16px;border-bottom:1px solid #E2E5E9;">
                        <span
                            style="padding:2px 8px;border-radius:5px;font-size:11px;font-weight:600;background:#E4F4EB;color:#2F9E68;">Aktif</span>
                    </td>
                    <td style="padding:12px 16px;border-bottom:1px solid #E2E5E9;">
                        <button onclick="document.getElementById('modalRole').style.display='flex'"
                            style="background:none;color:#6B7280;border:1px solid #E2E5E9;padding:7px 14px;border-radius:7px;font-size:12.5px;cursor:pointer;">Atur
                            Role</button>
                    </td>
                </tr>

                <!-- Row 4 -->
                <tr>
                    <td style="padding:12px 16px;font-size:13.5px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div
                                style="width:32px;height:32px;border-radius:50%;background:#E4EEEA;color:#123328;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;flex-shrink:0;">
                                SW</div>
                            Siti Wulandari <span style="color:#6B7280;">· siti.w</span>
                        </div>
                    </td>
                    <td style="padding:12px 16px;">
                        <span
                            style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#EEF0F2;color:#6B7280;">Belum
                            diset</span>
                    </td>
                    <td style="padding:12px 16px;">
                        <span
                            style="padding:2px 8px;border-radius:5px;font-size:11px;font-weight:600;background:#FBF0E0;color:#D68A2B;">Nonaktif</span>
                    </td>
                    <td style="padding:12px 16px;">
                        <button onclick="document.getElementById('modalRole').style.display='flex'"
                            style="background:none;color:#6B7280;border:1px solid #E2E5E9;padding:7px 14px;border-radius:7px;font-size:12.5px;cursor:pointer;">Atur
                            Role</button>
                    </td>
                </tr>

            </tbody>
        </table>

        <p style="font-size:12px;color:#9AA1A9;margin-top:16px;">
            Link terkait: → <a href="superadmin-2-koreksi-data" style="color:#1B4D3E;">Halaman 2: Panel Koreksi
                Data</a>
        </p>

    </div>

    <!-- ============ MODAL: Atur Role ============ -->
    <div id="modalRole"
        style="display:none;position:fixed;inset:0;background:rgba(20,25,22,0.45);align-items:center;justify-content:center;z-index:50;">
        <div
            style="background:#fff;border-radius:12px;width:480px;max-width:92vw;max-height:86vh;overflow-y:auto;padding:24px;">

            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;">
                <h3 style="margin:0;font-size:16px;">Atur Role — Rina Hartono</h3>
                <button onclick="document.getElementById('modalRole').style.display='none'"
                    style="background:none;border:none;font-size:18px;cursor:pointer;color:#6B7280;">&times;</button>
            </div>

            <div style="margin-bottom:16px;">
                <h5
                    style="font-size:11.5px;text-transform:uppercase;letter-spacing:0.4px;color:#6B7280;margin:0 0 8px 0;">
                    Role Saat Ini</h5>
                <div style="display:flex;gap:5px;flex-wrap:wrap;">
                    <span
                        style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#E4EEEA;color:#123328;">HR
                        &times;</span>
                    <span
                        style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#F6E9D8;color:#8A5A1E;">Kadiv
                        &times;</span>
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <h5
                    style="font-size:11.5px;text-transform:uppercase;letter-spacing:0.4px;color:#6B7280;margin:0 0 8px 0;">
                    Tambah Role Baru</h5>
                <div style="display:flex;gap:10px;flex-wrap:wrap;font-size:12.5px;">
                    <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox"> Owner</label>
                    <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox"> Ketua</label>
                    <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox"> Direktur
                        Utama</label>
                    <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox"> Direktur
                        Bisnis</label>
                    <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox"> Direktur
                        Operasional</label>
                    <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox"> SPI</label>
                    <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox" checked>
                        Kadiv</label>
                    <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox"> Staff</label>
                    <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox"> Korea</label>
                </div>
            </div>

            <!-- Muncul cuma kalau centang Korea -->
            <div
                style="margin-bottom:16px;background:#FAFBFC;border:1px dashed #E2E5E9;padding:12px;border-radius:8px;">
                <h5
                    style="font-size:11.5px;text-transform:uppercase;letter-spacing:0.4px;color:#6B7280;margin:0 0 8px 0;">
                    Pilih Branch yang Dinaungi (khusus role Korea)</h5>
                <div style="display:flex;gap:10px;flex-wrap:wrap;font-size:12.5px;">
                    <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox"> Cabang
                        Kemang</label>
                    <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox"> Cabang Blok
                        M</label>
                    <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox"> Cabang
                        Fatmawati</label>
                </div>
            </div>

            <div style="background:#E4EEEA;padding:12px;border-radius:8px;margin-bottom:18px;">
                <h5
                    style="font-size:11.5px;text-transform:uppercase;letter-spacing:0.4px;color:#6B7280;margin:0 0 8px 0;">
                    Preview Hasil Akhir</h5>
                <div style="display:flex;gap:5px;flex-wrap:wrap;">
                    <span
                        style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#fff;color:#123328;">HR</span>
                    <span
                        style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#fff;color:#8A5A1E;">Kadiv</span>
                </div>
            </div>

            <div style="display:flex;gap:10px;">
                <button onclick="document.getElementById('modalRole').style.display='none'"
                    style="background:#1B4D3E;color:#fff;border:none;padding:9px 16px;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;">Simpan</button>
                <button onclick="document.getElementById('modalRole').style.display='none'"
                    style="background:none;color:#6B7280;border:1px solid #E2E5E9;padding:9px 16px;border-radius:7px;font-size:13px;cursor:pointer;">Batal</button>
            </div>

        </div>
    </div>

</body>

</html>
