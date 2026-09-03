<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kadiv — Form Breakdown</title>
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

        <h1 style="font-size:20px;margin:0 0 4px 0;">Board Saya</h1>
        <p style="margin:0;color:#6B7280;font-size:13px;">Login sebagai: Kadiv Marketing</p>

        <div style="display:flex;gap:4px;margin:14px 0 20px 0;border-bottom:1px solid #E2E5E9;">
            <a href="kadiv-1-board" style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">Personal
                Board</a>
            <a href="kadiv-2-list-goal" style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">List
                Goal Diterima</a>
            <a href="kadiv-3-form-breakdown"
                style="text-decoration:none;padding:9px 14px;font-size:13px;color:#1B4D3E;border-bottom:2px solid #1B4D3E;font-weight:700;">Form
                Breakdown</a>
            <a href="kadiv-4-drilldown"
                style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">Drill-down Goal</a>
        </div>

        <div
            style="background:#fff;border-radius:10px;padding:22px;max-width:760px;box-shadow:0 1px 2px rgba(0,0,0,0.05);">

            <div
                style="background:#E4EEEA;padding:12px 14px;border-radius:8px;font-size:12.5px;margin-bottom:18px;color:#123328;">
                Goal induk: <strong>Rekrut Anggota Baru (porsi Marketing)</strong> — sisa alokasi kuantitatif: 70/hari
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12.5px;font-weight:600;margin-bottom:8px;">Assign ke</label>
                <div style="display:flex;gap:8px;">
                    <button
                        style="flex:1;padding:9px;border-radius:7px;font-size:12.5px;font-weight:600;cursor:pointer;background:#1B4D3E;color:#fff;border:none;">Staff</button>
                    <button
                        style="flex:1;padding:9px;border-radius:7px;font-size:12.5px;font-weight:600;cursor:pointer;background:#fff;color:#1B4D3E;border:1px solid #1B4D3E;">Korea</button>
                </div>
            </div>

            <div
                style="display:grid;grid-template-columns:1.3fr 1.2fr 0.9fr 0.6fr auto;gap:8px;padding:0 10px;font-size:10px;color:#6B7280;text-transform:uppercase;letter-spacing:0.3px;margin-bottom:6px;">
                <span>Assign ke</span><span>Judul Jobdesk</span><span>Tipe
                    Kontribusi</span><span>Porsi</span><span></span>
            </div>

            <div
                style="display:grid;grid-template-columns:1.3fr 1.2fr 0.9fr 0.6fr auto;gap:8px;align-items:center;background:#FAFBFC;padding:10px;border-radius:8px;margin-bottom:8px;border:1px solid #E2E5E9;">
                <select style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                    <option>Diri Sendiri</option>
                    <option selected>Staff — Dewi Anggraini</option>
                    <option>Staff — Fajar Ramadhan</option>
                    <option>Korea Jaksel</option>
                    <option>Korea Jakut</option>
                </select>
                <input type="text" value="Rekrut Anggota Baru"
                    style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                <select style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                    <option selected>Kuantitatif</option>
                    <option>Taktis</option>
                </select>
                <input type="text" value="35 /hari"
                    style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                <button
                    style="background:none;border:none;color:#C6493C;cursor:pointer;font-size:16px;">&times;</button>
            </div>

            <div
                style="display:grid;grid-template-columns:1.3fr 1.2fr 0.9fr 0.6fr auto;gap:8px;align-items:center;background:#FAFBFC;padding:10px;border-radius:8px;margin-bottom:8px;border:1px solid #E2E5E9;">
                <select style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                    <option>Diri Sendiri</option>
                    <option>Staff — Dewi Anggraini</option>
                    <option selected>Staff — Fajar Ramadhan</option>
                    <option>Korea Jaksel</option>
                    <option>Korea Jakut</option>
                </select>
                <input type="text" value="Rekrut Anggota Baru"
                    style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                <select style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                    <option selected>Kuantitatif</option>
                    <option>Taktis</option>
                </select>
                <input type="text" value="35 /hari"
                    style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                <button
                    style="background:none;border:none;color:#C6493C;cursor:pointer;font-size:16px;">&times;</button>
            </div>

            <!-- Row taktis: turunan jadi jobdesk berbeda satuan -->
            <div
                style="display:grid;grid-template-columns:1.3fr 1.2fr 0.9fr 0.6fr auto;gap:8px;align-items:center;background:#EDE8FA;padding:10px;border-radius:8px;margin-bottom:8px;border:1px solid #E2E5E9;">
                <select style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                    <option selected>Diri Sendiri</option>
                    <option>Staff — Dewi Anggraini</option>
                    <option>Korea Jaksel</option>
                </select>
                <input type="text" value="Sebar Brosur Promosi — 100 lembar/hari per FO"
                    style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                <select style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                    <option>Kuantitatif</option>
                    <option selected>Taktis</option>
                </select>
                <input type="text" value="—" disabled
                    style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;color:#9AA1A9;">
                <button
                    style="background:none;border:none;color:#C6493C;cursor:pointer;font-size:16px;">&times;</button>
            </div>

            <button
                style="background:none;border:1px dashed #E2E5E9;color:#1B4D3E;padding:8px;width:100%;border-radius:7px;font-size:12.5px;font-weight:600;cursor:pointer;margin-bottom:10px;">+
                Tambah Baris</button>

            <div style="font-size:12px;color:#6B7280;margin-bottom:16px;">Total dialokasikan kuantitatif: <strong
                    style="color:#1B4D3E;">70/70</strong></div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12.5px;font-weight:600;margin-bottom:6px;">Deadline</label>
                <input type="text" placeholder="Berkelanjutan (harian)"
                    style="width:100%;padding:9px 11px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12.5px;font-weight:600;margin-bottom:6px;">Lampiran Instruksi
                    (opsional)</label>
                <input type="text" placeholder="Upload file..."
                    style="width:100%;padding:9px 11px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;">
            </div>

            <div style="display:flex;gap:10px;margin-top:18px;">
                <a href="kadiv-4-drilldown.html" style="text-decoration:none;">
                    <button
                        style="background:#1B4D3E;color:#fff;border:none;padding:9px 16px;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;">Simpan
                        & Kirim</button>
                </a>
                <a href="kadiv-2-list-goal.html" style="text-decoration:none;">
                    <button
                        style="background:none;color:#6B7280;border:1px solid #E2E5E9;padding:9px 16px;border-radius:7px;font-size:13px;cursor:pointer;">Batal</button>
                </a>
            </div>

        </div>

    </div>

</body>

</html>
