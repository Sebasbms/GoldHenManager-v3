/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: js/app.js
 * ====================================================================
 */
var CREATOR_ATTRIBUTION = "by SeBaS";
console.log(`%c GOLDHEN MANAGER V4.0 - Developed ${CREATOR_ATTRIBUTION} `, "background: #111827; color: #22d3ee; font-weight: bold; padding: 4px;");

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => { navigator.serviceWorker.register('/sw.js').then(reg => reg.update()).catch(() => {}); });
}

var globalAppConfig = {
    ipConsola: '192.168.1.28',
    portFTP: 2121,
    statusConexion: false,
    alertasActivas: true,
    sonidosActivos: true,
    volumenSfx: 0.5,
    launcherLayout: '5-grid',
    biblioTheme: 'theme-glass',
    biblioCols: 3,
    particulaStyle: 'magic',
    particulaIntensidad: 1.0,
    particulaTamano: 1.0,
    sonidoTactil: 'select',
    notificacionStyle: 'toast-default',
    headerStyle: 'theme-default',
    radarStyle: 'anim_1'
};

var globalAudioCtx = null;
var lastTouchX = 0; var lastTouchY = 0;
var isGlobalTouching = false;
var lastTouchTime = 0; var lastSynthTime = 0;
var scanInterval; var scanTimeout;

var introNamesMap = { 'none': 'Sin Intro (Rápido)', 'intro-ps5': 'PlayStation 5', 'intro-ps4': 'PlayStation 4', 'intro-ps2': 'PlayStation 2 Clásica', 'intro-glitch': 'Glitch Hacker', 'intro-hud': 'Sci-Fi HUD', 'intro-neon': 'Cyberpunk Neon', 'intro-decrypt': 'Decrypt System', 'intro-arcade': 'Retro Arcade', 'intro-matrix-rain': 'Matrix Rain', 'intro-crt': 'Terminal CRT (Boot)', 'intro-gb': 'Game Boy Clásica', 'intro-breach': 'System Breach' };
var nombresLayouts = { '0-grid': '0. Default (Cuadrícula)', '0-list': '0. Default (Lista)', '1-grid': '1. Glass UI (Cuadrícula)', '1-list': '1. Glass UI (Lista)', '2-grid': '2. Cyber Neón (Cuadrícula)', '2-list': '2. Cyber Neón (Lista)', '3-grid': '3. Minimalista (Cuadrícula)', '3-list': '3. Minimalista (Lista)', '4-grid': '4. Neumorfismo (Cuadrícula)', '4-list': '4. Neumorfismo (Lista)', '5-grid': '5. Holograma 3D (Cuadrícula)', '5-list': '5. Holograma 3D (Lista)', '6-grid': '6. Mecánico Retro (Cuadrícula)', '6-list': '6. Mecánico Retro (Lista)', '7-grid': '7. Aura (Cuadrícula)', '7-list': '7. Aura (Lista)', '8-grid': '8. Sci-Fi Angular (Cuadrícula)', '8-list': '8. Sci-Fi Angular (Lista)', '9-grid': '9. Brutalismo (Cuadrícula)', '9-list': '9. Brutalismo (Lista)', '10-grid': '10. Acrílico (Cuadrícula)', '10-list': '10. Acrílico (Lista)' };
var nombresBiblio = { 'theme-glass': '1. Premium Glass', 'theme-liquid': '2. Liquid Glass', 'theme-frosted': '3. Frosted (Dark)', 'theme-translucent': '4. Translucent', 'theme-holographic': '5. Holograma 3D', 'theme-cyber': '6. Cyberpunk Tech', 'theme-neumorph': '7. Neumorfismo Dark', 'theme-minimal': '8. Minimalista Flat', 'theme-arcade': '9. Arcade Retro', 'theme-gold': '10. GoldHEN Luxury', 'theme-neon': '11. Synthwave Neon', 'theme-military': '12. Military Tactical', 'theme-blood': '13. Blood Vampire', 'theme-outline': '14. Outline Minimal', 'theme-radar': '15. Radar Console', 'theme-pureglass': '16. Pure Liquid Glass', 'theme-oled': '17. OLED True Black', 'theme-pop': '18. Pop Art Comic', 'theme-lightglass': '19. Light Glass (Claro)', 'theme-glitch': '20. Glitch Hacker' };

var sysParticleConfigs = {
    'moleculas': { colors: ['#00d4ff', '#8b5cf6', '#3b82f6', '#ffffff'], count: 12, minSize: 8, maxSize: 20, minDist: 30, maxDist: 100, minDur: 600, maxDur: 1000, class: 'pt-moleculas', char: '' },
    'plasma': { colors: ['#06b6d4', '#d946ef', '#3b82f6'], count: 8, minSize: 20, maxSize: 45, minDist: 20, maxDist: 80, minDur: 800, maxDur: 1500, class: 'pt-plasma', char: '' },
    'sparks': { colors: ['#fbbf24', '#fcd34d', '#ffffff', '#22d3ee'], count: 20, minSize: 3, maxSize: 8, minDist: 80, maxDist: 200, minDur: 300, maxDur: 600, class: 'pt-sparks', char: '' },
    'fuego': { colors: ['#ef4444', '#f97316', '#fbbf24', '#ffffff'], count: 15, minSize: 10, maxSize: 30, minDist: 40, maxDist: 150, minDur: 600, maxDur: 1200, class: 'pt-fuego', char: '' },
    'estrella': { colors: ['#fbbf24', '#fef08a', '#ffffff'], count: 8, minSize: 15, maxSize: 35, minDist: 50, maxDist: 120, minDur: 600, maxDur: 1200, class: 'pt-estrella', char: '' },
    'glitch': { colors: ['#00ff00', '#00ffff', '#ff00ff', '#ffffff'], count: 10, minSize: 10, maxSize: 18, minDist: 30, maxDist: 90, minDur: 200, maxDur: 600, class: 'pt-glitch', char: 'RND' },
    'magic': { colors: ['#00ffff', '#ff00ff', '#eab308', '#3b82f6', '#ffffff'], count: 10, minSize: 6, maxSize: 22, minDist: 40, maxDist: 120, minDur: 800, maxDur: 1500, class: 'pt-magic', char: '' },
    'nieve': { colors: ['#ffffff', '#cffafe', '#e0f2fe'], count: 12, minSize: 4, maxSize: 10, minDist: 20, maxDist: 60, minDur: 1000, maxDur: 2000, class: 'pt-nieve', char: '' },
    'matrix': { colors: ['#22c55e', '#4ade80', '#16a34a'], count: 15, minSize: 10, maxSize: 14, minDist: 0, maxDist: 40, minDur: 600, maxDur: 1200, class: 'pt-matrix', char: 'BIN' },
    'burbuja': { colors: ['#bae6fd', '#7dd3fc', '#ffffff'], count: 10, minSize: 10, maxSize: 25, minDist: 20, maxDist: 60, minDur: 800, maxDur: 1500, class: 'pt-burbuja', char: '' },
    'humo': { colors: ['#9ca3af', '#6b7280', '#4b5563'], count: 6, minSize: 30, maxSize: 60, minDist: 20, maxDist: 50, minDur: 1000, maxDur: 2000, class: 'pt-humo', char: '' },
    'corazon': { colors: ['#ec4899', '#f43f5e', '#fb7185'], count: 8, minSize: 12, maxSize: 20, minDist: 30, maxDist: 80, minDur: 800, maxDur: 1500, class: 'pt-corazon', char: '❤' },
    'polvo': { colors: ['#e9d5ff', '#d8b4fe', '#ffffff'], count: 25, minSize: 2, maxSize: 5, minDist: 50, maxDist: 150, minDur: 1000, maxDur: 2500, class: 'pt-polvo', char: '' },
    'hoja': { colors: ['#d97706', '#ea580c', '#ca8a04'], count: 8, minSize: 10, maxSize: 20, minDist: 40, maxDist: 100, minDur: 1000, maxDur: 2000, class: 'pt-hoja', char: '' },
    'hexagono': { colors: ['#06b6d4', '#8b5cf6', '#ec4899'], count: 6, minSize: 15, maxSize: 30, minDist: 40, maxDist: 100, minDur: 600, maxDur: 1200, class: 'pt-hexagono', char: '' },
    'laser': { colors: ['#ef4444', '#06b6d4', '#22c55e'], count: 12, minSize: 4, maxSize: 8, minDist: 100, maxDist: 300, minDur: 200, maxDur: 400, class: 'pt-laser', char: '' },
    'confeti': { colors: ['#ef4444', '#3b82f6', '#22c55e', '#eab308', '#a855f7'], count: 20, minSize: 6, maxSize: 12, minDist: 50, maxDist: 150, minDur: 1000, maxDur: 2000, class: 'pt-confeti', char: '' },
    'moneda': { colors: ['#fbbf24', '#f59e0b', '#d97706'], count: 8, minSize: 15, maxSize: 25, minDist: 30, maxDist: 90, minDur: 800, maxDur: 1200, class: 'pt-moneda', char: '' },
    'notas': { colors: ['#ffffff', '#a855f7', '#ec4899'], count: 8, minSize: 12, maxSize: 20, minDist: 40, maxDist: 100, minDur: 800, maxDur: 1500, class: 'pt-notas', char: 'MUSIC' },
    'sonar': { colors: ['#00ff00', '#06b6d4', '#f0f'], count: 3, minSize: 20, maxSize: 50, minDist: 0, maxDist: 0, minDur: 800, maxDur: 1500, class: 'pt-sonar', char: '' }
};

document.addEventListener("DOMContentLoaded", () => {
    try {
        if(window.inyectarEstilosParticulas) window.inyectarEstilosParticulas();
        if(window.cargarConfiguracionesLocales) window.cargarConfiguracionesLocales();
        if(window.configurarEventosDashboard) window.configurarEventosDashboard();
        if(window.inicializarValoresInterfazAjustes) window.inicializarValoresInterfazAjustes();
        if(window.iniciarMotorGlobalParticulas) window.iniciarMotorGlobalParticulas();
        if(window.verificarRadarInicial) window.verificarRadarInicial();
    } catch(e) {}
});

window.inyectarEstilosParticulas = function() {
    if (document.getElementById('sys-particles-style')) return;
    const style = document.createElement('style');
    style.id = 'sys-particles-style';
    style.innerHTML = `
        .sys-global-particles { position: fixed; pointer-events: none; z-index: 99999; top: 0; left: 0; width: 100vw; height: 100vh; overflow: visible; }
        .sys-particle { position: absolute; pointer-events: none; transform: translate(-50%, -50%); will-change: transform, opacity; display: flex; align-items: center; justify-content: center; }
        .pt-moleculas { border-radius: 50%; box-shadow: 0 0 15px currentColor, inset 0 0 8px white; animation: std-explode var(--duration) cubic-bezier(0.1,0.8,0.3,1) forwards; mix-blend-mode: screen; }
        .pt-plasma { border-radius: 50%; filter: blur(6px); animation: std-explode var(--duration) ease-out forwards; mix-blend-mode: screen; }
        .pt-sparks { border-radius: 2px !important; box-shadow: 0 0 10px currentColor; animation: spark-explode var(--duration) ease-out forwards; mix-blend-mode: screen; }
        .pt-fuego { border-radius: 50%; filter: blur(3px); box-shadow: 0 0 20px currentColor; animation: fire-explode var(--duration) ease-in forwards; mix-blend-mode: screen; }
        .pt-estrella { clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%); animation: star-explode var(--duration) ease-out forwards; filter: drop-shadow(0 0 10px currentColor); }
        .pt-glitch { font-family: 'Share Tech Mono', monospace; font-weight: bold; animation: std-explode var(--duration) ease-out forwards; text-shadow: 1px 0 red, -1px 0 cyan; }
        .pt-magic { border-radius: 50%; box-shadow: 0 0 15px currentColor, 0 0 30px currentColor; animation: magic-explode var(--duration) ease-out forwards; mix-blend-mode: screen; }
        .pt-nieve { border-radius: 50%; box-shadow: 0 0 10px white; filter: blur(1px); animation: snow-fall var(--duration) ease-in forwards; }
        .pt-matrix { font-family: 'Share Tech Mono', monospace; font-weight: bold; text-shadow: 0 0 8px currentColor; animation: rain-fall var(--duration) linear forwards; }
        .pt-burbuja { border-radius: 50%; border: 1px solid currentColor; box-shadow: inset 0 0 10px currentColor; background: transparent !important; animation: bubble-rise var(--duration) ease-in forwards; }
        .pt-humo { border-radius: 50%; filter: blur(15px); animation: smoke-fade var(--duration) ease-out forwards; }
        .pt-corazon { font-family: sans-serif; text-shadow: 0 0 10px currentColor; animation: float-up var(--duration) ease-out forwards; background: transparent !important;}
        .pt-polvo { border-radius: 50%; box-shadow: 0 0 5px currentColor; animation: slow-drift var(--duration) ease-out forwards; }
        .pt-hoja { border-radius: 0 50% 0 50%; animation: spin-fall var(--duration) ease-in forwards; box-shadow: inset 0 0 5px rgba(0,0,0,0.5); }
        .pt-hexagono { clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%); border: 2px solid currentColor; background: transparent !important; box-shadow: inset 0 0 10px currentColor, 0 0 10px currentColor; animation: star-explode var(--duration) ease-out forwards; }
        .pt-laser { border-radius: 10px; box-shadow: 0 0 15px currentColor; animation: laser-shoot var(--duration) ease-out forwards; transform-origin: center; }
        .pt-confeti { border-radius: 0; animation: confetti-fall var(--duration) ease-out forwards; }
        .pt-moneda { border-radius: 50%; border: 2px dashed rgba(0,0,0,0.3); animation: coin-spin var(--duration) ease-out forwards; box-shadow: 0 4px 0px #b45309; }
        .pt-notas { font-family: sans-serif; font-weight: bold; text-shadow: 0 0 10px currentColor; animation: float-dance var(--duration) ease-out forwards; background: transparent !important;}
        .pt-sonar { border-radius: 50%; border: 2px solid currentColor; background: transparent !important; box-shadow: 0 0 15px currentColor, inset 0 0 15px currentColor; animation: sonar-expand var(--duration) ease-out forwards; }
        
        @keyframes std-explode { 0% { transform: translate(-50%, -50%) scale(1); opacity: 1; } 100% { transform: translate(calc(-50% + var(--tx)), calc(-50% + var(--ty))) scale(0); opacity: 0; } }
        @keyframes spark-explode { 0% { transform: translate(-50%, -50%) scale(1) rotate(var(--rot)); opacity: 1; } 100% { transform: translate(calc(-50% + var(--tx)), calc(-50% + var(--ty))) scaleY(5) scaleX(0.1) rotate(var(--rot)); opacity: 0; } }
        @keyframes fire-explode { 0% { transform: translate(-50%, -50%) scale(1); opacity: 1; } 100% { transform: translate(calc(-50% + var(--tx)), calc(-50% + var(--ty) - 150px)) scale(0); opacity: 0; } }
        @keyframes star-explode { 0% { transform: translate(-50%, -50%) scale(0) rotate(0deg); opacity: 1; } 30% { transform: translate(calc(-50% + (var(--tx)/3)), calc(-50% + (var(--ty)/3))) scale(var(--scale-max)) rotate(90deg); opacity: 1; } 100% { transform: translate(calc(-50% + var(--tx)), calc(-50% + var(--ty))) scale(0) rotate(360deg); opacity: 0; } }
        @keyframes magic-explode { 0% { transform: translate(-50%, -50%) scale(0.2); opacity: 1; } 30% { transform: translate(calc(-50% + (var(--tx)*0.4)), calc(-50% + (var(--ty)*0.4))) scale(var(--scale-max)); opacity: 1; filter: brightness(1.5); } 100% { transform: translate(calc(-50% + var(--tx)), calc(-50% + var(--ty) - 100px)) scale(0); opacity: 0; } }
        @keyframes snow-fall { 0% { transform: translate(-50%, -50%) scale(0); opacity: 0; } 20% { opacity: 1; transform: translate(calc(-50% + var(--tx)/2), calc(-50% + 10px)) scale(1); } 100% { transform: translate(calc(-50% + var(--tx)), calc(-50% + 150px)) scale(0.5); opacity: 0; } }
        @keyframes rain-fall { 0% { transform: translate(-50%, -50%); opacity: 1; } 100% { transform: translate(-50%, calc(-50% + 200px)); opacity: 0; } }
        @keyframes bubble-rise { 0% { transform: translate(-50%, -50%) scale(0); opacity: 1; } 100% { transform: translate(calc(-50% + var(--tx)), calc(-50% - 150px)) scale(1.5); opacity: 0; } }
        @keyframes smoke-fade { 0% { transform: translate(-50%, -50%) scale(0.5); opacity: 0.8; } 100% { transform: translate(calc(-50% + var(--tx)), calc(-50% + var(--ty) - 50px)) scale(3); opacity: 0; } }
        @keyframes float-up { 0% { transform: translate(-50%, -50%) scale(0.5); opacity: 1; } 100% { transform: translate(calc(-50% + var(--tx)), calc(-50% - 100px)) scale(1.5); opacity: 0; } }
        @keyframes slow-drift { 0% { transform: translate(-50%, -50%) scale(1); opacity: 1; } 100% { transform: translate(calc(-50% + var(--tx)), calc(-50% + var(--ty))) scale(0.2); opacity: 0; } }
        @keyframes spin-fall { 0% { transform: translate(-50%, -50%) rotate(0deg); opacity: 1; } 100% { transform: translate(calc(-50% + var(--tx)), calc(-50% + 150px)) rotate(720deg); opacity: 0; } }
        @keyframes laser-shoot { 0% { transform: translate(-50%, -50%) scaleX(0.1) rotate(var(--rot)); opacity: 1; } 100% { transform: translate(calc(-50% + var(--tx)*2), calc(-50% + var(--ty)*2)) scaleX(2) rotate(var(--rot)); opacity: 0; } }
        @keyframes confetti-fall { 0% { transform: translate(-50%, -50%) rotate3d(1,1,1, 0deg); opacity: 1; } 100% { transform: translate(calc(-50% + var(--tx)), calc(-50% + 150px)) rotate3d(1,1,1, 720deg); opacity: 0; } }
        @keyframes coin-spin { 0% { transform: translate(-50%, -50%) rotateY(0deg); opacity: 1; } 100% { transform: translate(calc(-50% + var(--tx)), calc(-50% + var(--ty) - 50px)) rotateY(720deg); opacity: 0; } }
        @keyframes float-dance { 0% { transform: translate(-50%, -50%) scale(0.5) rotate(-15deg); opacity: 1; } 50% { transform: translate(calc(-50% + var(--tx)/2), calc(-50% - 50px)) scale(1.2) rotate(15deg); } 100% { transform: translate(calc(-50% + var(--tx)), calc(-50% - 100px)) scale(0.8) rotate(-15deg); opacity: 0; } }
        @keyframes sonar-expand { 0% { transform: translate(-50%, -50%) scale(0); opacity: 1; } 100% { transform: translate(-50%, -50%) scale(var(--scale-max)); opacity: 0; } }
        
        @keyframes a-spin-cw { 100% { transform: rotate(360deg); } }
        @keyframes a-spin-ccw { 100% { transform: rotate(-360deg); } }
        @keyframes a-pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(1.1); } }
        @keyframes a-scan-h { 0% { left: -10%; } 100% { left: 110%; } }
        @keyframes a-scan-v { 0% { top: -10%; } 100% { top: 110%; } }
        @keyframes a-bg-shift { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        @keyframes a-expand { 0% { transform: scale(0.1); opacity: 1; border-width: 4px; } 100% { transform: scale(3); opacity: 0; border-width: 0px; } }
        @keyframes a-expand-out { 0% { transform: scale(0.1); opacity: 1; border-width: 4px; } 100% { transform: scale(3); opacity: 0; border-width: 0px; } }
        @keyframes a-eq-bounce { 0% { height: 10%; } 100% { height: 100%; } }
        @keyframes a-glitch { 0% { transform: translate(0); } 20% { transform: translate(-4px, 4px); } 40% { transform: translate(-4px, -4px); } 60% { transform: translate(4px, 4px); } 80% { transform: translate(4px, -4px); } 100% { transform: translate(0); } }
        @keyframes a-glitch-skew { 0% { transform: skew(0deg); } 20% { transform: skew(-10deg); } 40% { transform: skew(10deg); } 60% { transform: skew(-5deg); } 80% { transform: skew(5deg); } 100% { transform: skew(0deg); } }
        @keyframes a-matrix-fall { 0% { transform: translateY(-100px); } 100% { transform: translateY(400px); } }
        @keyframes a-wave-x { 0% { transform: translateX(-20px); } 100% { transform: translateX(20px); } }
    `;
    document.head.appendChild(style);
    const layer = document.createElement('div');
    layer.id = 'sys-global-particles';
    layer.className = 'sys-global-particles';
    document.body.appendChild(layer);
};

window.alternarColumnasBiblioAjustes = function() {
    let cols = parseInt(localStorage.getItem('cfg_sebas_biblio_cols')) || 3;
    cols = cols === 3 ? 2 : 3;
    localStorage.setItem('cfg_sebas_biblio_cols', cols);
    globalAppConfig.biblioCols = cols;
    const btnColsAjustes = document.getElementById('btn-toggle-cols');
    if (btnColsAjustes) btnColsAjustes.innerText = cols + " COLUMNAS";
    if (typeof window.aplicarTemaBiblioteca === 'function') window.aplicarTemaBiblioteca();
};

window.aplicarTemaBiblioteca = function() {
    const theme = localStorage.getItem('cfg_sebas_biblio_theme') || 'theme-glass';
    const cols = parseInt(localStorage.getItem('cfg_sebas_biblio_cols')) || 3;
    const layer = document.getElementById('layer-biblioteca');
    if(layer) {
        const classesToRemove = Array.from(layer.classList).filter(c => c.startsWith('theme-'));
        classesToRemove.forEach(c => layer.classList.remove(c));
        layer.classList.add(theme);
    }
    const gridTarget = document.getElementById('dom-grid-target');
    if(gridTarget) { 
        gridTarget.style.display = 'grid';
        gridTarget.style.gridTemplateColumns = `repeat(${cols}, minmax(0, 1fr))`;
        gridTarget.style.gap = '1rem';
    }
    const iconBtn = document.getElementById('icono-columnas-grid');
    if (iconBtn) { iconBtn.className = cols === 2 ? "fa-solid fa-list text-[11px]" : "fa-solid fa-border-all text-[11px]"; }
};

window.cargarConfiguracionesLocales = function() {
    const ipGuardada = localStorage.getItem('sebas_ip_final_libre');
    const portGuardado = localStorage.getItem('sebas_port_libre') || '2121';
    globalAppConfig.alertasActivas = localStorage.getItem('cfg_sebas_alertas') !== 'false';
    globalAppConfig.sonidosActivos = localStorage.getItem('cfg_sebas_sonidos') !== 'false';
    globalAppConfig.volumenSfx = localStorage.getItem('cfg_sebas_volumen_sfx') ? parseFloat(localStorage.getItem('cfg_sebas_volumen_sfx')) : 0.5;
    globalAppConfig.launcherLayout = localStorage.getItem('cfg_sebas_launcher_layout') || '5-grid';
    window.renderizarDisenoLauncher(globalAppConfig.launcherLayout);
    globalAppConfig.biblioTheme = localStorage.getItem('cfg_sebas_biblio_theme') || 'theme-glass';
    globalAppConfig.biblioCols = parseInt(localStorage.getItem('cfg_sebas_biblio_cols')) || 3;
    globalAppConfig.particulaStyle = localStorage.getItem('cfg_sebas_particulas') || 'magic';
    globalAppConfig.particulaIntensidad = localStorage.getItem('cfg_sebas_part_intensidad') ? parseFloat(localStorage.getItem('cfg_sebas_part_intensidad')) : 1.0;
    globalAppConfig.particulaTamano = localStorage.getItem('cfg_sebas_part_tamano') ? parseFloat(localStorage.getItem('cfg_sebas_part_tamano')) : 1.0;
    globalAppConfig.sonidoTactil = localStorage.getItem('cfg_sebas_sonido_tactil') || 'select';
    globalAppConfig.notificacionStyle = localStorage.getItem('cfg_sebas_notificacion_style') || 'toast-default';
    globalAppConfig.headerStyle = localStorage.getItem('cfg_sebas_header_style') || 'theme-default';
    globalAppConfig.radarStyle = localStorage.getItem('cfg_sebas_radar_style') || 'anim_1';

    window.aplicarEstiloCabeceraGlobal(globalAppConfig.headerStyle);

    if (typeof window.aplicarTemaBiblioteca === 'function') window.aplicarTemaBiblioteca();
    if (ipGuardada) { globalAppConfig.ipConsola = ipGuardada; const inputIP = document.getElementById('ps-ip-full-input'); if (inputIP) inputIP.value = ipGuardada; }
    globalAppConfig.portFTP = parseInt(portGuardado, 10);
    const bgGuardado = localStorage.getItem('ps4_dynamic_bg') || 'bg-ps5';
    if (typeof changeDynamicWallpaper === 'function') { setTimeout(() => changeDynamicWallpaper(bgGuardado), 300); }
};

window.aplicarEstiloCabeceraGlobal = function(themeId) {
    const mainHeader = document.getElementById('main-header');
    if (mainHeader) {
        const classesToRemove = Array.from(mainHeader.classList).filter(c => c.startsWith('theme-'));
        classesToRemove.forEach(c => mainHeader.classList.remove(c));
        mainHeader.classList.add(themeId);
    }
};

window.inicializarValoresInterfazAjustes = function() {
    setTimeout(() => {
        const swAlertas = document.getElementById('cfg-switch-notificaciones'); if(swAlertas) swAlertas.checked = globalAppConfig.alertasActivas;
        const swSonidos = document.getElementById('cfg-switch-sonidos'); if(swSonidos) swSonidos.checked = globalAppConfig.sonidosActivos;
        const sliderVol = document.getElementById('cfg-slider-volumen'); const lblVol = document.getElementById('lbl-volumen-sfx');
        if(sliderVol && lblVol) { let val = Math.round(globalAppConfig.volumenSfx * 100); sliderVol.value = val; lblVol.innerText = val + '%'; }
        
        const sliderInt = document.getElementById('cfg-slider-intensidad'); const lblInt = document.getElementById('lbl-intensidad-part');
        if(sliderInt && lblInt) { let val = Math.round(globalAppConfig.particulaIntensidad * 100); sliderInt.value = val; lblInt.innerText = val + '%'; }
        
        const sliderTam = document.getElementById('cfg-slider-tamano'); const lblTam = document.getElementById('lbl-tamano-part');
        if(sliderTam && lblTam) { let val = Math.round(globalAppConfig.particulaTamano * 100); sliderTam.value = val; lblTam.innerText = val + '%'; }

        const selectWall = document.getElementById('custom-select-label');
        if (selectWall) { const bgNamesMap = { 'none': 'Apagar Fondos', 'bg-ps5': 'System Default (PS5)', 'bg-ps4': 'Olas Líquidas (PS4)', 'bg-ps2': 'Cubos 3D (PS2)', 'bg-matrix': 'Lluvia de Código (Matrix)', 'bg-warp': 'Velocidad Warp (Espacio)', 'bg-plasma': 'Fluido Plasma', 'bg-network': 'Red Neuronal (Network)' }; selectWall.innerText = bgNamesMap[localStorage.getItem('ps4_dynamic_bg') || 'bg-ps5'] || 'System Default (PS5)'; }

        const lblIntro = document.getElementById('custom-select-intro-label'); if (lblIntro) lblIntro.innerText = introNamesMap[localStorage.getItem('ps4_selected_intro') || 'none'] || 'Sin Intro (Rápido)';
        const lblLauncher = document.getElementById('custom-select-launcher-label'); if (lblLauncher) lblLauncher.innerText = nombresLayouts[globalAppConfig.launcherLayout] || '5. Holograma 3D (Cuadrícula)';
        const lblBiblio = document.getElementById('custom-select-biblio-label'); if (lblBiblio) lblBiblio.innerText = nombresBiblio[globalAppConfig.biblioTheme] || '1. Premium Glass';
        
        const lblPart = document.getElementById('custom-select-particulas-label'); 
        const ptNames = { 'moleculas':'1. Moléculas', 'plasma':'2. Plasma', 'sparks':'3. Chispas', 'fuego':'4. Fuego Fatuo', 'estrella':'5. Estrellas', 'glitch':'6. Glitch Data', 'magic':'7. Magic Boost', 'nieve':'8. Nieve', 'matrix':'9. Matrix Rain', 'burbuja':'10. Burbujas', 'humo':'11. Humo', 'corazon':'12. Corazones', 'polvo':'13. Polvo Cósmico', 'hoja':'14. Otoño', 'hexagono':'15. Hexágonos', 'laser':'16. Rayos Láser', 'confeti':'17. Confeti', 'moneda':'18. Monedas Oro', 'notas':'19. Notas Musicales', 'sonar':'20. Sonar Rings'};
        if(lblPart) lblPart.innerText = ptNames[globalAppConfig.particulaStyle] || '7. Magic Boost';

        const lblSnd = document.getElementById('custom-select-sonido-label');
        const sndNames = { 'select':'1. UI Default', 'liquid':'2. Liquid Drop', 'cyber':'3. Cyber Glitch', 'brutal':'4. Mech Heavy', 'spark':'5. Spark Ping', 'magic':'6. Magic Chime', 'coin':'7. Coin Collect', 'hover':'8. Sci-Fi Hover', 'bass':'9. Deep Bass', 'retro':'10. Retro 8-bit', 'bubble':'11. Bubble Pop', 'laser':'12. Laser Pew', 'tap':'13. Soft Tap', 'glass':'14. Crystal Glass', 'echo':'15. Echo Ping', 'sonar':'16. Sub Sonar', 'zap':'17. Electric Zap', 'whoosh':'18. Space Whoosh', 'type':'19. Typewriter', 'alien':'20. Alien Blaster'};
        if(lblSnd) lblSnd.innerText = sndNames[globalAppConfig.sonidoTactil] || '1. UI Default';
        
        const lblNotif = document.getElementById('custom-select-notificaciones-label');
        const notiNames = { 'toast-default': '1. Premium Default', 'toast-ps4': '2. PS4 Clásico', 'toast-liquid': '3. Liquid Glass (Abajo)', 'toast-glitch': '4. Glitch Alert', 'toast-cyber': '5. Cyberpunk Neon', 'toast-holographic': '6. Holograma 3D', 'toast-arcade': '7. Arcade Retro', 'toast-minimal': '8. Minimalista Outline', 'toast-military': '9. Military Tactical', 'toast-neumorph': '10. Neumorfismo Dark' };
        if(lblNotif) lblNotif.innerText = notiNames[globalAppConfig.notificacionStyle] || '1. Premium Default';

        const lblHeader = document.getElementById('custom-select-header-label');
        const headerNames = { 'theme-default': '1. Default', 'theme-liquid': '2. Liquid Glass', 'theme-cyber': '3. Cyberpunk', 'theme-neumorph': '4. Neumorfismo', 'theme-minimal': '5. Minimalista', 'theme-arcade': '6. Arcade Retro', 'theme-hologram': '7. Holograma 3D', 'theme-military': '8. Military', 'theme-blood': '9. Blood Vampire', 'theme-oled': '10. OLED Black', 'theme-terminal': '11. Terminal DOS', 'theme-frosted': '12. Frosted Glass', 'theme-pop': '13. Pop Art', 'theme-radar-style': '14. Radar Console', 'theme-translucent': '15. Translucent', 'theme-pureglass': '16. Pure Glass', 'theme-neon': '17. Neon Synth', 'theme-brutalism': '18. Brutalism', 'theme-gold': '19. GoldHEN', 'theme-outline': '20. Outline' };
        if(lblHeader) lblHeader.innerText = headerNames[globalAppConfig.headerStyle] || '1. Default';

        const lblRadar = document.getElementById('custom-select-radar-label');
        const radarNames = { 'anim_1': '1. Pulse Matrix', 'anim_2': '2. Cyber Rift', 'anim_3': '3. Neon Fluid', 'anim_4': '4. Quantum Tunnel', 'anim_5': '5. Retro Grid', 'anim_6': '6. Plasma Core', 'anim_7': '7. Data Stream', 'anim_8': '8. Hologram Wave', 'anim_9': '9. Energy Storm', 'anim_10': '10. Cosmic Dust', 'anim_11': '11. Hexagon Web', 'anim_12': '12. Sonar Ping', 'anim_13': '13. Laser Net', 'anim_14': '14. Digital Glitch', 'anim_15': '15. Synthwave Sun', 'anim_16': '16. Binary Code', 'anim_17': '17. Echolocation', 'anim_18': '18. Hyperspace', 'anim_19': '19. Radar Sweep', 'anim_20': '20. Aurora Flow' };
        if(lblRadar) lblRadar.innerText = radarNames[globalAppConfig.radarStyle] || '1. Pulse Matrix';

        const btnCols = document.getElementById('btn-toggle-cols'); if (btnCols) btnCols.innerText = globalAppConfig.biblioCols + " COLUMNAS";
    }, 200);
};

window.inicializarContextoAudio = function() {
    if (!globalAudioCtx) globalAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
    if (globalAudioCtx.state === 'suspended') globalAudioCtx.resume();
};

window.playSynth = function(typeOverride) {
    if (!globalAppConfig.sonidosActivos || globalAppConfig.volumenSfx <= 0) return;
    const nowMs = Date.now();
    if (nowMs - lastSynthTime < 100) return; 
    lastSynthTime = nowMs;

    try {
        window.inicializarContextoAudio();
        const ctx = globalAudioCtx; const osc = ctx.createOscillator(); const gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        const now = ctx.currentTime; const vol = 0.2 * globalAppConfig.volumenSfx;
        const type = typeOverride || globalAppConfig.sonidoTactil;

        if (type === 'liquid') { osc.type = 'sine'; osc.frequency.setValueAtTime(600, now); osc.frequency.exponentialRampToValueAtTime(1200, now + 0.1); gain.gain.setValueAtTime(vol, now); gain.gain.exponentialRampToValueAtTime(0.01, now + 0.1); osc.start(now); osc.stop(now + 0.1); } 
        else if (type === 'select') { osc.type = 'sine'; osc.frequency.setValueAtTime(800, now); osc.frequency.exponentialRampToValueAtTime(400, now + 0.08); gain.gain.setValueAtTime(vol, now); gain.gain.linearRampToValueAtTime(0.01, now + 0.08); osc.start(now); osc.stop(now + 0.08); }
        else if (type === 'cyber') { osc.type = 'sawtooth'; osc.frequency.setValueAtTime(1000, now); osc.frequency.linearRampToValueAtTime(200, now + 0.1); gain.gain.setValueAtTime(vol*0.5, now); gain.gain.exponentialRampToValueAtTime(0.01, now + 0.1); osc.start(now); osc.stop(now + 0.1); }
        else if (type === 'brutal') { osc.type = 'square'; osc.frequency.setValueAtTime(150, now); osc.frequency.exponentialRampToValueAtTime(40, now + 0.15); gain.gain.setValueAtTime(vol, now); gain.gain.exponentialRampToValueAtTime(0.01, now + 0.15); osc.start(now); osc.stop(now + 0.15); }
        else if (type === 'spark') { osc.type = 'triangle'; osc.frequency.setValueAtTime(2500, now); osc.frequency.linearRampToValueAtTime(1000, now + 0.05); gain.gain.setValueAtTime(vol*0.3, now); gain.gain.exponentialRampToValueAtTime(0.01, now + 0.05); osc.start(now); osc.stop(now + 0.05); }
        else if (type === 'magic') { osc.type = 'sine'; osc.frequency.setValueAtTime(1200, now); osc.frequency.exponentialRampToValueAtTime(2400, now + 0.2); gain.gain.setValueAtTime(vol*0.8, now); gain.gain.linearRampToValueAtTime(0.01, now + 0.2); osc.start(now); osc.stop(now + 0.2); }
        else if (type === 'coin') { osc.type = 'sine'; osc.frequency.setValueAtTime(987.77, now); osc.frequency.setValueAtTime(1318.51, now + 0.08); gain.gain.setValueAtTime(vol, now); gain.gain.linearRampToValueAtTime(0.01, now + 0.3); osc.start(now); osc.stop(now + 0.3); }
        else if (type === 'hover') { osc.type = 'sine'; osc.frequency.setValueAtTime(300, now); osc.frequency.linearRampToValueAtTime(400, now + 0.1); gain.gain.setValueAtTime(0, now); gain.gain.linearRampToValueAtTime(vol*0.5, now + 0.05); gain.gain.linearRampToValueAtTime(0, now + 0.1); osc.start(now); osc.stop(now + 0.1); }
        else if (type === 'bass') { osc.type = 'sine'; osc.frequency.setValueAtTime(80, now); osc.frequency.exponentialRampToValueAtTime(20, now + 0.2); gain.gain.setValueAtTime(vol*1.5, now); gain.gain.exponentialRampToValueAtTime(0.01, now + 0.2); osc.start(now); osc.stop(now + 0.2); }
        else if (type === 'retro') { osc.type = 'square'; osc.frequency.setValueAtTime(440, now); osc.frequency.setValueAtTime(880, now + 0.05); gain.gain.setValueAtTime(vol*0.3, now); gain.gain.setValueAtTime(0, now + 0.1); osc.start(now); osc.stop(now + 0.1); }
        else if (type === 'bubble') { osc.type = 'sine'; osc.frequency.setValueAtTime(400, now); osc.frequency.linearRampToValueAtTime(1000, now + 0.05); gain.gain.setValueAtTime(vol, now); gain.gain.linearRampToValueAtTime(0, now + 0.05); osc.start(now); osc.stop(now + 0.05); }
        else if (type === 'laser') { osc.type = 'sawtooth'; osc.frequency.setValueAtTime(2000, now); osc.frequency.exponentialRampToValueAtTime(100, now + 0.15); gain.gain.setValueAtTime(vol*0.4, now); gain.gain.exponentialRampToValueAtTime(0.01, now + 0.15); osc.start(now); osc.stop(now + 0.15); }
        else if (type === 'tap') { osc.type = 'triangle'; osc.frequency.setValueAtTime(200, now); osc.frequency.exponentialRampToValueAtTime(50, now + 0.05); gain.gain.setValueAtTime(vol*0.8, now); gain.gain.exponentialRampToValueAtTime(0.01, now + 0.05); osc.start(now); osc.stop(now + 0.05); }
        else if (type === 'glass') { osc.type = 'sine'; osc.frequency.setValueAtTime(1800, now); gain.gain.setValueAtTime(vol*0.8, now); gain.gain.exponentialRampToValueAtTime(0.01, now + 0.2); osc.start(now); osc.stop(now + 0.2); }
        else if (type === 'echo') { osc.type = 'sine'; osc.frequency.setValueAtTime(1000, now); gain.gain.setValueAtTime(vol, now); gain.gain.exponentialRampToValueAtTime(0.1, now + 0.1); gain.gain.setValueAtTime(vol*0.3, now + 0.15); gain.gain.exponentialRampToValueAtTime(0.01, now + 0.25); osc.start(now); osc.stop(now + 0.3); }
        else if (type === 'sonar') { osc.type = 'sine'; osc.frequency.setValueAtTime(1200, now); gain.gain.setValueAtTime(0, now); gain.gain.linearRampToValueAtTime(vol, now + 0.1); gain.gain.linearRampToValueAtTime(0, now + 0.4); osc.start(now); osc.stop(now + 0.4); }
        else if (type === 'zap') { osc.type = 'sawtooth'; osc.frequency.setValueAtTime(150, now); osc.frequency.linearRampToValueAtTime(500, now + 0.05); gain.gain.setValueAtTime(vol*0.5, now); gain.gain.exponentialRampToValueAtTime(0.01, now + 0.05); osc.start(now); osc.stop(now + 0.05); }
        else if (type === 'whoosh') { osc.type = 'triangle'; osc.frequency.setValueAtTime(100, now); osc.frequency.exponentialRampToValueAtTime(400, now + 0.2); gain.gain.setValueAtTime(0, now); gain.gain.linearRampToValueAtTime(vol, now + 0.1); gain.gain.linearRampToValueAtTime(0, now + 0.2); osc.start(now); osc.stop(now + 0.2); }
        else if (type === 'type') { osc.type = 'square'; osc.frequency.setValueAtTime(800, now); gain.gain.setValueAtTime(vol*0.3, now); gain.gain.linearRampToValueAtTime(0.01, now + 0.03); osc.start(now); osc.stop(now + 0.03); }
        else if (type === 'alien') { osc.type = 'sawtooth'; osc.frequency.setValueAtTime(800, now); osc.frequency.linearRampToValueAtTime(400, now + 0.1); osc.frequency.linearRampToValueAtTime(1200, now + 0.2); gain.gain.setValueAtTime(vol*0.3, now); gain.gain.exponentialRampToValueAtTime(0.01, now + 0.2); osc.start(now); osc.stop(now + 0.2); }
        else if (type === 'beep') { osc.type = 'square'; osc.frequency.setValueAtTime(1200, now); gain.gain.setValueAtTime(0.03, now); gain.gain.exponentialRampToValueAtTime(0.01, now + 0.05); osc.start(now); osc.stop(now + 0.05); }
        else if (type === 'success') { osc.type = 'sine'; osc.frequency.setValueAtTime(400, now); osc.frequency.setValueAtTime(600, now + 0.1); osc.frequency.setValueAtTime(1000, now + 0.2); gain.gain.setValueAtTime(0.2, now); gain.gain.linearRampToValueAtTime(0.01, now + 0.4); osc.start(now); osc.stop(now + 0.4); if("vibrate" in navigator) navigator.vibrate([50, 50, 100]); }
        else if (type === 'glitch_snd') { osc.type = 'sawtooth'; osc.frequency.setValueAtTime(100, now); osc.frequency.linearRampToValueAtTime(800, now + 0.1); gain.gain.setValueAtTime(0.1, now); gain.gain.exponentialRampToValueAtTime(0.01, now + 0.1); osc.start(now); osc.stop(now + 0.1); }
        else { osc.type = 'sine'; osc.frequency.setValueAtTime(600, now); osc.frequency.exponentialRampToValueAtTime(150, now + 0.08); gain.gain.setValueAtTime(vol, now); gain.gain.linearRampToValueAtTime(0.001, now + 0.08); osc.start(now); osc.stop(now + 0.08); }
    } catch (e) {}
};

window.iniciarMotorGlobalParticulas = function() {
    const attachEvents = (doc) => {
        doc.addEventListener('touchstart', (e) => {
            lastTouchTime = Date.now();
            isGlobalTouching = true; lastTouchX = e.touches[0].clientX; lastTouchY = e.touches[0].clientY;
            window.spawnGlobalParticles(lastTouchX, lastTouchY, 15);
            if ("vibrate" in navigator && globalAppConfig.sonidosActivos) navigator.vibrate(10);
            window.playSynth(); 
        }, { passive: true });
        
        doc.addEventListener('touchmove', (e) => {
            if (!isGlobalTouching) return;
            const cX = e.touches[0].clientX; const cY = e.touches[0].clientY;
            if (Math.hypot(cX - lastTouchX, cY - lastTouchY) > 15) { window.spawnGlobalParticles(cX, cY, 2); lastTouchX = cX; lastTouchY = cY; }
        }, { passive: true });
        
        doc.addEventListener('touchend', () => isGlobalTouching = false); doc.addEventListener('touchcancel', () => isGlobalTouching = false);
        
        doc.addEventListener('mousedown', (e) => { 
            if (Date.now() - lastTouchTime < 500) return; 
            isGlobalTouching = true; lastTouchX = e.clientX; lastTouchY = e.clientY; 
            window.spawnGlobalParticles(lastTouchX, lastTouchY, 15); 
            window.playSynth(); 
        });
        
        doc.addEventListener('mousemove', (e) => { 
            if (!isGlobalTouching) return; 
            if (Math.hypot(e.clientX - lastTouchX, e.clientY - lastTouchY) > 15) { window.spawnGlobalParticles(e.clientX, e.clientY, 2); lastTouchX = e.clientX; lastTouchY = e.clientY; } 
        });
        doc.addEventListener('mouseup', () => isGlobalTouching = false);
    };

    attachEvents(document);

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.tagName === 'IFRAME') {
                    node.addEventListener('load', () => {
                        try {
                            if (node.contentDocument) {
                                attachEvents(node.contentDocument);
                            }
                        } catch (e) {
                            console.warn("Iframe cross-origin policy prevents particle attachment.");
                        }
                    });
                }
            });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });

    document.querySelectorAll('iframe').forEach(iframe => {
        try {
            if (iframe.contentDocument) attachEvents(iframe.contentDocument);
        } catch (e) {}
    });
};

window.spawnGlobalParticles = function(x, y, baseAmount) {
    const c = sysParticleConfigs[globalAppConfig.particulaStyle];
    if (!c) return;
    const layer = document.getElementById('sys-global-particles');
    if(!layer) return;

    let finalAmount = Math.ceil(baseAmount * (c.count / 10) * globalAppConfig.particulaIntensidad);
    if (finalAmount > 60) finalAmount = 60; 

    const fragment = document.createDocumentFragment();
    for (let i = 0; i < finalAmount; i++) {
        const p = document.createElement('div');
        const color = c.colors[Math.floor(Math.random() * c.colors.length)];
        const size = (Math.random() * (c.maxSize - c.minSize) + c.minSize) * globalAppConfig.particulaTamano;
        const dist = Math.random() * (c.maxDist - c.minDist) + c.minDist;
        const dur = Math.random() * (c.maxDur - c.minDur) + c.minDur;
        
        let angle = Math.random() * Math.PI * 2;
        if (globalAppConfig.particulaStyle === 'fuego') angle = (Math.random() * Math.PI/2) + Math.PI + Math.PI/4;
        if (globalAppConfig.particulaStyle === 'nieve' || globalAppConfig.particulaStyle === 'matrix') angle = Math.random() * Math.PI;

        const tx = Math.cos(angle) * dist; const ty = Math.sin(angle) * dist;
        p.className = `sys-particle ${c.class}`; p.style.left = `${x}px`; p.style.top = `${y}px`; p.style.color = color;
        
        if (c.char === '') { p.style.backgroundColor = color; p.style.width = `${size}px`; p.style.height = `${size}px`; } 
        else {
            p.style.fontSize = `${size}px`;
            if (c.char === 'RND') { const chars = ['0', '1', '#', '!', 'X', '<>']; p.innerText = chars[Math.floor(Math.random() * chars.length)]; } 
            else if (c.char === 'BIN') { p.innerText = Math.random() > 0.5 ? '1' : '0'; } 
            else if (c.char === 'MUSIC') { const notas = ['♪', '♫', '♬']; p.innerText = notas[Math.floor(Math.random() * notas.length)]; } 
            else { p.innerText = c.char; }
        }
        
        p.style.setProperty('--tx', `${tx}px`); p.style.setProperty('--ty', `${ty}px`); p.style.setProperty('--duration', `${dur}ms`);
        if (globalAppConfig.particulaStyle === 'sparks' || globalAppConfig.particulaStyle === 'laser') p.style.setProperty('--rot', `${angle}rad`);
        if (globalAppConfig.particulaStyle === 'magic' || globalAppConfig.particulaStyle === 'estrella' || globalAppConfig.particulaStyle === 'sonar') p.style.setProperty('--scale-max', globalAppConfig.particulaStyle === 'sonar' ? `5` : `1.5`);

        fragment.appendChild(p);
        setTimeout(() => { p.remove(); }, dur);
    }
    layer.appendChild(fragment);
};

window.cambiarIntensidadParticulas = function(valorStr) {
    let porcentaje = parseInt(valorStr, 10);
    globalAppConfig.particulaIntensidad = porcentaje / 100;
    localStorage.setItem('cfg_sebas_part_intensidad', globalAppConfig.particulaIntensidad.toString());
    const lbl = document.getElementById('lbl-intensidad-part'); if(lbl) lbl.innerText = porcentaje + '%';
};

window.cambiarTamanoParticulas = function(valorStr) {
    let porcentaje = parseInt(valorStr, 10);
    globalAppConfig.particulaTamano = porcentaje / 100;
    localStorage.setItem('cfg_sebas_part_tamano', globalAppConfig.particulaTamano.toString());
    const lbl = document.getElementById('lbl-tamano-part'); if(lbl) lbl.innerText = porcentaje + '%';
};

window.cambiarVolumenSFX = function(valorStr) {
    let porcentaje = parseInt(valorStr, 10);
    globalAppConfig.volumenSfx = porcentaje / 100;
    localStorage.setItem('cfg_sebas_volumen_sfx', globalAppConfig.volumenSfx.toString());
    const lbl = document.getElementById('lbl-volumen-sfx'); if(lbl) lbl.innerText = porcentaje + '%';
    window.playSynth();
};

window.guardarAjustesNotificaciones = function(element) { globalAppConfig.alertasActivas = element.checked; localStorage.setItem('cfg_sebas_alertas', element.checked ? 'true' : 'false'); window.playSynth(); };
window.guardarAjustesSonidos = function(element) { globalAppConfig.sonidosActivos = element.checked; localStorage.setItem('cfg_sebas_sonidos', element.checked ? 'true' : 'false'); if(element.checked) window.playSynth(); };

window.toggleCustomSelectParticulas = function() {
    window.playSynth();
    const options = document.getElementById('custom-select-particulas-options');
    const icon = document.getElementById('custom-select-particulas-icon');
    if (options.classList.contains('scale-y-0')) { options.classList.remove('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(180deg)'; } 
    else { options.classList.add('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(0deg)'; }
};

window.seleccionarParticulaCustom = function(id, nombre) {
    document.getElementById('custom-select-particulas-label').innerText = nombre;
    window.toggleCustomSelectParticulas();
    localStorage.setItem('cfg_sebas_particulas', id);
    globalAppConfig.particulaStyle = id;
    window.sysNotification("EFECTOS", `Partículas cambiadas a ${nombre}.`, "fa-hand-sparkles");
};

window.toggleCustomSelectSonido = function() {
    window.playSynth();
    const options = document.getElementById('custom-select-sonido-options');
    const icon = document.getElementById('custom-select-sonido-icon');
    if (options.classList.contains('scale-y-0')) { options.classList.remove('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(180deg)'; } 
    else { options.classList.add('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(0deg)'; }
};

window.seleccionarSonidoCustom = function(id, nombre) {
    document.getElementById('custom-select-sonido-label').innerText = nombre;
    window.toggleCustomSelectSonido();
    localStorage.setItem('cfg_sebas_sonido_tactil', id);
    globalAppConfig.sonidoTactil = id;
    window.playSynth(id);
    window.sysNotification("AUDIO", `Tono cambiado a ${nombre}.`, "fa-volume-up");
};

window.toggleCustomSelectNotificaciones = function() {
    window.playSynth();
    const options = document.getElementById('custom-select-notificaciones-options');
    const icon = document.getElementById('custom-select-notificaciones-icon');
    if (options.classList.contains('scale-y-0')) { options.classList.remove('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(180deg)'; } 
    else { options.classList.add('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(0deg)'; }
};

window.seleccionarNotificacionCustom = function(id, nombre) {
    document.getElementById('custom-select-notificaciones-label').innerText = nombre;
    window.toggleCustomSelectNotificaciones();
    localStorage.setItem('cfg_sebas_notificacion_style', id);
    globalAppConfig.notificacionStyle = id;
    window.sysNotification("SISTEMA", `Notificación en modo ${nombre}`, "fa-bell");
};

window.toggleCustomSelectHeader = function() {
    window.playSynth();
    const options = document.getElementById('custom-select-header-options');
    const icon = document.getElementById('custom-select-header-icon');
    if (options.classList.contains('scale-y-0')) { options.classList.remove('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(180deg)'; } 
    else { options.classList.add('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(0deg)'; }
};

window.seleccionarHeaderCustom = function(id, nombre) {
    document.getElementById('custom-select-header-label').innerText = nombre;
    window.toggleCustomSelectHeader();
    localStorage.setItem('cfg_sebas_header_style', id);
    globalAppConfig.headerStyle = id;
    window.aplicarEstiloCabeceraGlobal(id);
    window.actualizarEstadoBotonInicio(globalAppConfig.statusConexion);
    window.sysNotification("SISTEMA", `Cabecera cambiada a ${nombre}`, "fa-heading");
};

window.toggleCustomSelectRadar = function() {
    window.playSynth();
    const options = document.getElementById('custom-select-radar-options');
    const icon = document.getElementById('custom-select-radar-icon');
    if (options.classList.contains('scale-y-0')) { options.classList.remove('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(180deg)'; } 
    else { options.classList.add('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(0deg)'; }
};

window.getScanLayoutHTML = function(style) {
    const bgStart = `<div class="absolute inset-0 w-full h-full flex items-center justify-center overflow-hidden z-0 pointer-events-none rounded-[2rem] opacity-90">`;
    const bgEnd = `</div>`;
    switch(style) {
        case 'anim_1': return bgStart + `<div class="absolute inset-0 opacity-40 bg-[linear-gradient(rgba(0,255,255,0.2)_1px,transparent_1px),linear-gradient(90deg,rgba(0,255,255,0.2)_1px,transparent_1px)] bg-[size:30px_30px]" style="transform: perspective(300px) rotateX(45deg); animation: a-spin-cw 10s linear infinite;"></div><i class="absolute m-auto inset-0 w-fit h-fit fa-solid fa-server text-cyan-400 text-6xl drop-shadow-[0_0_15px_#22d3ee] animate-pulse"></i>` + bgEnd;
        case 'anim_2': return bgStart + `<div class="absolute m-auto inset-0 w-full h-full bg-[radial-gradient(circle,rgba(255,255,255,0.8)_2px,transparent_3px)] bg-[size:40px_40px] opacity-30"></div><div class="absolute m-auto inset-0 w-20 h-20 rounded-full border-[3px] border-purple-500 shadow-[0_0_20px_#a855f7]" style="animation: a-expand-out 2s ease-out infinite;"></div><div class="absolute m-auto inset-0 w-20 h-20 rounded-full border-[3px] border-cyan-400 shadow-[0_0_20px_#22d3ee]" style="animation: a-expand-out 2s ease-out infinite 1s;"></div><i class="absolute m-auto inset-0 w-fit h-fit fa-solid fa-atom text-white/50 text-5xl" style="animation: a-spin-cw 8s linear infinite;"></i>` + bgEnd;
        case 'anim_3': return bgStart + `<div class="absolute m-auto inset-0 flex items-center justify-center"><div class="w-[600px] h-[600px] bg-[conic-gradient(from_0deg,#0ff,#f0f,#0ff)] opacity-40 blur-3xl mix-blend-screen" style="animation: a-spin-cw 3s linear infinite;"></div></div>` + bgEnd;
        case 'anim_4': return bgStart + `<div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(168,85,247,0.6)_2px,transparent_2px)] bg-[size:25px_100%] opacity-80" style="animation: a-scan-h 1.5s linear infinite;"></div><div class="absolute top-1/2 w-full h-[3px] bg-purple-500 shadow-[0_0_20px_#a855f7]"></div>` + bgEnd;
        case 'anim_5': return bgStart + `<div class="absolute m-auto inset-0 flex items-center justify-center"><div class="w-14 h-14 bg-cyan-400 rounded-full shadow-[0_0_50px_#0ff]" style="animation: a-pulse 1s infinite;"></div><div class="w-48 h-48 absolute border-[3px] border-purple-500 rounded-full" style="animation: a-expand-out 2s infinite;"></div><div class="w-48 h-48 absolute border-[3px] border-emerald-400 rounded-full" style="animation: a-expand-out 2s infinite 1s;"></div></div>` + bgEnd;
        case 'anim_6': return bgStart + `<div class="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_0%,rgba(0,0,0,0.8)_100%)] z-10"></div><div class="absolute m-auto inset-0 flex items-center justify-center overflow-hidden"><div class="absolute inset-0 m-auto w-[600px] h-[600px] bg-[conic-gradient(from_0deg,transparent_75%,#10b981_100%)] rounded-full mix-blend-screen" style="animation: a-spin-cw 2s linear infinite;"></div></div>` + bgEnd;
        case 'anim_7': return bgStart + `<div class="absolute inset-0 opacity-50 bg-[linear-gradient(30deg,rgba(168,85,247,0.5)_12%,transparent_12.5%,transparent_87%,rgba(168,85,247,0.5)_87.5%,rgba(168,85,247,0.5)),linear-gradient(150deg,rgba(168,85,247,0.5)_12%,transparent_12.5%,transparent_87%,rgba(168,85,247,0.5)_87.5%,rgba(168,85,247,0.5)),linear-gradient(30deg,rgba(168,85,247,0.5)_12%,transparent_12.5%,transparent_87%,rgba(168,85,247,0.5)_87.5%,rgba(168,85,247,0.5)),linear-gradient(150deg,rgba(168,85,247,0.5)_12%,transparent_12.5%,transparent_87%,rgba(168,85,247,0.5)_87.5%,rgba(168,85,247,0.5)),linear-gradient(60deg,rgba(168,85,247,0.5)_25%,transparent_25.5%,transparent_75%,rgba(168,85,247,0.5)_75%,rgba(168,85,247,0.5)),linear-gradient(60deg,rgba(168,85,247,0.5)_25%,transparent_25.5%,transparent_75%,rgba(168,85,247,0.5)_75%,rgba(168,85,247,0.5))] bg-[size:40px_70px]" style="background-position: 0 0, 0 0, 20px 35px, 20px 35px, 0 0, 20px 35px; animation: a-bg-shift 4s linear infinite;"></div>` + bgEnd;
        case 'anim_8': return bgStart + `<div class="absolute bottom-0 w-full h-[80px] flex items-end justify-center px-10 gap-3 opacity-70"><div class="w-full bg-cyan-400" style="animation: a-eq-bounce 0.3s ease-in-out infinite alternate;"></div><div class="w-full bg-purple-500" style="animation: a-eq-bounce 0.5s ease-in-out infinite alternate;"></div><div class="w-full bg-emerald-400" style="animation: a-eq-bounce 0.4s ease-in-out infinite alternate;"></div><div class="w-full bg-cyan-400" style="animation: a-eq-bounce 0.6s ease-in-out infinite alternate;"></div><div class="w-full bg-purple-500" style="animation: a-eq-bounce 0.35s ease-in-out infinite alternate;"></div></div>` + bgEnd;
        case 'anim_9': return bgStart + `<div class="absolute m-auto inset-0 flex items-center justify-center"><div class="absolute border-4 border-cyan-400 shadow-[0_0_30px_#0ff]" style="width:25px; height:25px; animation: a-expand-out 1s linear infinite;"></div><div class="absolute border-4 border-purple-500 shadow-[0_0_30px_#a855f7]" style="width:25px; height:25px; animation: a-expand-out 1s linear infinite 0.5s;"></div></div><div class="absolute inset-0 bg-cyan-900/30 mix-blend-screen"></div>` + bgEnd;
        case 'anim_10': return bgStart + `<div class="absolute inset-0 bg-red-500/30 mix-blend-screen" style="animation: a-glitch 0.15s infinite;"></div><div class="absolute inset-0 bg-[linear-gradient(transparent_50%,rgba(0,0,0,0.8)_50%)] bg-[size:100%_4px]"></div>` + bgEnd;
        case 'anim_11': return bgStart + `<div class="absolute inset-0 bg-[linear-gradient(rgba(16,185,129,0.5)_2px,transparent_2px)] bg-[size:100%_20px]" style="animation: a-matrix-fall 1s linear infinite;"></div>` + bgEnd;
        case 'anim_12': return bgStart + `<div class="absolute m-auto inset-0 flex items-center justify-center"><div class="w-full h-4 bg-gradient-to-r from-transparent via-cyan-400 to-transparent" style="animation: a-wave-x 2s ease infinite alternate;"></div><div class="w-full h-4 absolute bg-gradient-to-r from-transparent via-purple-500 to-transparent" style="animation: a-wave-x 2s ease infinite alternate-reverse;"></div></div>` + bgEnd;
        case 'anim_13': return bgStart + `<div class="absolute inset-0 bg-[radial-gradient(circle,rgba(255,255,255,0.8)_2px,transparent_3px)] bg-[size:50px_50px] opacity-70" style="animation: a-scan-h 4s linear infinite;"></div>` + bgEnd;
        case 'anim_14': return bgStart + `<div class="absolute left-0 w-full h-3 bg-red-500 shadow-[0_0_30px_red]" style="animation: a-scan-v 1.5s ease-in-out infinite;"></div>` + bgEnd;
        case 'anim_15': return bgStart + `<div class="absolute inset-0 border-[10px] border-cyan-400/50 mix-blend-screen" style="animation: a-pulse 0.5s infinite;"></div><div class="absolute inset-0 bg-cyan-900/40"></div>` + bgEnd;
        case 'anim_16': return bgStart + `<div class="absolute m-auto inset-0 flex items-center justify-center"><div class="absolute w-12 h-12 rounded-full border-4 border-emerald-400 shadow-[0_0_20px_#10b981]" style="animation: a-expand-out 2s infinite ease-out;"></div><div class="absolute w-12 h-12 rounded-full border-4 border-emerald-400 shadow-[0_0_20px_#10b981]" style="animation: a-expand-out 2s infinite ease-out 0.6s;"></div></div>` + bgEnd;
        case 'anim_17': return bgStart + `<div class="absolute m-auto inset-0 flex items-center justify-center"><div class="w-[300px] h-[300px] border-[4px] border-dashed border-cyan-500 rounded-full shadow-[0_0_30px_#0ff]" style="transform: perspective(300px) rotateX(70deg); animation: a-spin-cw 5s linear infinite;"></div></div>` + bgEnd;
        case 'anim_18': return bgStart + `<div class="absolute bottom-0 w-full h-full bg-gradient-to-t from-pink-500/80 via-orange-400/40 to-transparent"></div><div class="absolute inset-0 bg-[linear-gradient(transparent_80%,rgba(0,0,0,0.5)_80%)] bg-[size:100%_10px]" style="animation: a-scan-v 1s linear infinite;"></div>` + bgEnd;
        case 'anim_19': return bgStart + `<div class="absolute m-auto inset-0 flex items-center justify-center"><div class="w-40 h-40 bg-amber-400 rounded-full blur-3xl opacity-80" style="animation: a-pulse 1s infinite alternate;"></div></div>` + bgEnd;
        case 'anim_20': return bgStart + `<div class="absolute bottom-0 w-full h-[50px] bg-cyan-500/60 blur-md rounded-[100%]" style="animation: a-pulse 2s infinite alternate;"></div>` + bgEnd;
        default: return bgStart + `<div class="absolute inset-0 opacity-50 bg-[linear-gradient(rgba(0,212,255,0.4)_2px,transparent_2px),linear-gradient(90deg,rgba(0,212,255,0.4)_2px,transparent_2px)] bg-[size:30px_30px]"></div>` + bgEnd;
    }
};

window.seleccionarRadarCustom = function(id, nombre) {
    document.getElementById('custom-select-radar-label').innerText = nombre;
    window.toggleCustomSelectRadar();
    localStorage.setItem('cfg_sebas_radar_style', id);
    globalAppConfig.radarStyle = id;
    window.sysNotification("AJUSTES", `Escáner ${nombre} guardado.`, "fa-satellite-dish");
};

window.previewRadarActual = function() {
    window.playSynth('select');
    let id = globalAppConfig.radarStyle;
    let oldOverlay = document.getElementById('popup-radar-preview');
    if(oldOverlay) oldOverlay.remove();

    let overlay = document.createElement('div');
    overlay.id = 'popup-radar-preview';
    overlay.className = 'fixed inset-0 z-[999999] bg-[#02040a]/90 backdrop-blur-md flex flex-col items-center justify-center transition-opacity duration-300 opacity-0';
    
    let animHTML = window.getScanLayoutHTML(id);

    overlay.innerHTML = `
        <div class="relative w-[90%] max-w-[400px] h-[160px] rounded-[2rem] border border-cyan-500/30 bg-[#060913] shadow-[0_0_40px_rgba(34,211,238,0.3)] overflow-hidden flex items-center justify-center">
            ${animHTML}
            <div class="absolute z-20 flex flex-col items-center justify-center w-full h-full pointer-events-none">
                <div class="bg-[#060913]/90 backdrop-blur-md px-6 py-3 rounded-2xl border border-white/10 shadow-[0_0_30px_rgba(0,0,0,0.8)] flex flex-col items-center">
                    <h3 class="text-[12px] font-black uppercase tracking-widest text-cyan-400 drop-shadow-md">VISTA PREVIA</h3>
                    <p class="text-[10px] font-mono text-white mt-1 tracking-widest uppercase">Escáner Activo</p>
                </div>
            </div>
        </div>
        <div class="mt-8 w-32 h-1 bg-gray-800 rounded-full overflow-hidden">
            <div class="h-full w-full bg-cyan-400 origin-left" style="animation: prevScaleX 2s linear forwards;"></div>
        </div>
        <style>@keyframes prevScaleX { 0% { transform: scaleX(0); } 100% { transform: scaleX(1); } }</style>
    `;
    
    document.body.appendChild(overlay);
    void overlay.offsetWidth;
    overlay.classList.remove('opacity-0');
    
    if(window.radarPrevTimeout) clearTimeout(window.radarPrevTimeout);
    window.radarPrevTimeout = setTimeout(() => { 
        overlay.classList.add('opacity-0'); 
        setTimeout(() => overlay.remove(), 300); 
    }, 2000);
};

window.toggleCustomSelect = function() {
    window.playSynth();
    const options = document.getElementById('custom-select-options');
    const icon = document.getElementById('custom-select-icon');
    if (options.classList.contains('scale-y-0')) { options.classList.remove('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(180deg)'; } 
    else { options.classList.add('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(0deg)'; }
};

window.seleccionarFondoCustom = function(idFondo, nombreVisible) {
    document.getElementById('custom-select-label').innerText = nombreVisible;
    window.toggleCustomSelect();
    if (typeof changeDynamicWallpaper === 'function') { changeDynamicWallpaper(idFondo); window.sysNotification("SISTEMA VISUAL", `Fondo dinámico cargado correctamente.`, "fa-desktop"); }
};

window.toggleCustomSelectIntro = function() {
    window.playSynth();
    const options = document.getElementById('custom-select-intro-options');
    const icon = document.getElementById('custom-select-intro-icon');
    if (options.classList.contains('scale-y-0')) { options.classList.remove('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(180deg)'; } 
    else { options.classList.add('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(0deg)'; }
};

window.seleccionarIntroCustom = function(idIntro, nombreVisible) {
    document.getElementById('custom-select-intro-label').innerText = nombreVisible;
    window.toggleCustomSelectIntro();
    localStorage.setItem('ps4_selected_intro', idIntro);
    window.sysNotification("AJUSTES", `Intro ${nombreVisible} guardada.`, "fa-play");
};

window.previewIntroActual = function() {
    window.playSynth('select');
    const idIntro = localStorage.getItem('ps4_selected_intro') || 'none';
    const nombreVisible = introNamesMap[idIntro] || 'Intro';

    if (idIntro === 'none') {
        window.sysNotification("INFO", "No hay intro seleccionada.", "fa-info");
        return;
    }

    let wrap = document.getElementById('intro-wrapper');
    if (!wrap) {
        wrap = document.createElement('div');
        wrap.id = 'intro-wrapper';
        wrap.className = 'fixed inset-0 z-[9999] bg-[#05050a] flex items-center justify-center';
        document.body.appendChild(wrap);
    }
    
    wrap.style.display = 'flex';
    wrap.style.zIndex = '999999';

    const allIntros = document.querySelectorAll('.intro-container');
    allIntros.forEach(i => {
        i.classList.remove('active');
        i.style.opacity = '0';
        i.style.display = 'none';
    });

    const intro = document.getElementById(idIntro);
    if(intro) {
        intro.style.display = 'flex';
        intro.classList.add('active');
        intro.style.opacity = '1';

        if (idIntro === 'intro-ps5') { if(typeof generatePS5Particles === 'function') generatePS5Particles(); }
        if (idIntro === 'intro-ps2') { if(typeof generatePS2CubesIntro === 'function') generatePS2CubesIntro(); }
        if (idIntro === 'intro-decrypt') { if(typeof runDecryptEffect === 'function') runDecryptEffect(); }
        if (idIntro === 'intro-matrix-rain') { if(typeof initMatrixIntro === 'function') initMatrixIntro(); }
        if (idIntro === 'intro-crt') { if(typeof runCRTScript === 'function') runCRTScript(); }
        if (idIntro === 'intro-breach') { if(typeof runBreach === 'function') runBreach(); }
        
        let indicator = document.getElementById('preview-indicator');
        if(!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'preview-indicator';
            indicator.className = 'absolute bottom-8 left-1/2 transform -translate-x-1/2 bg-black/60 backdrop-blur-md px-4 py-2 rounded-full border border-white/20 z-[9999999] pointer-events-none flex flex-col items-center shadow-lg';
            indicator.innerHTML = `
                <span class="text-[9px] font-black tracking-widest uppercase text-cyan-400">${nombreVisible}</span>
                <span class="text-[7px] font-mono text-gray-300 mt-0.5 uppercase tracking-wider">VISTA PREVIA</span>
                <div class="mt-2 w-16 h-0.5 bg-gray-800 rounded-full overflow-hidden">
                    <div class="h-full w-full bg-cyan-400 origin-left" style="animation: prevScaleX 5.5s linear forwards;"></div>
                </div>
                <style>@keyframes prevScaleX { 0% { transform: scaleX(0); } 100% { transform: scaleX(1); } }</style>
            `;
            wrap.appendChild(indicator);
        }

        setTimeout(() => {
            intro.style.opacity = '0';
            if (typeof currentIntroInterval !== 'undefined' && currentIntroInterval) clearInterval(currentIntroInterval);
            if (indicator) indicator.remove();
            setTimeout(() => { wrap.style.display = 'none'; intro.style.display = 'none'; }, 500);
        }, 5500);
    }
};

window.toggleCustomSelectLauncher = function() {
    window.playSynth();
    const options = document.getElementById('custom-select-launcher-options');
    const icon = document.getElementById('custom-select-launcher-icon');
    if (options.classList.contains('scale-y-0')) { options.classList.remove('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(180deg)'; } 
    else { options.classList.add('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(0deg)'; }
};

window.seleccionarDisenoLauncher = function(id, nombreVisible) {
    document.getElementById('custom-select-launcher-label').innerText = nombreVisible;
    window.toggleCustomSelectLauncher();
    localStorage.setItem('cfg_sebas_launcher_layout', id);
    globalAppConfig.launcherLayout = id;
    window.renderizarDisenoLauncher(id);
    window.sysNotification("AJUSTES", `Diseño de menú actualizado.`, "fa-layer-group");
};

window.toggleCustomSelectBiblio = function() {
    window.playSynth();
    const options = document.getElementById('custom-select-biblio-options');
    const icon = document.getElementById('custom-select-biblio-icon');
    if (options.classList.contains('scale-y-0')) { options.classList.remove('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(180deg)'; } 
    else { options.classList.add('scale-y-0', 'opacity-0', 'pointer-events-none'); icon.style.transform = 'rotate(0deg)'; }
};

window.seleccionarDisenoBiblioteca = function(id, nombreVisible) {
    document.getElementById('custom-select-biblio-label').innerText = nombreVisible;
    window.toggleCustomSelectBiblio();
    localStorage.setItem('cfg_sebas_biblio_theme', id);
    globalAppConfig.biblioTheme = id;
    if (typeof window.aplicarTemaBiblioteca === 'function') window.aplicarTemaBiblioteca();
    window.sysNotification("AJUSTES", `Biblioteca actualizada a ${nombreVisible}.`, "fa-swatchbook");
};

window.renderizarDisenoLauncher = function(id) {
    const container = document.getElementById('launcher-design-container');
    if (!container) return;
    const modulos = [
        { id: 'biblioteca', nombre: 'Biblioteca', desc: 'Juegos e Iconos', icon: 'fa-gamepad', nativo: false },
        { id: 'explorador', nombre: 'Explorador', desc: 'Raíz Consola', icon: 'fa-folder-open', nativo: false },
        { id: 'modding', nombre: 'Modding', desc: 'Inyectar Portadas', icon: 'fa-wand-magic-sparkles', nativo: false },
        { id: 'transferir', nombre: 'Transferir', desc: 'Gestores y FTP', icon: 'fa-cloud-arrow-up', nativo: false },
        { id: 'mods', nombre: 'Game Mods', desc: 'Trucos y Parches', icon: 'fa-cubes', nativo: true },
        { id: 'ajustes', nombre: 'Ajustes', desc: 'Live BGs e Intros', icon: 'fa-sliders', nativo: false }
    ];
    const s = {
        'biblioteca': { txt: 'text-cyan-400', bg: 'bg-cyan-900/30', border: 'border-cyan-500/10', drop: 'drop-shadow-[0_0_8px_#22d3ee]', hex: '#22d3ee', hoverBg: 'hover:bg-cyan-900/20', lightBg: 'bg-cyan-500/10', holo: 'bg-cyan-500/20' },
        'explorador': { txt: 'text-emerald-400', bg: 'bg-emerald-900/30', border: 'border-emerald-500/10', drop: 'drop-shadow-[0_0_8px_#34d399]', hex: '#10b981', hoverBg: 'hover:bg-emerald-900/20', lightBg: 'bg-emerald-500/10', holo: 'bg-emerald-500/20' },
        'modding': { txt: 'text-purple-400', bg: 'bg-purple-900/30', border: 'border-purple-500/10', drop: 'drop-shadow-[0_0_8px_#a855f7]', hex: '#a855f7', hoverBg: 'hover:bg-purple-900/20', lightBg: 'bg-purple-500/10', holo: 'bg-purple-500/20' },
        'transferir': { txt: 'text-amber-400', bg: 'bg-amber-900/30', border: 'border-amber-500/10', drop: 'drop-shadow-[0_0_8px_#fbbf24]', hex: '#f59e0b', hoverBg: 'hover:bg-amber-900/20', lightBg: 'bg-amber-500/10', holo: 'bg-amber-500/20' },
        'mods': { txt: 'text-indigo-400', bg: 'bg-indigo-900/30', border: 'border-indigo-500/10', drop: 'drop-shadow-[0_0_8px_#818cf8]', hex: '#6366f1', hoverBg: 'hover:bg-indigo-900/20', lightBg: 'bg-indigo-500/10', holo: 'bg-indigo-500/20' },
        'ajustes': { txt: 'text-gray-300', bg: 'bg-gray-700/30', border: 'border-gray-500/10', drop: 'drop-shadow-[0_0_8px_#9ca3af]', hex: '#9ca3af', hoverBg: 'hover:bg-gray-800/50', lightBg: 'bg-gray-500/10', holo: 'bg-gray-500/20' }
    };
    const isList = id.includes('-list');
    const themeNum = id.split('-')[0];
    let html = isList ? `<div class="flex flex-col gap-3 md:gap-5 w-full max-w-3xl mx-auto pb-8">` : `<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6 lg:gap-8 w-full max-w-6xl mx-auto pb-8">`;

    modulos.forEach(m => {
        let action = m.nativo ? `window.abrirModuloNativo('${m.id}')` : `window.abrirModulo('${m.id}')`;
        let c = s[m.id];
        
        if (isList) {
            switch(themeNum) {
                case '0': html += `<div onclick="${action}" class="launcher-card p-4 md:p-6 rounded-2xl md:rounded-3xl flex items-center gap-4 md:gap-6 w-full cursor-pointer tactile-fx"><div class="w-10 h-10 md:w-16 md:h-16 rounded-xl md:rounded-2xl ${c.bg} ${c.txt} flex items-center justify-center border ${c.border} shrink-0"><i class="fa-solid ${m.icon} text-lg md:text-2xl"></i></div><div class="flex flex-col flex-1"><span class="text-[12px] md:text-base font-black tracking-wider uppercase text-white">${m.nombre}</span><span class="text-[9px] md:text-xs font-bold tracking-widest text-gray-500 uppercase mt-0.5 md:mt-1">${m.desc}</span></div><i class="fa-solid fa-chevron-right text-gray-600 text-xs md:text-lg"></i></div>`; break;
                case '1': html += `<div onclick="${action}" class="glass-panel p-4 md:p-6 rounded-2xl md:rounded-3xl flex items-center gap-4 md:gap-6 w-full cursor-pointer tactile-fx"><div class="w-10 h-10 md:w-16 md:h-16 rounded-full bg-white/5 flex items-center justify-center text-white shrink-0"><i class="fa-solid ${m.icon} ${c.txt} md:text-2xl"></i></div><div class="flex flex-col flex-1"><span class="text-[12px] md:text-base font-black tracking-widest uppercase text-white">${m.nombre}</span><span class="text-[9px] md:text-xs text-gray-400 uppercase mt-0.5 md:mt-1">${m.desc}</span></div><i class="fa-solid fa-caret-right text-gray-500 text-sm md:text-xl"></i></div>`; break;
                case '2': html += `<div onclick="${action}" class="cyber-border bg-[#0a0a12] p-4 md:p-6 rounded-xl md:rounded-2xl flex items-center gap-4 md:gap-6 w-full cursor-pointer tactile-fx ${c.hoverBg}"><i class="fa-solid ${m.icon} ${c.txt} text-xl md:text-3xl ${c.drop} shrink-0"></i><div class="flex flex-col flex-1"><span class="text-[11px] md:text-[15px] font-black tracking-widest uppercase text-white">${m.nombre}</span><span class="text-[8px] md:text-[10px] font-mono ${c.txt} mt-1 md:mt-2 uppercase">SISTEMA ACTIVO</span></div></div>`; break;
                case '3': html += `<div onclick="${action}" class="bg-[#1a1d24] p-4 md:p-6 rounded-2xl md:rounded-3xl flex items-center gap-4 md:gap-6 w-full cursor-pointer tactile-fx border border-transparent hover:border-white/5"><div class="w-10 h-10 md:w-16 md:h-16 rounded-full ${c.lightBg} flex items-center justify-center ${c.txt} shrink-0"><i class="fa-solid ${m.icon} md:text-2xl"></i></div><div class="flex flex-col flex-1"><span class="text-[12px] md:text-base font-bold text-white">${m.nombre}</span><span class="text-[9px] md:text-xs text-gray-500 uppercase mt-0.5 md:mt-1">${m.desc}</span></div><i class="fa-solid fa-chevron-right text-gray-600 text-xs md:text-lg"></i></div>`; break;
                case '4': html += `<div onclick="${action}" class="neu-card p-4 md:p-6 flex items-center gap-4 md:gap-6 w-full cursor-pointer tactile-fx"><div class="w-10 h-10 md:w-16 md:h-16 flex items-center justify-center shrink-0"><i class="fa-solid ${m.icon} ${c.txt} text-xl md:text-3xl opacity-80"></i></div><div class="flex flex-col flex-1"><span class="text-[11px] md:text-[15px] font-black tracking-widest uppercase text-gray-300">${m.nombre}</span><span class="text-[9px] md:text-xs font-bold text-gray-500 mt-0.5 md:mt-1">${m.desc}</span></div></div>`; break;
                case '5': html += `<div onclick="${action}" class="holo-card p-4 md:p-6 rounded-2xl md:rounded-3xl flex items-center gap-4 md:gap-6 w-full cursor-pointer relative overflow-hidden tactile-fx" style="border-color: ${c.hex}40;"><div class="absolute -right-4 -top-4 w-12 h-12 md:w-20 md:h-20 ${c.holo} rounded-full blur-xl md:blur-2xl pointer-events-none"></div><div class="w-10 h-10 md:w-16 md:h-16 rounded-xl md:rounded-2xl ${c.holo} border border-white/10 flex items-center justify-center shadow-[0_0_15px_${c.hex}33] shrink-0 transform translate-z-10"><i class="fa-solid ${m.icon} ${c.txt} text-lg md:text-2xl drop-shadow-md"></i></div><div class="flex flex-col flex-1 transform translate-z-10"><span class="text-[12px] md:text-base font-black tracking-widest text-white uppercase drop-shadow-md">${m.nombre}</span><span class="text-[9px] md:text-xs font-mono ${c.txt} mt-0.5 md:mt-1 opacity-80">${m.desc}</span></div></div>`; break;
                case '6': html += `<div onclick="${action}" class="mech-btn p-4 md:p-6 rounded-[1rem] md:rounded-[1.5rem] flex items-center gap-4 md:gap-6 w-full cursor-pointer border-b-[4px] md:border-b-[6px] tactile-fx" style="border-bottom-color: ${c.hex};"><i class="fa-solid ${m.icon} ${c.txt} text-xl md:text-3xl drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)] shrink-0 pl-2"></i><div class="flex flex-col flex-1"><span class="text-[11px] md:text-[15px] font-black tracking-widest uppercase text-white drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)]">${m.nombre}</span><span class="text-[8px] md:text-[10px] font-bold text-gray-400 mt-0.5 md:mt-1 uppercase tracking-widest">${m.desc}</span></div></div>`; break;
                case '7': html += `<div onclick="${action}" class="aura-card p-4 md:p-6 rounded-[1.2rem] md:rounded-[1.8rem] flex items-center gap-4 md:gap-6 w-full cursor-pointer tactile-fx"><div class="w-10 h-10 md:w-16 md:h-16 rounded-full bg-[#111827] flex items-center justify-center ${c.txt} shadow-inner shrink-0"><i class="fa-solid ${m.icon} text-lg md:text-2xl"></i></div><div class="flex flex-col flex-1"><span class="text-[11px] md:text-[15px] font-black uppercase text-white tracking-widest">${m.nombre}</span><span class="text-[8px] md:text-[10px] text-gray-400 mt-1 md:mt-2 uppercase">${m.desc}</span></div></div>`; break;
                case '8': html += `<div onclick="${action}" class="angle-cut p-4 md:p-6 flex items-center gap-4 md:gap-6 w-full cursor-pointer border-l-4 md:border-l-8 tactile-fx" style="border-left-color: ${c.hex};"><i class="fa-solid ${m.icon} ${c.txt} text-xl md:text-3xl shrink-0"></i><div class="flex flex-col flex-1"><span class="text-[11px] md:text-[15px] font-black tracking-widest uppercase text-white">${m.nombre}_SYS</span></div></div>`; break;
                case '9': html += `<div onclick="${action}" class="brutal-cyber p-4 md:p-6 flex items-center justify-between w-full cursor-pointer tactile-fx" style="box-shadow: 4px 4px 0px ${c.hex};"><span class="text-[12px] md:text-[16px] font-black tracking-widest text-white">${m.nombre}</span><i class="fa-solid ${m.icon} ${c.txt} text-xl md:text-3xl"></i></div>`; break;
                case '10': html += `<div onclick="${action}" class="acrylic-card p-4 md:p-6 rounded-[1.2rem] md:rounded-[1.8rem] flex items-center gap-4 md:gap-6 w-full cursor-pointer tactile-fx"><div class="w-10 h-10 md:w-16 md:h-16 rounded-full bg-white/20 flex items-center justify-center text-white shrink-0"><i class="fa-solid ${m.icon} drop-shadow-md md:text-2xl"></i></div><div class="flex flex-col flex-1"><span class="text-[11px] md:text-[15px] font-black tracking-widest uppercase text-white drop-shadow-md">${m.nombre}</span><span class="text-[8px] md:text-[10px] text-gray-300 mt-1 md:mt-2 uppercase drop-shadow-md">${m.desc}</span></div></div>`; break;
            }
        } else {
            switch(themeNum) {
                case '0': html += `<div onclick="${action}" class="launcher-card p-5 md:p-8 rounded-[2rem] md:rounded-[2.5rem] flex flex-col items-center justify-center cursor-pointer aspect-square tactile-fx border border-white/5 bg-[#0a0d18]/55"><div class="w-12 h-12 md:w-20 md:h-20 rounded-[1rem] md:rounded-[1.5rem] ${c.bg} ${c.txt} flex items-center justify-center mb-3 md:mb-5 border ${c.border}"><i class="fa-solid ${m.icon} text-xl md:text-4xl"></i></div><span class="text-xs md:text-base font-black tracking-wider uppercase text-white">${m.nombre}</span><span class="text-[8px] md:text-[11px] font-bold tracking-widest text-gray-500 uppercase mt-1 md:mt-2 text-center">${m.desc}</span></div>`; break;
                case '1': html += `<div onclick="${action}" class="glass-panel rounded-3xl md:rounded-[2.5rem] p-5 md:p-8 flex flex-col items-center justify-center gap-3 md:gap-5 tactile-fx cursor-pointer aspect-square"><div class="w-12 h-12 md:w-20 md:h-20 rounded-full bg-white/5 flex items-center justify-center text-white text-xl md:text-4xl"><i class="fa-solid ${m.icon} ${c.txt}"></i></div><span class="text-[10px] md:text-sm font-bold tracking-widest uppercase text-center">${m.nombre}</span></div>`; break;
                case '2': html += `<div onclick="${action}" class="cyber-border rounded-2xl md:rounded-[2rem] p-5 md:p-8 flex flex-col items-center justify-center gap-4 md:gap-6 bg-[#0a0a12] tactile-fx cursor-pointer ${c.hoverBg} aspect-square"><i class="fa-solid ${m.icon} ${c.txt} text-3xl md:text-5xl ${c.drop}"></i><span class="text-[9px] md:text-xs font-black tracking-widest uppercase text-white text-center">${m.nombre}</span></div>`; break;
                case '3': html += `<div onclick="${action}" class="bg-[#1a1d24] p-5 md:p-8 rounded-3xl md:rounded-[2.5rem] flex flex-col items-center justify-center gap-3 md:gap-5 border border-transparent hover:border-white/5 cursor-pointer tactile-fx aspect-square"><div class="w-12 h-12 md:w-20 md:h-20 rounded-full ${c.lightBg} flex items-center justify-center ${c.txt} mb-1 md:mb-2"><i class="fa-solid ${m.icon} text-xl md:text-4xl"></i></div><span class="text-[11px] md:text-[15px] font-bold text-white uppercase tracking-widest text-center">${m.nombre}</span></div>`; break;
                case '4': html += `<div onclick="${action}" class="neu-card p-5 md:p-8 flex flex-col items-center justify-center gap-4 md:gap-6 cursor-pointer tactile-fx aspect-square"><i class="fa-solid ${m.icon} ${c.txt} text-3xl md:text-5xl opacity-80"></i><span class="text-[10px] md:text-[13px] font-black tracking-widest uppercase text-gray-400 text-center">${m.nombre}</span></div>`; break;
                case '5': 
                    let cCol = 'card-cyan';
                    if(m.id==='explorador') cCol = 'card-green';
                    else if(m.id==='modding') cCol = 'card-purple';
                    else if(m.id==='transferir') cCol = 'card-yellow';
                    else if(m.id==='mods' || m.id==='game_mods') cCol = 'card-indigo';
                    else if(m.id==='ajustes') cCol = 'card-grey';
                    let actionDelay = `window.abrirModuloRetrasado('${m.id}', ${m.nativo}, this)`;
                    html += `<div onclick="${actionDelay}" class="cyber-card ${cCol} tactile-fx"><div class="card-mask"></div><div class="card-inner"><div class="icon-box md:w-20 md:h-20 md:rounded-2xl"><i class="fa-solid ${m.icon} md:text-4xl"></i></div><span class="text-[12px] md:text-base font-black tracking-widest text-white uppercase mt-2 md:mt-4 text-center drop-shadow-md z-10 relative pointer-events-none">${m.nombre}</span></div></div>`; 
                    break;
                case '6': html += `<div onclick="${action}" class="mech-btn p-5 md:p-8 flex flex-col items-center justify-center gap-3 md:gap-5 cursor-pointer aspect-square tactile-fx" style="border-bottom-color: ${c.hex}; border-bottom-width: 6px;"><i class="fa-solid ${m.icon} ${c.txt} text-3xl md:text-5xl drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)] mt-1 mb-1"></i><span class="text-[10px] md:text-sm font-black tracking-widest uppercase text-white drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)] text-center">${m.nombre}</span></div>`; break;
                case '7': html += `<div onclick="${action}" class="aura-card p-5 md:p-8 flex flex-col items-center justify-center gap-4 md:gap-6 cursor-pointer aspect-square tactile-fx"><div class="w-12 h-12 md:w-20 md:h-20 rounded-full bg-[#111827] flex items-center justify-center ${c.txt} shadow-inner"><i class="fa-solid ${m.icon} text-xl md:text-4xl"></i></div><span class="text-[10px] md:text-[13px] font-black uppercase text-white tracking-widest text-center">${m.nombre}</span></div>`; break;
                case '8': html += `<div onclick="${action}" class="angle-cut p-5 md:p-8 flex flex-col items-center justify-center gap-4 md:gap-6 cursor-pointer border-b-4 md:border-b-[6px] aspect-square tactile-fx" style="border-bottom-color: ${c.hex};"><i class="fa-solid ${m.icon} ${c.txt} text-3xl md:text-5xl"></i><span class="text-[9px] md:text-xs font-black tracking-widest uppercase text-white text-center">${m.nombre}_SYS</span></div>`; break;
                case '9': html += `<div onclick="${action}" class="brutal-cyber p-5 md:p-8 flex flex-col items-center justify-center gap-4 md:gap-6 cursor-pointer aspect-square tactile-fx" style="box-shadow: 4px 4px 0px ${c.hex};"><i class="fa-solid ${m.icon} ${c.txt} text-3xl md:text-5xl"></i><span class="text-[11px] md:text-[15px] font-black tracking-widest text-white text-center">${m.nombre}</span></div>`; break;
                case '10': html += `<div onclick="${action}" class="acrylic-card p-5 md:p-8 flex flex-col items-center justify-center gap-4 md:gap-6 cursor-pointer aspect-square tactile-fx"><i class="fa-solid ${m.icon} text-white text-3xl md:text-5xl drop-shadow-md"></i><span class="text-[10px] md:text-[14px] font-black tracking-widest uppercase text-white drop-shadow-md text-center">${m.nombre}</span></div>`; break;
            }
        }
    });
    html += `</div>`;
    container.innerHTML = html;
};

window.actualizarEstadoBotonInicio = function(conectado) {
    const btn = document.getElementById('btn-iniciar-principal');
    const lblStatus = document.getElementById('console-status-label');
    const pingIndicator = document.getElementById('connection-ping-indicator');

    if (btn) {
        if(conectado) {
            btn.classList.add('connected');
            btn.innerHTML = `<i class="fa-solid fa-check-circle text-current text-[12px] md:text-[14px] drop-shadow-[0_0_8px_currentColor]"></i><span class="text-[11px] md:text-[13px] font-black tracking-widest text-current uppercase drop-shadow-[0_0_8px_currentColor] mt-0.5 md:mt-1">CONECTADO</span>`;
        } else {
            btn.classList.remove('connected');
            btn.innerHTML = `<i class="fa-solid fa-power-off text-current text-[11px] md:text-[13px] icon-btn"></i><span class="text-[11px] md:text-[13px] font-black tracking-widest uppercase text-btn mt-0.5 md:mt-1">CONECTAR</span>`;
        }
    }
    
    if (conectado) {
        if (lblStatus) { lblStatus.innerText = "CONECTADO"; lblStatus.className = "text-[9px] md:text-[11px] font-mono text-emerald-400 font-bold uppercase"; }
        if (pingIndicator) { pingIndicator.className = "w-2 h-2 md:w-3 md:h-3 bg-emerald-500 rounded-full shadow-[0_0_8px_#10b981]"; }
    } else {
        if (lblStatus) { lblStatus.innerText = "DESCONECTADO"; lblStatus.className = "text-[9px] md:text-[11px] font-mono text-red-400 font-bold uppercase"; }
        if (pingIndicator) { pingIndicator.className = "w-2 h-2 md:w-3 md:h-3 bg-red-500 rounded-full shadow-[0_0_8px_#ef4444]"; }
    }
};

window.abrirPanelConexion = function() {
    window.playSynth('select');
    const normal = document.getElementById('header-estado-normal');
    const inputs = document.getElementById('header-estado-inputs');
    if(!normal || !inputs) return;
    normal.classList.add('opacity-0', 'pointer-events-none');
    setTimeout(() => {
        normal.classList.add('hidden'); normal.classList.remove('flex');
        inputs.classList.remove('hidden'); inputs.classList.add('flex');
        setTimeout(() => { inputs.classList.remove('opacity-0', 'scale-95', 'pointer-events-none'); }, 50);
    }, 300);
};

window.cerrarPanelConexion = function() {
    window.playSynth('select');
    const normal = document.getElementById('header-estado-normal');
    const inputs = document.getElementById('header-estado-inputs');
    const scanner = document.getElementById('header-estado-scanner');
    if(inputs) inputs.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
    if(scanner) scanner.classList.add('opacity-0', 'pointer-events-none');
    setTimeout(() => {
        if(inputs) { inputs.classList.add('hidden'); inputs.classList.remove('flex'); }
        if(scanner) { scanner.classList.add('hidden'); scanner.classList.remove('flex'); }
        if(normal) {
            normal.classList.remove('hidden'); normal.classList.add('flex');
            normal.classList.remove('pointer-events-none');
            setTimeout(() => normal.classList.remove('opacity-0'), 50);
        }
    }, 300);
};

window.conectarManual = async function(e) {
    if(e) e.preventDefault();
    window.playSynth('select');
    
    const btn = e.currentTarget;
    if (!btn.dataset.origHtml) btn.dataset.origHtml = btn.innerHTML;
    
    btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-2"></i> ENLAZANDO`;
    btn.classList.add('opacity-80', 'pointer-events-none');
    
    try {
        const inputIP = document.getElementById('ps-ip-full-input');
        const inputPort = document.getElementById('ps-port-input');
        const ip = inputIP ? inputIP.value.trim() : globalAppConfig.ipConsola;
        const port = inputPort ? inputPort.value.trim() : globalAppConfig.portFTP;
        
        if (!window.validarEstructuraIP(ip)) {
            window.sysNotification("ERROR", "IP inválida.", "fa-circle-xmark");
            btn.innerHTML = btn.dataset.origHtml;
            btn.classList.remove('opacity-80', 'pointer-events-none');
            return;
        }
        
        localStorage.setItem('sebas_ip_final_libre', ip);
        localStorage.setItem('sebas_port_libre', port);
        globalAppConfig.ipConsola = ip;
        globalAppConfig.portFTP = parseInt(port, 10) || 2121;
        
        window.sysNotification("ENLACE", "Comprobando conexión...", "fa-rotate");
        
        btn.innerHTML = `<i class="fa-solid fa-database fa-spin mr-2"></i> BAJANDO DB`;
        
        let exito = await window.verificarRadarInicial();
        
        if (exito) {
            window.sysNotification("CONECTADO", "Sistema enlazado correctamente.", "fa-check-circle");
            setTimeout(() => window.cerrarPanelConexion(), 1000);
        } else { 
            throw new Error("Offline"); 
        }
    } catch (err) {
        window.sysNotification("DESCONECTADO", "Consola fuera de línea.", "fa-unlink");
    } finally {
        btn.innerHTML = btn.dataset.origHtml;
        btn.classList.remove('opacity-80', 'pointer-events-none');
    }
};

window.iniciarEscaneoAnimado = async function() {
    window.playSynth('select');
    
    const inputs = document.getElementById('header-estado-inputs');
    const scanner = document.getElementById('header-estado-scanner');
    const box = document.getElementById('escaneo-animacion-box');
    const title = document.getElementById('escaneo-status-title');
    const log = document.getElementById('escaneo-log');
    
    if(!inputs || !scanner || !box) return;

    inputs.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
    
    setTimeout(async () => {
        inputs.classList.add('hidden');
        inputs.classList.remove('flex');
        
        box.innerHTML = window.getScanLayoutHTML(globalAppConfig.radarStyle);
        scanner.classList.remove('hidden');
        scanner.classList.add('flex');
        
        setTimeout(() => { scanner.classList.remove('opacity-0', 'pointer-events-none'); }, 50);
        
        const textContainer = title ? title.parentElement : null;
        if (textContainer) {
            textContainer.className = "absolute m-auto inset-0 flex flex-col items-center justify-center z-30 pointer-events-none bg-[#060913]/90 backdrop-blur-md border border-cyan-500/30 rounded-[1.5rem] p-4 shadow-[0_0_40px_rgba(0,0,0,0.9)] h-fit w-fit";
        }
        
        if(title) {
            title.innerText = "ANALYZING.NETWORK";
            title.className = "text-[12px] font-black tracking-widest text-cyan-400 uppercase mb-2 text-center drop-shadow-[0_0_8px_rgba(34,211,238,1)] animate-pulse";
        }
        if(log) {
            log.innerHTML = `<span class="text-cyan-400 font-mono mr-2">></span><span class="typewriter text-white font-mono text-[10px]">Verificando IP guardada...</span>`;
            log.className = "terminal-text text-[10px] text-cyan-300 font-mono tracking-widest text-center h-[16px] drop-shadow-[0_0_2px_black]";
        }

        try {
            const cacheTimeout = new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout')), 2500));
            let resCache = await Promise.race([fetch(`api/scanner.php?ip=${globalAppConfig.ipConsola}&port=${globalAppConfig.portFTP}`), cacheTimeout]); 
            let dataCache = await resCache.json();
            
            if (dataCache && dataCache.status === 'success') {
                if(log) log.innerHTML = `<span class="text-emerald-400 font-mono text-[10px]">> PS4 HALLADA EN CACHÉ: ${globalAppConfig.ipConsola}</span>`;
                if(title) { title.innerText = "LINK.ESTABLISHED"; title.classList.replace('text-cyan-400', 'text-emerald-400'); }
                window.playSynth('success');
                globalAppConfig.statusConexion = true;
                window.actualizarEstadoBotonInicio(true);
                
                if (typeof window.sincronizarCatalogoSilencioso === 'function') {
                    if(log) log.innerHTML = `<span class="text-cyan-400 font-mono text-[10px]">> BAJANDO BASE DE DATOS...</span>`;
                    await window.sincronizarCatalogoSilencioso();
                }
                
                window.arrancarSincronizacionCapturasFondo();
                setTimeout(() => { window.cerrarPanelConexion(); if(box) box.innerHTML=''; }, 2000);
                return;
            }
            throw new Error("Cache offline");
        } catch (e) {
            if(log) log.innerHTML = `<span class="text-cyan-400 font-mono mr-2">></span><span class="typewriter text-white font-mono text-[10px]">Caché offline, iniciando Radar...</span>`;
            
            try {
                const radarTimeout = new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout')), 8000));
                let resRadar = await Promise.race([fetch('api/radar_api.php'), radarTimeout]);
                let dataRadar = await resRadar.json();
                
                if (dataRadar && dataRadar.status === 'success' && dataRadar.ps4_ips && dataRadar.ps4_ips.length > 0) {
                    let nuevaIP = dataRadar.ps4_ips[0];
                    if(log) log.innerHTML = `<span class="text-emerald-400 font-mono text-[10px]">> NUEVA CONSOLA: ${nuevaIP}</span>`;
                    if(title) { title.innerText = "LINK.ESTABLISHED"; title.classList.replace('text-cyan-400', 'text-emerald-400'); }
                    window.playSynth('success');
                    
                    globalAppConfig.ipConsola = nuevaIP;
                    localStorage.setItem('sebas_ip_final_libre', nuevaIP);
                    const inputIP = document.getElementById('ps-ip-full-input'); if (inputIP) inputIP.value = nuevaIP;
                    
                    globalAppConfig.statusConexion = true;
                    window.actualizarEstadoBotonInicio(true);
                    
                    if (typeof window.sincronizarCatalogoSilencioso === 'function') {
                        if(log) log.innerHTML = `<span class="text-cyan-400 font-mono text-[10px]">> BAJANDO BASE DE DATOS...</span>`;
                        await window.sincronizarCatalogoSilencioso();
                    }
                    
                    window.arrancarSincronizacionCapturasFondo();
                    setTimeout(() => { window.cerrarPanelConexion(); if(box) box.innerHTML=''; }, 2500);
                } else {
                    throw new Error("Ninguna PS4 respondió");
                }
            } catch (errRadar) {
                if(log) log.innerHTML = `<span class="text-red-500 font-mono text-[10px]">> Consola No Encontrada</span>`;
                if(title) { title.innerText = "SCAN.FAILED"; title.classList.replace('text-cyan-400', 'text-red-500'); }
                window.playSynth('glitch_snd');
                globalAppConfig.statusConexion = false;
                window.actualizarEstadoBotonInicio(false);
                setTimeout(() => { window.cerrarPanelConexion(); if(box) box.innerHTML=''; }, 3000);
            }
        }
    }, 50);
};

window.abortarEscaneo = function() {
    window.playSynth('select');
    window.cerrarPanelConexion();
    setTimeout(() => { const box = document.getElementById('escaneo-animacion-box'); if(box) box.innerHTML=''; }, 400);
};

window.conectarIPManualValidando = async function() {
    const inputIP = document.getElementById('ps-ip-full-input'); const inputPort = document.getElementById('ps-port-input');
    if (!inputIP) return;
    const ip = inputIP.value.trim(); const port = inputPort ? inputPort.value.trim() : '2121';
    if (!window.validarEstructuraIP(ip)) { window.sysNotification("ERROR", "IP inválida.", "fa-circle-xmark"); return; }
    
    window.localStorage.setItem('sebas_ip_final_libre', ip); window.localStorage.setItem('sebas_port_libre', port);
    globalAppConfig.ipConsola = ip; globalAppConfig.portFTP = parseInt(port, 10);
    window.sysNotification("ENLACE", "Sincronizando...", "fa-rotate");
    await window.verificarRadarInicial();
};

window.arrancarSincronizacionCapturasFondo = async function() {
    const ip = localStorage.getItem('sebas_ip_final_libre'); if (!ip) return;
    try { let fd = new FormData(); fd.append('action', 'get_all_caps'); fd.append('host_ip', ip); fd.append('force', '0');
        let res = await fetch('api/ps4_screenshots_api.php', { method: 'POST', body: fd }); let data = await res.json();
        if (data && data.status === 'success' && data.missing && data.missing.length > 0) {
            for (let i = 0; i < data.missing.length; i++) { try { let fdSingle = new FormData(); fdSingle.append('action', 'download_single_cap'); fdSingle.append('host_ip', ip); fdSingle.append('remote_path', data.missing[i]); await fetch('api/ps4_screenshots_api.php', { method: 'POST', body: fdSingle }); } catch(err) {} }
        }
    } catch(e) {}
};

window.verificarRadarInicial = async function() {
    try {
        const timeoutPromise = new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout')), 3000));
        let response = await Promise.race([fetch(`api/scanner.php?ip=${globalAppConfig.ipConsola}&port=${globalAppConfig.portFTP}`), timeoutPromise]);
        let json = await response.json();
        
        if (json && json.status === 'success') {
            globalAppConfig.statusConexion = true;
            window.actualizarEstadoBotonInicio(true);
            
            if (typeof window.sincronizarCatalogoSilencioso === 'function') {
                await window.sincronizarCatalogoSilencioso();
            }
            
            window.arrancarSincronizacionCapturasFondo();
            return true;
        } else { throw new Error("Offline"); }
    } catch (e) {
        globalAppConfig.statusConexion = false;
        window.actualizarEstadoBotonInicio(false);
        return false;
    }
};

window.validarEstructuraIP = function(ipString) { return /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(ipString); };

window.sysNotification = function(titulo, mensaje, iconoClase = "fa-info-circle") {
    if (!globalAppConfig.alertasActivas) return;
    const style = globalAppConfig.notificacionStyle || 'toast-default';
    let holderId = 'sys-notif-holder-' + style;
    let holder = document.getElementById(holderId);
    if (!holder) {
        holder = document.createElement('div');
        holder.id = holderId;
        if (style === 'toast-default') holder.className = "fixed top-4 md:top-6 right-4 md:right-6 z-[9999] flex flex-col gap-2 pointer-events-none items-end";
        else if (style === 'toast-ps4') holder.className = "fixed top-6 md:top-8 left-0 z-[9999] flex flex-col gap-2 pointer-events-none items-start";
        else if (style === 'toast-liquid') holder.className = "fixed bottom-6 md:bottom-10 left-1/2 transform -translate-x-1/2 z-[9999] flex flex-col gap-2 pointer-events-none items-center";
        else if (style === 'toast-glitch') holder.className = "fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-[9999] flex flex-col gap-2 pointer-events-none items-center";
        else if (style === 'toast-cyber') holder.className = "fixed top-10 md:top-12 right-0 z-[9999] flex flex-col gap-2 pointer-events-none items-end";
        else if (style === 'toast-holographic') holder.className = "fixed top-10 md:top-12 left-1/2 transform -translate-x-1/2 z-[9999] flex flex-col gap-2 pointer-events-none items-center";
        else if (style === 'toast-arcade') holder.className = "fixed top-4 md:top-6 left-1/2 transform -translate-x-1/2 z-[9999] flex flex-col gap-2 pointer-events-none items-center";
        else if (style === 'toast-minimal') holder.className = "fixed bottom-10 md:bottom-12 right-4 md:right-6 z-[9999] flex flex-col gap-2 pointer-events-none items-end";
        else if (style === 'toast-military') holder.className = "fixed top-4 md:top-6 left-4 md:left-6 z-[9999] flex flex-col gap-2 pointer-events-none items-start";
        else if (style === 'toast-neumorph') holder.className = "fixed top-1/2 right-4 md:right-6 transform -translate-y-1/2 z-[9999] flex flex-col gap-2 pointer-events-none items-end";
        else holder.className = "fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none items-end";
        document.body.appendChild(holder);
    }
    const b = document.createElement('div');
    if (style === 'toast-default') {
        b.className = "flex items-center gap-3 p-3 bg-[#0d1321]/95 backdrop-blur-md border border-white/10 rounded-2xl shadow-[0_10px_30px_rgba(0,0,0,0.5)] pointer-events-auto transform translate-x-full transition-all duration-400";
        b.innerHTML = `<div class="flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-br from-cyan-500/20 to-purple-500/20 border border-cyan-500/30 text-cyan-400"><i class="fa-solid ${iconoClase} text-xs"></i></div><div class="flex flex-col flex-1 min-w-[150px]"><span class="text-[8px] md:text-[10px] font-black tracking-widest text-cyan-400 uppercase">${titulo}</span><span class="text-[9px] md:text-[11px] font-bold text-gray-200 truncate mt-0.5">${mensaje}</span></div>`;
    } else if (style === 'toast-ps4') {
        b.className = "flex items-start gap-4 p-4 bg-[#121212]/95 backdrop-blur-sm border-l-4 border-gray-300 rounded-r-lg shadow-[10px_10px_30px_rgba(0,0,0,0.8)] pointer-events-auto transform -translate-x-full transition-all duration-300 w-[280px] md:w-[320px]";
        b.innerHTML = `<div class="flex items-center justify-center w-10 h-10 bg-white/10 text-white shrink-0 mt-0.5"><i class="fa-brands fa-playstation text-xl"></i></div><div class="flex flex-col flex-1 min-w-0"><span class="text-[12px] md:text-[14px] font-medium text-white/90 leading-tight">${mensaje}</span><span class="text-[10px] md:text-[11px] text-gray-500 mt-1 uppercase tracking-wider">${titulo}</span></div>`;
    } else if (style === 'toast-liquid') {
        b.className = "flex items-center gap-3 px-5 py-3 bg-white/10 backdrop-blur-2xl border border-white/20 text-white rounded-[2rem] shadow-[0_10px_30px_rgba(0,0,0,0.5),inset_0_0_15px_rgba(255,255,255,0.2)] pointer-events-auto transform translate-y-[150%] transition-all duration-400";
        b.innerHTML = `<i class="fa-solid ${iconoClase} text-cyan-300 drop-shadow-md"></i><div class="flex flex-col"><span class="text-[11px] md:text-[13px] font-black tracking-widest text-white drop-shadow-md uppercase">${titulo}</span><span class="text-[9px] md:text-[11px] font-bold text-gray-200 drop-shadow-md">${mensaje}</span></div>`;
    } else if (style === 'toast-glitch') {
        b.className = "flex items-center gap-4 p-4 bg-black border border-white shadow-[4px_4px_0_red,-4px_-4px_0_cyan] pointer-events-auto transform scale-50 opacity-0 transition-all duration-200";
        b.innerHTML = `<i class="fa-solid fa-triangle-exclamation text-white animate-ping"></i><div class="flex flex-col"><span class="text-[14px] md:text-[16px] font-black uppercase text-white" style="text-shadow: 2px 0 0 red, -2px 0 0 cyan;">${titulo}</span><span class="text-[10px] md:text-[12px] font-mono text-white tracking-widest uppercase">${mensaje}</span></div>`;
    } else if (style === 'toast-cyber') {
        b.className = "flex items-center gap-3 p-3 bg-[#0a0a12]/90 backdrop-blur-md border border-cyan-500 pointer-events-auto transform translate-x-full transition-all duration-300";
        b.style.clipPath = "polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px)";
        b.innerHTML = `<i class="fa-solid ${iconoClase} text-cyan-400 drop-shadow-[0_0_8px_cyan]"></i><div class="flex flex-col"><span class="text-[10px] md:text-[12px] font-black text-cyan-400 tracking-widest uppercase">${titulo}</span><span class="text-[9px] md:text-[11px] font-mono text-gray-300">${mensaje}</span></div>`;
    } else if (style === 'toast-holographic') {
        b.className = "flex items-center gap-3 p-3 bg-cyan-900/20 backdrop-blur-lg border border-cyan-500/50 rounded-xl shadow-[0_0_15px_rgba(34,211,238,0.3),inset_0_0_10px_rgba(34,211,238,0.2)] pointer-events-auto transform -translate-y-full opacity-0 transition-all duration-400";
        b.innerHTML = `<i class="fa-solid ${iconoClase} text-cyan-300"></i><div class="flex flex-col"><span class="text-[9px] md:text-[11px] font-black tracking-widest text-cyan-300 uppercase">${titulo}</span><span class="text-[9px] md:text-[11px] font-medium text-cyan-100">${mensaje}</span></div>`;
    } else if (style === 'toast-arcade') {
        b.className = "flex items-center gap-3 p-3 bg-black border-2 border-yellow-400 shadow-[4px_4px_0_#ef4444] pointer-events-auto transform -translate-y-full transition-all duration-300";
        b.innerHTML = `<i class="fa-solid ${iconoClase} text-yellow-400 text-lg md:text-xl"></i><div class="flex flex-col"><span class="text-[10px] font-['Press_Start_2P'] text-white uppercase" style="font-size:7px; line-height:1.5;">${titulo}</span><span class="text-[9px] font-['Press_Start_2P'] text-yellow-400 uppercase" style="font-size:6px; line-height:1.5;">${mensaje}</span></div>`;
    } else if (style === 'toast-minimal') {
        b.className = "flex items-center gap-3 p-3 bg-transparent border border-white/30 rounded-[1rem] backdrop-blur-md pointer-events-auto transform translate-x-full transition-all duration-400";
        b.innerHTML = `<i class="fa-solid ${iconoClase} text-white"></i><div class="flex flex-col"><span class="text-[10px] md:text-[12px] font-light tracking-widest text-white uppercase">${titulo}</span><span class="text-[9px] md:text-[11px] font-medium text-gray-300">${mensaje}</span></div>`;
    } else if (style === 'toast-military') {
        b.className = "flex items-center gap-3 p-3 bg-[#1a2421]/95 border-l-4 border-[#84cc16] pointer-events-auto transform -translate-x-full transition-all duration-300";
        b.innerHTML = `<i class="fa-solid ${iconoClase} text-[#84cc16]"></i><div class="flex flex-col"><span class="text-[10px] md:text-[12px] font-mono font-bold text-[#84cc16] uppercase">${titulo}</span><span class="text-[9px] md:text-[11px] font-mono text-gray-300 uppercase">${mensaje}</span></div>`;
    } else if (style === 'toast-neumorph') {
        b.className = "flex items-center gap-3 p-4 bg-[#1a1b1e] rounded-2xl shadow-[4px_4px_8px_rgba(0,0,0,0.5),-4px_-4px_8px_rgba(255,255,255,0.05)] pointer-events-auto transform translate-x-full transition-all duration-400";
        b.innerHTML = `<i class="fa-solid ${iconoClase} text-gray-400"></i><div class="flex flex-col"><span class="text-[10px] md:text-[12px] font-black tracking-widest text-gray-400 uppercase">${titulo}</span><span class="text-[9px] md:text-[11px] font-medium text-gray-500">${mensaje}</span></div>`;
    }
    
    holder.appendChild(b);
    
    setTimeout(() => {
        if (style === 'toast-default' || style === 'toast-cyber' || style === 'toast-minimal' || style === 'toast-neumorph') b.classList.remove('translate-x-full');
        else if (style === 'toast-ps4' || style === 'toast-military') b.classList.remove('-translate-x-full');
        else if (style === 'toast-liquid') b.classList.remove('translate-y-[150%]');
        else if (style === 'toast-holographic') { b.classList.remove('-translate-y-full', 'opacity-0'); }
        else if (style === 'toast-arcade') { b.classList.remove('-translate-y-full'); }
        else if (style === 'toast-glitch') { b.classList.remove('scale-50', 'opacity-0'); b.classList.add('scale-100', 'opacity-100'); }
        else b.classList.remove('translate-x-full');
    }, 50);
    
    setTimeout(() => {
        if (style === 'toast-default' || style === 'toast-cyber' || style === 'toast-minimal' || style === 'toast-neumorph') b.classList.add('translate-x-full');
        else if (style === 'toast-ps4' || style === 'toast-military') b.classList.add('-translate-x-full');
        else if (style === 'toast-liquid') b.classList.add('translate-y-[150%]');
        else if (style === 'toast-holographic') { b.classList.add('-translate-y-full', 'opacity-0'); }
        else if (style === 'toast-arcade') { b.classList.add('-translate-y-full'); }
        else if (style === 'toast-glitch') { b.classList.remove('scale-100', 'opacity-100'); b.classList.add('scale-50', 'opacity-0'); }
        else b.classList.add('translate-x-full');
        setTimeout(() => { b.remove(); }, 400);
    }, style === 'toast-ps4' ? 5000 : 3500);
};

window.abrirModuloReal = function(moduloId) {
    if (typeof window.abrirModuloNativo === 'function') { window.abrirModuloNativo(moduloId); } 
    else {
        document.querySelectorAll('.app-layer').forEach(layer => { layer.classList.remove('active', 'flex'); layer.classList.add('hidden'); });
        const target = document.getElementById('layer-' + moduloId);
        if (target) { target.classList.remove('hidden'); setTimeout(() => target.classList.add('active', 'flex'), 10); }
    }
};

window.abrirModulo = function(moduloId) {
    if (moduloId === 'transferir') { window.mostrarSelectorTransferencia(); return; }
    window.abrirModuloReal(moduloId);
};

window.seleccionarGestorAccordeon = function(tipo, element, colorHex, nombreModulo) {
    if(window.playSynth) window.playSynth('select');
    if (element.classList.contains('expanded')) return;
    
    const accordionContainer = document.getElementById('accordion-container');
    const cards = document.querySelectorAll('.gestor-card');
    
    cards.forEach(c => {
        c.style.pointerEvents = 'none';
        if (c === element) { c.classList.add('expanded'); }
        else { c.classList.add('shrunk'); }
    });
    
    setTimeout(() => {
        if(window.playSynth) window.playSynth('magic');
        
        document.getElementById('loader-icon-selector').style.color = colorHex;
        document.getElementById('loading-text-selector').innerText = nombreModulo;
        document.getElementById('loading-text-selector').style.color = colorHex;

        document.getElementById('modal-title-selector').style.opacity = '0';
        document.getElementById('modal-handle-selector').style.opacity = '0';
        
        accordionContainer.style.opacity = '0';
        accordionContainer.style.transform = 'scale(0.95)';
        accordionContainer.style.filter = 'blur(5px)';

        setTimeout(() => {
            document.getElementById('loading-state-selector').classList.add('active');
        }, 200);

        setTimeout(() => {
            window.ejecutarSelectorTransferencia(tipo, true);
        }, 600); 

    }, 600);
};

window.mostrarSelectorTransferencia = function() {
    let modal = document.getElementById('modal-selector-transferencia');
    if (modal) modal.remove();

    modal = document.createElement('div');
    modal.id = 'modal-selector-transferencia';
    modal.className = "fixed inset-0 z-[9999] bg-[#02040a]/80 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300 hidden overflow-hidden";
    
    modal.innerHTML = `
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_15%_50%,rgba(34,211,238,0.05)_0%,transparent_50%),radial-gradient(circle_at_85%_30%,rgba(168,85,247,0.05)_0%,transparent_50%)] z-0 pointer-events-none"></div>
        <div class="w-full max-w-sm relative z-10 bg-[rgba(6,9,19,0.7)] backdrop-blur-[20px] rounded-[2rem] border border-white/5 border-t-white/20 shadow-[0_30px_60px_rgba(0,0,0,0.9)] p-6 flex flex-col transform scale-95 transition-transform duration-300 overflow-hidden" id="modal-content-selector-trans">
            <button onclick="window.cerrarSelectorTransferencia()" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-white/10 text-white flex items-center justify-center hover:bg-red-500/50 transition-colors z-[70]"><i class="fa-solid fa-xmark"></i></button>
            <div id="modal-handle-selector" class="w-12 h-1 bg-white/20 rounded-full mx-auto mb-5 z-20 relative transition-opacity duration-500"></div>
            <h2 id="modal-title-selector" class="text-center font-black tracking-widest text-white mb-6 uppercase text-[14px] drop-shadow-md z-20 relative transition-opacity duration-500">Elegir Gestor</h2>
            
            <div class="relative h-[300px] w-full">
                <div class="absolute inset-0 flex flex-col gap-3 transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]" id="accordion-container">
                    <div onclick="window.seleccionarGestorAccordeon('rpi', this, '#06b6d4', 'RPI SENDER')" class="gestor-card" style="--c: #06b6d4;">
                        <div class="card-content">
                            <div class="w-10 h-10 rounded-full bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-lg shrink-0 icon-wrapper border border-cyan-500/20"><i class="fa-solid fa-gamepad"></i></div>
                            <div class="flex flex-col text-wrapper">
                                <span class="text-[13px] md:text-[15px] font-black uppercase text-white tracking-widest leading-none">RPI Sender</span>
                                <span class="text-[9px] md:text-[11px] text-cyan-300 mt-1 uppercase font-bold tracking-widest opacity-80">Instalar PKGs Pesados</span>
                            </div>
                        </div>
                    </div>
                    <div onclick="window.seleccionarGestorAccordeon('ftp', this, '#a855f7', 'FTP MANUAL')" class="gestor-card" style="--c: #a855f7;">
                        <div class="card-content">
                            <div class="w-10 h-10 rounded-full bg-purple-500/10 text-purple-400 flex items-center justify-center text-lg shrink-0 icon-wrapper border border-purple-500/20"><i class="fa-solid fa-sitemap"></i></div>
                            <div class="flex flex-col text-wrapper">
                                <span class="text-[13px] md:text-[15px] font-black uppercase text-white tracking-widest leading-none">FTP Manual</span>
                                <span class="text-[9px] md:text-[11px] text-purple-300 mt-1 uppercase font-bold tracking-widest opacity-80">Archivos Pequeños</span>
                            </div>
                        </div>
                    </div>
                    <div onclick="window.seleccionarGestorAccordeon('roms', this, '#f59e0b', 'INYECTOR ROMS')" class="gestor-card" style="--c: #f59e0b;">
                        <div class="card-content">
                            <div class="w-10 h-10 rounded-full bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg shrink-0 icon-wrapper border border-amber-500/20"><i class="fa-solid fa-ghost"></i></div>
                            <div class="flex flex-col text-wrapper">
                                <span class="text-[13px] md:text-[15px] font-black uppercase text-white tracking-widest leading-none">Inyector ROMs</span>
                                <span class="text-[9px] md:text-[11px] text-amber-300 mt-1 uppercase font-bold tracking-widest opacity-80">Enviar a Emuladores</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="loading-state-selector" class="absolute inset-0 flex flex-col items-center justify-center opacity-0 pointer-events-none transform scale-95 transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)] z-50">
                    <i id="loader-icon-selector" class="fa-solid fa-circle-notch fa-spin text-5xl mb-4 drop-shadow-[0_0_15px_currentColor]"></i>
                    <span class="text-[13px] md:text-[15px] font-black tracking-widest uppercase text-white drop-shadow-md">CONECTANDO</span>
                    <span id="loading-text-selector" class="text-[10px] md:text-[12px] font-mono mt-2 tracking-widest uppercase opacity-80"></span>
                </div>
            </div>
        </div>
        <style>
            .gestor-card { flex: 1; display: flex; align-items: center; padding: 0 20px; background: rgba(255,255,255,0.02); border-radius: 1.2rem; border: 1px solid rgba(255,255,255,0.05); position: relative; overflow: hidden; transition: flex 0.5s cubic-bezier(0.25,1,0.5,1), background-color 0.3s, border-color 0.3s, opacity 0.3s, filter 0.3s; cursor: pointer; z-index: 10; }
            .gestor-card.shrunk { flex: 0.5; opacity: 0.2; filter: grayscale(100%) blur(1px); pointer-events: none; }
            .gestor-card.expanded { flex: 2; background: rgba(255,255,255,0.06); border-color: var(--c); box-shadow: inset 0 0 20px rgba(255,255,255,0.02); pointer-events: none; }
            .gestor-card .card-content { display: flex; align-items: center; width: 100%; transition: transform 0.5s cubic-bezier(0.25,1,0.5,1); }
            .gestor-card.expanded .card-content { flex-direction: column; justify-content: center; text-align: center; }
            .gestor-card .icon-wrapper { transition: transform 0.5s cubic-bezier(0.25,1,0.5,1), background-color 0.3s, color 0.3s, box-shadow 0.3s; }
            .gestor-card.expanded .icon-wrapper { transform: scale(1.3); background: var(--c); color: #000 !important; border-color: transparent; margin-bottom: 12px; box-shadow: 0 0 20px var(--c); }
            .gestor-card .text-wrapper { margin-left: 16px; transition: margin 0.5s, transform 0.5s; }
            .gestor-card.expanded .text-wrapper { margin-left: 0; transform: scale(1.1); }
            #loading-state-selector.active { opacity: 1; transform: scale(1); }
        </style>
    `;
    
    document.body.appendChild(modal);
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        document.getElementById('modal-content-selector-trans').classList.remove('scale-95');
    }, 10);
};

window.cerrarSelectorTransferencia = function() {
    const modal = document.getElementById('modal-selector-transferencia');
    if (modal) { 
        const content = document.getElementById('modal-content-selector-trans');
        if(content) content.classList.add('scale-95'); 
        modal.classList.add('opacity-0'); 
        setTimeout(() => { modal.classList.add('hidden'); }, 300); 
    }
};

window.ejecutarSelectorTransferencia = function(tipo, skipCloseAnim = false) {
    const openLayer = () => {
        if (tipo === 'ftp') { window.abrirModuloReal('transferir'); } 
        else if (tipo === 'roms') { window.abrirModuloReal('roms'); } 
        else if (tipo === 'rpi') {
            let layer = document.getElementById('layer-rpi_avanzado');
            if (!layer) {
                layer = document.createElement('div'); layer.id = 'layer-rpi_avanzado';
                layer.className = 'app-layer hidden flex-col h-[100dvh] w-full bg-[#050508] relative z-[15]';
                layer.innerHTML = `
                    <div class="w-full flex items-center justify-between z-30 shrink-0 p-4 pt-5 mb-2 border-b border-white/5 bg-[#060913]/80 backdrop-blur-md">
                        <div class="flex items-center gap-3">
                            <button onclick="window.volverAlLauncher()" class="w-10 h-10 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-center tactile-fx hover:bg-white/5"><i class="fa-solid fa-arrow-left text-gray-300"></i></button>
                            <div class="flex flex-col"><h2 class="text-[17px] md:text-[19px] font-black tracking-tighter uppercase text-cyan-400 leading-none">RPI Sender</h2><span class="text-[9px] md:text-[11px] font-mono text-gray-400 tracking-widest mt-0.5">Gestor de Instalación</span></div>
                        </div>
                    </div>
                    <iframe src="modulos/rpi_avanzado.php" class="flex-1 w-full border-none outline-none bg-transparent"></iframe>`;
                document.body.appendChild(layer);
            }
            window.abrirModuloReal('rpi_avanzado');
        }
    };

    if (!skipCloseAnim) {
        window.cerrarSelectorTransferencia();
        setTimeout(openLayer, 300);
    } else {
        openLayer();
        const modal = document.getElementById('modal-selector-transferencia');
        if (modal) {
            modal.style.transition = 'opacity 0.4s ease';
            modal.style.opacity = '0';
            setTimeout(() => modal.remove(), 400);
        }
    }
};

window.lanzarRadarVentanaEmergente = async function() {
    const modal = document.getElementById('modal-radar-emergente'); const caja = document.getElementById('radar-caja'); const logTerm = document.getElementById('radar-log-terminal'); const subLabel = document.getElementById('radar-subnet-txt');
    if (!modal || !logTerm) return;
    modal.classList.remove('hidden'); setTimeout(() => { modal.classList.add('opacity-100'); if (caja) { caja.classList.remove('scale-90'); } }, 10);
    logTerm.innerHTML = `<p class="text-emerald-600">[SYS] Iniciando Protocolo de Detección Zero-Delay...</p>`; if (subLabel) subLabel.innerText = `ANALYZING.NETWORK`;
    try {
        logTerm.innerHTML += `<p class="text-gray-500">Verificando IP guardada: ${window.globalAppConfig.ipConsola}...</p>`; logTerm.scrollTop = logTerm.scrollHeight;
        
        const cacheTimeout = new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout')), 2000));
        let resCache = await Promise.race([fetch(`api/scanner.php?ip=${window.globalAppConfig.ipConsola}&port=${window.globalAppConfig.portFTP}`), cacheTimeout]); 
        let dataCache = await resCache.json();
        
        if (dataCache && dataCache.status === 'success') {
            logTerm.innerHTML += `<p class="text-white font-bold bg-emerald-950/50 px-1 border border-emerald-500/20">🚀 PS4 HALLADA EN CACHÉ: ${window.globalAppConfig.ipConsola}</p>`;
            if (subLabel) subLabel.innerText = `LINK.ESTABLISHED`; await window.verificarRadarInicial(); setTimeout(() => { window.abortarYEstabilizarRadar(); }, 1200); return;
        }
        throw new Error("Offline");
    } catch (e) { 
        logTerm.innerHTML += `<p class="text-yellow-500">Caché offline o cambio de red detectado.</p>`; logTerm.innerHTML += `<p class="text-emerald-400">Aislando nueva subred y lanzando Escaneo Masivo Paralelo...</p>`; logTerm.scrollTop = logTerm.scrollHeight;
        try {
            const radarTimeout = new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout')), 7000));
            let resRadar = await Promise.race([fetch('api/radar_api.php'), radarTimeout]); 
            let dataRadar = await resRadar.json();
            
            if (dataRadar && dataRadar.status === 'success') {
                if (subLabel) subLabel.innerText = `SCANNING.${dataRadar.segmento}`; logTerm.innerHTML += `<p class="text-gray-500">Barriendo 254 IPs simultáneas en subred ${dataRadar.segmento} (Timeout 200ms)...</p>`; logTerm.scrollTop = logTerm.scrollHeight;
                if (dataRadar.ps4_ips && dataRadar.ps4_ips.length > 0) {
                    let nuevaIP = dataRadar.ps4_ips[0];
                    logTerm.innerHTML += `<p class="text-white font-bold bg-emerald-950/50 px-1 border border-emerald-500/20">🎯 NUEVA CONSOLA HALLADA EN: ${nuevaIP}</p>`;
                    window.globalAppConfig.ipConsola = nuevaIP; localStorage.setItem('sebas_ip_final_libre', nuevaIP); const inputIP = document.getElementById('ps-ip-full-input'); if (inputIP) inputIP.value = nuevaIP;
                    await window.verificarRadarInicial(); setTimeout(() => { window.abortarYEstabilizarRadar(); }, 2000);
                } else { logTerm.innerHTML += `<p class="text-red-400">[ERROR] Ninguna PS4 respondió en la red ${dataRadar.segmento}.</p>`; if (subLabel) subLabel.innerText = `SCAN.FAILED`; }
            } else { logTerm.innerHTML += `<p class="text-red-500">[ERROR] ${dataRadar.message || 'Fallo de interfaz de red.'}</p>`; if (subLabel) subLabel.innerText = `SCAN.FAILED`;}
        } catch (err2) {
            logTerm.innerHTML += `<p class="text-red-500">[ERROR CRÍTICO] Servidor local no responde.</p>`;
            if (subLabel) subLabel.innerText = `SCAN.FAILED`;
        }
    }
    logTerm.scrollTop = logTerm.scrollHeight;
};

window.contadorToquesSecreto = 0;
window.timerResetToques = null;
window.audioCtxEaster = null;

window.playToneEaster = function(freq, dur, type = 'sine') {
    try {
        if (!window.audioCtxEaster) window.audioCtxEaster = new (window.AudioContext || window.webkitAudioContext)();
        if (window.audioCtxEaster.state === 'suspended') window.audioCtxEaster.resume();
        const osc = window.audioCtxEaster.createOscillator();
        const gain = window.audioCtxEaster.createGain();
        osc.type = type;
        osc.frequency.setValueAtTime(freq, window.audioCtxEaster.currentTime);
        gain.gain.setValueAtTime(0.12, window.audioCtxEaster.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, window.audioCtxEaster.currentTime + dur);
        osc.connect(gain);
        gain.connect(window.audioCtxEaster.destination);
        osc.start();
        osc.stop(window.audioCtxEaster.currentTime + dur);
    } catch (e) {}
};

window.playTrophyFanfare = function() {
    const notas = [ { f: 523.25, t: 0, d: 0.12 }, { f: 659.25, t: 0.10, d: 0.12 }, { f: 783.99, t: 0.20, d: 0.15 }, { f: 1046.50, t: 0.32, d: 0.45 } ];
    notas.forEach(n => { setTimeout(() => window.playToneEaster(n.f, n.d, 'triangle'), n.t * 1000); });
    if ("vibrate" in navigator) navigator.vibrate([40, 60, 120]);
};

window.registrarToqueSecreto = function(e) {
    if(e) e.stopPropagation();
    window.contadorToquesSecreto++;
    clearTimeout(window.timerResetToques);
    window.playToneEaster(300 + (window.contadorToquesSecreto * 90), 0.07, 'sine');

    const toast = document.getElementById('toast-secreto');
    if (window.contadorToquesSecreto >= 4 && window.contadorToquesSecreto < 7) {
        toast.innerText = `⚡ Faltan ${7 - window.contadorToquesSecreto} toques...`;
        toast.classList.remove('opacity-0', 'scale-90');
        toast.classList.add('opacity-100', 'scale-100');
    }

    if (window.contadorToquesSecreto >= 7) {
        window.contadorToquesSecreto = 0;
        toast.classList.remove('opacity-100', 'scale-100');
        toast.classList.add('opacity-0', 'scale-90');
        window.abrirEasterEgg();
        return;
    }

    window.timerResetToques = setTimeout(() => {
        window.contadorToquesSecreto = 0;
        toast.classList.remove('opacity-100', 'scale-100');
        toast.classList.add('opacity-0', 'scale-90');
    }, 1200);
};

window.animFrameIdEgg = null;
window.particlesEgg = [];
window.mouseEgg = { x: null, y: null, radius: 130 };

class ParticleEgg {
    constructor(x, y, color) {
        this.x = x; this.y = y;
        this.size = Math.random() * 2 + 1.5;
        this.density = (Math.random() * 30) + 1;
        this.color = color;
        this.vx = (Math.random() - 0.5) * 0.5;
        this.vy = (Math.random() - 0.5) * 0.5;
    }
    draw(ctx) {
        ctx.fillStyle = this.color;
        ctx.shadowBlur = 10;
        ctx.shadowColor = this.color;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.closePath();
        ctx.fill();
        ctx.shadowBlur = 0;
    }
    update(canvas) {
        let dx = window.mouseEgg.x - this.x;
        let dy = window.mouseEgg.y - this.y;
        let distance = Math.sqrt(dx * dx + dy * dy);
        
        if (window.mouseEgg.x !== null && distance < window.mouseEgg.radius) {
            let forceDirectionX = dx / distance;
            let forceDirectionY = dy / distance;
            let force = (window.mouseEgg.radius - distance) / window.mouseEgg.radius;
            let directionX = forceDirectionX * force * this.density;
            let directionY = forceDirectionY * force * this.density;
            this.x -= directionX;
            this.y -= directionY;
        } else {
            this.x += this.vx;
            this.y += this.vy;
            if(this.x < 0 || this.x > canvas.width) this.vx *= -1;
            if(this.y < 0 || this.y > canvas.height) this.vy *= -1;
        }
    }
}

window.initCanvasEgg = function() {
    const canvas = document.getElementById('particle-canvas');
    if(!canvas) return;
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    window.particlesEgg = [];
    let numParticles = (canvas.width * canvas.height) / 12000;
    const colores = ['#ff00ff', '#00ffff', '#ffff00', '#ffffff', '#ff1493', '#00bfff']; 
    for (let i = 0; i < numParticles; i++) {
        let x = Math.random() * canvas.width;
        let y = Math.random() * canvas.height;
        let color = colores[Math.floor(Math.random() * colores.length)];
        window.particlesEgg.push(new ParticleEgg(x, y, color));
    }
};

window.animateParticlesEgg = function() {
    const canvas = document.getElementById('particle-canvas');
    if(!canvas) return;
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    for(let i=0; i<window.particlesEgg.length; i++){
        window.particlesEgg[i].update(canvas);
        window.particlesEgg[i].draw(ctx);
    }
    
    window.animFrameIdEgg = requestAnimationFrame(window.animateParticlesEgg);
};

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('particle-canvas');
    if(canvas) {
        canvas.addEventListener('touchmove', (e) => {
            window.mouseEgg.x = e.touches[0].clientX;
            window.mouseEgg.y = e.touches[0].clientY;
        }, {passive: true});
        canvas.addEventListener('touchend', () => { window.mouseEgg.x = null; window.mouseEgg.y = null; });
    }

    const card3D = document.getElementById('dev-holo-card');
    if(card3D) {
        card3D.addEventListener('touchmove', (e) => {
            const touch = e.touches[0];
            const rect = card3D.getBoundingClientRect();
            const x = touch.clientX - rect.left - rect.width / 2;
            const y = touch.clientY - rect.top - rect.height / 2;
            card3D.style.transform = `scale(1) rotateX(${(-y / 20).toFixed(2)}deg) rotateY(${(x / 20).toFixed(2)}deg)`;
        }, { passive: true });
        card3D.addEventListener('touchend', () => { card3D.style.transform = `scale(1) rotateX(0deg) rotateY(0deg)`; });
    }
});

window.abrirEasterEgg = function() {
    window.playTrophyFanfare();
    window.initCanvasEgg();
    window.animateParticlesEgg();

    const modal = document.getElementById('modal-easter-egg');
    const card = document.getElementById('dev-holo-card');
    const trofeo = document.getElementById('trofeo-banner');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    setTimeout(() => {
        modal.classList.remove('opacity-0');
        card.classList.remove('scale-90');
        card.classList.add('scale-100');
        
        trofeo.classList.remove('translate-y-[200%]', 'opacity-0');
        trofeo.classList.add('translate-y-0', 'opacity-100');
        setTimeout(() => {
            trofeo.classList.remove('translate-y-0', 'opacity-100');
            trofeo.classList.add('translate-y-[200%]', 'opacity-0');
        }, 4000);
    }, 20);
};

window.cerrarEasterEgg = function() {
    window.playToneEaster(500, 0.08, 'sine');
    const modal = document.getElementById('modal-easter-egg');
    const card = document.getElementById('dev-holo-card');
    const canvas = document.getElementById('particle-canvas');
    
    cancelAnimationFrame(window.animFrameIdEgg);

    modal.classList.add('opacity-0');
    card.classList.remove('scale-100');
    card.classList.add('scale-90');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        if(canvas) canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
    }, 500);
};

// ====================================================================
// SISTEMA DE ACTUALIZACIONES OTA (GITHUB - BASADO EN COMMITS)
// ====================================================================
let otaUpdateUrl = "";
let latestCommitData = "";

window.buscarActualizacionOTA = async function() {
    if(typeof window.playSynth === 'function') window.playSynth('select');
    
    const modal = document.getElementById('modal-ota-update');
    const content = document.getElementById('content-ota-update');
    const icon = document.getElementById('ota-icon');
    const title = document.getElementById('ota-title');
    const verLocal = document.getElementById('ota-version-local');
    const verNueva = document.getElementById('ota-version-nueva');
    const changelogContainer = document.getElementById('ota-changelog-container');
    const changelogText = document.getElementById('ota-changelog');
    const btnInstall = document.getElementById('btn-ota-install');
    const btnCancel = document.getElementById('btn-ota-cancel');
    const progressContainer = document.getElementById('ota-progress-container');
    
    // Leemos el código del último commit instalado (si existe)
    const localCommit = localStorage.getItem('ghm_local_commit') || 'Ninguno';

    icon.className = "fa-solid fa-cloud-arrow-down animate-bounce";
    title.innerText = "Comparando archivos...";
    verLocal.innerText = localCommit !== 'Ninguno' ? localCommit.substring(0, 7) : 'Base v4.0';
    verNueva.innerText = "--";
    changelogContainer.classList.add('hidden');
    progressContainer.classList.add('hidden');
    btnInstall.classList.add('hidden');
    btnCancel.innerText = "Cancelar";
    btnCancel.classList.remove('hidden');

    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        content.classList.remove('scale-95');
    }, 10);

    try {
        // Consultamos el último commit (cambio) directamente a la rama 'main'
        const response = await fetch('https://api.github.com/repos/Sebasbms/GoldHenManager-v3/commits/main');
        if (!response.ok) throw new Error("No se pudo conectar con GitHub");
        
        const data = await response.json();
        latestCommitData = data.sha;
        const commitMessage = data.commit.message;
        const commitDate = new Date(data.commit.committer.date).toLocaleString('es-AR');
        
        verNueva.innerText = latestCommitData.substring(0, 7);

        // Comparamos el código del commit local con el de GitHub
        if (localCommit !== latestCommitData) {
            icon.className = "fa-solid fa-triangle-exclamation text-amber-400";
            document.getElementById('ota-icon-container').className = "w-16 h-16 rounded-full bg-amber-500/20 text-amber-500 flex items-center justify-center text-3xl mb-4 shadow-[0_0_15px_rgba(245,158,11,0.4)]";
            title.innerText = "¡Hay Cambios Nuevos!";
            
            changelogText.innerHTML = `<b>Fecha:</b> ${commitDate}<br><b>Mensaje:</b> ${commitMessage}`;
            changelogContainer.classList.remove('hidden');
            
            // Enlace de descarga directa del código fuente (rama main completa)
            otaUpdateUrl = 'https://github.com/Sebasbms/GoldHenManager-v3/archive/refs/heads/main.zip';
            
            btnInstall.classList.remove('hidden');
            btnInstall.onclick = () => window.iniciarInstalacionOTA();
        } else {
            icon.className = "fa-solid fa-check text-emerald-400";
            document.getElementById('ota-icon-container').className = "w-16 h-16 rounded-full bg-emerald-500/20 text-emerald-500 flex items-center justify-center text-3xl mb-4 shadow-[0_0_15px_rgba(16,185,129,0.4)]";
            title.innerText = "Sistema Sincronizado";
            verNueva.innerText = "Al Día";
            btnCancel.innerText = "Cerrar";
        }

    } catch (error) {
        icon.className = "fa-solid fa-xmark text-red-400";
        document.getElementById('ota-icon-container').className = "w-16 h-16 rounded-full bg-red-500/20 text-red-500 flex items-center justify-center text-3xl mb-4 shadow-[0_0_15px_rgba(239,68,68,0.4)]";
        title.innerText = "Error de Conexión";
        changelogText.innerText = "No se pudo leer el historial de Github.";
        changelogContainer.classList.remove('hidden');
        btnCancel.innerText = "Cerrar";
    }
};

window.iniciarInstalacionOTA = async function() {
    if(typeof window.playSynth === 'function') window.playSynth('magic');
    
    const btnInstall = document.getElementById('btn-ota-install');
    const btnCancel = document.getElementById('btn-ota-cancel');
    const progressContainer = document.getElementById('ota-progress-container');
    const progressBar = document.getElementById('ota-bar');
    const progressPercent = document.getElementById('ota-percent');
    const statusText = document.getElementById('ota-progress-status');
    const icon = document.getElementById('ota-icon');
    
    btnInstall.classList.add('hidden');
    btnCancel.classList.add('hidden');
    progressContainer.classList.remove('hidden');
    progressContainer.classList.add('flex');
    
    icon.className = "fa-solid fa-gear fa-spin text-cyan-400";
    document.getElementById('ota-icon-container').className = "w-16 h-16 rounded-full bg-cyan-500/20 text-cyan-500 flex items-center justify-center text-3xl mb-4 shadow-[0_0_15px_rgba(6,182,212,0.4)]";
    document.getElementById('ota-title').innerText = "Instalando Cambios...";
    
    let simProgress = 0;
    const simInterval = setInterval(() => {
        if (simProgress < 90) {
            simProgress += Math.floor(Math.random() * 5) + 1;
            if(simProgress > 90) simProgress = 90;
            progressBar.style.width = simProgress + '%';
            progressPercent.innerText = simProgress + '%';
        }
    }, 500);

    try {
        let fd = new FormData();
        fd.append('action', 'install_ota');
        fd.append('zip_url', otaUpdateUrl);

        // Llamamos al backend que gestionará la descompresión
        let res = await fetch('api/ota_update.php', { method: 'POST', body: fd });
        let data = await res.json();
        
        clearInterval(simInterval);
        
        if (data.status === 'success') {
            progressBar.style.width = '100%';
            progressPercent.innerText = '100%';
            statusText.innerText = "¡Sincronización Completada!";
            
            // Guardamos el nuevo Commit en localStorage para saber que ya estamos actualizados
            localStorage.setItem('ghm_local_commit', latestCommitData);
            
            icon.className = "fa-solid fa-check text-emerald-400";
            document.getElementById('ota-icon-container').className = "w-16 h-16 rounded-full bg-emerald-500/20 text-emerald-500 flex items-center justify-center text-3xl mb-4 shadow-[0_0_15px_rgba(16,185,129,0.4)]";
            document.getElementById('ota-title').innerText = "App Sincronizada";
            
            btnInstall.innerText = "Reiniciar Aplicación";
            btnInstall.classList.remove('hidden');
            btnInstall.onclick = () => window.location.reload(true);
        } else {
            throw new Error(data.message || "Fallo durante la extracción en servidor.");
        }
    } catch (error) {
        clearInterval(simInterval);
        progressBar.style.background = "red";
        statusText.innerText = "Error en la instalación";
        
        icon.className = "fa-solid fa-xmark text-red-400";
        document.getElementById('ota-icon-container').className = "w-16 h-16 rounded-full bg-red-500/20 text-red-500 flex items-center justify-center text-3xl mb-4 shadow-[0_0_15px_rgba(239,68,68,0.4)]";
        document.getElementById('ota-title').innerText = "Fallo Crítico";
        
        document.getElementById('ota-changelog').innerText = error.message;
        document.getElementById('ota-changelog-container').classList.remove('hidden');
        
        btnCancel.innerText = "Cerrar";
        btnCancel.classList.remove('hidden');
    }
};
// Developed By SeBaS
