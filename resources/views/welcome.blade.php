<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AideChain — Coordination Humanitaire au Tchad</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css" crossorigin="">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        :root {
            --blue:      #1553D4;
            --blue-dk:   #0D3EA8;
            --blue-lt:   #EBF0FF;
            --black:     #101010;
            --gray:      #606060;
            --gray-lt:   #A0A0A0;
            --border:    #E5E7EB;
            --bg:        #FFFFFF;
            --bg-alt:    #F8F9FB;
            --sw:        220px;
        }

        body {
            font-family: 'Jost', sans-serif;
            background: var(--bg);
            color: var(--black);
            font-size: 16px;
            line-height: 1.65;
            overflow-x: hidden;
        }
        a { color: inherit; text-decoration: none; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-alt); }
        ::-webkit-scrollbar-thumb { background: var(--blue); border-radius: 3px; }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed; left: 0; top: 0;
            width: var(--sw); height: 100vh;
            border-right: 1px solid var(--border);
            background: var(--bg);
            z-index: 100;
            display: flex; flex-direction: column;
            padding: 48px 30px;
        }
        .logo {
            display: flex; align-items: center; gap: 9px;
        }
        .logo-dot {
            width: 10px; height: 10px; border-radius: 50%;
            background: var(--blue);
            box-shadow: 0 0 0 4px var(--blue-lt);
            flex-shrink: 0;
        }
        .logo-name {
            font-size: 15px; font-weight: 800; color: var(--black); letter-spacing: -.3px;
        }
        .snav {
            margin-top: 60px; flex: 1;
        }
        .snav ul {
            position: relative; padding-left: 24px; list-style: none;
        }
        .snav ul::before {
            content: ''; position: absolute;
            left: 0; top: 4px;
            width: 1px; height: calc(100% - 8px);
            background: var(--border);
        }
        .snav li { margin-bottom: 26px; }
        .snav a {
            font-size: 14px; font-weight: 500; color: var(--gray);
            transition: color .2s; position: relative;
        }
        .snav a::before {
            content: ''; position: absolute;
            left: -30px; top: 50%; transform: translateY(-50%);
            width: 8px; height: 8px; border-radius: 50%;
            border: 1.5px solid var(--border);
            background: var(--bg);
            transition: background .2s, border-color .2s;
        }
        .snav a:hover, .snav a.active { color: var(--blue); }
        .snav a:hover::before, .snav a.active::before {
            background: var(--blue); border-color: var(--blue);
        }
        .ssocial {
            display: flex; gap: 10px; margin-top: 32px;
        }
        .ssocial a {
            display: flex; align-items: center; justify-content: center;
            width: 34px; height: 34px;
            border-radius: 8px; border: 1px solid var(--border);
            color: var(--gray);
            transition: color .2s, border-color .2s, background .2s;
        }
        .ssocial a:hover { color: var(--blue); border-color: var(--blue); background: var(--blue-lt); }

        /* ── Mobile nav ── */
        .mnav {
            display: none; position: fixed;
            top: 0; left: 0; right: 0; height: 60px;
            background: var(--bg); border-bottom: 1px solid var(--border);
            z-index: 200; padding: 0 20px;
            align-items: center; justify-content: space-between;
        }
        .burger {
            display: flex; flex-direction: column; gap: 5px;
            background: none; border: none; cursor: pointer; padding: 4px;
        }
        .burger span {
            display: block; width: 22px; height: 2px;
            background: var(--black); border-radius: 1px;
            transition: transform .25s, opacity .25s;
        }
        .burger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .burger.open span:nth-child(2) { opacity: 0; }
        .burger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
        .mmenu {
            display: none; position: fixed;
            top: 60px; left: 0; right: 0;
            background: var(--bg); border-bottom: 1px solid var(--border);
            z-index: 190; padding: 16px 20px;
        }
        .mmenu.open { display: block; }
        .mmenu a {
            display: block; padding: 13px 0;
            font-weight: 500; color: var(--gray);
            border-bottom: 1px solid var(--border);
            transition: color .2s;
        }
        .mmenu a:last-child { border-bottom: none; }
        .mmenu a:hover { color: var(--blue); }

        /* ── Layout ── */
        .main { margin-left: var(--sw); }

        /* ── Section base ── */
        .sec {
            padding: 120px 80px;
            border-bottom: 1px solid var(--border);
        }
        .sec-inner { max-width: 1060px; width: 100%; }
        .sec-tag {
            display: inline-flex; align-items: center; gap: 10px;
            font-size: 11px; font-weight: 700;
            letter-spacing: 2.5px; text-transform: uppercase;
            color: var(--gray); margin-bottom: 18px;
        }
        .sec-tag::before {
            content: ''; width: 8px; height: 8px;
            border-radius: 50%; background: var(--blue); flex-shrink: 0;
        }
        h2 {
            font-size: clamp(32px, 3.5vw, 44px) !important;
            font-weight: 700 !important; line-height: 1.15 !important;
            letter-spacing: -1px !important;
        }

        /* ── Hero ── */
        #home {
            min-height: 100vh; display: flex; align-items: center;
            padding: 80px; border-bottom: 1px solid var(--border);
        }
        .hero-wrap {
            max-width: 1060px; width: 100%;
            display: grid; grid-template-columns: 1.1fr .9fr;
            gap: 80px; align-items: center;
        }
        .hero-tag {
            display: inline-flex; align-items: center; gap: 10px;
            font-size: 11px; font-weight: 700;
            letter-spacing: 2.5px; text-transform: uppercase;
            color: var(--gray); margin-bottom: 24px;
        }
        .hero-tag::before {
            content: ''; width: 8px; height: 8px;
            border-radius: 50%; background: var(--blue);
        }
        .hero-h1 {
            font-size: clamp(46px, 5.5vw, 70px) !important;
            font-weight: 900 !important; line-height: 1.05 !important;
            letter-spacing: -2.5px !important;
            color: var(--black); margin-bottom: 22px;
        }
        .hero-h1 span { color: var(--blue); }
        .hero-desc {
            font-size: 17px; color: var(--gray);
            line-height: 1.75; max-width: 460px;
            margin-bottom: 38px; font-weight: 400;
        }
        .hero-btns { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }

        .btn-p {
            display: inline-flex; align-items: center; gap: 10px;
            background: var(--blue); color: #fff;
            font-family: 'Jost', sans-serif; font-weight: 600; font-size: 15px;
            padding: 13px 28px; border-radius: 8px;
            box-shadow: 0 4px 20px rgba(21,83,212,.3);
            position: relative; overflow: hidden;
            transition: transform .2s, box-shadow .2s;
        }
        .btn-p::after {
            content: ''; position: absolute;
            top: 0; left: 100%; width: 100%; height: 100%;
            background: var(--blue-dk);
            transition: left .4s ease;
        }
        .btn-p:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(21,83,212,.4); }
        .btn-p:hover::after { left: 0; }
        .btn-p > * { position: relative; z-index: 1; }

        .btn-o {
            display: inline-flex; align-items: center; gap: 8px;
            color: var(--black); font-weight: 600; font-size: 15px;
            padding: 12px 24px; border-radius: 8px;
            border: 1.5px solid var(--border);
            transition: border-color .2s, color .2s;
        }
        .btn-o:hover { border-color: var(--blue); color: var(--blue); }

        /* Hero right */
        .hero-visual {
            display: flex; align-items: center; justify-content: center;
        }
        .hero-card {
            width: 100%; max-width: 360px;
            background: var(--bg-alt);
            border: 1px solid var(--border);
            border-radius: 20px; padding: 44px 36px;
            text-align: center;
        }
        .hero-card-live {
            display: inline-flex; align-items: center; gap: 6px;
            background: #FFF0E6; color: #C85000;
            font-size: 10px; font-weight: 700;
            letter-spacing: 2px; text-transform: uppercase;
            padding: 5px 12px; border-radius: 100px; margin-bottom: 24px;
        }
        .live-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: #C85000;
            animation: blink 1.6s ease-in-out infinite;
        }
        @keyframes blink {
            0%,100% { opacity:1; } 50% { opacity:.3; }
        }
        .hero-big {
            font-size: clamp(72px, 11vw, 120px);
            font-weight: 900; line-height: 1;
            color: var(--blue); letter-spacing: -4px;
        }
        .hero-card-txt {
            font-size: 14px; color: var(--gray);
            margin-top: 10px; line-height: 1.6;
        }
        .hero-card-src {
            font-size: 10px; color: var(--gray-lt);
            margin-top: 20px; padding-top: 20px;
            border-top: 1px solid var(--border);
            letter-spacing: 1.5px; text-transform: uppercase;
        }

        /* ── Problème ── */
        .prob-wrap {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 80px; align-items: center;
        }
        .prob-right {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 1px; background: var(--border);
            border: 1px solid var(--border); border-radius: 16px; overflow: hidden;
        }
        .sblock {
            background: var(--bg); padding: 28px 24px;
        }
        .sblock:first-child { background: var(--blue); }
        .sblock:first-child .snum,
        .sblock:first-child .slbl { color: #fff; }
        .sblock:first-child .slbl { color: rgba(255,255,255,.7); }
        .snum {
            font-size: 40px; font-weight: 800;
            letter-spacing: -1.5px; line-height: 1; margin-bottom: 6px;
        }
        .slbl { font-size: 12px; color: var(--gray); line-height: 1.5; }

        /* ── Solution ── */
        .feat-grid {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 1px; background: var(--border);
            border: 1px solid var(--border); border-radius: 16px;
            overflow: hidden; margin-top: 56px;
        }
        .feat {
            background: var(--bg); padding: 32px 28px;
            transition: background .2s;
        }
        .feat:hover { background: var(--blue-lt); }
        .feat-ico {
            width: 46px; height: 46px; border-radius: 12px;
            background: var(--blue-lt); color: var(--blue);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 18px;
        }
        .feat-title { font-size: 17px; font-weight: 700; margin-bottom: 10px; }
        .feat-text { font-size: 14px; color: var(--gray); line-height: 1.7; }

        /* ── Équipe ── */
        .team-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 20px; margin-top: 56px;
        }
        .tcard {
            border: 1px solid var(--border); border-radius: 14px;
            overflow: hidden;
            transition: transform .25s, box-shadow .25s;
        }
        .tcard:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,.07); }
        .tphoto {
            width: 100%; aspect-ratio: 1; object-fit: cover; display: block;
        }
        .tfall {
            width: 100%; aspect-ratio: 1;
            background: var(--blue-lt); color: var(--blue);
            display: flex; align-items: center; justify-content: center;
            font-size: 30px; font-weight: 800; display: none;
        }
        .tinfo { padding: 18px 16px; }
        .tname { font-size: 14px; font-weight: 700; margin-bottom: 3px; }
        .trole { font-size: 12px; color: var(--gray); margin-bottom: 10px; }
        .tlinks { display: flex; gap: 6px; flex-wrap: wrap; }
        .tlink {
            font-size: 11px; font-weight: 600; color: var(--blue);
            transition: opacity .15s;
        }
        .tlink:hover { opacity: .7; }
        .tlink + .tlink::before { content: '·'; margin-right: 6px; color: var(--border); }

        .mentor {
            margin-top: 20px;
            border: 1.5px solid var(--blue);
            border-radius: 14px; padding: 24px 28px;
            background: var(--blue-lt);
            display: flex; align-items: center; gap: 22px;
        }
        .mphoto {
            width: 200px; height: 200px; border-radius: 50%;
            object-fit: cover; flex-shrink: 0;
        }
        .mfall {
            width: 72px; height: 72px; border-radius: 50%;
            background: var(--blue); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 800; flex-shrink: 0; display: none;
        }
        .mbadge {
            display: inline-flex;
            background: var(--blue); color: #fff;
            font-size: 12px; font-weight: 700;
            letter-spacing: 2px; text-transform: uppercase;
            padding: 3px 10px; border-radius: 100px; margin-bottom: 5px;
        }
        .mname { font-size: 30px; font-weight: 800; margin-bottom: 2px; }
        .mtitle { font-size: 18px; color: var(--gray); margin-bottom: 10px; }
        .mlinks { display: flex; gap: 14px; }
        .mlink {
            font-size: 15px; font-weight: 600; color: var(--blue);
            transition: opacity .15s;
        }
        .mlink:hover { opacity: .7; }

        /* ── Hackathon ── */
        .hack-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 32px; margin-top: 56px;
        }
        .hcard {
            border: 1px solid var(--border); border-radius: 16px;
            padding: 32px; background: var(--bg-alt);
        }
        .hcard.blue {
            background: var(--blue); border-color: var(--blue); color: #fff;
        }
        .hcard.blue .hlbl { color: rgba(255,255,255,.65); }
        .hcard.blue .htxt { color: rgba(255,255,255,.8); }
        .hlbl {
            font-size: 10px; font-weight: 700;
            letter-spacing: 2px; text-transform: uppercase;
            color: var(--gray); margin-bottom: 10px;
        }
        .htitle { font-size: 26px; font-weight: 800; margin-bottom: 10px; letter-spacing: -.5px; }
        .htxt { font-size: 14px; line-height: 1.75; color: var(--gray); }

        .hack-steps {
            list-style: none; margin-top: 40px;
        }
        .hstep {
            display: flex; align-items: flex-start; gap: 20px;
            padding: 22px 0; border-bottom: 1px solid var(--border);
        }
        .hstep:last-child { border-bottom: none; }
        .hnum {
            font-size: 12px; font-weight: 700; color: var(--blue);
            letter-spacing: 1px; min-width: 28px; margin-top: 2px;
        }
        .hstep-title { font-size: 15px; font-weight: 700; margin-bottom: 4px; }
        .hstep-txt { font-size: 14px; color: var(--gray); line-height: 1.65; }

        /* ── Footer ── */
        footer {
            padding: 40px 80px;
            display: flex; align-items: center;
            justify-content: space-between;
            flex-wrap: wrap; gap: 14px;
        }
        .foot-brand { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 800; }
        .foot-txt { font-size: 13px; color: var(--gray); }
        .foot-links { display: flex; gap: 20px; }
        .foot-link {
            font-size: 13px; color: var(--gray);
            transition: color .2s;
        }
        .foot-link:hover { color: var(--blue); }

        /* ── Carte ── */
        .map-wrap {
            display: grid;
            grid-template-columns: 1fr 240px;
            gap: 32px;
            margin-top: 48px;
            align-items: start;
        }
        #chad-map {
            height: 540px;
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
            position: relative;
            z-index: 0;
        }
        .map-panel { display: flex; flex-direction: column; gap: 14px; }
        .map-stat-card {
            background: var(--bg-alt);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px 22px;
        }
        .map-stat-num {
            font-size: 36px;
            font-weight: 900;
            color: var(--blue);
            letter-spacing: -1.5px;
            line-height: 1;
        }
        .map-stat-lbl { font-size: 12px; color: var(--gray); margin-top: 4px; line-height: 1.4; }
        .map-note { font-size: 11px; color: var(--gray-lt); margin-top: 14px; line-height: 1.7; }
        /* Leaflet info control */
        .leaflet-info-chad {
            padding: 9px 13px;
            font-family: 'Jost', sans-serif;
            font-size: 13px;
            background: rgba(255,255,255,0.94);
            box-shadow: 0 2px 12px rgba(0,0,0,.12);
            border-radius: 10px;
            border: 1px solid var(--border);
            min-width: 130px;
            pointer-events: none;
        }
        /* Leaflet legend control */
        .leaflet-legend-chad {
            padding: 10px 13px;
            font-family: 'Jost', sans-serif;
            font-size: 12px;
            line-height: 22px;
            background: rgba(255,255,255,0.94);
            box-shadow: 0 2px 12px rgba(0,0,0,.12);
            border-radius: 10px;
            border: 1px solid var(--border);
        }
        .leaflet-legend-chad i {
            display: inline-block;
            width: 14px; height: 14px;
            border-radius: 3px;
            margin-right: 7px;
            vertical-align: middle;
            border: 1px solid rgba(0,0,0,.08);
        }
        @media (max-width: 1024px) {
            .map-wrap { grid-template-columns: 1fr; }
            #chad-map { height: 380px; }
        }

        /* ── Scroll reveal ── */
        [data-aos] {
            opacity: 0; transform: translateY(28px);
            transition: opacity .65s ease, transform .65s ease;
        }
        [data-aos="right"] { transform: translateX(-28px); }
        [data-aos="left"]  { transform: translateX(28px); }
        [data-aos].in { opacity: 1; transform: none; }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .sidebar { display: none; }
            .mnav { display: flex; }
            .main { margin-left: 0; padding-top: 60px; }
            .sec { padding: 80px 40px; }
            #home { padding: 60px 40px; min-height: auto; }
            .hero-wrap { grid-template-columns: 1fr; gap: 40px; }
            .hero-visual { display: none; }
            .prob-wrap { grid-template-columns: 1fr; gap: 40px; }
            .feat-grid { grid-template-columns: 1fr 1fr; }
            .team-grid { grid-template-columns: 1fr 1fr; }
            .hack-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .sec { padding: 60px 24px; }
            #home { padding: 40px 24px; }
            .feat-grid { grid-template-columns: 1fr; }
            .prob-right { grid-template-columns: 1fr; }
            footer { padding: 40px 24px; flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

<a href="#main" style="position:absolute;top:8px;left:8px;background:var(--blue);color:#fff;padding:7px 14px;border-radius:6px;z-index:999;font-size:13px;opacity:0;pointer-events:none;transition:opacity .2s" onfocus="this.style.opacity='1';this.style.pointerEvents='auto'" onblur="this.style.opacity='0';this.style.pointerEvents='none'">Aller au contenu</a>

<!-- Sidebar -->
<aside class="sidebar" aria-label="Navigation principale">
    <a href="/" class="logo" aria-label="Accueil AideChain">
        <span class="logo-dot"></span>
        <span class="logo-name">AideChain</span>
    </a>
    <nav class="snav" aria-label="Sections de la page">
        <ul>
            <li><a href="#home"      class="nav-link">Accueil</a></li>
            <li><a href="#probleme"  class="nav-link">Problème</a></li>
            <li><a href="#solution"  class="nav-link">Solution</a></li>
            <li><a href="#equipe"    class="nav-link">Équipe</a></li>
            <li><a href="#carte"     class="nav-link">Carte</a></li>
            <li><a href="#hackathon" class="nav-link">Hackathon</a></li>
        </ul>
    </nav>
    <div class="ssocial" aria-label="Liens sociaux">
        <a href="https://github.com/sk0rzeny" target="_blank" rel="noopener" aria-label="GitHub">
            <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
        </a>
        <a href="https://www.linkedin.com/in/sk0rzeny/" target="_blank" rel="noopener" aria-label="LinkedIn">
            <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
        </a>
    </div>
</aside>

<!-- Mobile nav -->
<div class="mnav" role="banner">
    <a href="/" class="logo" aria-label="Accueil AideChain">
        <span class="logo-dot"></span>
        <span class="logo-name">AideChain</span>
    </a>
    <button class="burger" id="burger" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="mmenu">
        <span></span><span></span><span></span>
    </button>
</div>
<nav class="mmenu" id="mmenu" aria-label="Navigation mobile">
    <a href="#home">Accueil</a>
    <a href="#probleme">Problème</a>
    <a href="#solution">Solution</a>
    <a href="#equipe">Équipe</a>
    <a href="#carte">Carte</a>
    <a href="#hackathon">Hackathon</a>
</nav>

<!-- Main -->
<main class="main" id="main">

    <!-- ── HERO ── -->
    <section id="home">
        <div class="hero-wrap">
            <div>
                <div class="hero-tag">Tchad · MIABE Hackathon 2026</div>
                <h1 class="hero-h1">
                    Protéger chaque<br>
                    <span>bénéficiaire</span>,<br>
                    éliminer les doublons.
                </h1>
                <p class="hero-desc">
                    AideChain est une plateforme de coordination humanitaire qui détecte en temps réel les bénéficiaires recevant plusieurs aides de différentes ONG, pour une distribution juste et traçable.
                </p>
                <div class="hero-btns">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-p" wire:navigate>
                            <span>Accéder à la plateforme</span>
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-p">
                            <span>Se connecter</span>
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                        <a href="{{ route('register') }}" class="btn-o">Créer un compte</a>
                    @endauth
                </div>
            </div>
            <div class="hero-visual" aria-hidden="true">
                <div class="hero-card">
                    <div class="hero-card-live">
                        <span class="live-dot"></span>
                        En compétition
                    </div>
                    <div class="hero-big">35%</div>
                    <p class="hero-card-txt">des bénéficiaires reçoivent<br>des aides en double au Tchad</p>
                    <p class="hero-card-src">Source · OCHA Tchad 2024</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── PROBLÈME ── -->
    <section id="probleme" class="sec" aria-labelledby="prob-h">
        <div class="sec-inner">
            <div class="prob-wrap">
                <div data-aos="right">
                    <div class="sec-tag">Le problème</div>
                    <h2 id="prob-h">Une crise humanitaire mal coordonnée</h2>
                    <p style="color:var(--gray);margin-top:20px;line-height:1.8;font-size:16px">
                        Au Tchad, plus de 6 millions de personnes ont besoin d'aide humanitaire. Les ONG opèrent de manière isolée, sans visibilité sur les distributions des autres. Résultat : certains bénéficiaires accumulent les aides tandis que d'autres sont oubliés.
                    </p>
                    <p style="color:var(--gray);margin-top:14px;line-height:1.8;font-size:16px">
                        L'absence d'un registre partagé rend la détection des doublons impossible en temps réel — du gaspillage dans un contexte d'urgence absolue.
                    </p>
                </div>
                <div class="prob-right" data-aos="left">
                    <div class="sblock">
                        <div class="snum" data-count="6" data-suffix="M+">0</div>
                        <div class="slbl">Personnes nécessitant une aide humanitaire au Tchad</div>
                    </div>
                    <div class="sblock" style="background:var(--bg-alt)">
                        <div class="snum" data-count="35" data-suffix="%">0</div>
                        <div class="slbl">Des bénéficiaires reçoivent des aides en double</div>
                    </div>
                    <div class="sblock" style="background:var(--bg-alt)">
                        <div class="snum" data-count="120" data-suffix="+">0</div>
                        <div class="slbl">ONG actives sans coordination centralisée</div>
                    </div>
                    <div class="sblock">
                        <div class="snum" style="color:var(--blue)">0</div>
                        <div class="slbl">Registre partagé inter-ONG existant à ce jour</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── SOLUTION ── -->
    <section id="solution" class="sec" aria-labelledby="sol-h">
        <div class="sec-inner">
            <div data-aos>
                <div class="sec-tag">La solution</div>
                <h2 id="sol-h" style="max-width:560px">AideChain — Coordination en temps réel</h2>
                <p style="color:var(--gray);margin-top:14px;max-width:580px;line-height:1.8;font-size:16px">
                    Plateforme web et mobile qui centralise les bénéficiaires, détecte les doublons entre ONG, et donne aux coordinateurs une vue complète de la couverture humanitaire.
                </p>
            </div>
            <div class="feat-grid">
                <div class="feat" data-aos style="transition-delay:.08s">
                    <div class="feat-ico">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <div class="feat-title">Registre unifié</div>
                    <p class="feat-text">Chaque bénéficiaire est enregistré une seule fois. Toutes les ONG partagent un registre sécurisé avec contrôle d'accès strict.</p>
                </div>
                <div class="feat" data-aos style="transition-delay:.16s">
                    <div class="feat-ico">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    </div>
                    <div class="feat-title">Détection de doublons</div>
                    <p class="feat-text">Avant chaque distribution, AideChain vérifie en temps réel si le bénéficiaire a déjà reçu une aide similaire d'une autre ONG.</p>
                </div>
                <div class="feat" data-aos style="transition-delay:.24s">
                    <div class="feat-ico">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                    </div>
                    <div class="feat-title">Dashboard ONG</div>
                    <p class="feat-text">Les représentants ONG gèrent leurs projets, agents et bénéficiaires depuis un tableau de bord avec statistiques en direct.</p>
                </div>
                <div class="feat" data-aos style="transition-delay:.08s">
                    <div class="feat-ico">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/></svg>
                    </div>
                    <div class="feat-title">Application mobile</div>
                    <p class="feat-text">Les agents terrain enregistrent les bénéficiaires hors connexion. Synchronisation automatique au retour en réseau.</p>
                </div>
                <div class="feat" data-aos style="transition-delay:.16s">
                    <div class="feat-ico">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div class="feat-title">Couverture géographique</div>
                    <p class="feat-text">Visualisation des zones couvertes et non couvertes. Identification des zones de concentration et des zones abandonnées.</p>
                </div>
                <div class="feat" data-aos style="transition-delay:.24s">
                    <div class="feat-ico">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div class="feat-title">Traçabilité complète</div>
                    <p class="feat-text">Chaque aide distribuée est enregistrée avec horodatage, agent et ONG. Audit trail complet et immuable.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── CARTE ── -->
    <section id="carte" class="sec" aria-labelledby="carte-h">
        <div class="sec-inner">
            <div data-aos>
                <div class="sec-tag">Couverture géographique</div>
                <h2 id="carte-h">Bénéficiaires par région</h2>
                <p style="color:var(--gray);margin-top:14px;max-width:580px;line-height:1.8;font-size:16px">
                    Visualisation en temps réel de la couverture humanitaire au Tchad — chaque région colorée selon le nombre de bénéficiaires enregistrés dans le registre partagé.
                </p>
            </div>
            <div class="map-wrap">
                <div id="chad-map"></div>
                <div class="map-panel" data-aos="left">
                    <div class="map-stat-card">
                        <div class="map-stat-num" id="map-total">—</div>
                        <div class="map-stat-lbl">Bénéficiaires enregistrés dans le registre</div>
                    </div>
                    <div class="map-stat-card">
                        <div class="map-stat-num" id="map-regions-actives" style="color:var(--black)">—</div>
                        <div class="map-stat-lbl">Régions couvertes par au moins un projet d'aide</div>
                    </div>
                    <p class="map-note">Cliquez sur une région pour zoomer. Survolez pour voir le nombre de bénéficiaires. La légende et le détail sont affichés directement sur la carte.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── ÉQUIPE ── -->
    <section id="equipe" class="sec" aria-labelledby="team-h">
        <div class="sec-inner">
            <div data-aos>
                <div class="sec-tag">L'équipe</div>
                <h2 id="team-h">Toumai Coders</h2>
                <p style="color:var(--gray);margin-top:14px;max-width:520px;line-height:1.8;font-size:16px">
                    Des développeurs tchadiens passionnés, réunis pour concevoir une solution concrète au MIABE Hackathon 2026.
                </p>
            </div>
            <div class="team-grid">
                <div class="tcard" data-aos style="transition-delay:.08s">
                    <img src="{{ asset('images/team/hamit.jpg') }}" alt="Hamit Ali Mahamat" class="tphoto"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="tfall" aria-hidden="true">HA</div>
                    <div class="tinfo">
                        <div class="tname">Hamit Ali Mahamat</div>
                        <div class="trole">Chef d'équipe · Backend</div>
                        <div class="tlinks">
                            <a href="https://www.linkedin.com/in/hamit-ali-mahamat-716960258" target="_blank" rel="noopener" class="tlink" aria-label="LinkedIn Hamit">LinkedIn</a>
                            <a href="https://hamitalimahamat01.github.io" target="_blank" rel="noopener" class="tlink" aria-label="Portfolio Hamit">Portfolio</a>
                        </div>
                    </div>
                </div>
                <div class="tcard" data-aos style="transition-delay:.16s">
                    <img src="{{ asset('images/team/ngong.jpg') }}" alt="Ngong-né Tchoubou" class="tphoto"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="tfall" aria-hidden="true">NT</div>
                    <div class="tinfo">
                        <div class="tname">Ngong-né Tchoubou</div>
                        <div class="trole">Développeur Frontend Web</div>
                        <div class="tlinks">
                            <a href="https://www.linkedin.com/in/ngong-n%C3%A9-tchoubou-65611b359" target="_blank" rel="noopener" class="tlink" aria-label="LinkedIn Ngong-né">LinkedIn</a>
                        </div>
                    </div>
                </div>
                <div class="tcard" data-aos style="transition-delay:.24s">
                    <img src="{{ asset('images/team/idriss.jpg') }}" alt="Mahamat Idriss Mahamat" class="tphoto"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="tfall" aria-hidden="true">MI</div>
                    <div class="tinfo">
                        <div class="tname">Mahamat Idriss Mahamat</div>
                        <div class="trole">Développeur Mobile</div>
                        <div class="tlinks">
                            <a href="https://www.linkedin.com/in/mahamat-idriss-mahamat-1b640b32a" target="_blank" rel="noopener" class="tlink" aria-label="LinkedIn Mahamat Idriss">LinkedIn</a>
                        </div>
                    </div>
                </div>
                <div class="tcard" data-aos style="transition-delay:.32s">
                    <img src="{{ asset('images/team/ludovic.jpg') }}" alt="Ludovic Mateyan Nelson" class="tphoto"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="tfall" aria-hidden="true">LM</div>
                    <div class="tinfo">
                        <div class="tname">Ludovic Mateyan Nelson</div>
                        <div class="trole">Développeur Full Stack</div>
                    </div>
                </div>
            </div>
            <div class="mentor" data-aos>
                <img src="{{ asset('images/team/skorzeny.jpg') }}" alt="Kemleyogoto Skorzeny" class="mphoto"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="mfall" aria-hidden="true">KS</div>
                <div>
                    <div class="mbadge">Mentor</div>
                    <div class="mname">Kemleyogoto Skorzeny</div>
                    <div class="mtitle">Développeur Full Stack · Mentor technique · MIABE Hackathon 2026</div>
                    <div class="mlinks">
                        <a href="https://www.linkedin.com/in/sk0rzeny/" target="_blank" rel="noopener" class="mlink" aria-label="LinkedIn Kemleyogoto Skorzeny">LinkedIn</a>
                        <a href="https://skorzeny.dev" target="_blank" rel="noopener" class="mlink">skorzeny.dev</a>
                        <a href="https://github.com/sk0rzeny" target="_blank" rel="noopener" class="mlink" aria-label="GitHub Kemleyogoto Skorzeny">GitHub</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── HACKATHON ── -->
    <section id="hackathon" class="sec" style="border-bottom:none" aria-labelledby="hack-h">
        <div class="sec-inner">
            <div data-aos>
                <div class="sec-tag">Hackathon</div>
                <h2 id="hack-h">MIABE Hackathon 2026</h2>
            </div>
            <div class="hack-grid">
                <div class="hcard blue" data-aos="right">
                    <div class="hlbl">Compétition nationale</div>
                    <div class="htitle">MIABE 2026</div>
                    <p class="htxt">
                        AideChain a été conçu et développé dans le cadre du MIABE Hackathon 2026, une compétition d'innovation technologique au Tchad.
                    </p>
                    <div style="margin-top:24px;padding-top:24px;border-top:1px solid rgba(255,255,255,.2)">
                        <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,.6);margin-bottom:6px">Équipe</div>
                        <div style="font-size:18px;font-weight:800">Toumai Coders</div>
                    </div>
                </div>
                <div class="hcard" data-aos="left">
                    <div class="hlbl">Notre approche</div>
                    <div class="htitle" style="font-size:20px">Pourquoi AideChain ?</div>
                    <p class="htxt" style="margin-top:12px">
                        Face à une crise humanitaire chronique et une coordination défaillante entre ONG, nous avons choisi de construire un outil concret et immédiatement déployable qui répond à un vrai problème de terrain.
                    </p>
                </div>
            </div>
            <ul class="hack-steps" role="list">
                <li class="hstep" data-aos style="transition-delay:.08s">
                    <span class="hnum">01</span>
                    <div>
                        <div class="hstep-title">Identification du problème</div>
                        <p class="hstep-txt">Analyse des rapports OCHA et retours terrain. Les doublons représentent la principale fuite de ressources humanitaires au Tchad.</p>
                    </div>
                </li>
                <li class="hstep" data-aos style="transition-delay:.16s">
                    <span class="hnum">02</span>
                    <div>
                        <div class="hstep-title">Conception de la solution</div>
                        <p class="hstep-txt">Architecture multi-ONG avec contrôle d'accès strict, détection de doublons atomique et application mobile hors-ligne.</p>
                    </div>
                </li>
                <li class="hstep" data-aos style="transition-delay:.24s">
                    <span class="hnum">03</span>
                    <div>
                        <div class="hstep-title">Développement full-stack</div>
                        <p class="hstep-txt">Laravel 13 + Livewire + Flux UI pour le web, Flutter 3.41 pour le mobile. Audit trail complet et immuable en base de données.</p>
                    </div>
                </li>
                <li class="hstep" data-aos style="transition-delay:.32s">
                    <span class="hnum">04</span>
                    <div>
                        <div class="hstep-title">Démonstration live</div>
                        <p class="hstep-txt">Scénario complet : ONG A enregistre un bénéficiaire — ONG B tente une deuxième aide — doublon détecté et bloqué en temps réel.</p>
                    </div>
                </li>
            </ul>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="foot-brand">
            <span class="logo-dot"></span>
            <span>AideChain</span>
        </div>
        <p class="foot-txt">Toumai Coders · MIABE Hackathon 2026 · Tchad</p>
        <div class="foot-links">
            <a href="{{ route('login') }}"    class="foot-link">Connexion</a>
            <a href="{{ route('register') }}" class="foot-link">Inscription</a>
            <a href="https://github.com/sk0rzeny" target="_blank" rel="noopener" class="foot-link" aria-label="GitHub">GitHub</a>
        </div>
    </footer>

</main>

<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js" crossorigin=""></script>
<script>
(function () {
    if (!document.getElementById('chad-map')) return;

    // Correspondance GADM NAME_1 → nom français en base
    var GADM_TO_FR = {
        'BarhelGhazel'      : 'Barh El Gazel',
        'Batha'             : 'Batha',
        'Borkou'            : 'Borkou',
        'Chari-Baguirmi'    : 'Chari-Baguirmi',
        'EnnediEst'         : 'Ennedi Est',
        'EnnediOuest'       : 'Ennedi Ouest',
        'Guéra'        : 'Guéra',
        'Hadjer-Lamis'      : 'Hadjer-Lamis',
        'Kanem'             : 'Kanem',
        'Lac'               : 'Lac',
        'LogoneOccidental'  : 'Logone Occidental',
        'LogoneOriental'    : 'Logone Oriental',
        'Mandoul'           : 'Mandoul',
        'Mayo-KebbiEst'     : 'Mayo-Kebbi Est',
        'Mayo-KebbiOuest'   : 'Mayo-Kebbi Ouest',
        'Moyen-Chari'       : 'Moyen-Chari',
        'Ouaddaï'           : 'Ouaddaï',
        'Salamat'           : 'Salamat',
        'Sila'              : 'Sila',
        'Tandjilé'          : 'Tandjilé',
        'Tibesti'           : 'Tibesti',
        "VilledeN’Djamena" : "N’Djamena",
        'WadiFira'          : 'Wadi Fira'
    };

    // Initialisation de la carte
    var map = L.map('chad-map', {
        scrollWheelZoom: false
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors © <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 12
    }).addTo(map);

    function getColor(n) {
        return n > 100 ? '#1553D4' :
               n > 50  ? '#3B82F6' :
               n > 20  ? '#60A5FA' :
               n > 5   ? '#93C5FD' :
               n > 0   ? '#DBEAFE' :
                         '#F1F5F9';
    }

    // Contrôle info (survol) — pattern exact du tutoriel Leaflet
    var info = L.control();
    info.onAdd = function () {
        this._div = L.DomUtil.create('div', 'leaflet-info-chad');
        this.update();
        return this._div;
    };
    info.update = function (props, count) {
        if (props) {
            var frName = GADM_TO_FR[props.NAME_1] || props.NAME_1;
            this._div.innerHTML =
                '<strong style="font-size:13px">' + frName + '</strong><br>' +
                '<span style="color:#606060">' + (count || 0) +
                ' bénéficiaire' + ((count || 0) !== 1 ? 's' : '') + '</span>';
        } else {
            this._div.innerHTML = '<span style="color:#A0A0A0;font-size:12px">Survolez une région</span>';
        }
    };
    info.addTo(map);

    // Contrôle légende — pattern exact du tutoriel Leaflet
    var legend = L.control({ position: 'bottomright' });
    legend.onAdd = function () {
        var div    = L.DomUtil.create('div', 'leaflet-legend-chad');
        var grades = [0, 1, 6, 21, 51, 101];
        var labels = ['Non couvert', '1–5', '6–20', '21–50', '51–100', '100+'];
        div.innerHTML = '<strong style="font-size:11px;letter-spacing:1px;text-transform:uppercase;color:#606060">Bénéficiaires</strong><br>';
        for (var i = 0; i < grades.length; i++) {
            div.innerHTML +=
                '<i style="background:' + getColor(grades[i]) + '"></i>' + labels[i] + '<br>';
        }
        return div;
    };
    legend.addTo(map);

    // Couche GeoJSON (déclarée en dehors des callbacks pour resetStyle)
    var geojsonLayer;
    var regionCounts = {};

    function featureStyle(feature) {
        var frName = GADM_TO_FR[feature.properties.NAME_1] || feature.properties.NAME_1;
        return {
            fillColor   : getColor(regionCounts[frName] || 0),
            weight      : 1,
            opacity     : 1,
            color       : '#CBD5E1',
            fillOpacity : 0.75
        };
    }

    function highlightFeature(e) {
        var layer = e.target;
        layer.setStyle({ weight: 2.5, color: '#1553D4', fillOpacity: 0.92 });
        layer.bringToFront();
        var frName = GADM_TO_FR[layer.feature.properties.NAME_1] || layer.feature.properties.NAME_1;
        info.update(layer.feature.properties, regionCounts[frName] || 0);
    }

    function resetHighlight(e) {
        geojsonLayer.resetStyle(e.target);
        info.update();
    }

    function zoomToFeature(e) {
        map.fitBounds(e.target.getBounds());
    }

    function onEachFeature(feature, layer) {
        layer.on({
            mouseover : highlightFeature,
            mouseout  : resetHighlight,
            click     : zoomToFeature
        });
    }

    // Chargement parallèle : GeoJSON local + données API
    Promise.all([
        fetch('/geojson/chad-regions.geojson').then(function (r) { return r.json(); }),
        fetch('/map/regions').then(function (r) { return r.json(); })
    ]).then(function (results) {
        var geo  = results[0];
        var data = results[1];

        regionCounts = data.regions || {};
        var actives  = Object.values(regionCounts).filter(function (v) { return v > 0; }).length;

        var totalEl = document.getElementById('map-total');
        var actEl   = document.getElementById('map-regions-actives');
        if (totalEl) totalEl.textContent = data.total_beneficiaires || 0;
        if (actEl)   actEl.textContent   = data.regions_couvertes || actives;

        geojsonLayer = L.geoJSON(geo, {
            style          : featureStyle,
            onEachFeature  : onEachFeature
        }).addTo(map);

        map.fitBounds(geojsonLayer.getBounds(), { padding: [10, 10] });
    }).catch(function (err) {
        console.error('Carte AideChain :', err);
    });
})();
</script>
<script>
(function () {
    // Scroll spy
    const links = document.querySelectorAll('.nav-link');
    const secs  = document.querySelectorAll('section[id]');
    const spy = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting)
                links.forEach(l => l.classList.toggle('active', l.getAttribute('href') === '#' + e.target.id));
        });
    }, { threshold: 0.35 });
    secs.forEach(s => spy.observe(s));

    // Scroll reveal
    const reveal = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); reveal.unobserve(e.target); } });
    }, { threshold: 0.1 });
    document.querySelectorAll('[data-aos]').forEach(el => reveal.observe(el));

    // Counters
    const cnt = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (!e.isIntersecting) return;
            const el = e.target;
            const target = parseFloat(el.dataset.count);
            const suffix = el.dataset.suffix || '';
            const dur = 1600, t0 = performance.now();
            (function tick(now) {
                const p = Math.min((now - t0) / dur, 1);
                const v = target * (1 - Math.pow(1 - p, 3));
                el.textContent = (Number.isInteger(target) ? Math.round(v) : v.toFixed(1)) + suffix;
                if (p < 1) requestAnimationFrame(tick);
            })(t0);
            cnt.unobserve(el);
        });
    }, { threshold: 0.5 });
    document.querySelectorAll('[data-count]').forEach(el => cnt.observe(el));

    // Hamburger
    const burger = document.getElementById('burger');
    const mmenu  = document.getElementById('mmenu');
    burger.addEventListener('click', () => {
        const open = mmenu.classList.toggle('open');
        burger.classList.toggle('open', open);
        burger.setAttribute('aria-expanded', String(open));
    });
    mmenu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
        mmenu.classList.remove('open');
        burger.classList.remove('open');
        burger.setAttribute('aria-expanded', 'false');
    }));
})();
</script>
</body>
</html>
