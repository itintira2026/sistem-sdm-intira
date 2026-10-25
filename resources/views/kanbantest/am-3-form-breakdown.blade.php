<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AM — Form Breakdown ke FO</title>
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
        <p style="margin:0;color:#6B7280;font-size:13px;">Login sebagai: AM Cabang Kemang</p>

        <div style="display:flex;gap:4px;margin:14px 0 20px 0;border-bottom:1px solid #E2E5E9;">
            <a href="am-1-board" style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">Personal
                Board</a>
            <a href="am-2-list-goal" style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">List
                Goal Diterima</a>
            <a href="am-3-form-breakdown"
                style="text-decoration:none;padding:9px 14px;font-size:13px;color:#1B4D3E;border-bottom:2px solid #1B4D3E;font-weight:700;">Form
                Breakdown</a>
            <a href="am-4-drilldown"
                style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">Drill-down Goal</a>
        </div>

        <div
            style="background:#fff;border-radius:10px;padding:22px;max-width:760px;box-shadow:0 1px 2px rgba(0,0,0,0.05);">

            <div
                style="background:#E4EEEA;padding:12px 14px;border-radius:8px;font-size:12.5px;margin-bottom:18px;color:#123328;">
                Goal induk: <strong>Rekrut Anggota Baru (porsi Cabang Kemang)</strong> — sisa alokasi kuantitatif:
                15/hari
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12.5px;font-weight:600;margin-bottom:8px;">Cara Assign</label>
                <div style="display:flex;gap:8px;">
                    <button
                        style="flex:1;padding:9px;border-radius:7px;font-size:12.5px;font-weight:600;cursor:pointer;background:#1B4D3E;color:#fff;border:none;">Pilih
                        Manual</button>
                    <button
                        style="flex:1;padding:9px;border-radius:7px;font-size:12.5px;font-weight:600;cursor:pointer;background:#fff;color:#1B4D3E;border:1px solid #1B4D3E;">Assign
                        ke Semua FO</button>
                </div>
                <p style="font-size:11.5px;color:#6B7280;margin:8px 0 0 0;">
                    "Assign ke Semua FO" otomatis generate 1 baris per FO aktif di Cabang Kemang (via data
                    <code>branch_users</code>). Jumlah FO tiap cabang bisa beda-beda, sistem menyesuaikan otomatis.
                </p>
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
                    <option selected>FO — Andi Nugraha</option>
                    <option>FO — Melati Putri</option>
                    <option>FO — Yusuf Hakim</option>
                </select>
                <input type="text" value="Rekrut Anggota Baru"
                    style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                <select style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                    <option selected>Kuantitatif</option>
                    <option>Taktis</option>
                </select>
                <input type="text" value="5 /hari"
                    style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                <button
                    style="background:none;border:none;color:#C6493C;cursor:pointer;font-size:16px;">&times;</button>
            </div>

            <div
                style="display:grid;grid-template-columns:1.3fr 1.2fr 0.9fr 0.6fr auto;gap:8px;align-items:center;background:#FAFBFC;padding:10px;border-radius:8px;margin-bottom:8px;border:1px solid #E2E5E9;">
                <select style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                    <option>Diri Sendiri</option>
                    <option>FO — Andi Nugraha</option>
                    <option selected>FO — Melati Putri</option>
                    <option>FO — Yusuf Hakim</option>
                </select>
                <input type="text" value="Rekrut Anggota Baru"
                    style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                <select style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                    <option selected>Kuantitatif</option>
                    <option>Taktis</option>
                </select>
                <input type="text" value="5 /hari"
                    style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                <button
                    style="background:none;border:none;color:#C6493C;cursor:pointer;font-size:16px;">&times;</button>
            </div>

            <!-- Row taktis, contoh persis dari user: sebar brosur 100/hari per FO -->
            <div
                style="display:grid;grid-template-columns:1.3fr 1.2fr 0.9fr 0.6fr auto;gap:8px;align-items:center;background:#FBEAE3;padding:10px;border-radius:8px;margin-bottom:8px;border:1px solid #E2E5E9;">
                <select style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                    <option>Diri Sendiri</option>
                    <option>FO — Andi Nugraha</option>
                    <option>FO — Melati Putri</option>
                    <option selected>FO — Yusuf Hakim</option>
                </select>
                <input type="text" value="Sebar Brosur Promosi"
                    style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                <select style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                    <option>Kuantitatif</option>
                    <option selected>Taktis</option>
                </select>
                <input type="text" value="100 lembar/hari"
                    style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                <button
                    style="background:none;border:none;color:#C6493C;cursor:pointer;font-size:16px;">&times;</button>
            </div>
            <p style="font-size:11px;color:#6B7280;margin:-2px 0 12px 0;">
                Catatan: baris Taktis boleh tetap punya angka (mis. "100 lembar/hari") untuk keperluan pelacakan
                aktivitasnya sendiri — bedanya, angka ini <strong>tidak</strong> dijumlahkan ke total 15/hari goal induk
                karena satuannya beda (lembar vs orang).
            </p>

            <button
                style="background:none;border:1px dashed #E2E5E9;color:#1B4D3E;padding:8px;width:100%;border-radius:7px;font-size:12.5px;font-weight:600;cursor:pointer;margin-bottom:16px;">+
                Tambah Baris</button>

            <div style="display:flex;gap:10px;margin-top:18px;">
                <a href="am-4-drilldown.html" style="text-decoration:none;">
                    <button
                        style="background:#1B4D3E;color:#fff;border:none;padding:9px 16px;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;">Simpan
                        & Kirim</button>
                </a>
                <a href="am-2-list-goal.html" style="text-decoration:none;">
                    <button
                        style="background:none;color:#6B7280;border:1px solid #E2E5E9;padding:9px 16px;border-radius:7px;font-size:13px;cursor:pointer;">Batal</button>
                </a>
            </div>

        </div>

    </div>

</body>

</html>
