<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AM — Drill-down Goal</title>
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

    <div style="padding:28px 32px;max-width:1000px;margin:0 auto;">

        <h1 style="font-size:20px;margin:0 0 4px 0;">Board Saya</h1>
        <p style="margin:0;color:#6B7280;font-size:13px;">Login sebagai: AM Cabang Kemang</p>

        <div style="display:flex;gap:4px;margin:14px 0 20px 0;border-bottom:1px solid #E2E5E9;">
            <a href="am-1-board" style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">Personal
                Board</a>
            <a href="am-2-list-goal" style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">List
                Goal Diterima</a>
            <a href="am-3-form-breakdown"
                style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">Form Breakdown</a>
            <a href="am-4-drilldown"
                style="text-decoration:none;padding:9px 14px;font-size:13px;color:#1B4D3E;border-bottom:2px solid #1B4D3E;font-weight:700;">Drill-down
                Goal</a>
        </div>

        <a href="am-2-list-goal" style="text-decoration:none;">
            <button
                style="background:none;color:#6B7280;border:1px solid #E2E5E9;padding:9px 16px;border-radius:7px;font-size:13px;cursor:pointer;margin-bottom:14px;">&larr;
                Kembali ke List Goal</button>
        </a>

        <div style="background:#E4EEEA;padding:14px 16px;border-radius:9px;margin-bottom:14px;">
            <h3 style="margin:0 0 4px 0;font-size:15px;color:#123328;">Rekrut Anggota Baru (porsi Cabang Kemang)</h3>
            <div style="font-size:12.5px;color:#6B7280;">15/hari · dari Korea Jaksel</div>
            <div style="display:flex;align-items:center;gap:8px;margin-top:10px;">
                <div style="flex:1;height:7px;background:#fff;border-radius:4px;overflow:hidden;">
                    <div style="height:100%;width:87%;background:#C98A3D;"></div>
                </div>
                <span style="font-size:12px;font-weight:700;color:#C98A3D;width:36px;text-align:right;">87%</span>
            </div>
        </div>

        <a href="am-3-form-breakdown" style="text-decoration:none;">
            <button
                style="background:#fff;color:#1B4D3E;border:1px solid #1B4D3E;padding:9px 16px;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;margin-bottom:14px;">Breakdown
                Ulang</button>
        </a>

        <p style="font-size:12px;color:#9AA1A9;margin-bottom:12px;">1 tingkat saja (AM → FO) — paling sederhana dari
            semua drill-down.</p>

        <div style="background:#fff;border-radius:10px;padding:8px;box-shadow:0 1px 2px rgba(0,0,0,0.05);">

            <div
                style="padding:10px 12px;border-radius:7px;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
                    <span style="font-weight:600;">Andi Nugraha</span><span
                        style="font-size:11px;color:#6B7280;font-weight:400;">FO · 5/hari</span>
                </div>
                <div style="font-size:12px;color:#6B7280;width:130px;text-align:right;">4/5 (80%)</div>
            </div>
            <div
                style="padding:10px 12px;border-radius:7px;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
                    <span style="font-weight:600;">Melati Putri</span><span
                        style="font-size:11px;color:#6B7280;font-weight:400;">FO · 5/hari</span>
                </div>
                <div style="font-size:12px;color:#6B7280;width:130px;text-align:right;">5/5 (100%)</div>
            </div>
            <div
                style="padding:10px 12px;border-radius:7px;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
                    <span style="font-weight:600;">Yusuf Hakim</span><span
                        style="font-size:11px;color:#6B7280;font-weight:400;">FO · 5/hari</span>
                </div>
                <div style="font-size:12px;color:#6B7280;width:130px;text-align:right;">4/5 (80%)</div>
            </div>
            <div
                style="padding:10px 12px;border-radius:7px;display:flex;justify-content:space-between;align-items:center;gap:12px;background:#FBEAE3;">
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
                    <span style="font-weight:600;">Sebar Brosur Promosi</span>
                    <span style="font-size:11px;color:#6B7280;font-weight:400;">Yusuf Hakim</span>
                    <span
                        style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;background:#fff;color:#B5502F;">Taktis</span>
                </div>
                <div style="font-size:12px;color:#6B7280;width:130px;text-align:right;">100/hari, 90% terlaksana</div>
            </div>

        </div>

    </div>

</body>

</html>
