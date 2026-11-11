<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0b1217">
    <title>Sign in · Europe Sales</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{font-family:"DM Sans",sans-serif;color:#ecf2ef;background:#091116;font-synthesis:none}
        *{box-sizing:border-box}
        body{margin:0;min-height:100vh;background:#091116}
        button,input,select{font:inherit}
        .shell{min-height:100svh;max-width:1540px;margin:auto;padding:clamp(12px,2.4vw,36px);display:flex;align-items:center;justify-content:center}
        .topbar,.brand,.status-line,.login-heading,.field-label,.form-options,.overview-trigger,.feature-row,.modal-header,.node,.gateway-foot{display:flex;align-items:center}
        .topbar{justify-content:space-between}
        .brand{gap:12px;color:#eef6f2;text-decoration:none}
        .brand-mark{width:39px;height:39px;display:grid;place-items:center;border:1px solid rgba(112,223,181,.24);border-radius:12px;background:#142329;box-shadow:0 7px 20px rgba(0,0,0,.2)}
        .brand-mark img{width:29px;height:29px}
        .brand-name{font:800 13px Manrope,sans-serif;letter-spacing:.13em}
        .brand-sub{display:block;color:#80958f;font-size:10px;letter-spacing:.08em;margin-top:3px}
        .language{color:#c6d6ce;border:1px solid rgba(159,190,177,.18);border-radius:9px;padding:8px 30px 8px 11px;background:#111b20}
        .main{width:100%;min-height:min(850px,calc(100svh - 48px));display:grid;grid-template-columns:minmax(390px,.84fr) minmax(0,1.16fr);overflow:hidden;border:1px solid rgba(145,180,169,.16);border-radius:26px;background:#111b20;box-shadow:0 28px 90px rgba(0,0,0,.3)}
        .login-side{display:flex;flex-direction:column;padding:clamp(22px,3vw,40px);background:linear-gradient(150deg,rgba(17,27,32,.98),rgba(15,24,29,.98))}
        .intro{max-width:520px;margin:clamp(20px,3.8vh,34px) 0 18px}
        .eyebrow{display:inline-flex;align-items:center;gap:9px;padding:7px 11px;border:1px solid rgba(104,217,172,.17);border-radius:999px;background:rgba(38,110,86,.12);color:#9ce2c1;font-size:10px;letter-spacing:.12em;text-transform:uppercase}
        .eyebrow-dot,.live-dot{width:7px;height:7px;border-radius:50%;background:#55d49b;box-shadow:0 0 9px rgba(85,212,155,.45)}
        h1{font:700 clamp(35px,3.8vw,48px)/1.08 Manrope,sans-serif;letter-spacing:-.06em;margin:17px 0 11px;max-width:510px;color:#ecf2ef}
        h1 span{color:#83d9b2}
        .lead{max-width:490px;margin:0;color:#a4b4ad;font-size:13px;line-height:1.8}
        .status-line{gap:9px;margin-top:19px;color:#a4b4ad;font-size:11px}
        .status-line strong{color:#d7e9df;font-weight:700}
        .status-separator{width:3px;height:3px;border-radius:50%;background:#60766e;margin:0 3px}
        .overview-trigger{gap:12px;margin-top:19px;padding:10px 12px;border:1px solid rgba(112,223,181,.22);border-radius:11px;background:rgba(21,29,34,.84);color:#bcebd2;font-size:11px;font-weight:700;cursor:pointer;box-shadow:0 7px 19px rgba(0,0,0,.14);transition:color .2s,transform .2s,border-color .2s,background .2s}
        .overview-trigger:hover{color:#fff;transform:translateY(-2px);border-color:rgba(112,223,181,.42);background:rgba(28,40,43,.96)}
        .overview-label{display:flex;flex-direction:column;align-items:flex-start;gap:3px}
        .overview-label small{color:#a1b9ad;font-size:9px;font-weight:400}
        .spark{width:32px;height:32px;display:grid;place-items:center;border:1px solid rgba(112,223,181,.2);border-radius:9px;background:rgba(42,123,91,.15);font-size:14px}
        .login-card{padding:clamp(18px,2vw,23px);border:1px solid rgba(145,180,169,.15);border-radius:15px;background:rgba(21,29,34,.78);box-shadow:0 12px 32px rgba(0,0,0,.15)}
        .login-heading{justify-content:space-between;margin-bottom:13px}
        .login-heading h2{margin:0;font:700 20px Manrope,sans-serif;letter-spacing:-.04em;color:#ecf2ef}
        .login-heading p{margin:5px 0 0;color:#a4b4ad;font-size:11px}
        .secure-icon{width:35px;height:35px;display:grid;place-items:center;border-radius:10px;background:rgba(83,190,147,.1);color:#88ddb4}
        .field{margin-bottom:10px}
        .field-label{justify-content:space-between;margin-bottom:6px;color:#cbd9d3;font-size:11px;font-weight:700}
        .field input{width:100%;height:38px;padding:0 12px;border:1px solid rgba(145,180,169,.2);border-radius:8px;outline:none;background:rgba(7,14,18,.62);color:#ecf2ef;font-size:12px;transition:border-color .2s,box-shadow .2s}
        .field input:focus{border-color:#5bcba5;box-shadow:0 0 0 3px rgba(50,184,141,.16)}
        .field input::placeholder{color:#83968e}
        .form-options{justify-content:space-between;margin:3px 0 11px;color:#a4b4ad;font-size:10px}
        .auth-note{display:flex;gap:7px;align-items:flex-start;color:#a4b4ad;font-size:9px;line-height:1.5;margin:0 0 9px}
        .auth-note strong{color:#c0d8cc}
        .submit{width:100%;height:43px;display:flex;justify-content:center;align-items:center;gap:9px;border:0;border-radius:8px;background:linear-gradient(110deg,#1d9e7b,#247c85);color:white;font-weight:700;font-size:12px;cursor:pointer;box-shadow:0 7px 17px rgba(38,158,113,.16);transition:transform .2s,filter .2s,box-shadow .2s}
        .submit:hover{transform:translateY(-2px);filter:brightness(1.08);box-shadow:0 10px 23px rgba(38,158,113,.23)}
        .quick-access{width:100%;margin-top:9px;padding:10px;border:1px solid rgba(145,180,169,.2);border-radius:8px;background:transparent;color:#b8c8c0;font-size:10px;font-weight:600;cursor:pointer;transition:border-color .2s,color .2s,background .2s}
        .quick-access:hover{border-color:rgba(94,205,158,.38);color:#e1f4e9;background:rgba(59,139,102,.08)}
        .error{margin:-5px 0 12px;color:#ff9e9e;font-size:10px}
        .footer{display:flex;justify-content:space-between;gap:8px;margin-top:auto;padding-top:14px;color:#879a91;font-size:9px}
        .visual-side{position:relative;min-height:100%;overflow:hidden;background:linear-gradient(180deg,rgba(9,17,22,.12) 35%,rgba(9,17,22,.58)),url('{{ asset('images/europe-flag-landscape.png') }}') center 42%/cover no-repeat,#183d59}
        .visual-side::before{position:absolute;inset:0;background:linear-gradient(90deg,rgba(9,17,22,.22),transparent 34%),linear-gradient(180deg,rgba(9,17,22,.05),rgba(9,17,22,.3));content:""}
        .visual-caption{position:absolute;right:clamp(20px,4vw,48px);bottom:clamp(20px,4vw,48px);left:clamp(20px,4vw,48px);padding:18px 20px;border:1px solid rgba(255,255,255,.25);border-radius:15px;background:rgba(9,17,22,.46);color:#fff;backdrop-filter:blur(9px)}
        .visual-caption span{display:block;color:#f5dc82;font-size:9px;letter-spacing:.16em;text-transform:uppercase}
        .visual-caption strong{display:block;margin-top:7px;font:700 clamp(19px,2.2vw,28px)/1.2 Manrope,sans-serif}
        .visual-caption small{display:block;margin-top:6px;color:rgba(255,255,255,.82);font-size:10px}
        .modal-backdrop{position:fixed;inset:0;display:grid;place-items:center;padding:20px;background:rgba(3,8,11,.72);backdrop-filter:blur(9px);opacity:0;visibility:hidden;transition:opacity .22s,visibility .22s;z-index:10}
        .modal-backdrop.open{opacity:1;visibility:visible}
        .modal{width:min(800px,100%);max-height:88vh;overflow-y:auto;padding:clamp(21px,4vw,34px);border:1px solid rgba(160,198,177,.2);border-radius:20px;background:linear-gradient(145deg,#1b2b2d,#101a20);box-shadow:0 32px 100px #0009;transform:translateY(12px) scale(.99);transition:transform .24s}
        .open .modal{transform:translateY(0) scale(1)}
        .modal-header{justify-content:space-between;align-items:flex-start}
        .modal-header h2{margin:0;font:700 21px Manrope,sans-serif}
        .modal-header p{margin:7px 0 0;color:#93a69e;font-size:12px;line-height:1.6}
        .close{width:34px;height:34px;border:1px solid rgba(160,198,177,.15);border-radius:9px;background:transparent;color:#c2d5cb;cursor:pointer}
        .feature-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin:22px 0}
        .feature-card{min-height:124px;padding:15px;border:1px solid rgba(143,179,161,.14);border-radius:13px;background:rgba(6,15,18,.3)}
        .feature-title{display:flex;align-items:center;gap:9px;color:#e3f2e9;font-size:12px;font-weight:700}
        .feature-icon{width:29px;height:29px;display:grid;place-items:center;flex:0 0 auto;border:1px solid rgba(112,223,181,.2);border-radius:9px;background:rgba(42,123,91,.14);color:#83dcb1;font-size:14px}
        .feature-card p{margin:10px 0 0;color:#9eb0a6;font-size:11px;line-height:1.65}
        .feature-row{justify-content:space-between;gap:18px;padding:16px;border:1px solid rgba(110,184,147,.15);border-radius:13px;background:rgba(55,123,91,.08)}
        .feature-row p{margin:0;color:#b1c2b9;font-size:11px;line-height:1.6}
        .feature-row strong{display:block;color:#e3f2e9;font-size:12px}
        .toggle{flex:0 0 auto;padding:8px 11px;border:1px solid rgba(111,208,158,.28);border-radius:8px;background:rgba(59,156,111,.13);color:#c6efd5;font-size:10px;font-weight:700;cursor:pointer}
        .rate-preview{margin-top:14px;color:#869a91;font-size:11px}
        .rate-preview output{color:#a7e4c2;font-weight:700}
        @media(max-width:860px){.main{grid-template-columns:minmax(345px,.92fr) minmax(0,1.08fr)}.login-side{padding:clamp(22px,3.5vw,36px)}.intro{margin-top:35px}h1{font-size:clamp(35px,5vw,48px)}}
        @media(max-width:680px){.shell{padding:10px}.main{grid-template-columns:1fr;min-height:0;border-radius:20px}.login-side{padding:20px;min-height:calc(100svh - 20px)}.intro{margin:38px 0 20px}h1{font-size:clamp(38px,11vw,52px);margin-top:17px}.lead{max-width:560px}.login-card{max-width:510px;width:100%;margin:0}.footer{gap:10px;flex-wrap:wrap}.visual-side{min-height:310px;order:2}.feature-grid{grid-template-columns:1fr}.feature-card{min-height:0}}
        @media(prefers-reduced-motion:reduce){*,*::before,*::after{scroll-behavior:auto!important;animation-duration:.01ms!important;transition-duration:.01ms!important}}
    </style>
</head>
<body>
<div class="shell">
    <main class="main">
        <section class="login-side">
            <header class="topbar">
                <a class="brand" href="{{ route('login') }}" aria-label="Europe Sales home">
                    <span class="brand-mark"><img src="{{ asset('images/eu-flag.svg') }}" alt="Bendera Uni Eropa"></span>
                    <span><span class="brand-name">EUROPE SALES</span><span class="brand-sub">INTELLIGENCE PLATFORM</span></span>
                </a>
                <select class="language" id="language" aria-label="Select language">
                    <option value="id" selected>ID · Indonesia</option>
                    <option value="en">EN · English</option>
                    <option value="de">DE · Deutsch</option>
                    <option value="fr">FR · Français</option>
                </select>
            </header>

            <section class="intro">
                <div class="eyebrow"><span class="eyebrow-dot"></span><span data-copy="eyebrow">RITEL EROPA, DALAM SATU PANDANGAN</span></div>
                <h1 data-copy="headline">Pahami <span>setiap pasar.</span></h1>
                <p class="lead" data-copy="lead">Satu ruang kerja terpadu untuk kinerja penjualan Eropa—dari transaksi mentah hingga wawasan yang siap ditindaklanjuti.</p>
                <div class="status-line"><span class="live-dot"></span><strong data-copy="pipeline">Ruang kerja pipeline siap digunakan</strong><span class="status-separator"></span><span data-copy="rate">Pratinjau kurs · EUR / USD</span></div>
                <button type="button" class="overview-trigger" id="open-overview">
                    <span class="spark" aria-hidden="true">✦</span>
                    <span class="overview-label"><span data-copy="overview">Lihat deskripsi fitur website</span><small data-copy="overview-hint">Klik untuk melihat fitur yang tersedia di dalam website</small></span>
                    <span aria-hidden="true">↗</span>
                </button>
            </section>

            <section class="login-card" aria-labelledby="login-title">
                <div class="login-heading">
                    <div><h2 id="login-title" data-copy="welcome">Selamat datang kembali</h2><p data-copy="subtitle">Masuk ke ruang kerja retail Anda.</p></div>
                    <span class="secure-icon" aria-hidden="true">⌑</span>
                </div>
                <form method="POST" action="{{ route('login.authenticate') }}" id="login-form">
                    @csrf
                    <input type="hidden" name="destination" id="destination" value="{{ session('destination', 'gateway') }}">
                    <div class="field">
                        <label class="field-label" for="username"><span data-copy="username">Nama pengguna</span></label>
                        <input id="username" name="username" type="text" autocomplete="username" placeholder="Masukkan nama pengguna" value="{{ old('username') }}" required autofocus>
                    </div>
                    <div class="field">
                        <label class="field-label" for="password"><span data-copy="password">Kata sandi</span></label>
                        <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan kata sandi" required>
                    </div>
                    @if ($errors->any())
                        <p class="error" role="alert">{{ $errors->first() }}</p>
                    @endif
                    <p class="auth-note"><span aria-hidden="true">◉</span><span><strong data-copy="authorized">Akses demo · admin / admin</strong> · <span data-copy="authorized-note">Akses admin diperlukan untuk analitik dan alat ETL.</span></span></p>
                    <button class="submit" type="submit" data-copy="sign-in">Masuk ke ruang kerja →</button>
                    <button class="quick-access" type="submit" id="quick-etl" data-copy="quick-etl">↗ &nbsp; Konsol Pipeline ETL · akses langsung</button>
                </form>
            </section>
            <footer class="footer"><span>© {{ date('Y') }} Europe Sales Intelligence</span><span data-copy="footer">Akses aman · Wilayah Eropa</span></footer>
        </section>
        <aside class="visual-side" aria-label="Pemandangan bendera Uni Eropa di Eropa">
            <div class="visual-caption">
                <span data-copy="visual-eyebrow">Satu pasar · banyak peluang</span>
                <strong data-copy="visual-title">Wawasan retail untuk seluruh Eropa.</strong>
                <small data-copy="visual-note">Penjualan · profitabilitas · alur data terpadu</small>
            </div>
        </aside>
    </main>
</div>

<div class="modal-backdrop" id="overview-modal" role="dialog" aria-modal="true" aria-labelledby="overview-title" aria-hidden="true">
    <section class="modal">
        <div class="modal-header">
            <div><h2 id="overview-title" data-copy="modal-title">Fitur yang tersedia di website</h2><p data-copy="modal-subtitle">Ringkasan kemampuan yang bisa Anda gunakan setelah masuk ke workspace penjualan retail Eropa.</p></div>
            <button class="close" type="button" id="close-overview" aria-label="Close overview">✕</button>
        </div>
        <div class="feature-grid">
            <article class="feature-card"><div class="feature-title"><span class="feature-icon">▦</span><span data-copy="dashboard-title">Dashboard penjualan</span></div><p data-copy="dashboard-note">Lihat total revenue, biaya, profit, margin, dan unit terjual. Bandingkan tren bulanan, produk, serta kanal penjualan dalam grafik.</p></article>
            <article class="feature-card"><div class="feature-title"><span class="feature-icon">⌕</span><span data-copy="filter-title">Filter &amp; performa pasar</span></div><p data-copy="filter-note">Saring data berdasarkan negara, jenis produk, kanal penjualan, dan rentang tanggal untuk melihat ringkasan yang relevan.</p></article>
            <article class="feature-card"><div class="feature-title"><span class="feature-icon">↳</span><span data-copy="drilldown-title">Analisis Drill-Down</span></div><p data-copy="drilldown-note">Telusuri hasil dari negara ke kanal, jenis produk, lalu detail transaksi untuk menemukan sumber performa.</p></article>
            <article class="feature-card"><div class="feature-title"><span class="feature-icon">◈</span><span data-copy="whatif-title">Simulasi What-If</span></div><p data-copy="whatif-note">Uji perubahan harga, jumlah unit, atau biaya dan lihat dampaknya terhadap revenue, profit, serta margin.</p></article>
            <article class="feature-card"><div class="feature-title"><span class="feature-icon">⌘</span><span data-copy="etl-title">ETL Management</span></div><p data-copy="etl-note">Unggah CSV/TSV, validasi 14 kolom data, tanggal, angka, serta ID duplikat; lalu muat dengan upsert atau ganti data setelah file lolos validasi.</p></article>
            <article class="feature-card"><div class="feature-title"><span class="feature-icon">€</span><span data-copy="currency-title">Pratinjau konversi kurs</span></div><p data-copy="currency-note">Coba simulasi nilai EUR/USD untuk memahami konversi. Kurs di halaman ini hanya ilustrasi, bukan feed kurs live.</p></article>
        </div>
        <div class="feature-row">
            <p><strong data-copy="before-after">Contoh sebelum dan sesudah ETL</strong><span id="etl-preview">Baris mentah · format angka beragam · ID belum divalidasi</span></p>
            <button class="toggle" type="button" id="toggle-etl">Pratinjau transformasi</button>
        </div>
        <p class="rate-preview"><span data-copy="simulation">Simulasi kurs</span>: <input id="fx-amount" type="number" value="100" min="0" style="width:76px;padding:5px 7px;border:1px solid #355449;border-radius:6px;background:#0d191b;color:#d9eee2"> USD × sample 0.92 = <output id="fx-result">€92.00</output> <span data-copy="sample-rate">(kurs ilustrasi)</span></p>
    </section>
</div>

<script>
const modal = document.getElementById('overview-modal');
const openButton = document.getElementById('open-overview');
const closeButton = document.getElementById('close-overview');
const preview = document.getElementById('etl-preview');
const toggle = document.getElementById('toggle-etl');
const translations = {
    id: {eyebrow:'RITEL EROPA, DALAM SATU PANDANGAN',headline:'Pahami <span>setiap pasar.</span>',lead:'Satu ruang kerja terpadu untuk kinerja penjualan Eropa—dari transaksi mentah hingga wawasan yang siap ditindaklanjuti.',pipeline:'Ruang kerja pipeline siap digunakan',rate:'Pratinjau kurs · EUR / USD',overview:'Lihat deskripsi fitur website', 'overview-hint':'Klik untuk melihat fitur yang tersedia di dalam website',welcome:'Selamat datang kembali',subtitle:'Masuk ke ruang kerja retail Anda.',username:'Nama pengguna',password:'Kata sandi',authorized:'Akses demo · admin / admin','authorized-note':'Akses admin diperlukan untuk analitik dan alat ETL.','sign-in':'Masuk ke ruang kerja →','quick-etl':'↗  Konsol Pipeline ETL · akses langsung',footer:'Akses aman · Wilayah Eropa','modal-title':'Fitur yang tersedia di website','modal-subtitle':'Ringkasan kemampuan yang bisa Anda gunakan setelah masuk ke workspace penjualan retail Eropa.','dashboard-title':'Dashboard penjualan','dashboard-note':'Lihat total revenue, biaya, profit, margin, dan unit terjual. Bandingkan tren bulanan, produk, serta kanal penjualan dalam grafik.','filter-title':'Filter & performa pasar','filter-note':'Saring data berdasarkan negara, jenis produk, kanal penjualan, dan rentang tanggal untuk melihat ringkasan yang relevan.','drilldown-title':'Analisis Drill-Down','drilldown-note':'Telusuri hasil dari negara ke kanal, jenis produk, lalu detail transaksi untuk menemukan sumber performa.','whatif-title':'Simulasi What-If','whatif-note':'Uji perubahan harga, jumlah unit, atau biaya dan lihat dampaknya terhadap revenue, profit, serta margin.','etl-title':'ETL Management','etl-note':'Unggah CSV/TSV, validasi 14 kolom data, tanggal, angka, serta ID duplikat; lalu muat dengan upsert atau ganti data setelah file lolos validasi.','currency-title':'Pratinjau konversi kurs','currency-note':'Coba simulasi nilai EUR/USD untuk memahami konversi. Kurs di halaman ini hanya ilustrasi, bukan feed kurs live.','before-after':'Contoh sebelum dan sesudah ETL','raw-preview':'Baris mentah · format angka beragam · ID belum divalidasi','clean-preview':'Baris tervalidasi · angka ternormalisasi · siap dimuat','preview-transform':'Pratinjau transformasi','show-raw':'Tampilkan data mentah','simulation':'Simulasi kurs','sample-rate':'(kurs ilustrasi)','username-placeholder':'Masukkan nama pengguna','password-placeholder':'Masukkan kata sandi'},
    en: {eyebrow:'EUROPEAN RETAIL, IN FOCUS',headline:'A clearer view of <span>every market.</span>',lead:'One connected workspace for European sales performance — from raw transaction data to decisions you can act on.',pipeline:'Pipeline workspace ready',rate:'FX preview · EUR / USD',overview:'View website feature guide','overview-hint':'Click to explore the tools available in this workspace',welcome:'Welcome back',subtitle:'Sign in to your retail workspace.',username:'Username',password:'Password',authorized:'Demo access · admin / admin','authorized-note':'Admin access is required for analytics and ETL tools.','sign-in':'Sign in to workspace →','quick-etl':'↗  ETL Pipeline Console · direct access',footer:'Secure access · Europe region','modal-title':'Features available in this website','modal-subtitle':'A practical guide to the tools available in the European retail sales workspace.','dashboard-title':'Sales dashboard','dashboard-note':'View revenue, costs, profit, margin, and units sold. Compare monthly, product, and sales-channel trends in charts.','filter-title':'Filters & market performance','filter-note':'Filter by country, product type, sales channel, and date range to focus on the data you need.','drilldown-title':'Drill-down analysis','drilldown-note':'Follow results from country to channel, product type, and individual transactions.','whatif-title':'What-if simulation','whatif-note':'Test changes to price, units, or costs and see the impact on revenue, profit, and margin.','etl-title':'ETL management','etl-note':'Upload CSV/TSV, validate the 14-column schema, dates, numeric values, and duplicate IDs, then upsert or replace records after validation.','currency-title':'Currency conversion preview','currency-note':'Try an illustrative EUR/USD conversion. This is a sample simulation, not a live exchange-rate feed.','before-after':'Example before and after ETL','raw-preview':'Raw rows · mixed number formats · unvalidated IDs','clean-preview':'Validated rows · normalized amounts · ready to load','preview-transform':'Preview transformation','show-raw':'Show raw data','simulation':'Currency simulation','sample-rate':'(illustrative rate)','username-placeholder':'Enter your username','password-placeholder':'Enter your password'},
    de: {eyebrow:'EUROPÄISCHER HANDEL IM BLICK',headline:'Klarer Überblick über <span>jeden Markt.</span>',lead:'Ein vernetzter Arbeitsbereich für den europäischen Einzelhandel – von Rohdaten bis zu fundierten Entscheidungen.',pipeline:'Pipeline-Arbeitsbereich bereit',rate:'Wechselkursvorschau · EUR / USD',overview:'Funktionsübersicht anzeigen','overview-hint':'Klicken Sie hier, um die verfügbaren Funktionen zu entdecken',welcome:'Willkommen zurück',subtitle:'Melden Sie sich in Ihrem Arbeitsbereich an.',username:'Benutzername',password:'Passwort',authorized:'Demo-Zugang · admin / admin','authorized-note':'Für Analyse- und ETL-Tools ist Admin-Zugriff erforderlich.','sign-in':'Im Arbeitsbereich anmelden →','quick-etl':'↗  ETL-Pipeline-Konsole · Direktzugriff',footer:'Sicherer Zugriff · Europa','modal-title':'Funktionen dieser Website','modal-subtitle':'Übersicht der Funktionen im europäischen Einzelhandels-Arbeitsbereich.','dashboard-title':'Verkaufsdashboard','dashboard-note':'Umsatz, Kosten, Gewinn, Marge und verkaufte Einheiten sowie monatliche, produkt- und kanalbezogene Trends ansehen.','filter-title':'Filter & Marktleistung','filter-note':'Nach Land, Produkttyp, Vertriebskanal und Zeitraum filtern.','drilldown-title':'Drill-down-Analyse','drilldown-note':'Ergebnisse vom Land über Kanal und Produkttyp bis zu einzelnen Transaktionen verfolgen.','whatif-title':'What-if-Simulation','whatif-note':'Preis-, Mengen- oder Kostenänderungen testen und Auswirkungen auf Umsatz, Gewinn und Marge sehen.','etl-title':'ETL-Verwaltung','etl-note':'CSV/TSV hochladen, 14 Spalten, Datums- und Zahlenformate sowie doppelte IDs prüfen und Daten danach ergänzen oder ersetzen.','currency-title':'Währungsvorschau','currency-note':'Beispielhafte EUR/USD-Umrechnung testen. Der Kurs ist illustrativ und nicht live.','before-after':'Beispiel vor und nach ETL','raw-preview':'Rohdaten · gemischte Zahlenformate · ungeprüfte IDs','clean-preview':'Geprüfte Datensätze · normalisierte Beträge · bereit zum Laden','preview-transform':'Transformation ansehen','show-raw':'Rohdaten anzeigen','simulation':'Währungssimulation','sample-rate':'(Beispielkurs)','username-placeholder':'Benutzernamen eingeben','password-placeholder':'Passwort eingeben'},
    fr: {eyebrow:'LE COMMERCE EUROPÉEN EN UN COUP D’ŒIL',headline:'Une vision plus claire de <span>chaque marché.</span>',lead:'Un espace connecté pour les ventes européennes, des données brutes aux décisions concrètes.',pipeline:'Espace pipeline prêt',rate:'Aperçu FX · EUR / USD',overview:'Voir les fonctionnalités','overview-hint':'Cliquez pour découvrir les outils disponibles',welcome:'Ravi de vous revoir',subtitle:'Connectez-vous à votre espace de vente.',username:'Nom d’utilisateur',password:'Mot de passe',authorized:'Accès démo · admin / admin','authorized-note':'Un accès admin est requis pour l’analyse et les outils ETL.','sign-in':'Se connecter à l’espace →','quick-etl':'↗  Console pipeline ETL · accès direct',footer:'Accès sécurisé · Europe','modal-title':'Fonctionnalités du site','modal-subtitle':'Guide des outils de cet espace de vente dédié au commerce européen.','dashboard-title':'Tableau de bord des ventes','dashboard-note':'Consultez chiffre d’affaires, coûts, bénéfices, marge et unités vendues, ainsi que les tendances mensuelles, produits et canaux.','filter-title':'Filtres et performance des marchés','filter-note':'Filtrez par pays, type de produit, canal de vente et période.','drilldown-title':'Analyse détaillée','drilldown-note':'Suivez les résultats du pays au canal, au produit, puis aux transactions.','whatif-title':'Simulation hypothétique','whatif-note':'Testez les variations de prix, de volumes ou de coûts et observez leurs effets sur les résultats.','etl-title':'Gestion ETL','etl-note':'Importez des fichiers CSV/TSV, validez les 14 colonnes, dates, nombres et identifiants en double, puis ajoutez ou remplacez les données après validation.','currency-title':'Aperçu de conversion','currency-note':'Testez une conversion EUR/USD indicative. Le taux est simulé, pas actualisé en direct.','before-after':'Exemple avant et après ETL','raw-preview':'Données brutes · formats variés · identifiants non vérifiés','clean-preview':'Données validées · montants normalisés · prêtes à charger','preview-transform':'Aperçu de la transformation','show-raw':'Afficher les données brutes','simulation':'Simulation de devise','sample-rate':'(taux indicatif)','username-placeholder':'Saisir votre nom','password-placeholder':'Saisir votre mot de passe'}
};
Object.assign(translations.id, {'visual-eyebrow':'Satu pasar · banyak peluang','visual-title':'Wawasan retail untuk seluruh Eropa.','visual-note':'Penjualan · profitabilitas · alur data terpadu'});
Object.assign(translations.en, {'visual-eyebrow':'One market · many opportunities','visual-title':'Retail insights across Europe.','visual-note':'Sales · profitability · connected data'});
Object.assign(translations.de, {'visual-eyebrow':'Ein Markt · viele Chancen','visual-title':'Einblicke in den europäischen Einzelhandel.','visual-note':'Umsatz · Rentabilität · vernetzte Daten'});
Object.assign(translations.fr, {'visual-eyebrow':'Un marché · de nombreuses opportunités','visual-title':'Des analyses retail pour toute l’Europe.','visual-note':'Ventes · rentabilité · données connectées'});
const applyLanguage = language => {
    document.documentElement.lang = language;
    document.querySelectorAll('[data-copy]').forEach(element => {
        const translation = translations[language][element.dataset.copy];
        if (translation) element.innerHTML = translation;
    });
    document.getElementById('username').placeholder = translations[language]['username-placeholder'];
    document.getElementById('password').placeholder = translations[language]['password-placeholder'];
    preview.textContent = toggle.dataset.transformed === 'true' ? translations[language]['clean-preview'] : translations[language]['raw-preview'];
    toggle.textContent = translations[language][toggle.dataset.transformed === 'true' ? 'show-raw' : 'preview-transform'];
};
document.getElementById('language').addEventListener('change', event => applyLanguage(event.target.value));
applyLanguage(document.getElementById('language').value);
openButton.addEventListener('click', () => { modal.classList.add('open'); modal.setAttribute('aria-hidden', 'false'); closeButton.focus(); });
const closeModal = () => { modal.classList.remove('open'); modal.setAttribute('aria-hidden', 'true'); openButton.focus(); };
closeButton.addEventListener('click', closeModal);
modal.addEventListener('click', event => { if (event.target === modal) closeModal(); });
document.addEventListener('keydown', event => { if (event.key === 'Escape' && modal.classList.contains('open')) closeModal(); });
toggle.addEventListener('click', () => {
    const transformed = toggle.dataset.transformed !== 'true';
    toggle.dataset.transformed = String(transformed);
    const language = document.getElementById('language').value;
    preview.textContent = translations[language][transformed ? 'clean-preview' : 'raw-preview'];
    toggle.textContent = translations[language][transformed ? 'show-raw' : 'preview-transform'];
});
document.getElementById('fx-amount').addEventListener('input', event => {
    const amount = Number(event.target.value);
    document.getElementById('fx-result').textContent = `€${(amount * 0.92).toFixed(2)}`;
});
document.getElementById('quick-etl').addEventListener('click', () => { document.getElementById('destination').value = 'etl'; });
</script>
</body>
</html>
