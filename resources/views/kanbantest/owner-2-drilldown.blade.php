<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner — Drill-down Goal</title>
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

        <a href="owner-1-dashboard" style="text-decoration:none;">
            <button
                style="background:none;color:#6B7280;border:1px solid #E2E5E9;padding:9px 16px;border-radius:7px;font-size:13px;cursor:pointer;margin-bottom:16px;">&larr;
                Kembali ke Dashboard</button>
        </a>

        <!-- Info goal induk -->
        <div style="background:#E4EEEA;padding:14px 16px;border-radius:9px;margin-bottom:18px;">
            <h3 style="margin:0 0 4px 0;font-size:15px;color:#123328;">Pertumbuhan Anggota Q3</h3>
            <div style="font-size:12.5px;color:#6B7280;">Dibuat oleh Ketua · Target 200 anggota baru/hari · Periode
                Jul–Sep 2026</div>
            <div style="display:flex;align-items:center;gap:8px;margin-top:10px;">
                <div style="flex:1;height:7px;background:#fff;border-radius:4px;overflow:hidden;">
                    <div style="height:100%;width:89%;background:#C98A3D;"></div>
                </div>
                <span style="font-size:12px;font-weight:700;color:#C98A3D;width:36px;text-align:right;">89%</span>
            </div>
        </div>

        <!-- Legenda -->
        <div style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:16px;font-size:11.5px;color:#6B7280;">
            <span style="display:inline-flex;align-items:center;gap:5px;"><i
                    style="width:10px;height:10px;border-radius:3px;background:#1B4D3E;display:inline-block;"></i>Kuantitatif
                (dijumlah ke induk)</span>
            <span style="display:inline-flex;align-items:center;gap:5px;"><i
                    style="width:10px;height:10px;border-radius:3px;background:#B5502F;display:inline-block;"></i>Taktis
                (aktivitas pendukung, tidak dijumlah)</span>
        </div>

        <!-- Tree view -->
        <div style="background:#fff;border-radius:10px;padding:8px;box-shadow:0 1px 2px rgba(0,0,0,0.05);">

            <!-- Level 1: Divisi -->
            <div
                style="padding:10px 12px;border-radius:7px;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
                    <span style="color:#6B7280;width:14px;display:inline-block;">&#9662;</span>
                    <span style="font-weight:600;">Divisi Marketing</span>
                    <span style="font-size:11px;color:#6B7280;font-weight:400;">Kadiv</span>
                </div>
                <div style="font-size:12px;color:#6B7280;width:130px;text-align:right;">92%</div>
            </div>

            <div style="margin-left:26px;border-left:1.5px dashed #E2E5E9;padding-left:14px;">

                <!-- Level 2: Area (Korea) -->
                <div
                    style="padding:10px 12px;border-radius:7px;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                    <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
                        <span style="color:#6B7280;width:14px;display:inline-block;">&#9662;</span>
                        <span style="font-weight:600;">Area Jakarta Selatan</span>
                        <span style="font-size:11px;color:#6B7280;font-weight:400;">Korea</span>
                    </div>
                    <div style="font-size:12px;color:#6B7280;width:130px;text-align:right;">70%</div>
                </div>

                <div style="margin-left:26px;border-left:1.5px dashed #E2E5E9;padding-left:14px;">

                    <!-- Level 3: Cabang (AM) -->
                    <div
                        style="padding:10px 12px;border-radius:7px;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                        <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
                            <span style="color:#6B7280;width:14px;display:inline-block;">&#9662;</span>
                            <span style="font-weight:600;">Cabang Kemang</span>
                            <span style="font-size:11px;color:#6B7280;font-weight:400;">AM</span>
                        </div>
                        <div style="font-size:12px;color:#6B7280;width:130px;text-align:right;">87%</div>
                    </div>

                    <div style="margin-left:26px;border-left:1.5px dashed #E2E5E9;padding-left:14px;">
                        <div
                            style="padding:10px 12px;border-radius:7px;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                            <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
                                <span style="font-weight:600;">Andi Nugraha</span><span
                                    style="font-size:11px;color:#6B7280;font-weight:400;">FO</span>
                            </div>
                            <div style="font-size:12px;color:#6B7280;width:130px;text-align:right;">4/5 (80%)</div>
                        </div>
                        <div
                            style="padding:10px 12px;border-radius:7px;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                            <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
                                <span style="font-weight:600;">Melati Putri</span><span
                                    style="font-size:11px;color:#6B7280;font-weight:400;">FO</span>
                            </div>
                            <div style="font-size:12px;color:#6B7280;width:130px;text-align:right;">5/5 (100%)</div>
                        </div>
                        <!-- Card taktis, ditandai beda -->
                        <div
                            style="padding:10px 12px;border-radius:7px;display:flex;justify-content:space-between;align-items:center;gap:12px;background:#FBEAE3;">
                            <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
                                <span style="font-weight:600;">Sebar Brosur — 100/hari per FO</span>
                                <span style="font-size:11px;color:#6B7280;font-weight:400;">Aktivitas Taktis</span>
                                <span
                                    style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;background:#fff;color:#B5502F;">Taktis</span>
                            </div>
                            <div style="font-size:12px;color:#6B7280;width:130px;text-align:right;">85% terlaksana</div>
                        </div>
                    </div>

                    <!-- Cabang lain, progress rendah -> highlight -->
                    <div
                        style="padding:10px 12px;border-radius:7px;display:flex;justify-content:space-between;align-items:center;gap:12px;background:#FBE9E7;">
                        <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
                            <span style="color:#6B7280;width:14px;display:inline-block;">&#9662;</span>
                            <span style="font-weight:600;">Cabang Blok M</span>
                            <span style="font-size:11px;color:#6B7280;font-weight:400;">AM</span>
                        </div>
                        <div style="font-size:12px;color:#C6493C;font-weight:700;width:130px;text-align:right;">53% ⚠
                        </div>
                    </div>
                    <div style="margin-left:26px;border-left:1.5px dashed #E2E5E9;padding-left:14px;">
                        <div
                            style="padding:10px 12px;border-radius:7px;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                            <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
                                <span style="font-weight:600;">Rangga Saputra</span><span
                                    style="font-size:11px;color:#6B7280;font-weight:400;">FO</span>
                            </div>
                            <div style="font-size:12px;color:#C6493C;font-weight:700;width:130px;text-align:right;">2/5
                                (40%)</div>
                        </div>
                        <div
                            style="padding:10px 12px;border-radius:7px;display:flex;justify-content:space-between;align-items:center;gap:12px;">
                            <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
                                <span style="font-weight:600;">Ika Lestari</span><span
                                    style="font-size:11px;color:#6B7280;font-weight:400;">FO</span>
                            </div>
                            <div style="font-size:12px;color:#6B7280;width:130px;text-align:right;">3/4 (75%)</div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Cabang taktis milik Direktur sendiri -->
            <div
                style="padding:10px 12px;border-radius:7px;display:flex;justify-content:space-between;align-items:center;gap:12px;background:#FBEAE3;">
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
                    <span style="font-weight:600;">Follow up 5 partner strategis/minggu</span>
                    <span style="font-size:11px;color:#6B7280;font-weight:400;">Direktur Bisnis (diri sendiri)</span>
                    <span
                        style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;background:#fff;color:#B5502F;">Taktis</span>
                </div>
                <div style="font-size:12px;color:#6B7280;width:130px;text-align:right;">3/5 minggu ini</div>
            </div>

        </div>

        <p style="font-size:12px;color:#9AA1A9;margin-top:16px;">
            ← <a href="owner-1-dashboard" style="color:#1B4D3E;">Kembali ke Halaman 1: Dashboard Utama</a>
        </p>

    </div>

</body>

</html>
