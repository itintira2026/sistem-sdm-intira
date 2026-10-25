<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner — Dashboard Utama</title>
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

        <div style="margin-bottom:18px;">
            <h1 style="font-size:20px;margin:0 0 4px 0;">Dashboard Goal Strategis</h1>
            <p style="margin:0;color:#6B7280;font-size:13px;">Ringkasan seluruh goal, seluruh direktorat — read only</p>
        </div>

        <!-- Filter -->
        <div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
            <select style="padding:9px 12px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;background:#fff;">
                <option>Semua Ketua</option>
            </select>
            <select style="padding:9px 12px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;background:#fff;">
                <option>Semua Direktur</option>
            </select>
            <select style="padding:9px 12px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;background:#fff;">
                <option>Semua Divisi</option>
            </select>
            <select style="padding:9px 12px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;background:#fff;">
                <option>Periode: Q3 2026</option>
            </select>
            <label style="display:flex;align-items:center;gap:6px;font-size:12.5px;color:#6B7280;">
                <input type="checkbox"> Tampilkan yang di bawah target
            </label>
        </div>

        <!-- Grid goal strategis -->
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">

            <a href="owner-2-drilldown" style="text-decoration:none;color:inherit;">
                <div
                    style="background:#fff;border-radius:10px;padding:16px;box-shadow:0 1px 2px rgba(0,0,0,0.05);border-top:3px solid #1B4D3E;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                        <span
                            style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#E4EEEA;color:#123328;">Ketua</span>
                        <span
                            style="padding:2px 8px;border-radius:5px;font-size:11px;font-weight:600;background:#E4F4EB;color:#2F9E68;">89%</span>
                    </div>
                    <h3 style="font-size:14.5px;margin:0 0 4px 0;">Pertumbuhan Anggota Q3</h3>
                    <div style="font-size:11.5px;color:#6B7280;">Target 200/hari · Jul–Sep 2026</div>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:12px;">
                        <div style="flex:1;height:7px;background:#E7E9EB;border-radius:4px;overflow:hidden;">
                            <div style="height:100%;width:89%;background:#C98A3D;"></div>
                        </div>
                        <span
                            style="font-size:12px;font-weight:700;color:#C98A3D;width:36px;text-align:right;">89%</span>
                    </div>
                    <div style="font-size:12px;color:#6B7280;margin-top:6px;">178/200 realisasi hari ini</div>
                </div>
            </a>

            <a href="owner-2-drilldown" style="text-decoration:none;color:inherit;">
                <div
                    style="background:#fff;border-radius:10px;padding:16px;box-shadow:0 1px 2px rgba(0,0,0,0.05);border-top:3px solid #1B4D3E;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                        <span
                            style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#E4EEEA;color:#123328;">Direktur
                            Bisnis</span>
                        <span
                            style="padding:2px 8px;border-radius:5px;font-size:11px;font-weight:600;background:#FBF0E0;color:#D68A2B;">64%</span>
                    </div>
                    <h3 style="font-size:14.5px;margin:0 0 4px 0;">Ekspansi Cabang Baru</h3>
                    <div style="font-size:11.5px;color:#6B7280;">Tipe Task · Deadline 30 Sep 2026</div>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:12px;">
                        <div style="flex:1;height:7px;background:#E7E9EB;border-radius:4px;overflow:hidden;">
                            <div style="height:100%;width:64%;background:#C98A3D;"></div>
                        </div>
                        <span
                            style="font-size:12px;font-weight:700;color:#C98A3D;width:36px;text-align:right;">64%</span>
                    </div>
                </div>
            </a>

            <a href="owner-2-drilldown" style="text-decoration:none;color:inherit;">
                <div
                    style="background:#fff;border-radius:10px;padding:16px;box-shadow:0 1px 2px rgba(0,0,0,0.05);border-top:3px solid #1B4D3E;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                        <span
                            style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#E4EEEA;color:#123328;">SPI</span>
                        <span
                            style="padding:2px 8px;border-radius:5px;font-size:11px;font-weight:600;background:#FBE9E7;color:#C6493C;">41%</span>
                    </div>
                    <h3 style="font-size:14.5px;margin:0 0 4px 0;">Audit Kepatuhan Cabang</h3>
                    <div style="font-size:11.5px;color:#6B7280;">Tipe Task · 12 cabang</div>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:12px;">
                        <div style="flex:1;height:7px;background:#E7E9EB;border-radius:4px;overflow:hidden;">
                            <div style="height:100%;width:41%;background:#C98A3D;"></div>
                        </div>
                        <span
                            style="font-size:12px;font-weight:700;color:#C98A3D;width:36px;text-align:right;">41%</span>
                    </div>
                </div>
            </a>

        </div>

        <p style="font-size:12px;color:#9AA1A9;margin-top:20px;">
            Klik salah satu card → <a href="owner-2-drilldown" style="color:#1B4D3E;">Halaman 2: Drill-down
                Goal</a>
        </p>

    </div>

</body>

</html>
