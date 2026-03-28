<?php 
$appUrl = \App\Config\Config::getAppUrl(); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A4R LEADERBOARD - SPIN GLOW V4</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@500;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --neon-red: #ff0000;
            --f1-red: #e10600;
            --bg-dark: #000000;
            --sidebar-bg: rgba(8, 8, 8, 0.98);
            --item-bg: linear-gradient(90deg, rgba(20,20,20,0.9) 0%, rgba(30,30,30,0.4) 100%);
            --gold: #ffcc00;
            --silver: #e0e0e0;
            --bronze: #cd7f32;
        }

        @keyframes rotateGlow {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        * { margin: 0; padding: 0; box-sizing: border-box; cursor: none; }
        body { 
            background: var(--bg-dark); 
            color: #fff; 
            font-family: 'Rajdhani', sans-serif;
            height: 100vh;
            overflow: hidden;
            text-transform: uppercase;
        }

        #bg-layer {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-size: cover; background-position: center;
            opacity: 0.15; z-index: 0; transition: background 1.5s ease;
        }

        .hud-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.1) 50%),
                        linear-gradient(90deg, rgba(255, 0, 0, 0.02), rgba(0, 255, 0, 0.01), rgba(0, 0, 255, 0.02));
            background-size: 100% 4px, 4px 100%;
            z-index: 1000; pointer-events: none;
        }

        #main-container {
            display: grid;
            grid-template-columns: 28% 72%;
            width: 100%; height: 100vh;
            padding: 20px; gap: 20px;
            position: relative; z-index: 10;
        }

        /* --- SIDEBAR --- */
        .sidebar {
            background: var(--sidebar-bg);
            border-left: 5px solid var(--f1-red);
            padding: 25px;
            display: flex; flex-direction: column;
            box-shadow: 20px 0 50px rgba(0,0,0,1);
            border-radius: 8px;
        }

        .brand-header { margin-bottom: 25px; border-bottom: 2px solid rgba(255,255,255,0.05); padding-bottom: 15px; }
        .brand-header h1 { font-family: 'Orbitron'; font-weight: 900; font-size: 1.8vw; color: #fff; line-height: 1; }
        .brand-header h2 { font-family: 'Orbitron'; font-weight: 700; font-size: 1vw; color: var(--f1-red); text-shadow: 0 0 10px var(--f1-red); }

        .rank-list-holder { flex: 1; background: rgba(0,0,0,0.5); padding: 5px; border-radius: 5px; overflow: hidden; }
        .rank-table { width: 100%; border-collapse: separate; border-spacing: 0 3px; }
        .rank-item { background: var(--item-bg); animation: f1FadeIn 0.5s both; }
        .pos-num { font-family: 'Orbitron'; font-weight: 900; color: var(--f1-red); font-size: 1.1vw; width: 35px; font-style: italic; }
        .driver-avatar img { width: 30px; height: 30px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.2); object-fit: cover; }
        .driver-name { font-weight: 700; font-size: 0.8vw; color: #ddd; }
        .driver-time { font-family: 'Orbitron'; color: #00ffca; font-size: 1vw; font-weight: 700; text-align: right; }

        /* --- CONTENT AREA --- */
        .content-area { display: flex; flex-direction: column; overflow: hidden; position: relative; }

        .top-banner {
            background: linear-gradient(180deg, rgba(160, 0, 0, 0.6) 0%, rgba(15, 0, 0, 0.9) 100%);
            backdrop-filter: blur(10px);
            padding: 15px 40px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 0, 0, 0.4);
            border-radius: 12px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            box-shadow: 0 8px 30px rgba(0,0,0,0.6);
            position: relative;
        }
        .banner-info { text-align: center; }
        .banner-info .season { font-family: 'Orbitron'; font-size: 0.7vw; color: rgba(255,255,255,0.7); letter-spacing: 4px; margin-bottom: 3px; }
        .banner-info .title { font-family: 'Orbitron'; font-size: 2.3vw; font-weight: 900; color: #fff; letter-spacing: 2px; }
        .live-status { 
            position: absolute; right: 25px; top: 50%; transform: translateY(-50%);
            background: #00ffca; color: #000; padding: 4px 14px; font-weight: 900; font-size: 0.7vw; border-radius: 3px; 
            box-shadow: 0 0 15px rgba(0,255,202,0.4);
        }

        .carousel-view { flex: 1; position: relative; }
        .slide {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            display: flex; align-items: flex-end; justify-content: center;
            opacity: 0; visibility: hidden; transition: 0.8s ease-in-out;
        }
        .slide.active { opacity: 1; visibility: visible; }

        .podium-wrap { display: flex; align-items: flex-end; justify-content: center; gap: 2vw; width: 100%; height: 100%; padding-bottom: 50px; }
        
        .p-card {
            flex: 1; max-width: 320px; background: rgba(10, 10, 10, 0.85);
            border: 1px solid rgba(255,255,255,0.03); border-radius: 15px;
            padding: 35px 20px; text-align: center; position: relative;
            transform: translateY(15vw); opacity: 0;
            box-shadow: 0 25px 50px rgba(0,0,0,0.9);
            backdrop-filter: blur(20px);
        }
        .slide.active .p-card { transform: translateY(0); opacity: 1; transition: 1.2s cubic-bezier(0.19, 1, 0.22, 1); }
        .p-card.p1 { height: 98%; border-top: 4px solid var(--gold); }
        .p-card.p2 { height: 82%; border-top: 4px solid var(--silver); }
        .p-card.p3 { height: 72%; border-top: 4px solid var(--bronze); }

        /* EFECTO GIRO "SPIN GLOW" */
        .p-img-box {
            position: relative; 
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 25px;
            z-index: 1;
        }
        /* El borde que gira */
        .p-img-box::before {
            content: ""; position: absolute; top: -8px; left: -8px; right: -8px; bottom: -8px;
            border-radius: 50%;
            background: conic-gradient(from 0deg, transparent, var(--spin-color), transparent 50%);
            animation: rotateGlow 2s linear infinite;
            z-index: -1;
        }
        .p2 .p-img-box { --spin-color: var(--silver); }
        .p1 .p-img-box { --spin-color: var(--gold); }
        .p3 .p-img-box { --spin-color: var(--bronze); }

        .p-img { 
            width: 140px; height: 140px; border-radius: 50%; object-fit: cover; 
            border: 3px solid rgba(0,0,0,0.8); background: #111;
        }
        .p1 .p-img { width: 210px; height: 210px; }
        
        .p-badge { font-family: 'Orbitron'; font-weight: 900; font-size: 0.9vw; color: rgba(255,255,255,0.5); margin-bottom: 10px; display: block; }
        .p1 .p-badge { color: var(--gold); font-size: 1.4vw; }
        .p-name { font-family: 'Orbitron'; font-weight: 700; font-size: 1.5vw; color: #fff; margin-bottom: 4px; }
        .p-time { font-family: 'Orbitron'; font-size: 2.3vw; color: #00ffca; font-weight: 900; text-shadow: 0 0 20px rgba(0,255,202,0.6); }

        .ad-frame { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; padding: 25px; }
        .ad-img { max-width: 95%; max-height: 95%; border-radius: 12px; box-shadow: 0 0 100px rgba(0,0,0,1); }

        @keyframes f1FadeIn { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }

        /* --- MARQUEE / TICKER --- */
        #marquee-container {
            position: fixed; bottom: 15px; right: 20px; left: calc(28% + 40px);
            background: rgba(0, 0, 0, 0.85);
            border-left: 4px solid var(--f1-red);
            padding: 10px 0;
            overflow: hidden;
            border-radius: 5px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.8);
            backdrop-filter: blur(10px);
            z-index: 1001;
            display: none; /* Se activa si hay mensajes */
        }
        .marquee-content {
            display: inline-block;
            white-space: nowrap;
            padding-left: 100%;
            animation: marqueeSync 30s linear infinite;
            font-family: 'Orbitron';
            color: #fff;
            font-size: 0.9vw;
            font-weight: 700;
        }
        @keyframes marqueeSync {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }
        .marquee-item { display: inline-block; margin-right: 80px; }
        .marquee-item::before { content: "•"; color: var(--f1-red); margin-right: 15px; }

        /* --- NEW LAP ALERT OVERLAY --- */
        #new-lap-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255, 0, 0, 0.9);
            z-index: 9999; display: none; align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.5s ease;
            pointer-events: none;
            overflow: hidden;
        }
        #new-lap-overlay.active {
            opacity: 1; display: flex; animation: flashRedBackground 0.4s infinite alternate;
        }
        @keyframes flashRedBackground {
            from { background: rgba(255, 0, 0, 0.85); }
            to { background: rgba(140, 0, 0, 1); }
        }
        .alert-content { text-align: center; transform: scale(0.5); transition: 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        #new-lap-overlay.active .alert-content { transform: scale(1); }
        .alert-header { 
            font-family: 'Orbitron'; font-weight: 900; font-size: 6vw; color: #fff; 
            text-shadow: 0 0 50px #000, 0 0 20px #ff0; letter-spacing: 5px; margin-bottom: 20px;
        }
        .alert-driver { font-family: 'Orbitron'; font-size: 4vw; color: #ffcc00; margin-bottom: 10px; }
        .alert-time { font-family: 'Orbitron'; font-size: 7vw; font-weight: 900; color: #fff; text-shadow: 0 0 30px rgba(0,0,0,0.8); }
        
        /* Efecto de líneas de velocidad */
        .speed-lines {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: repeating-linear-gradient(45deg, transparent, transparent 100px, rgba(255,255,255,0.05) 100px, rgba(255,255,255,0.05) 101px);
            z-index: -1;
        }
    </style>
</head>
<body>

<div class="hud-overlay"></div>
<div id="bg-layer"></div>

<div id="main-container">
    <div class="sidebar">
        <div class="brand-header">
            <h1 id="ranking-title-main">A4R SIMRACING</h1>
            <h2 id="ranking-title-sub">HOT LAP RANKING</h2>
        </div>
        <div class="rank-list-holder">
            <table class="rank-table">
                <tbody id="table-body"></tbody>
            </table>
        </div>
    </div>

    <div class="content-area">
        <header class="top-banner">
            <div class="banner-info">
                <div class="season" id="label-banner-top">LOADING...</div>
                <div class="title" id="label-banner-main">PÓDIUM</div>
            </div>
            <div class="live-status">LIVE DATA STREAM</div>
        </header>

        <div class="carousel-view" id="carousel-view">
            <div class="slide active" id="slide-podium">
                <div class="podium-wrap">
                    <div class="p-card p2">
                        <span class="p-badge">🥈 2ND PLACE</span>
                        <div class="p-img-box">
                            <img id="p2-img" src="<?php echo $appUrl; ?>/assets/img/defaultpiloto.png" class="p-img">
                        </div>
                        <div class="p-name" id="p2-name">---</div>
                        <div class="p-time" id="p2-time">--:--.---</div>
                    </div>
                    <div class="p-card p1">
                        <span class="p-badge">👑 SEASON WINNER</span>
                        <div class="p-img-box">
                            <img id="p1-img" src="<?php echo $appUrl; ?>/assets/img/defaultpiloto.png" class="p-img">
                        </div>
                        <div class="p-name" id="p1-name">---</div>
                        <div class="p-time" id="p1-time">--:--.---</div>
                    </div>
                    <div class="p-card p3">
                        <span class="p-badge">🥉 3RD PLACE</span>
                        <div class="p-img-box">
                            <img id="p3-img" src="<?php echo $appUrl; ?>/assets/img/defaultpiloto.png" class="p-img">
                        </div>
                        <div class="p-name" id="p3-name">---</div>
                        <div class="p-time" id="p3-time">--:--.---</div>
                    </div>
                </div>
            </div>
        </div>

        <div id="marquee-container">
            <div id="marquee-text" class="marquee-content"></div>
        </div>
    </div>
</div>

<!-- CAPA DE ALERTA ALTA INTENSIDAD -->
<div id="new-lap-overlay">
    <div class="speed-lines"></div>
    <div class="alert-content">
        <div id="alert-type-label" class="alert-header">NUEVA VUELTA</div>
        <div id="alert-driver-name" class="alert-driver">PILOTO</div>
        <div id="alert-driver-time" class="alert-time">00:00.000</div>
    </div>
</div>

<!-- AUDIO HUD -->
<audio id="hud-sound" preload="auto">
    <source src="<?php echo $appUrl; ?>/public/assets/sounds/formula-1-sound.mp3" type="audio/mpeg">
</audio>

<script>
    const BASE_URL = '<?php echo $appUrl; ?>';
    let DEFAULT_IMG = `${BASE_URL}/assets/img/defaultpiloto.png`;
    
    let current = 0, mainTimer = null, lastRecordId = null, loading = false, ads = [];
    let rankDur = 15000;
    let labels = {
        ranking_title_main: 'A4R SIMRACING',
        ranking_title_sub: 'HOT LAP RANKING',
        banner_top_text: 'A4R SIMRACING · TEMPORADA 2026',
        banner_podium_text: 'PÓDIUM',
        banner_ads_text: 'A4R SIMRACING'
    };

    function getImg(p) { return p ? `${BASE_URL}/${p}` : DEFAULT_IMG; }

    async function syncData() {
        if(loading) return; loading = true;
        try {
            const r = await fetch(`${BASE_URL}/api/ranking/data`);
            const d = await r.json();
            if(!d.success) return;
            
            rankDur = d.ranking_duration || 15000;
            if(d.labels) labels = d.labels;
            if(d.default_pilot_image) DEFAULT_IMG = `${BASE_URL}/${d.default_pilot_image}`;

            // --- DETECCIÓN DE NUEVA VUELTA (EFECTO HUD) ---
            if (lastRecordId !== null && d.latest_id > lastRecordId) {
                // Buscamos si el nuevo ID está en el TOP actual
                const newLap = d.top.find(x => x.id == d.latest_id);
                if(newLap) {
                    const isPurple = (d.top[0].id == d.latest_id); // ¿Es el nuevo #1 mundial?
                    triggerNewLapAnimation(newLap, isPurple);
                }
            }
            lastRecordId = d.latest_id;

            document.getElementById('ranking-title-main').innerText = labels.ranking_title_main;
            document.getElementById('ranking-title-sub').innerText = labels.ranking_title_sub;
            document.getElementById('label-banner-top').innerText = labels.banner_top_text;

            const bg = document.getElementById('bg-layer');
            if(d.background_image && d.background_image !== "") {
                bg.style.backgroundImage = `url('${BASE_URL}/${d.background_image}')`;
                bg.style.opacity = d.background_opacity || 0.15;
            } else { bg.style.backgroundImage = 'none'; bg.style.backgroundColor = '#000'; bg.style.opacity = 1; }

            const tbody = document.getElementById('table-body');
            tbody.innerHTML = '';
            d.top.slice(0, 20).forEach((row, i) => {
                const tr = document.createElement('tr');
                tr.className = 'rank-item';
                tr.innerHTML = `<td class="pos-num">${(i+1).toString().padStart(2,'0')}</td><td class="driver-avatar"><img src="${getImg(row.foto_path)}"></td><td class="driver-name">${row.piloto_nombre.substring(0,18)}</td><td class="driver-time">${row.tiempo}</td>`;
                tbody.appendChild(tr);
            });

            const top3 = d.top.slice(0, 3);
            const setP = (n, row) => {
                document.getElementById(`p${n}-name`).innerText = row ? row.piloto_nombre : '---';
                document.getElementById(`p${n}-time`).innerText = row ? row.tiempo : '--:--.---';
                document.getElementById(`p${n}-img`).src = getImg(row ? row.foto_path : null);
            };
            setP(1, top3[0]); setP(2, top3[1]); setP(3, top3[2]);

            ads = d.ads || [];
            
            // Render Marquee
            const marqCont = document.getElementById('marquee-container');
            const marqText = document.getElementById('marquee-text');
            if(d.marquee && d.marquee.length > 0) {
                marqCont.style.display = 'block';
                marqText.innerHTML = d.marquee.map(t => `<span class="marquee-item">${t}</span>`).join('');
                const duration = Math.max(20, d.marquee.join('').length / 10);
                marqText.style.animationDuration = duration + 's';
            } else {
                marqCont.style.display = 'none';
            }

            if(!mainTimer) initCarousel();
        } catch (e) { console.error(e); }
        finally { loading = false; }
    }

    function triggerNewLapAnimation(data, isPurple) {
        if(!data) return;
        
        const overlay = document.getElementById('new-lap-overlay');
        const sound = document.getElementById('hud-sound');
        const label = document.getElementById('alert-type-label');
        const name = document.getElementById('alert-driver-name');
        const time = document.getElementById('alert-driver-time');
        
        label.innerText = isPurple ? "NUEVO RÉCORD" : "NUEVA VUELTA";
        label.style.color = isPurple ? "#ff0" : "#fff";
        name.innerText = data.piloto_nombre.toUpperCase();
        time.innerText = data.tiempo;
        
        // Ejecutar HUD
        overlay.style.display = 'flex';
        setTimeout(() => overlay.classList.add('active'), 10);
        
        // Intentar reproducir sonido (F1 Sound)
        try {
            sound.currentTime = 0;
            sound.play();
        } catch(e) { console.warn("Audio bloqueado por el navegador"); }
        
        // Auto-cerrar tras 6 segundos
        setTimeout(() => {
            overlay.classList.remove('active');
            setTimeout(() => { overlay.style.display = 'none'; }, 500);
        }, 6000);
    }

    function initCarousel() {
        const view = document.getElementById('carousel-view');
        const update = () => {
            clearTimeout(mainTimer);
            view.querySelectorAll('.ad-slide').forEach(s => s.remove());
            ads.forEach(ad => {
                const div = document.createElement('div');
                div.className = 'slide ad-slide';
                div.innerHTML = `<div class="ad-frame"><img src="${BASE_URL}/${ad.image_path}" class="ad-img"></div>`;
                view.appendChild(div);
            });
            const slides = view.querySelectorAll('.slide');
            const next = () => {
                slides[current].classList.remove('active');
                current = (current + 1) % slides.length;
                slides[current].classList.add('active');
                const bl = document.getElementById('label-banner-main');
                let wait = rankDur;
                if(slides[current].classList.contains('ad-slide')) {
                    bl.innerText = labels.banner_ads_text;
                    wait = (ads[current-1]?.duration_seconds || 10) * 1000;
                } else { bl.innerText = labels.banner_podium_text; syncData(); }
                mainTimer = setTimeout(next, wait);
            };
            mainTimer = setTimeout(next, rankDur);
        };
        update();
    }
    syncData();
    setInterval(syncData, 12000);
</script>
</body>
</html>
