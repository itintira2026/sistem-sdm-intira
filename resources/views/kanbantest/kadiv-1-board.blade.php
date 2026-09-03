<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kadiv — Board Saya</title>
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

    <div style="padding:28px 32px;max-width:1100px;margin:0 auto;position:relative;min-height:80vh;">

        <h1 style="font-size:20px;margin:0 0 4px 0;">Board Saya</h1>
        <p style="margin:0;color:#6B7280;font-size:13px;">Login sebagai: Kadiv Marketing <span style="color:#9AA1A9;">(1
                desain untuk Operasional / Marketing / Keuangan / Penjualan / HRD)</span></p>

        <div style="display:flex;gap:4px;margin:14px 0 20px 0;border-bottom:1px solid #E2E5E9;">
            <a href="kadiv-1-board"
                style="text-decoration:none;padding:9px 14px;font-size:13px;color:#1B4D3E;border-bottom:2px solid #1B4D3E;font-weight:700;">Personal
                Board</a>
            <a href="kadiv-2-list-goal" style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">List
                Goal Diterima</a>
            <a href="kadiv-3-form-breakdown"
                style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">Form Breakdown</a>
            <a href="kadiv-4-drilldown"
                style="text-decoration:none;padding:9px 14px;font-size:13px;color:#6B7280;">Drill-down Goal</a>
        </div>

        <div style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:16px;font-size:11.5px;color:#6B7280;">
            <span style="display:inline-flex;align-items:center;gap:5px;"><i
                    style="width:10px;height:10px;border-radius:3px;background:#C98A3D;display:inline-block;"></i>Target
                (kuantitatif)</span>
            <span style="display:inline-flex;align-items:center;gap:5px;"><i
                    style="width:10px;height:10px;border-radius:3px;background:#B5502F;display:inline-block;"></i>Taktis
                (pendukung)</span>
        </div>

        <div
            style="display:flex;gap:4px;margin-bottom:16px;background:#EAECEE;padding:3px;border-radius:8px;width:fit-content;">
            <button
                onclick="document.getElementById('tabAtasan').style.display='block';document.getElementById('tabAdhoc').style.display='none';this.style.background='#fff';this.nextElementSibling.style.background='none';"
                style="border:none;background:#fff;padding:7px 15px;font-size:12.5px;font-weight:600;color:#123328;border-radius:6px;cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,0.08);">Goal
                dari Atasan</button>
            <button
                onclick="document.getElementById('tabAdhoc').style.display='block';document.getElementById('tabAtasan').style.display='none';this.style.background='#fff';this.previousElementSibling.style.background='none';"
                style="border:none;background:none;padding:7px 15px;font-size:12.5px;font-weight:600;color:#6B7280;border-radius:6px;cursor:pointer;">Ad-hoc</button>
        </div>

        <div id="tabAtasan" style="display:block;">
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;align-items:start;">

                <div style="background:#EEF1F0;border-radius:10px;padding:12px;min-height:200px;">
                    <h4
                        style="margin:0 0 10px 4px;font-size:12.5px;color:#6B7280;text-transform:uppercase;letter-spacing:0.4px;">
                        To Do <span
                            style="background:#DCE2DF;color:#1E2430;border-radius:10px;padding:0 7px;font-weight:700;float:right;">2</span>
                    </h4>

                    <div
                        style="background:#fff;border-radius:9px;padding:12px 13px;margin-bottom:10px;border-left:3px solid #C98A3D;box-shadow:0 1px 2px rgba(20,30,25,0.05);">
                        <div style="display:flex;gap:5px;margin-bottom:6px;"><span
                                style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;background:#E4EEEA;color:#123328;">Kuantitatif</span>
                        </div>
                        <div style="font-weight:600;font-size:13.5px;margin-bottom:6px;">Rekrut Anggota Baru (porsi
                            Marketing)</div>
                        <div
                            style="display:flex;justify-content:space-between;font-size:11.5px;color:#6B7280;margin-top:8px;">
                            <span>Dari: Direktur Bisnis</span><span>70/hari</span>
                        </div>
                        <div style="height:5px;border-radius:3px;background:#E7E9EB;margin-top:8px;overflow:hidden;">
                            <div style="height:100%;width:82%;background:#C98A3D;"></div>
                        </div>
                    </div>

                    <div
                        style="background:#fff;border-radius:9px;padding:12px 13px;border-left:3px solid #B5502F;box-shadow:0 1px 2px rgba(20,30,25,0.05);">
                        <div style="display:flex;gap:5px;margin-bottom:6px;"><span
                                style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;background:#FBEAE3;color:#B5502F;">Taktis</span>
                        </div>
                        <div style="font-weight:600;font-size:13.5px;margin-bottom:6px;">Sebar Brosur Promosi — 100
                            lembar/hari per FO</div>
                        <div
                            style="display:flex;justify-content:space-between;font-size:11.5px;color:#6B7280;margin-top:8px;">
                            <span>Diri Sendiri (turunan goal anggota)</span><span>Aktivitas pendukung</span>
                        </div>
                    </div>
                </div>

                <div style="background:#EEF1F0;border-radius:10px;padding:12px;min-height:200px;">
                    <h4
                        style="margin:0 0 10px 4px;font-size:12.5px;color:#6B7280;text-transform:uppercase;letter-spacing:0.4px;">
                        In Progress <span
                            style="background:#DCE2DF;color:#1E2430;border-radius:10px;padding:0 7px;font-weight:700;float:right;">0</span>
                    </h4>
                </div>
                <div style="background:#EEF1F0;border-radius:10px;padding:12px;min-height:200px;">
                    <h4
                        style="margin:0 0 10px 4px;font-size:12.5px;color:#6B7280;text-transform:uppercase;letter-spacing:0.4px;">
                        Review <span
                            style="background:#DCE2DF;color:#1E2430;border-radius:10px;padding:0 7px;font-weight:700;float:right;">0</span>
                    </h4>
                </div>
                <div style="background:#EEF1F0;border-radius:10px;padding:12px;min-height:200px;">
                    <h4
                        style="margin:0 0 10px 4px;font-size:12.5px;color:#6B7280;text-transform:uppercase;letter-spacing:0.4px;">
                        Done <span
                            style="background:#DCE2DF;color:#1E2430;border-radius:10px;padding:0 7px;font-weight:700;float:right;">0</span>
                    </h4>
                </div>

            </div>
        </div>

        <div id="tabAdhoc" style="display:none;">
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;align-items:start;">
                <div style="background:#EEF1F0;border-radius:10px;padding:12px;min-height:160px;">
                    <h4
                        style="margin:0 0 10px 4px;font-size:12.5px;color:#6B7280;text-transform:uppercase;letter-spacing:0.4px;">
                        To Do <span
                            style="background:#DCE2DF;color:#1E2430;border-radius:10px;padding:0 7px;font-weight:700;float:right;">0</span>
                    </h4>
                </div>
                <div style="background:#EEF1F0;border-radius:10px;padding:12px;min-height:160px;">
                    <h4
                        style="margin:0 0 10px 4px;font-size:12.5px;color:#6B7280;text-transform:uppercase;letter-spacing:0.4px;">
                        In Progress <span
                            style="background:#DCE2DF;color:#1E2430;border-radius:10px;padding:0 7px;font-weight:700;float:right;">0</span>
                    </h4>
                </div>
                <div style="background:#EEF1F0;border-radius:10px;padding:12px;min-height:160px;">
                    <h4
                        style="margin:0 0 10px 4px;font-size:12.5px;color:#6B7280;text-transform:uppercase;letter-spacing:0.4px;">
                        Review <span
                            style="background:#DCE2DF;color:#1E2430;border-radius:10px;padding:0 7px;font-weight:700;float:right;">0</span>
                    </h4>
                </div>
                <div style="background:#EEF1F0;border-radius:10px;padding:12px;min-height:160px;">
                    <h4
                        style="margin:0 0 10px 4px;font-size:12.5px;color:#6B7280;text-transform:uppercase;letter-spacing:0.4px;">
                        Done <span
                            style="background:#DCE2DF;color:#1E2430;border-radius:10px;padding:0 7px;font-weight:700;float:right;">0</span>
                    </h4>
                </div>
            </div>
            <button
                style="position:fixed;bottom:26px;right:34px;background:#C98A3D;color:#fff;border:none;padding:12px 18px;border-radius:24px;font-weight:700;font-size:13px;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,0.18);">+
                Tambah List Sendiri</button>
        </div>

    </div>

</body>

</html>
