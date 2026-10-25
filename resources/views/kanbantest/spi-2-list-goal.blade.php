<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPI — List Goal Level Direktur</title>
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
            <a href="spi-2-list-goal"
                style="text-decoration:none;padding:9px 14px;font-size:13px;color:#1B4D3E;border-bottom:2px solid #1B4D3E;font-weight:700;">List
                Goal Direktur</a>
            <a href="spi-3-form-breakdown"
                style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">Form Breakdown</a>
            <a href="spi-4-drilldown"
                style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">Drill-down Goal</a>
        </div>

        <div
            style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px;flex-wrap:wrap;gap:12px;">
            <div>
                <h2 style="margin:0 0 4px 0;font-size:17px;">Goal Level Direktur</h2>
                <p style="margin:0;color:#6B7280;font-size:13px;">SPI juga muncul lintas-direktorat, sama seperti
                    Direktur lain</p>
            </div>
            <a href="spi-3-form-breakdown.html" style="text-decoration:none;">
                <button
                    style="background:#1B4D3E;color:#fff;border:none;padding:9px 16px;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;">+
                    Buat Goal Sendiri</button>
            </a>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">

            <a href="spi-4-drilldown.html" style="text-decoration:none;color:inherit;">
                <div
                    style="background:#fff;border-radius:10px;padding:16px;box-shadow:0 1px 2px rgba(0,0,0,0.05);border-top:3px solid #1B4D3E;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                        <span
                            style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#E4EEEA;color:#123328;">SPI</span>
                        <span
                            style="padding:2px 8px;border-radius:5px;font-size:11px;font-weight:600;background:#FBE9E7;color:#C6493C;">41%</span>
                    </div>
                    <h3 style="font-size:14.5px;margin:0 0 4px 0;">Audit Kepatuhan Cabang</h3>
                    <div style="font-size:11.5px;color:#6B7280;">Dari Ketua · 12 cabang</div>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:12px;">
                        <div style="flex:1;height:7px;background:#E7E9EB;border-radius:4px;overflow:hidden;">
                            <div style="height:100%;width:41%;background:#C98A3D;"></div>
                        </div>
                        <span
                            style="font-size:12px;font-weight:700;color:#C98A3D;width:36px;text-align:right;">41%</span>
                    </div>
                </div>
            </a>

            <a href="spi-4-drilldown.html" style="text-decoration:none;color:inherit;">
                <div
                    style="background:#fff;border-radius:10px;padding:16px;box-shadow:0 1px 2px rgba(0,0,0,0.05);border-top:3px solid #C7CCD1;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                        <span
                            style="display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#EEF0F2;color:#6B7280;">Direktur
                            Bisnis</span>
                        <span
                            style="padding:2px 8px;border-radius:5px;font-size:11px;font-weight:600;background:#E4F4EB;color:#2F9E68;">75%</span>
                    </div>
                    <h3 style="font-size:14.5px;margin:0 0 4px 0;">Pertumbuhan Anggota Q3 (porsi Bisnis)</h3>
                    <div style="font-size:11.5px;color:#6B7280;">Dari Ketua · read-only</div>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:12px;">
                        <div style="flex:1;height:7px;background:#E7E9EB;border-radius:4px;overflow:hidden;">
                            <div style="height:100%;width:75%;background:#C98A3D;"></div>
                        </div>
                        <span
                            style="font-size:12px;font-weight:700;color:#C98A3D;width:36px;text-align:right;">75%</span>
                    </div>
                </div>
            </a>

        </div>

    </div>

</body>

</html>
