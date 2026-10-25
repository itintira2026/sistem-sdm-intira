<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff / FO — Board Saya</title>
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
        <p style="margin:0;color:#6B7280;font-size:13px;">Login sebagai: Andi Nugraha (FO — Cabang Kemang) <span
                style="color:#9AA1A9;">— struktur identik untuk role Staff</span></p>

        <div style="display:flex;gap:14px;flex-wrap:wrap;margin:16px 0;font-size:11.5px;color:#6B7280;">
            <span style="display:inline-flex;align-items:center;gap:5px;"><i
                    style="width:10px;height:10px;border-radius:3px;background:#1B4D3E;display:inline-block;"></i>Task</span>
            <span style="display:inline-flex;align-items:center;gap:5px;"><i
                    style="width:10px;height:10px;border-radius:3px;background:#C98A3D;display:inline-block;"></i>Target
                (angka)</span>
            <span style="display:inline-flex;align-items:center;gap:5px;"><i
                    style="width:10px;height:10px;border-radius:3px;background:#B9BFC6;display:inline-block;"></i>Personal
                / Ad-hoc</span>
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

        <!-- Tab: Goal dari Atasan -->
        <div id="tabAtasan" style="display:block;">
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;align-items:start;">

                <div style="background:#EEF1F0;border-radius:10px;padding:12px;min-height:220px;">
                    <h4
                        style="margin:0 0 10px 4px;font-size:12.5px;color:#6B7280;text-transform:uppercase;letter-spacing:0.4px;">
                        To Do <span
                            style="background:#DCE2DF;color:#1E2430;border-radius:10px;padding:0 7px;font-weight:700;float:right;">1</span>
                    </h4>

                    <!-- Card target, dengan input realisasi langsung -->
                    <div
                        style="background:#fff;border-radius:9px;padding:12px 13px;border-left:3px solid #C98A3D;box-shadow:0 1px 2px rgba(20,30,25,0.05);">
                        <div style="font-weight:600;font-size:13.5px;margin-bottom:6px;">Rekrut Anggota Baru</div>
                        <div
                            style="display:flex;justify-content:space-between;font-size:11.5px;color:#6B7280;margin-top:4px;">
                            <span>Dari: AM Kemang</span><span>Target 5/hari</span></div>
                        <div style="height:5px;border-radius:3px;background:#E7E9EB;margin-top:8px;overflow:hidden;">
                            <div style="height:100%;width:80%;background:#C98A3D;"></div>
                        </div>
                        <div style="display:flex;gap:6px;margin-top:9px;align-items:center;">
                            <input type="number" value="4"
                                style="width:56px;padding:5px 7px;border:1px solid #E2E5E9;border-radius:5px;font-size:12.5px;">
                            <button
                                style="padding:5px 9px;font-size:11.5px;border:none;background:#1B4D3E;color:#fff;border-radius:5px;cursor:pointer;">Simpan
                                Realisasi</button>
                        </div>
                    </div>
                </div>

                <div style="background:#EEF1F0;border-radius:10px;padding:12px;min-height:220px;">
                    <h4
                        style="margin:0 0 10px 4px;font-size:12.5px;color:#6B7280;text-transform:uppercase;letter-spacing:0.4px;">
                        In Progress <span
                            style="background:#DCE2DF;color:#1E2430;border-radius:10px;padding:0 7px;font-weight:700;float:right;">1</span>
                    </h4>
                    <div onclick="document.getElementById('modalCard').style.display='flex'"
                        style="cursor:pointer;background:#fff;border-radius:9px;padding:12px 13px;border-left:3px solid #1B4D3E;box-shadow:0 1px 2px rgba(20,30,25,0.05);">
                        <div style="font-weight:600;font-size:13.5px;margin-bottom:6px;">Follow up member trial minggu
                            ini</div>
                        <div
                            style="display:flex;justify-content:space-between;font-size:11.5px;color:#6B7280;margin-top:8px;">
                            <span>Dari: AM Kemang</span><span style="color:#D68A2B;font-weight:600;">Due 5 Sep</span>
                        </div>
                    </div>
                </div>

                <div style="background:#EEF1F0;border-radius:10px;padding:12px;min-height:220px;">
                    <h4
                        style="margin:0 0 10px 4px;font-size:12.5px;color:#6B7280;text-transform:uppercase;letter-spacing:0.4px;">
                        Review <span
                            style="background:#DCE2DF;color:#1E2430;border-radius:10px;padding:0 7px;font-weight:700;float:right;">0</span>
                    </h4>
                </div>

                <div style="background:#EEF1F0;border-radius:10px;padding:12px;min-height:220px;">
                    <h4
                        style="margin:0 0 10px 4px;font-size:12.5px;color:#6B7280;text-transform:uppercase;letter-spacing:0.4px;">
                        Done <span
                            style="background:#DCE2DF;color:#1E2430;border-radius:10px;padding:0 7px;font-weight:700;float:right;">1</span>
                    </h4>
                    <div
                        style="background:#fff;border-radius:9px;padding:12px 13px;border-left:3px solid #C98A3D;box-shadow:0 1px 2px rgba(20,30,25,0.05);">
                        <div style="font-weight:600;font-size:13.5px;margin-bottom:6px;">Rekrut Anggota Baru — Kemarin
                        </div>
                        <div
                            style="display:flex;justify-content:space-between;font-size:11.5px;color:#6B7280;margin-top:8px;">
                            <span>5/5 tercapai</span><span>31 Agu</span></div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Tab: Ad-hoc -->
        <div id="tabAdhoc" style="display:none;">
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;align-items:start;">
                <div style="background:#EEF1F0;border-radius:10px;padding:12px;min-height:160px;">
                    <h4
                        style="margin:0 0 10px 4px;font-size:12.5px;color:#6B7280;text-transform:uppercase;letter-spacing:0.4px;">
                        To Do <span
                            style="background:#DCE2DF;color:#1E2430;border-radius:10px;padding:0 7px;font-weight:700;float:right;">1</span>
                    </h4>
                    <div
                        style="background:#fff;border-radius:9px;padding:12px 13px;border-left:3px solid #B9BFC6;box-shadow:0 1px 2px rgba(20,30,25,0.05);">
                        <div style="font-weight:600;font-size:13.5px;margin-bottom:6px;">Bantu setup booth event weekend
                        </div>
                        <div
                            style="display:flex;justify-content:space-between;font-size:11.5px;color:#6B7280;margin-top:8px;">
                            <span>Personal</span><span>6 Sep</span></div>
                    </div>
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

    <!-- Modal Detail Card -->
    <div id="modalCard"
        style="display:none;position:fixed;inset:0;background:rgba(20,25,22,0.45);align-items:center;justify-content:center;z-index:50;">
        <div
            style="background:#fff;border-radius:12px;width:480px;max-width:92vw;max-height:86vh;overflow-y:auto;padding:24px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;">
                <h3 style="margin:0;font-size:16px;">Follow up member trial minggu ini</h3>
                <button onclick="document.getElementById('modalCard').style.display='none'"
                    style="background:none;border:none;font-size:18px;cursor:pointer;color:#6B7280;">&times;</button>
            </div>
            <p style="font-size:13px;color:#6B7280;margin-top:-8px;">Dari: AM Kemang · Due 5 Sep 2026</p>

            <div style="margin-bottom:16px;">
                <h5
                    style="font-size:11.5px;text-transform:uppercase;letter-spacing:0.4px;color:#6B7280;margin:0 0 8px 0;">
                    Status</h5>
                <select style="width:100%;padding:9px 11px;border:1px solid #E2E5E9;border-radius:7px;font-size:13px;">
                    <option>To Do</option>
                    <option selected>In Progress</option>
                    <option>Review</option>
                    <option>Done</option>
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <h5
                    style="font-size:11.5px;text-transform:uppercase;letter-spacing:0.4px;color:#6B7280;margin:0 0 8px 0;">
                    Bukti Kerja (opsional)</h5>
                <div
                    style="display:inline-flex;align-items:center;gap:5px;background:#F0F2F3;padding:5px 10px;border-radius:6px;font-size:12px;margin:0 6px 6px 0;">
                    📷 foto-followup-01.jpg</div>
                <button
                    style="display:block;margin-top:6px;background:none;border:1px dashed #E2E5E9;color:#1B4D3E;padding:6px 12px;border-radius:6px;font-size:12px;cursor:pointer;">+
                    Upload File</button>
            </div>

            <div style="margin-bottom:16px;">
                <h5
                    style="font-size:11.5px;text-transform:uppercase;letter-spacing:0.4px;color:#6B7280;margin:0 0 8px 0;">
                    Lampiran Instruksi dari Atasan</h5>
                <div
                    style="display:inline-flex;align-items:center;gap:5px;background:#F0F2F3;padding:5px 10px;border-radius:6px;font-size:12px;">
                    📄 daftar-member-trial.pdf</div>
            </div>

            <button onclick="document.getElementById('modalCard').style.display='none'"
                style="background:#1B4D3E;color:#fff;border:none;padding:9px 16px;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;">Simpan</button>
        </div>
    </div>

</body>

</html>
