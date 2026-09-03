<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPI — Form Breakdown</title>
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
        <p style="margin:0;color:#6B7280;font-size:13px;">Login sebagai: SPI</p>

        <div style="display:flex;gap:4px;margin:14px 0 20px 0;border-bottom:1px solid #E2E5E9;">
            <a href="spi-1-board" style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">Personal
                Board</a>
            <a href="spi-2-list-goal" style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">List
                Goal Direktur</a>
            <a href="spi-3-form-breakdown"
                style="text-decoration:none;padding:9px 14px;font-size:13px;color:#1B4D3E;border-bottom:2px solid #1B4D3E;font-weight:700;">Form
                Breakdown</a>
            <a href="spi-4-drilldown"
                style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">Drill-down Goal</a>
        </div>

        <div
            style="background:#fff;border-radius:10px;padding:22px;max-width:760px;box-shadow:0 1px 2px rgba(0,0,0,0.05);">

            <div
                style="background:#E4EEEA;padding:12px 14px;border-radius:8px;font-size:12.5px;margin-bottom:18px;color:#123328;">
                Goal induk: <strong>Audit Kepatuhan Cabang</strong> — Tipe: Task, 12 cabang
            </div>

            <!-- BEDA UTAMA DARI DIREKTUR LAIN: toggle Staff Langsung vs Kadiv -->
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12.5px;font-weight:600;margin-bottom:8px;">Assign ke (pilih
                    rute)</label>
                <div style="display:flex;gap:8px;margin-bottom:12px;">
                    <button
                        onclick="document.getElementById('optStaff').style.display='block';document.getElementById('optKadiv').style.display='none';this.style.background='#1B4D3E';this.style.color='#fff';this.style.border='none';this.nextElementSibling.style.background='#fff';this.nextElementSibling.style.color='#1B4D3E';this.nextElementSibling.style.border='1px solid #1B4D3E';"
                        style="flex:1;padding:9px;border-radius:7px;font-size:12.5px;font-weight:600;cursor:pointer;background:#1B4D3E;color:#fff;border:none;">Staff
                        Langsung</button>
                    <button
                        onclick="document.getElementById('optKadiv').style.display='block';document.getElementById('optStaff').style.display='none';this.style.background='#1B4D3E';this.style.color='#fff';this.style.border='none';this.previousElementSibling.style.background='#fff';this.previousElementSibling.style.color='#1B4D3E';this.previousElementSibling.style.border='1px solid #1B4D3E';"
                        style="flex:1;padding:9px;border-radius:7px;font-size:12.5px;font-weight:600;cursor:pointer;background:#fff;color:#1B4D3E;border:1px solid #1B4D3E;">Kadiv</button>
                </div>
                <p style="font-size:11.5px;color:#6B7280;margin:0 0 12px 0;">
                    Ini yang membedakan SPI dari Direktur lain — SPI boleh melompati Kadiv dan assign langsung ke Staff,
                    atau tetap lewat Kadiv seperti biasa.
                </p>

                <select id="optStaff"
                    style="display:block;width:100%;padding:9px 11px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;">
                    <option>Staff — Dewi Anggraini</option>
                    <option>Staff — Fajar Ramadhan</option>
                </select>
                <select id="optKadiv"
                    style="display:none;width:100%;padding:9px 11px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;">
                    <option>Kadiv Marketing</option>
                    <option>Kadiv Operasional</option>
                </select>
            </div>

            <label style="font-size:12.5px;font-weight:600;display:block;margin-bottom:8px;">Baris Breakdown (bisa
                campur Staff & Kadiv dalam 1 breakdown)</label>

            <div
                style="display:grid;grid-template-columns:1.3fr 1.2fr 0.9fr 0.6fr auto;gap:8px;padding:0 10px;font-size:10px;color:#6B7280;text-transform:uppercase;letter-spacing:0.3px;margin-bottom:6px;">
                <span>Assign ke</span><span>Judul Jobdesk</span><span>Tipe
                    Kontribusi</span><span>Porsi</span><span></span>
            </div>

            <!-- Row langsung ke staff -->
            <div
                style="display:grid;grid-template-columns:1.3fr 1.2fr 0.9fr 0.6fr auto;gap:8px;align-items:center;background:#FAFBFC;padding:10px;border-radius:8px;margin-bottom:8px;border:1px solid #E2E5E9;">
                <select style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                    <option>Diri Sendiri</option>
                    <option selected>Staff — Dewi Anggraini</option>
                    <option>Kadiv Marketing</option>
                </select>
                <input type="text" value="Audit dokumen cabang Jaksel"
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

            <!-- Row lewat kadiv -->
            <div
                style="display:grid;grid-template-columns:1.3fr 1.2fr 0.9fr 0.6fr auto;gap:8px;align-items:center;background:#FAFBFC;padding:10px;border-radius:8px;margin-bottom:8px;border:1px solid #E2E5E9;">
                <select style="padding:7px 9px;font-size:12px;border:1px solid #E2E5E9;border-radius:6px;">
                    <option>Diri Sendiri</option>
                    <option>Staff — Fajar Ramadhan</option>
                    <option selected>Kadiv Operasional</option>
                </select>
                <input type="text" value="Koordinasi audit cabang wilayah timur"
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
                style="background:none;border:1px dashed #E2E5E9;color:#1B4D3E;padding:8px;width:100%;border-radius:7px;font-size:12.5px;font-weight:600;cursor:pointer;margin-bottom:16px;">+
                Tambah Baris</button>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12.5px;font-weight:600;margin-bottom:6px;">Deadline</label>
                <input type="text" placeholder="15 Sep 2026"
                    style="width:100%;padding:9px 11px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12.5px;font-weight:600;margin-bottom:6px;">Lampiran Instruksi
                    (opsional)</label>
                <input type="text" placeholder="Upload file..."
                    style="width:100%;padding:9px 11px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;">
            </div>

            <div style="display:flex;gap:10px;margin-top:18px;">
                <a href="spi-4-drilldown.html" style="text-decoration:none;">
                    <button
                        style="background:#1B4D3E;color:#fff;border:none;padding:9px 16px;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;">Simpan
                        & Kirim</button>
                </a>
                <a href="spi-2-list-goal.html" style="text-decoration:none;">
                    <button
                        style="background:none;color:#6B7280;border:1px solid #E2E5E9;padding:9px 16px;border-radius:7px;font-size:13px;cursor:pointer;">Batal</button>
                </a>
            </div>

        </div>

    </div>

</body>

</html>
