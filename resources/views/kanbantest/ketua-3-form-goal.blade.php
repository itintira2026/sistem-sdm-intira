<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ketua — Form Buat Goal Strategis</title>
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
        <p style="margin:0;color:#6B7280;font-size:13px;">Login sebagai: Ketua</p>

        <div style="display:flex;gap:4px;margin:14px 0 20px 0;border-bottom:1px solid #E2E5E9;">
            <a href="ketua-1-board" style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">Personal
                Board</a>
            <a href="ketua-2-list-goal" style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">List
                Goal Strategis</a>
            <a href="ketua-3-form-goal"
                style="text-decoration:none;padding:9px 14px;font-size:13px;color:#1B4D3E;border-bottom:2px solid #1B4D3E;font-weight:700;">Form
                Buat Goal</a>
            <a href="ketua-4-drilldown"
                style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">Drill-down Goal</a>
        </div>

        <div
            style="background:#fff;border-radius:10px;padding:22px;max-width:640px;box-shadow:0 1px 2px rgba(0,0,0,0.05);">

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12.5px;font-weight:600;margin-bottom:6px;">Judul Goal</label>
                <input type="text" placeholder="mis. Pertumbuhan Anggota Q4"
                    style="width:100%;padding:9px 11px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12.5px;font-weight:600;margin-bottom:6px;">Deskripsi</label>
                <textarea placeholder="Konteks & latar belakang goal ini..."
                    style="width:100%;padding:9px 11px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;min-height:60px;resize:vertical;"></textarea>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12.5px;font-weight:600;margin-bottom:6px;">Tipe Goal</label>
                <div style="display:flex;gap:16px;flex-wrap:wrap;">
                    <label style="display:flex;align-items:center;gap:6px;font-size:13px;"><input type="radio"
                            name="tipe" checked> Target (angka + periode)</label>
                    <label style="display:flex;align-items:center;gap:6px;font-size:13px;"><input type="radio"
                            name="tipe"> Task (selesai/belum)</label>
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12.5px;font-weight:600;margin-bottom:6px;">Nilai Target</label>
                <input type="number" placeholder="200"
                    style="width:100%;padding:9px 11px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12.5px;font-weight:600;margin-bottom:6px;">Satuan &
                    Periode</label>
                <div style="display:flex;gap:10px;">
                    <input type="text" placeholder="orang"
                        style="width:120px;padding:9px 11px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;">
                    <select style="flex:1;padding:9px 11px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;">
                        <option>Harian</option>
                        <option>Mingguan</option>
                        <option>Bulanan</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12.5px;font-weight:600;margin-bottom:6px;">Assign ke</label>
                <div style="display:flex;gap:14px;flex-wrap:wrap;font-size:13px;">
                    <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox" checked> Direktur
                        Bisnis</label>
                    <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox" checked> Direktur
                        Operasional</label>
                    <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox"> Direktur
                        Utama</label>
                    <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox"> SPI</label>
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12.5px;font-weight:600;margin-bottom:6px;">Periode Berlaku</label>
                <input type="text" placeholder="1 Jul – 30 Sep 2026"
                    style="width:100%;padding:9px 11px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12.5px;font-weight:600;margin-bottom:6px;">Lampiran Instruksi
                    (opsional)</label>
                <input type="text" placeholder="Upload file..."
                    style="width:100%;padding:9px 11px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;">
            </div>

            <div style="display:flex;gap:10px;margin-top:18px;">
                <a href="ketua-4-drilldown.html" style="text-decoration:none;">
                    <button
                        style="background:#1B4D3E;color:#fff;border:none;padding:9px 16px;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;">Simpan
                        & Buat</button>
                </a>
                <a href="ketua-2-list-goal.html" style="text-decoration:none;">
                    <button
                        style="background:none;color:#6B7280;border:1px solid #E2E5E9;padding:9px 16px;border-radius:7px;font-size:13px;cursor:pointer;">Batal</button>
                </a>
            </div>

        </div>

    </div>

</body>

</html>
