<!doctype html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Mockup — Kanban Goal Cascading (Data-Driven)</title>
        <style>
            :root {
                --bg: #f4f6f8;
                --surface: #ffffff;
                --primary: #1b4d3e;
                --primary-dark: #123328;
                --primary-soft: #e4eeea;
                --accent: #c98a3d;
                --accent-soft: #f6e9d8;
                --text: #1e2430;
                --text-muted: #6b7280;
                --border: #e2e5e9;
                --success: #2f9e68;
                --warning: #d68a2b;
                --danger: #c6493c;
                --tactical: #b5502f;
                --tactical-soft: #fbeae3;
                --self: #5b3fa6;
                --self-soft: #ede8fa;
                --radius: 10px;
            }
            * {
                box-sizing: border-box;
            }
            body {
                margin: 0;
                font-family:
                    "Inter",
                    -apple-system,
                    BlinkMacSystemFont,
                    "Segoe UI",
                    sans-serif;
                background: var(--bg);
                color: var(--text);
                font-size: 14px;
            }
            .app {
                display: flex;
                min-height: 100vh;
            }
            .sidebar {
                width: 240px;
                background: var(--primary-dark);
                color: #eaf2ee;
                flex-shrink: 0;
                padding: 20px 0;
                position: sticky;
                top: 0;
                height: 100vh;
                overflow-y: auto;
            }
            .sidebar-brand {
                padding: 0 20px 20px 20px;
                font-weight: 700;
                font-size: 16px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.12);
                margin-bottom: 14px;
            }
            .sidebar-brand span {
                display: block;
                font-weight: 400;
                font-size: 12px;
                color: #9fc3b2;
                margin-top: 2px;
            }
            .role-nav {
                list-style: none;
                margin: 0;
                padding: 0;
            }
            .role-nav button {
                width: 100%;
                text-align: left;
                background: none;
                border: none;
                color: #cfe3d9;
                padding: 11px 20px;
                font-size: 13.5px;
                cursor: pointer;
                border-left: 3px solid transparent;
                display: flex;
                flex-direction: column;
                gap: 1px;
            }
            .role-nav button small {
                color: #7fa593;
                font-size: 11px;
                font-weight: 400;
            }
            .role-nav button:hover {
                background: rgba(255, 255, 255, 0.06);
            }
            .role-nav button.active {
                background: rgba(255, 255, 255, 0.1);
                border-left-color: var(--accent);
                color: #fff;
                font-weight: 600;
            }
            .main {
                flex: 1;
                min-width: 0;
                display: flex;
                flex-direction: column;
            }
            .topbar {
                background: var(--surface);
                border-bottom: 1px solid var(--border);
                padding: 16px 32px 0 32px;
            }
            .topbar-title {
                font-size: 20px;
                font-weight: 700;
                margin: 0 0 4px 0;
            }
            .topbar-sub {
                color: var(--text-muted);
                font-size: 13px;
                margin-bottom: 14px;
            }
            .page-tabs {
                display: flex;
                gap: 4px;
            }
            .page-tabs button {
                background: none;
                border: none;
                padding: 9px 14px;
                font-size: 13px;
                color: var(--text-muted);
                cursor: pointer;
                border-bottom: 2px solid transparent;
                font-weight: 500;
            }
            .page-tabs button.active {
                color: var(--primary);
                border-bottom-color: var(--primary);
                font-weight: 700;
            }
            .content {
                padding: 28px 32px 60px 32px;
            }
            .page {
                display: none;
            }
            .page.active {
                display: block;
            }
            .panel-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 18px;
                flex-wrap: wrap;
                gap: 12px;
            }
            .panel-header h2 {
                margin: 0 0 4px 0;
                font-size: 17px;
            }
            .panel-header p {
                margin: 0;
                color: var(--text-muted);
                font-size: 13px;
            }
            .btn {
                background: var(--primary);
                color: #fff;
                border: none;
                padding: 9px 16px;
                border-radius: 7px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
            }
            .btn:hover {
                background: var(--primary-dark);
            }
            .btn.secondary {
                background: var(--surface);
                color: var(--primary);
                border: 1px solid var(--primary);
            }
            .btn.secondary:hover {
                background: var(--primary-soft);
            }
            .btn.ghost {
                background: none;
                color: var(--text-muted);
                border: 1px solid var(--border);
            }
            .chip {
                display: inline-block;
                padding: 3px 9px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 600;
                background: var(--primary-soft);
                color: var(--primary-dark);
            }
            .chip.accent {
                background: var(--accent-soft);
                color: #8a5a1e;
            }
            .chip.muted {
                background: #eef0f2;
                color: var(--text-muted);
            }
            .tag-self {
                background: var(--self-soft);
                color: var(--self);
                font-size: 11px;
                padding: 2px 8px;
                border-radius: 12px;
                font-weight: 600;
            }
            .tag-tactical {
                background: var(--tactical-soft);
                color: var(--tactical);
                font-size: 11px;
                padding: 2px 8px;
                border-radius: 12px;
                font-weight: 600;
            }
            .tag-quant {
                background: var(--primary-soft);
                color: var(--primary-dark);
                font-size: 11px;
                padding: 2px 8px;
                border-radius: 12px;
                font-weight: 600;
            }
            .badge-status {
                padding: 2px 8px;
                border-radius: 5px;
                font-size: 11px;
                font-weight: 600;
            }
            .badge-status.ok {
                background: #e4f4eb;
                color: var(--success);
            }
            .badge-status.warn {
                background: #fbf0e0;
                color: var(--warning);
            }
            .badge-status.risk {
                background: #fbe9e7;
                color: var(--danger);
            }
            .search-row {
                display: flex;
                gap: 10px;
                margin-bottom: 16px;
                flex-wrap: wrap;
            }
            .search-row input,
            .search-row select {
                padding: 8px 12px;
                border: 1px solid var(--border);
                border-radius: 7px;
                font-size: 13px;
                background: #fff;
            }
            .search-row input {
                flex: 1;
                min-width: 200px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                background: var(--surface);
                border-radius: var(--radius);
                overflow: hidden;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            }
            th {
                text-align: left;
                font-size: 11.5px;
                text-transform: uppercase;
                letter-spacing: 0.4px;
                color: var(--text-muted);
                background: #fafbfc;
                padding: 11px 16px;
                border-bottom: 1px solid var(--border);
            }
            td {
                padding: 12px 16px;
                border-bottom: 1px solid var(--border);
                font-size: 13.5px;
                vertical-align: middle;
            }
            tr:last-child td {
                border-bottom: none;
            }
            .user-cell {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .avatar {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: var(--primary-soft);
                color: var(--primary-dark);
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 12px;
                flex-shrink: 0;
            }
            .role-badges {
                display: flex;
                gap: 5px;
                flex-wrap: wrap;
            }
            .kanban {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 14px;
                align-items: start;
            }
            .kcol {
                background: #eef1f0;
                border-radius: var(--radius);
                padding: 12px;
                min-height: 160px;
            }
            .kcol h4 {
                margin: 0 0 10px 4px;
                font-size: 12.5px;
                color: var(--text-muted);
                text-transform: uppercase;
                letter-spacing: 0.4px;
                display: flex;
                justify-content: space-between;
            }
            .kcol h4 span {
                background: #dce2df;
                color: var(--text);
                border-radius: 10px;
                padding: 0 7px;
                font-weight: 700;
            }
            .kcard {
                background: var(--surface);
                border-radius: 9px;
                padding: 12px 13px;
                margin-bottom: 10px;
                cursor: pointer;
                border-left: 3px solid var(--border);
                box-shadow: 0 1px 2px rgba(20, 30, 25, 0.05);
            }
            .kcard:hover {
                box-shadow: 0 3px 10px rgba(20, 30, 25, 0.1);
            }
            .kcard.target {
                border-left-color: var(--accent);
            }
            .kcard.task {
                border-left-color: var(--primary);
            }
            .kcard.adhoc {
                border-left-color: #b9bfc6;
            }
            .kcard.tactical {
                border-left-color: var(--tactical);
            }
            .kcard-title {
                font-weight: 600;
                font-size: 13.5px;
                margin-bottom: 6px;
                line-height: 1.3;
            }
            .kcard-tags {
                display: flex;
                gap: 5px;
                flex-wrap: wrap;
                margin-bottom: 6px;
            }
            .kcard-meta {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 11.5px;
                color: var(--text-muted);
                margin-top: 8px;
            }
            .kcard-progress {
                height: 5px;
                border-radius: 3px;
                background: #e7e9eb;
                margin-top: 8px;
                overflow: hidden;
            }
            .kcard-progress i {
                display: block;
                height: 100%;
                background: var(--accent);
            }
            .kcard-input {
                display: flex;
                gap: 6px;
                margin-top: 9px;
                align-items: center;
            }
            .kcard-input input {
                width: 56px;
                padding: 5px 7px;
                border: 1px solid var(--border);
                border-radius: 5px;
                font-size: 12.5px;
            }
            .kcard-input button {
                padding: 5px 9px;
                font-size: 11.5px;
                border: none;
                background: var(--primary);
                color: #fff;
                border-radius: 5px;
                cursor: pointer;
            }
            .fab {
                position: fixed;
                bottom: 26px;
                right: 34px;
                background: var(--accent);
                color: #fff;
                border: none;
                padding: 12px 18px;
                border-radius: 24px;
                font-weight: 700;
                font-size: 13px;
                cursor: pointer;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
            }
            .tabgroup {
                display: flex;
                gap: 4px;
                margin-bottom: 16px;
                background: #eaecee;
                padding: 3px;
                border-radius: 8px;
                width: fit-content;
            }
            .tabgroup button {
                border: none;
                background: none;
                padding: 7px 15px;
                font-size: 12.5px;
                font-weight: 600;
                color: var(--text-muted);
                border-radius: 6px;
                cursor: pointer;
            }
            .tabgroup button.active {
                background: #fff;
                color: var(--primary-dark);
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
            }
            .tabpane {
                display: none;
            }
            .tabpane.active {
                display: block;
            }
            .empty-state {
                padding: 50px 20px;
                text-align: center;
                color: var(--text-muted);
                background: #fff;
                border: 1px dashed var(--border);
                border-radius: var(--radius);
            }
            .empty-state strong {
                display: block;
                color: var(--text);
                margin-bottom: 4px;
                font-size: 13.5px;
            }
            .goal-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 14px;
            }
            .goal-card {
                background: var(--surface);
                border-radius: var(--radius);
                padding: 16px;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
                cursor: pointer;
                border-top: 3px solid var(--primary);
            }
            .goal-card.foreign {
                border-top-color: #c7ccd1;
            }
            .goal-card:hover {
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.09);
            }
            .goal-card-top {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 8px;
            }
            .goal-card h3 {
                font-size: 14.5px;
                margin: 0 0 4px 0;
            }
            .goal-card .src {
                font-size: 11.5px;
                color: var(--text-muted);
            }
            .progress-row {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-top: 12px;
            }
            .progress-bar {
                flex: 1;
                height: 7px;
                background: #e7e9eb;
                border-radius: 4px;
                overflow: hidden;
            }
            .progress-bar i {
                display: block;
                height: 100%;
                background: var(--accent);
            }
            .progress-num {
                font-size: 12px;
                font-weight: 700;
                color: var(--accent);
                width: 36px;
                text-align: right;
            }
            .form-card {
                background: var(--surface);
                border-radius: var(--radius);
                padding: 22px;
                max-width: 680px;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            }
            .form-card .goal-induk {
                background: var(--primary-soft);
                padding: 12px 14px;
                border-radius: 8px;
                font-size: 12.5px;
                margin-bottom: 18px;
                color: var(--primary-dark);
            }
            .field {
                margin-bottom: 16px;
            }
            .field label {
                display: block;
                font-size: 12.5px;
                font-weight: 600;
                margin-bottom: 6px;
                color: var(--text);
            }
            .field .hint {
                font-size: 11.5px;
                color: var(--text-muted);
                font-weight: 400;
                margin-top: 4px;
            }
            .field input[type="text"],
            .field input[type="number"],
            .field textarea,
            .field select {
                width: 100%;
                padding: 9px 11px;
                border: 1px solid var(--border);
                border-radius: 7px;
                font-size: 13px;
                font-family: inherit;
            }
            .field textarea {
                resize: vertical;
                min-height: 60px;
            }
            .radio-row {
                display: flex;
                gap: 16px;
                flex-wrap: wrap;
            }
            .radio-row label {
                display: flex;
                align-items: center;
                gap: 6px;
                font-weight: 400;
                font-size: 13px;
            }
            .breakdown-row {
                display: grid;
                grid-template-columns: 1.3fr 1.2fr 0.9fr 0.6fr auto;
                gap: 8px;
                align-items: center;
                background: #fafbfc;
                padding: 10px;
                border-radius: 8px;
                margin-bottom: 8px;
                border: 1px solid var(--border);
            }
            .breakdown-row select,
            .breakdown-row input {
                padding: 7px 9px;
                font-size: 12px;
                border: 1px solid var(--border);
                border-radius: 6px;
                width: 100%;
            }
            .breakdown-row .rm {
                background: none;
                border: none;
                color: var(--danger);
                cursor: pointer;
                font-size: 16px;
            }
            .breakdown-row.is-tactical {
                background: var(--tactical-soft);
            }
            .breakdown-row.is-self {
                background: var(--self-soft);
            }
            .col-label {
                font-size: 10px;
                color: var(--text-muted);
                text-transform: uppercase;
                letter-spacing: 0.3px;
                margin-bottom: 6px;
                display: grid;
                grid-template-columns: 1.3fr 1.2fr 0.9fr 0.6fr auto;
                gap: 8px;
                padding: 0 10px;
            }
            .add-row-btn {
                background: none;
                border: 1px dashed var(--border);
                color: var(--primary);
                padding: 8px;
                width: 100%;
                border-radius: 7px;
                font-size: 12.5px;
                font-weight: 600;
                cursor: pointer;
                margin-bottom: 16px;
            }
            .form-actions {
                display: flex;
                gap: 10px;
                margin-top: 18px;
            }
            .assign-shortcut {
                display: flex;
                gap: 8px;
                margin-bottom: 12px;
            }
            .assign-shortcut button {
                flex: 1;
                padding: 9px;
                border-radius: 7px;
                font-size: 12.5px;
                font-weight: 600;
                cursor: pointer;
            }
            .assign-shortcut .opt-a {
                background: var(--primary);
                color: #fff;
                border: none;
            }
            .assign-shortcut .opt-b {
                background: #fff;
                color: var(--primary);
                border: 1px solid var(--primary);
            }
            .alloc-note {
                font-size: 12px;
                color: var(--text-muted);
                margin-bottom: 14px;
            }
            .tree {
                background: var(--surface);
                border-radius: var(--radius);
                padding: 8px;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            }
            .tree-node {
                padding: 10px 12px;
                border-radius: 7px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
            }
            .tree-node:hover {
                background: #fafbfc;
            }
            .tree-node .left {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 13px;
            }
            .tree-children {
                margin-left: 26px;
                border-left: 1.5px dashed var(--border);
                padding-left: 14px;
            }
            .tree-num {
                font-size: 12px;
                color: var(--text-muted);
                width: 130px;
                text-align: right;
                white-space: nowrap;
            }
            .tree-name {
                font-weight: 600;
            }
            .tree-role {
                font-size: 11px;
                color: var(--text-muted);
                font-weight: 400;
            }
            .toggle-caret {
                color: var(--text-muted);
                width: 14px;
                display: inline-block;
            }
            .induk-info {
                background: var(--primary-soft);
                padding: 14px 16px;
                border-radius: 9px;
                margin-bottom: 16px;
            }
            .induk-info h3 {
                margin: 0 0 4px 0;
                font-size: 15px;
            }
            .overlay {
                position: fixed;
                inset: 0;
                background: rgba(20, 25, 22, 0.45);
                display: none;
                align-items: center;
                justify-content: center;
                z-index: 50;
            }
            .overlay.active {
                display: flex;
            }
            .modal {
                background: #fff;
                border-radius: 12px;
                width: 520px;
                max-width: 92vw;
                max-height: 86vh;
                overflow-y: auto;
                padding: 24px;
            }
            .modal-head {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 14px;
            }
            .modal-head h3 {
                margin: 0;
                font-size: 16px;
            }
            .modal-close {
                background: none;
                border: none;
                font-size: 18px;
                cursor: pointer;
                color: var(--text-muted);
            }
            .modal-section {
                margin-bottom: 16px;
            }
            .modal-section h5 {
                font-size: 11.5px;
                text-transform: uppercase;
                letter-spacing: 0.4px;
                color: var(--text-muted);
                margin: 0 0 8px 0;
            }
            .log-item {
                display: flex;
                justify-content: space-between;
                font-size: 12.5px;
                padding: 7px 0;
                border-bottom: 1px solid var(--border);
            }
            .file-chip {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                background: #f0f2f3;
                padding: 5px 10px;
                border-radius: 6px;
                font-size: 12px;
                margin: 0 6px 6px 0;
            }
            .branch-chips {
                display: flex;
                gap: 6px;
                flex-wrap: wrap;
                margin-top: 6px;
            }
            .legend {
                display: flex;
                gap: 14px;
                flex-wrap: wrap;
                margin-bottom: 16px;
                font-size: 11.5px;
                color: var(--text-muted);
            }
            .legend span {
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }
            .legend i {
                width: 10px;
                height: 10px;
                border-radius: 3px;
                display: inline-block;
            }
        </style>
    </head>
    <body>
        <div class="app">
            <div class="sidebar">
                <div class="sidebar-brand">
                    Goal Cascade <span>Mockup — data-driven via JSON</span>
                </div>
                <ul class="role-nav" id="roleNav"></ul>
            </div>

            <div class="main">
                <div class="topbar">
                    <h1 class="topbar-title" id="roleTitle"></h1>
                    <div class="topbar-sub" id="roleSub"></div>
                    <div class="page-tabs" id="pageTabs"></div>
                </div>
                <div class="content" id="content"></div>
            </div>
        </div>

        <div class="overlay" id="modalRole">
            <div class="modal">
                <div class="modal-head">
                    <h3 id="modalRoleName">Atur Role</h3>
                    <button
                        class="modal-close"
                        onclick="closeModal('modalRole')"
                    >
                        &times;
                    </button>
                </div>
                <div class="modal-section">
                    <h5>Role Saat Ini</h5>
                    <div class="role-badges" id="modalRoleCurrent"></div>
                </div>
                <div class="modal-section">
                    <h5>Tambah Role Baru</h5>
                    <div
                        class="radio-row"
                        id="modalRoleOptions"
                        style="font-size: 12.5px"
                    ></div>
                </div>
                <div
                    class="modal-section"
                    style="
                        background: var(--primary-soft);
                        padding: 12px;
                        border-radius: 8px;
                    "
                >
                    <h5 style="margin-top: 0">Preview Hasil Akhir</h5>
                    <div class="role-badges" id="modalRolePreview"></div>
                </div>
                <div class="form-actions">
                    <button class="btn" onclick="closeModal('modalRole')">
                        Simpan</button
                    ><button
                        class="btn ghost"
                        onclick="closeModal('modalRole')"
                    >
                        Batal
                    </button>
                </div>
            </div>
        </div>

        <div class="overlay" id="modalCard">
            <div class="modal">
                <div class="modal-head">
                    <h3 id="mcTitle"></h3>
                    <button
                        class="modal-close"
                        onclick="closeModal('modalCard')"
                    >
                        &times;
                    </button>
                </div>
                <p
                    style="
                        font-size: 13px;
                        color: var(--text-muted);
                        margin-top: -8px;
                    "
                    id="mcMeta"
                ></p>
                <div class="modal-section" id="mcRealisasi"></div>
                <div class="modal-section">
                    <h5>Bukti Kerja</h5>
                    <div class="file-chip">📷 foto-closing-01.jpg</div>
                    <div class="file-chip">📷 foto-closing-02.jpg</div>
                </div>
                <div class="modal-section">
                    <h5>Histori Realisasi</h5>
                    <div id="mcLogs"></div>
                </div>
                <div class="modal-section">
                    <h5>Lampiran Instruksi dari Atasan</h5>
                    <div class="file-chip">📄 panduan-goal.pdf</div>
                </div>
            </div>
        </div>

        <script>
            /* =====================================================================
   DATA — semua konten mockup ada di sini (JSON). Ganti/tambah di sini
   untuk update tampilan, tidak perlu sentuh HTML/CSS di atas.
===================================================================== */
            const DATA = {
                users: [
                    {
                        name: "Rina Hartono",
                        uname: "rina.hr",
                        roles: ["HR", "Kadiv"],
                        status: "Aktif",
                    },
                    {
                        name: "Budi Santoso",
                        uname: "budi.s",
                        roles: ["Manager", "Korea"],
                        status: "Aktif",
                    },
                    {
                        name: "Andi Nugraha",
                        uname: "andi.n",
                        roles: ["FO"],
                        status: "Aktif",
                    },
                    {
                        name: "Siti Wulandari",
                        uname: "siti.w",
                        roles: [],
                        status: "Nonaktif",
                    },
                ],

                allRoles: [
                    "Owner",
                    "Ketua",
                    "Direktur Utama",
                    "Direktur Bisnis",
                    "Direktur Operasional",
                    "SPI",
                    "Kadiv",
                    "Staff",
                    "Korea",
                ],

                /* Personal Board per role: 2 tab (atasan / adhoc), tiap card:
     type: task|target | contribution: quantitative|tactical | selfAssigned: bool */
                boards: {
                    ketua: {
                        atasan: [],
                        adhoc: [
                            {
                                title: "Rapat evaluasi tahunan bersama pemegang saham",
                                type: "task",
                                status: "todo",
                                meta: "Personal · 20 Sep",
                            },
                            {
                                title: "Review laporan tahunan draft 1",
                                type: "task",
                                status: "done",
                                meta: "Personal · Selesai",
                            },
                        ],
                    },
                    direktur: {
                        atasan: [
                            {
                                title: "Pertumbuhan Anggota Q3 (porsi Bisnis)",
                                type: "target",
                                contribution: "quantitative",
                                status: "todo",
                                source: "Ketua",
                                value: "120/hari",
                                progress: 75,
                            },
                            {
                                title: "Follow up 5 partner strategis/minggu",
                                type: "task",
                                contribution: "tactical",
                                selfAssigned: true,
                                status: "inprogress",
                                source: "Diri Sendiri",
                                meta: "3/5 minggu ini",
                            },
                            {
                                title: "Restrukturisasi Divisi Operasional",
                                type: "task",
                                status: "todo",
                                source: "Ketua",
                                meta: "Belum di-breakdown",
                            },
                        ],
                        adhoc: [
                            {
                                title: "Diskusi partnership vendor baru",
                                type: "task",
                                status: "todo",
                                meta: "Personal · 3 Sep",
                            },
                        ],
                    },
                    spi: {
                        atasan: [
                            {
                                title: "Audit Kepatuhan Cabang",
                                type: "task",
                                status: "todo",
                                source: "Ketua",
                                meta: "12 cabang",
                            },
                        ],
                        adhoc: [],
                    },
                    kadiv: {
                        atasan: [
                            {
                                title: "Rekrut Anggota Baru (porsi Marketing)",
                                type: "target",
                                contribution: "quantitative",
                                status: "todo",
                                source: "Direktur Bisnis",
                                value: "70/hari",
                                progress: 82,
                            },
                            {
                                title: "Sebar Brosur Promosi — 100 lembar/hari per FO",
                                type: "task",
                                contribution: "tactical",
                                status: "inprogress",
                                source: "Diri Sendiri (turunan goal anggota)",
                                meta: "Aktivitas pendukung",
                            },
                        ],
                        adhoc: [],
                    },
                    korea: {
                        atasan: [
                            {
                                title: "Rekrut Anggota Baru (porsi Area Jaksel)",
                                type: "target",
                                contribution: "quantitative",
                                status: "todo",
                                source: "Kadiv Marketing",
                                value: "30/hari",
                                progress: 70,
                            },
                        ],
                        adhoc: [],
                    },
                    am: {
                        atasan: [
                            {
                                title: "Rekrut Anggota Baru (porsi Cabang Kemang)",
                                type: "target",
                                contribution: "quantitative",
                                status: "todo",
                                source: "Korea Jaksel",
                                value: "15/hari",
                                progress: 87,
                            },
                        ],
                        adhoc: [],
                    },
                    staff: {
                        atasan: [
                            {
                                title: "Rekrut Anggota Baru",
                                type: "target",
                                contribution: "quantitative",
                                status: "todo",
                                source: "AM Kemang",
                                value: "Target 5/hari",
                                progress: 80,
                                inputToday: 4,
                            },
                            {
                                title: "Follow up member trial minggu ini",
                                type: "task",
                                status: "inprogress",
                                source: "AM Kemang",
                                meta: "Due 5 Sep",
                            },
                            {
                                title: "Rekrut Anggota Baru — Kemarin",
                                type: "target",
                                status: "done",
                                meta: "5/5 tercapai · 31 Agu",
                            },
                        ],
                        adhoc: [
                            {
                                title: "Bantu setup booth event weekend",
                                type: "task",
                                status: "todo",
                                meta: "Personal · 6 Sep",
                            },
                        ],
                    },
                },

                goalLists: {
                    ketua: [
                        {
                            title: "Pertumbuhan Anggota Q3",
                            type: "Target",
                            badge: "",
                            status: "ok",
                            progress: 89,
                            meta: "3 target direktorat · dibuat 1 Jul 2026",
                        },
                        {
                            title: "Restrukturisasi Divisi Operasional",
                            type: "Task",
                            status: "warn",
                            progress: 55,
                            meta: "1 target direktorat · dibuat 12 Agu 2026",
                        },
                    ],
                    direktur: [
                        {
                            title: "Pertumbuhan Anggota Q3 (porsi Bisnis)",
                            source: "Direktur Bisnis",
                            status: "ok",
                            progress: 75,
                            meta: "Dari Ketua · 120/hari",
                            own: true,
                        },
                        {
                            title: "Efisiensi Operasional Cabang",
                            source: "Direktur Operasional",
                            status: "warn",
                            progress: 60,
                            meta: "Inisiatif sendiri · read-only",
                            own: false,
                        },
                    ],
                    spi: [
                        {
                            title: "Audit Kepatuhan Cabang",
                            source: "SPI",
                            status: "risk",
                            progress: 41,
                            meta: "Dari Ketua · 12 cabang",
                            own: true,
                        },
                    ],
                    kadiv: [
                        {
                            title: "Rekrut Anggota Baru (porsi Marketing)",
                            type: "Target",
                            status: "ok",
                            progress: 82,
                            meta: "70/hari · Sudah dipecah ke 2 staff + 1 taktis",
                        },
                    ],
                    korea: [
                        {
                            title: "Rekrut Anggota Baru (porsi Area Jaksel)",
                            type: "Target",
                            status: "ok",
                            progress: 70,
                            meta: "30/hari · dipecah ke 2 AM",
                        },
                    ],
                    am: [
                        {
                            title: "Rekrut Anggota Baru (porsi Cabang Kemang)",
                            type: "Target",
                            status: "ok",
                            progress: 87,
                            meta: "15/hari · dipecah ke 3 FO",
                        },
                    ],
                },

                /* Form breakdown: goal induk + baris breakdown contoh + opsi target */
                breakdown: {
                    direktur: {
                        induk: {
                            title: "Pertumbuhan Anggota Q3 (porsi Bisnis)",
                            meta: "Sisa alokasi kuantitatif: 120/hari",
                        },
                        targetOptions: [
                            "Kadiv Marketing",
                            "Kadiv Penjualan",
                            "Kadiv Keuangan",
                            "Kadiv Operasional",
                            "Kadiv HRD",
                        ],
                        rows: [
                            {
                                assignee: "Kadiv Marketing",
                                jobdesk: "Rekrut Anggota Baru",
                                contrib: "quantitative",
                                porsi: "70",
                                unit: "/hari",
                            },
                            {
                                assignee: "Kadiv Penjualan",
                                jobdesk: "Rekrut Anggota Baru",
                                contrib: "quantitative",
                                porsi: "50",
                                unit: "/hari",
                            },
                            {
                                assignee: "Diri Sendiri",
                                jobdesk: "Follow up 5 partner strategis/minggu",
                                contrib: "tactical",
                                porsi: "",
                                unit: "",
                            },
                        ],
                    },
                    spi: {
                        induk: {
                            title: "Audit Kepatuhan Cabang",
                            meta: "Tipe: Task",
                        },
                        targetOptions: [
                            "Staff — Dewi Anggraini",
                            "Staff — Fajar Ramadhan",
                            "Kadiv Marketing",
                            "Kadiv Operasional",
                        ],
                        dualMode: true,
                        rows: [
                            {
                                assignee: "Staff — Dewi Anggraini",
                                jobdesk: "Audit dokumen cabang Jaksel",
                                contrib: "tactical",
                                porsi: "",
                                unit: "",
                            },
                        ],
                    },
                    kadiv: {
                        induk: {
                            title: "Rekrut Anggota Baru (porsi Marketing)",
                            meta: "Sisa alokasi kuantitatif: 70/hari",
                        },
                        targetOptions: [
                            "Staff — Dewi Anggraini",
                            "Staff — Fajar Ramadhan",
                            "Korea Jaksel",
                            "Korea Jakut",
                        ],
                        rows: [
                            {
                                assignee: "Staff — Dewi Anggraini",
                                jobdesk: "Rekrut Anggota Baru",
                                contrib: "quantitative",
                                porsi: "35",
                                unit: "/hari",
                            },
                            {
                                assignee: "Staff — Fajar Ramadhan",
                                jobdesk: "Rekrut Anggota Baru",
                                contrib: "quantitative",
                                porsi: "35",
                                unit: "/hari",
                            },
                            {
                                assignee: "Diri Sendiri",
                                jobdesk:
                                    "Sebar Brosur Promosi — 100 lembar/hari per FO",
                                contrib: "tactical",
                                porsi: "",
                                unit: "",
                            },
                        ],
                    },
                    korea: {
                        induk: {
                            title: "Rekrut Anggota Baru (porsi Area Jaksel)",
                            meta: "Sisa alokasi kuantitatif: 30/hari",
                        },
                        targetOptions: [
                            "AM — Kemang",
                            "AM — Blok M",
                            "AM — Fatmawati",
                        ],
                        assignAllLabel: "Assign ke Semua AM",
                        rows: [
                            {
                                assignee: "AM — Kemang",
                                jobdesk: "Rekrut Anggota Baru",
                                contrib: "quantitative",
                                porsi: "15",
                                unit: "/hari",
                            },
                            {
                                assignee: "AM — Blok M",
                                jobdesk: "Rekrut Anggota Baru",
                                contrib: "quantitative",
                                porsi: "15",
                                unit: "/hari",
                            },
                        ],
                    },
                    am: {
                        induk: {
                            title: "Rekrut Anggota Baru (porsi Cabang Kemang)",
                            meta: "Sisa alokasi kuantitatif: 15/hari",
                        },
                        targetOptions: [
                            "FO — Andi Nugraha",
                            "FO — Melati Putri",
                            "FO — Yusuf Hakim",
                        ],
                        assignAllLabel: "Assign ke Semua FO",
                        rows: [
                            {
                                assignee: "FO — Andi Nugraha",
                                jobdesk: "Rekrut Anggota Baru",
                                contrib: "quantitative",
                                porsi: "5",
                                unit: "/hari",
                            },
                            {
                                assignee: "FO — Melati Putri",
                                jobdesk: "Rekrut Anggota Baru",
                                contrib: "quantitative",
                                porsi: "5",
                                unit: "/hari",
                            },
                            {
                                assignee: "FO — Yusuf Hakim",
                                jobdesk: "Sebar Brosur Promosi",
                                contrib: "tactical",
                                porsi: "100",
                                unit: "lembar/hari",
                            },
                        ],
                    },
                },

                /* Drill-down tree: nested, generic. contrib='tactical' ditandai beda dari 'quantitative' */
                tree: {
                    name: "Divisi Marketing",
                    role: "Kadiv",
                    progress: 92,
                    children: [
                        {
                            name: "Area Jakarta Selatan",
                            role: "Korea",
                            progress: 70,
                            children: [
                                {
                                    name: "Cabang Kemang",
                                    role: "AM",
                                    progress: 87,
                                    children: [
                                        {
                                            name: "Andi Nugraha",
                                            role: "FO",
                                            value: "4/5 (80%)",
                                        },
                                        {
                                            name: "Melati Putri",
                                            role: "FO",
                                            value: "5/5 (100%)",
                                        },
                                        {
                                            name: "Yusuf Hakim",
                                            role: "FO",
                                            value: "4/5 (80%)",
                                        },
                                        {
                                            name: "Sebar Brosur — 100/hari per FO",
                                            role: "Aktivitas Taktis",
                                            contrib: "tactical",
                                            value: "85% terlaksana",
                                        },
                                    ],
                                },
                                {
                                    name: "Cabang Blok M",
                                    role: "AM",
                                    progress: 53,
                                    children: [
                                        {
                                            name: "Rangga Saputra",
                                            role: "FO",
                                            value: "2/5 (40%)",
                                        },
                                        {
                                            name: "Ika Lestari",
                                            role: "FO",
                                            value: "3/4 (75%)",
                                        },
                                    ],
                                },
                            ],
                        },
                        {
                            name: "Follow up 5 partner strategis/minggu",
                            role: "Direktur Bisnis (diri sendiri)",
                            contrib: "tactical",
                            value: "3/5 minggu ini",
                        },
                    ],
                },
            };

            /* =====================================================================
   ROLE CONFIG — struktur halaman tiap role (pages, label, tabs)
===================================================================== */
            const ROLE_CONFIG = {
                superadmin: {
                    title: "Superadmin",
                    sub: "Kelola role setiap user di sistem goal cascading",
                    tabs: [
                        ["p1", "Manajemen Role"],
                        ["p2", "Panel Koreksi Data"],
                    ],
                    type: "superadmin",
                },
                owner: {
                    title: "Owner",
                    sub: "Dashboard read-only, visibilitas penuh ke semua goal",
                    tabs: [
                        ["p1", "Dashboard Utama"],
                        ["p2", "Drill-down Goal"],
                    ],
                    type: "owner",
                },
                ketua: {
                    title: "Ketua",
                    sub: "Sumber goal strategis tertinggi",
                    tabs: [
                        ["p1", "Personal Board"],
                        ["p2", "List Goal Strategis"],
                        ["p3", "Form Buat Goal"],
                        ["p4", "Drill-down Goal"],
                    ],
                    type: "cascader",
                    boardKey: "ketua",
                    goalKey: "ketua",
                    noAtasan: true,
                },
                direktur: {
                    title: "Direktur",
                    sub: "Utama / Bisnis / Operasional — 1 desain, beda orang",
                    tabs: [
                        ["p1", "Personal Board"],
                        ["p2", "List Goal Direktur"],
                        ["p3", "Form Breakdown"],
                        ["p4", "Drill-down Goal"],
                    ],
                    type: "cascader",
                    boardKey: "direktur",
                    goalKey: "direktur",
                    breakdownKey: "direktur",
                },
                spi: {
                    title: "SPI",
                    sub: "Setara direktur — bisa assign langsung ke Staff atau Kadiv",
                    tabs: [
                        ["p1", "Personal Board"],
                        ["p2", "List Goal Direktur"],
                        ["p3", "Form Breakdown"],
                        ["p4", "Drill-down Goal"],
                    ],
                    type: "cascader",
                    boardKey: "spi",
                    goalKey: "spi",
                    breakdownKey: "spi",
                },
                kadiv: {
                    title: "Kadiv",
                    sub: "Operasional / Marketing / Keuangan / Penjualan / HRD",
                    tabs: [
                        ["p1", "Personal Board"],
                        ["p2", "List Goal Diterima"],
                        ["p3", "Form Breakdown"],
                        ["p4", "Drill-down Goal"],
                    ],
                    type: "cascader",
                    boardKey: "kadiv",
                    goalKey: "kadiv",
                    breakdownKey: "kadiv",
                },
                korea: {
                    title: "Korea",
                    sub: "Koordinator Area — menaungi beberapa cabang",
                    tabs: [
                        ["p1", "Personal Board"],
                        ["p2", "List Goal Diterima"],
                        ["p3", "Form Breakdown"],
                        ["p4", "Drill-down Goal"],
                    ],
                    type: "cascader",
                    boardKey: "korea",
                    goalKey: "korea",
                    breakdownKey: "korea",
                    branches: [
                        "Cabang Kemang",
                        "Cabang Blok M",
                        "Cabang Fatmawati",
                    ],
                },
                am: {
                    title: "AM",
                    sub: "Area Manager — 1 cabang",
                    tabs: [
                        ["p1", "Personal Board"],
                        ["p2", "List Goal Diterima"],
                        ["p3", "Form Breakdown"],
                        ["p4", "Drill-down Goal"],
                    ],
                    type: "cascader",
                    boardKey: "am",
                    goalKey: "am",
                    breakdownKey: "am",
                },
                staff: {
                    title: "Staff / FO",
                    sub: "Titik akhir rantai — eksekusi & input realisasi",
                    tabs: [["p1", "Personal Board"]],
                    type: "leaf",
                    boardKey: "staff",
                },
            };

            /* =====================================================================
   RENDER HELPERS
===================================================================== */
            function contribTag(c) {
                if (c === "tactical")
                    return '<span class="tag-tactical">Taktis</span>';
                if (c === "quantitative")
                    return '<span class="tag-quant">Kuantitatif</span>';
                return "";
            }
            function selfTag(card) {
                return card.selfAssigned
                    ? '<span class="tag-self">Kerjaan Sendiri</span>'
                    : "";
            }

            function cardHTML(card) {
                const cls =
                    card.type === "target"
                        ? "target"
                        : card.contribution === "tactical"
                          ? "tactical"
                          : "task";
                let progress = "";
                if (card.progress != null)
                    progress = `<div class="kcard-progress"><i style="width:${card.progress}%"></i></div>`;
                let input = "";
                if (card.inputToday != null) {
                    input = `<div class="kcard-input"><input type="number" value="${card.inputToday}"><button>Simpan Realisasi</button></div>`;
                }
                const metaRight = card.value || card.meta || "";
                return `<div class="kcard ${cls}" onclick='openCardModal(${JSON.stringify(card)})'>
      <div class="kcard-tags">${contribTag(card.contribution)}${selfTag(card)}</div>
      <div class="kcard-title">${card.title}</div>
      <div class="kcard-meta"><span>${card.source ? "Dari: " + card.source : "Personal"}</span><span>${metaRight}</span></div>
      ${progress}${input}
    </div>`;
            }

            function renderKanban(cards) {
                const cols = {
                    todo: "To Do",
                    inprogress: "In Progress",
                    review: "Review",
                    done: "Done",
                };
                let html = '<div class="kanban">';
                for (const key in cols) {
                    const items = cards.filter((c) => c.status === key);
                    html += `<div class="kcol"><h4>${cols[key]} <span>${items.length}</span></h4>${items.map(cardHTML).join("")}</div>`;
                }
                html += "</div>";
                return html;
            }

            function goalCardHTML(g, role) {
                const cls = g.own === false ? "goal-card foreign" : "goal-card";
                const badge = g.source
                    ? `<span class="chip ${g.own === false ? "muted" : ""}">${g.source}</span>`
                    : g.type
                      ? `<span class="chip">${g.type}</span>`
                      : "";
                return `<div class="${cls}" onclick="switchPage('${role}','p4')">
      <div class="goal-card-top">${badge}<span class="badge-status ${g.status}">${g.progress}%</span></div>
      <h3>${g.title}</h3>
      <div class="src">${g.meta}</div>
      <div class="progress-row"><div class="progress-bar"><i style="width:${g.progress}%"></i></div><span class="progress-num">${g.progress}%</span></div>
    </div>`;
            }

            function breakdownRowHTML(row, targetOptions) {
                const isTactical = row.contrib === "tactical";
                const isSelf = row.assignee === "Diri Sendiri";
                const rowClass = isSelf
                    ? "is-self"
                    : isTactical
                      ? "is-tactical"
                      : "";
                const options = ["Diri Sendiri", ...targetOptions]
                    .map(
                        (o) =>
                            `<option ${o === row.assignee ? "selected" : ""}>${o}</option>`,
                    )
                    .join("");
                return `<div class="breakdown-row ${rowClass}">
      <select>${options}</select>
      <input type="text" value="${row.jobdesk}">
      <select>
        <option ${row.contrib === "quantitative" ? "selected" : ""}>Kuantitatif</option>
        <option ${row.contrib === "tactical" ? "selected" : ""}>Taktis</option>
      </select>
      <input type="text" value="${isTactical ? "—" : row.porsi + " " + row.unit}" ${isTactical ? "disabled" : ""}>
      <button class="rm">&times;</button>
    </div>`;
            }

            function treeNodeHTML(node) {
                const isTactical = node.contrib === "tactical";
                const num = node.value
                    ? node.value
                    : node.progress != null
                      ? node.progress + "%"
                      : "";
                const children =
                    node.children && node.children.length
                        ? `<div class="tree-children">${node.children.map(treeNodeHTML).join("")}</div>`
                        : "";
                const caret =
                    node.children && node.children.length
                        ? '<span class="toggle-caret">▾</span>'
                        : "";
                return `<div class="tree-node">
      <div class="left">${caret}<span class="tree-name">${node.name}</span>
        <span class="tree-role">${node.role}</span>${isTactical ? '<span class="tag-tactical">Taktis</span>' : ""}</div>
      <div class="tree-num">${num}</div>
    </div>${children}`;
            }

            /* =====================================================================
   PAGE BUILDERS per tipe role
===================================================================== */
            function boardPageHTML(role, cfg) {
                const board = DATA.boards[cfg.boardKey] || {
                    atasan: [],
                    adhoc: [],
                };
                const atasanContent = cfg.noAtasan
                    ? `<div class="empty-state"><strong>Tidak ada goal dari atasan</strong>Kamu berada di level tertinggi rantai goal dalam sistem ini.</div>`
                    : board.atasan.length
                      ? renderKanban(board.atasan)
                      : `<div class="empty-state"><strong>Belum ada goal masuk</strong></div>`;
                const adhocContent = renderKanban(board.adhoc);
                return `<div class="page active" data-page="p1">
      <div class="panel-header"><div><h2>Board Saya</h2></div></div>
      <div class="legend">
        <span><i style="background:var(--primary)"></i>Task</span>
        <span><i style="background:var(--accent)"></i>Target (angka)</span>
        <span><i style="background:var(--tactical)"></i>Taktis (pendukung)</span>
        <span><i style="background:var(--self)"></i>Kerjaan Sendiri</span>
      </div>
      <div class="tabgroup">
        <button class="active" onclick="switchTab(this,'${role}Tab1')">Goal dari Atasan</button>
        <button onclick="switchTab(this,'${role}Tab2')">Ad-hoc</button>
      </div>
      <div class="tabpane active" id="${role}Tab1">${atasanContent}</div>
      <div class="tabpane" id="${role}Tab2">${adhocContent}<button class="fab">+ Tambah List Sendiri</button></div>
    </div>`;
            }

            function goalListPageHTML(role, cfg) {
                const goals = DATA.goalLists[cfg.goalKey] || [];
                const branchChips = cfg.branches
                    ? `<div class="branch-chips">${cfg.branches.map((b) => `<span class="chip muted">${b}</span>`).join("")}</div>`
                    : "";
                const showTabs =
                    role === "direktur"
                        ? `<div class="tabgroup"><button class="active">Semua</button><button>Milik Saya</button><button>Dari Ketua</button></div>`
                        : "";
                const createBtn =
                    role === "ketua" || role === "direktur" || role === "spi"
                        ? `<button class="btn" onclick="switchPage('${role}','p3')">+ ${role === "ketua" ? "Buat Goal Strategis Baru" : "Buat Goal Sendiri"}</button>`
                        : "";
                return `<div class="page" data-page="p2">
      <div class="panel-header"><div><h2>${role === "ketua" ? "Goal Strategis Saya" : role === "korea" ? "Goal Area Saya" : role === "am" ? "Goal Branch Saya" : "Goal Diterima"}</h2>${branchChips}</div>${createBtn}</div>
      ${showTabs}
      <div class="goal-grid">${goals.map((g) => goalCardHTML(g, role)).join("")}</div>
    </div>`;
            }

            function breakdownFormPageHTML(role, cfg) {
                const bd = DATA.breakdown[cfg.breakdownKey];
                if (!bd)
                    return `<div class="page" data-page="p3"><div class="empty-state">Form belum tersedia untuk role ini.</div></div>`;
                const shortcut = bd.assignAllLabel
                    ? `<div class="assign-shortcut"><button class="opt-a">Pilih Manual</button><button class="opt-b">${bd.assignAllLabel}</button></div>`
                    : "";
                const dualNote = bd.dualMode
                    ? `<div class="assign-shortcut"><button class="opt-a">Staff Langsung</button><button class="opt-b">Kadiv</button></div>`
                    : "";
                const rows = bd.rows
                    .map((r) => breakdownRowHTML(r, bd.targetOptions))
                    .join("");
                return `<div class="page" data-page="p3">
      <div class="form-card">
        <div class="goal-induk">Goal induk: <strong>${bd.induk.title}</strong><br>${bd.induk.meta}</div>
        ${dualNote}${shortcut}
        <div class="col-label"><span>Assign ke</span><span>Judul Jobdesk</span><span>Tipe Kontribusi</span><span>Porsi</span><span></span></div>
        ${rows}
        <button class="add-row-btn">+ Tambah Baris</button>
        <div class="alloc-note">Baris "Taktis" &amp; "Diri Sendiri" tidak ikut dijumlah ke total kuantitatif induk — dilacak sebagai aktivitas pendukung.</div>
        <div class="field"><label>Deadline / Periode</label><input type="text" placeholder="Berkelanjutan (harian)"></div>
        <div class="field"><label>Lampiran Instruksi (opsional)</label><input type="text" placeholder="Upload file..."></div>
        <div class="form-actions"><button class="btn" onclick="switchPage('${role}','p4')">Simpan &amp; Kirim</button><button class="btn ghost" onclick="switchPage('${role}','p2')">Batal</button></div>
      </div>
    </div>`;
            }

            function drilldownPageHTML(role, cfg) {
                const goals = DATA.goalLists[cfg.goalKey] || [
                    { title: "Goal", progress: 0 },
                ];
                const g = goals[0];
                const breakdownBtn = cfg.breakdownKey
                    ? `<button class="btn secondary" style="margin-bottom:14px" onclick="switchPage('${role}','p3')">Breakdown Ulang</button>`
                    : "";
                return `<div class="page" data-page="p4">
      <button class="btn ghost" style="margin-bottom:14px" onclick="switchPage('${role}','p2')">&larr; Kembali ke List Goal</button>
      <div class="induk-info"><h3>${g.title}</h3><div style="font-size:12.5px;color:var(--text-muted)">${g.meta || ""}</div>
        <div class="progress-row"><div class="progress-bar"><i style="width:${g.progress}%"></i></div><span class="progress-num">${g.progress}%</span></div></div>
      ${breakdownBtn}
      <div class="tree">${treeNodeHTML(DATA.tree)}</div>
    </div>`;
            }

            function ketuaFormPageHTML() {
                return `<div class="page" data-page="p3">
    <div class="form-card">
      <div class="field"><label>Judul Goal</label><input type="text" placeholder="mis. Pertumbuhan Anggota Q4"></div>
      <div class="field"><label>Deskripsi</label><textarea placeholder="Konteks & latar belakang goal ini..."></textarea></div>
      <div class="field"><label>Tipe Goal</label><div class="radio-row">
        <label><input type="radio" name="tipe" checked> Target (angka + periode)</label>
        <label><input type="radio" name="tipe"> Task (selesai/belum)</label></div></div>
      <div class="field"><label>Nilai Target</label><input type="number" placeholder="200"></div>
      <div class="field"><label>Assign ke</label><div class="radio-row" style="flex-wrap:wrap">
        <label><input type="checkbox" checked> Direktur Bisnis</label>
        <label><input type="checkbox" checked> Direktur Operasional</label>
        <label><input type="checkbox"> Direktur Utama</label>
        <label><input type="checkbox"> SPI</label></div></div>
      <div class="field"><label>Periode Berlaku</label><input type="text" placeholder="1 Jul – 30 Sep 2026"></div>
      <div class="field"><label>Lampiran Instruksi (opsional)</label><input type="text" placeholder="Upload file..."></div>
      <div class="form-actions"><button class="btn" onclick="switchPage('ketua','p4')">Simpan &amp; Buat</button><button class="btn ghost" onclick="switchPage('ketua','p2')">Batal</button></div>
    </div>
  </div>`;
            }

            function superadminHTML() {
                const rows = DATA.users
                    .map(
                        (u) => `<tr>
      <td><div class="user-cell"><div class="avatar">${u.name
          .split(" ")
          .map((w) => w[0])
          .slice(0, 2)
          .join(
              "",
          )}</div>${u.name} <span style="color:var(--text-muted)">· ${u.uname}</span></div></td>
      <td><div class="role-badges">${u.roles.length ? u.roles.map((r, i) => `<span class="chip ${i > 0 ? "accent" : ""}">${r}</span>`).join("") : '<span class="chip muted">Belum diset</span>'}</div></td>
      <td><span class="badge-status ${u.status === "Aktif" ? "ok" : "warn"}">${u.status}</span></td>
      <td><button class="btn ghost" onclick='openRoleModal(${JSON.stringify(u)})'>Atur Role</button></td>
    </tr>`,
                    )
                    .join("");
                return `<div class="page active" data-page="p1">
      <div class="panel-header"><div><h2>Manajemen Role</h2><p>Semua user dalam sistem, cari &amp; atur role-nya</p></div><button class="btn secondary">+ Tambah User</button></div>
      <div class="search-row"><input type="text" placeholder="Cari nama / username / email..."><select><option>Semua Role</option></select><select><option>Semua Status</option></select></div>
      <table><thead><tr><th>Nama</th><th>Role Saat Ini</th><th>Status</th><th>Aksi</th></tr></thead><tbody>${rows}</tbody></table>
    </div>
    <div class="page" data-page="p2">
      <div class="panel-header"><div><h2>Panel Koreksi Data</h2><p>Akses penuh ke semua board — reassign / hapus card yang salah input</p></div></div>
      <div class="empty-state"><strong>Reuse tampilan board role lain</strong>Superadmin membuka board siapapun lewat pencarian user, lalu muncul tombol admin override (Reassign / Hapus / Reset Status) di tiap card.</div>
    </div>`;
            }

            function ownerHTML() {
                const goals = [
                    {
                        source: "Ketua",
                        title: "Pertumbuhan Anggota Q3",
                        meta: "Target 200/hari · Jul–Sep 2026",
                        status: "ok",
                        progress: 89,
                    },
                    {
                        source: "Direktur Bisnis",
                        title: "Ekspansi Cabang Baru",
                        meta: "Tipe Task · Deadline 30 Sep 2026",
                        status: "warn",
                        progress: 64,
                    },
                    {
                        source: "SPI",
                        title: "Audit Kepatuhan Cabang",
                        meta: "Tipe Task · 12 cabang",
                        status: "risk",
                        progress: 41,
                    },
                ];
                return `<div class="page active" data-page="p1">
      <div class="panel-header"><div><h2>Dashboard Goal Strategis</h2><p>Ringkasan seluruh goal, seluruh direktorat — read only</p></div></div>
      <div class="search-row"><select><option>Semua Ketua</option></select><select><option>Semua Direktur</option></select><select><option>Semua Divisi</option></select><select><option>Periode: Q3 2026</option></select></div>
      <div class="goal-grid">${goals.map((g) => goalCardHTML(g, "owner")).join("")}</div>
    </div>
    <div class="page" data-page="p2">
      <button class="btn ghost" style="margin-bottom:14px" onclick="switchPage('owner','p1')">&larr; Kembali ke Dashboard</button>
      <div class="induk-info"><h3>Pertumbuhan Anggota Q3</h3><div style="font-size:12.5px;color:var(--text-muted)">Dibuat oleh Ketua · Target 200 anggota baru/hari</div>
        <div class="progress-row"><div class="progress-bar"><i style="width:89%"></i></div><span class="progress-num">89%</span></div></div>
      <div class="tree">${treeNodeHTML(DATA.tree)}</div>
    </div>`;
            }

            /* =====================================================================
   MAIN RENDER
===================================================================== */
            function buildRoleNav() {
                const nav = document.getElementById("roleNav");
                nav.innerHTML = Object.keys(ROLE_CONFIG)
                    .map((key) => {
                        const c = ROLE_CONFIG[key];
                        return `<li><button data-role="${key}">${c.title}<small>${c.sub}</small></button></li>`;
                    })
                    .join("");
                nav.querySelectorAll("button").forEach((b) =>
                    b.addEventListener("click", () =>
                        renderRole(b.dataset.role),
                    ),
                );
            }

            function pageSetHTML(role) {
                const cfg = ROLE_CONFIG[role];
                if (cfg.type === "superadmin") return superadminHTML();
                if (cfg.type === "owner") return ownerHTML();
                if (cfg.type === "leaf") return boardPageHTML(role, cfg);
                // cascader (ketua/direktur/spi/kadiv/korea/am)
                let html =
                    boardPageHTML(role, cfg) + goalListPageHTML(role, cfg);
                html +=
                    role === "ketua"
                        ? ketuaFormPageHTML()
                        : breakdownFormPageHTML(role, cfg);
                html += drilldownPageHTML(role, cfg);
                return html;
            }

            function renderRole(role) {
                document
                    .querySelectorAll(".role-nav button")
                    .forEach((b) =>
                        b.classList.toggle("active", b.dataset.role === role),
                    );
                const cfg = ROLE_CONFIG[role];
                document.getElementById("roleTitle").textContent = cfg.title;
                document.getElementById("roleSub").textContent = cfg.sub;
                document.getElementById("content").innerHTML =
                    pageSetHTML(role);
                document.getElementById("content").dataset.role = role;

                const tabWrap = document.getElementById("pageTabs");
                tabWrap.innerHTML = "";
                cfg.tabs.forEach((t, i) => {
                    const btn = document.createElement("button");
                    btn.textContent = t[1];
                    btn.className = i === 0 ? "active" : "";
                    btn.onclick = () => switchPage(role, t[0], btn);
                    tabWrap.appendChild(btn);
                });
                // page p1 already has class active from boardPageHTML; ensure others hidden
                document
                    .querySelectorAll(`#content .page`)
                    .forEach((p, i) =>
                        p.classList.toggle("active", p.dataset.page === "p1"),
                    );
            }

            function switchPage(role, pageId, btnEl) {
                document
                    .querySelectorAll(`#content .page`)
                    .forEach((p) =>
                        p.classList.toggle("active", p.dataset.page === pageId),
                    );
                const cfg = ROLE_CONFIG[role];
                const idx = cfg.tabs.findIndex((t) => t[0] === pageId);
                document
                    .querySelectorAll("#pageTabs button")
                    .forEach((b, i) => b.classList.toggle("active", i === idx));
            }

            function switchTab(btn, paneId) {
                const group = btn.parentElement;
                group
                    .querySelectorAll("button")
                    .forEach((b) => b.classList.remove("active"));
                btn.classList.add("active");
                const panes = group.parentElement.querySelectorAll(".tabpane");
                panes.forEach((p) =>
                    p.classList.toggle("active", p.id === paneId),
                );
            }

            function openModal(id) {
                document.getElementById(id).classList.add("active");
            }
            function closeModal(id) {
                document.getElementById(id).classList.remove("active");
            }
            document.addEventListener("click", (e) => {
                if (
                    e.target.classList &&
                    e.target.classList.contains("overlay")
                )
                    e.target.classList.remove("active");
            });

            function openRoleModal(user) {
                document.getElementById("modalRoleName").textContent =
                    "Atur Role — " + user.name;
                document.getElementById("modalRoleCurrent").innerHTML = user
                    .roles.length
                    ? user.roles
                          .map((r) => `<span class="chip">${r} &times;</span>`)
                          .join("")
                    : '<span class="chip muted">Belum ada role</span>';
                document.getElementById("modalRoleOptions").innerHTML =
                    DATA.allRoles
                        .map((r) => {
                            const checked = user.roles.includes(r)
                                ? "checked"
                                : "";
                            return `<label><input type="checkbox" ${checked}> ${r}</label>`;
                        })
                        .join("");
                document.getElementById("modalRolePreview").innerHTML = user
                    .roles.length
                    ? user.roles
                          .map((r) => `<span class="chip">${r}</span>`)
                          .join("")
                    : '<span class="chip muted">—</span>';
                openModal("modalRole");
            }

            function openCardModal(card) {
                document.getElementById("mcTitle").textContent = card.title;
                document.getElementById("mcMeta").innerHTML =
                    `${card.value ? "Target: " + card.value + " · " : ""}Dari: ${card.source || "Personal"} ${contribTag(card.contribution)} ${selfTag(card)}`;
                const realisasiHTML =
                    card.type === "target"
                        ? `<h5>Input Realisasi Hari Ini</h5><div class="kcard-input" style="margin-top:0"><input type="number" value="${card.inputToday || ""}"><button>Simpan</button><span style="font-size:12px;color:var(--text-muted)">Hari ini</span></div>`
                        : `<h5>Status</h5><select><option>To Do</option><option>In Progress</option><option>Review</option><option selected>Done</option></select>`;
                document.getElementById("mcRealisasi").innerHTML =
                    realisasiHTML;
                document.getElementById("mcLogs").innerHTML = [
                    { d: "27 Agu 2026", v: "5/5 tercapai" },
                    { d: "26 Agu 2026", v: "3/5" },
                    { d: "25 Agu 2026", v: "5/5 tercapai" },
                ]
                    .map(
                        (l) =>
                            `<div class="log-item"><span>${l.d}</span><span><strong>${l.v}</strong></span></div>`,
                    )
                    .join("");
                openModal("modalCard");
            }

            /* INIT */
            buildRoleNav();
            renderRole("superadmin");
        </script>
    </body>
</html>
