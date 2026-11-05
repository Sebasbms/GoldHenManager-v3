<?php
/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: index.php
 * ====================================================================
 */
error_reporting(0);
@ini_set('display_errors', 0);
@ini_set('memory_limit', '512M'); 

header('Content-Type: text/html; charset=utf-8');
$firma = chr(83).chr(101).chr(66).chr(97).chr(83); 
header('X-Author: ' . $firma);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>GoldHen Manager V4.0</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#060913">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/js/icon-192.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&family=Orbitron:wght@500;900&family=Share+Tech+Mono&family=Press+Start+2P&family=Montserrat:wght@700;800&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        html { background-color: #060913; }
        body { font-family: 'Outfit', sans-serif; background-color: transparent !important; overflow: hidden; height: 100dvh; }
        .bg-radial-glow { background: transparent !important; }
        .app-layer { position: fixed; inset: 0; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); opacity: 0; pointer-events: none; transform: scale(0.97); z-index: 10; background-color: transparent !important; }
        .app-layer.active { opacity: 1; pointer-events: auto; transform: scale(1); }
        .glass-panel { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .cyber-border { border: 1px solid rgba(34, 211, 238, 0.3); box-shadow: inset 0 0 15px rgba(34, 211, 238, 0.1), 0 0 10px rgba(168, 85, 247, 0.2); }
        .neu-card { background: rgba(11, 16, 26, 0.7); backdrop-filter: blur(10px); border-radius: 1.5rem; box-shadow: -6px -6px 14px rgba(255, 255, 255, 0.03), 8px 8px 18px rgba(0, 0, 0, 0.6); transition: all 0.2s ease; }
        .neu-card:active { box-shadow: inset -4px -4px 10px rgba(255, 255, 255, 0.02), inset 6px 6px 15px rgba(0, 0, 0, 0.5); transform: scale(0.98); }
        .scene-3d { perspective: 1000px; }
        .holo-card { background: linear-gradient(135deg, rgba(34, 211, 238, 0.1), rgba(168, 85, 247, 0.05)); border: 1px solid rgba(34, 211, 238, 0.3); transform: rotateX(15deg) translateZ(0); box-shadow: 0 20px 30px rgba(0, 0, 0, 0.8), inset 0 1px 1px rgba(255, 255, 255, 0.2); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s ease; transform-style: preserve-3d; backdrop-filter: blur(8px); }
        .holo-card:active { transform: rotateX(0deg) translateZ(-10px); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.9), inset 0 1px 1px rgba(255, 255, 255, 0.1); }
        .mech-btn { background: rgba(17, 24, 39, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.05); border-bottom: 6px solid #0891b2; border-radius: 1.2rem; transition: all 0.1s ease; box-shadow: 0 8px 15px rgba(0,0,0,0.6); }
        .mech-btn:active { transform: translateY(4px); border-bottom-width: 2px; box-shadow: 0 2px 5px rgba(0,0,0,0.8); }
        .aura-card { position: relative; background: rgba(10, 14, 20, 0.7); backdrop-filter: blur(10px); border-radius: 1.5rem; z-index: 1; }
        .aura-card::before { content: ""; position: absolute; inset: -2px; border-radius: 1.6rem; background: linear-gradient(45deg, #00f2fe, #4facfe, #9333ea, #00f2fe); background-size: 300%; z-index: -1; animation: aura-spin 4s linear infinite; filter: blur(4px); opacity: 0.5; transition: opacity 0.3s; }
        .aura-card:active::before { opacity: 1; filter: blur(8px); }
        @keyframes aura-spin { 0% { background-position: 0% 50%; } 100% { background-position: 100% 50%; } }
        .angle-cut { clip-path: polygon(10% 0, 100% 0, 90% 100%, 0% 100%); background: rgba(17, 24, 39, 0.8); backdrop-filter: blur(10px); transition: all 0.2s; }
        .angle-cut:active { transform: scale(0.95) skewX(2deg); background: rgba(31, 41, 55, 0.9); }
        .brutal-cyber { background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(10px); border: 2px solid #fff; box-shadow: 4px 4px 0px #22d3ee; transition: all 0.1s; }
        .brutal-cyber:active { transform: translate(4px, 4px); box-shadow: 0px 0px 0px #22d3ee; }
        .acrylic-card { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(25px) saturate(200%); border: 1px solid rgba(255, 255, 255, 0.15); border-top: 1px solid rgba(255, 255, 255, 0.3); border-left: 1px solid rgba(255, 255, 255, 0.2); box-shadow: 0 25px 40px rgba(0, 0, 0, 0.5); border-radius: 1.5rem; transition: transform 0.2s; }
        .acrylic-card:active { transform: scale(0.97); }
        .tactile-fx { transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1), filter 0.15s ease; cursor: pointer; transform-origin: center center; will-change: transform; }
        .tactile-fx:active { transform: scale(0.92); filter: brightness(1.2); }
        #main-header { transition: height 0.4s cubic-bezier(0.4, 0, 0.2, 1), background 0.3s ease, border 0.3s ease; width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative; overflow: hidden; z-index: 20; min-height: 140px; }
        .header-title { font-size: 32px !important; font-weight: 900 !important; white-space: nowrap !important; text-transform: uppercase !important; text-align: center !important; line-height: 1 !important; letter-spacing: -0.02em !important; margin: 0 0 4px 0 !important; transition: all 0.4s ease; }
        @media (min-width: 640px) { .header-title { font-size: 38px !important; } }
        .header-deco { display: flex; align-items: center; gap: 0.75rem; opacity: 0.8; margin-top: 0.2rem; margin-bottom: 0.8rem; transition: all 0.4s ease; }
        .btn-iniciar { transition: all 0.4s ease; outline: none !important; display: flex !important; align-items: center !important; justify-content: center !important; gap: 6px !important; height: 32px !important; padding: 0 18px !important; border-radius: 999px !important; cursor: pointer !important; margin: 0 auto !important; width: fit-content !important; min-width: 120px !important; position: relative; z-index: 50; }
        #main-header.theme-default { background: transparent; border-bottom: 1px solid rgba(34,211,238,0.2); backdrop-filter: blur(4px); }
        #main-header.theme-default .header-title { background: linear-gradient(to bottom, #ffffff, #a1a1aa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; filter: drop-shadow(0 2px 4px rgba(34,211,238,0.3)); }
        #main-header.theme-default .deco-text { color: #22d3ee; }
        #main-header.theme-default .btn-iniciar { background: linear-gradient(135deg, #06b6d4, #3b82f6); color: white; box-shadow: 0 4px 15px rgba(6,182,212,0.4); border: 1px solid rgba(255,255,255,0.2); }
        #main-header.theme-default .btn-iniciar.connected { background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.5); box-shadow: 0 0 15px rgba(16, 185, 129, 0.2); color: #34d399; animation: none; }
        #main-header.theme-liquid { background: rgba(34,211,238,0.05); border-bottom: 1px solid rgba(34,211,238,0.3); border-radius: 0 0 2rem 2rem; backdrop-filter: blur(15px); }
        #main-header.theme-liquid .header-title { color: #fff; text-shadow: 0 0 10px rgba(34,211,238,0.8); }
        #main-header.theme-liquid .deco-text { color: #a5f3fc; }
        #main-header.theme-liquid .btn-iniciar { background: rgba(0,0,0,0.4); color: #22d3ee; border: 1px solid #22d3ee; border-radius: 2rem !important; box-shadow: inset 0 0 10px rgba(34,211,238,0.3); }
        #main-header.theme-liquid .btn-iniciar.connected { color: #34d399; border-color: #34d399; box-shadow: inset 0 0 10px rgba(52,211,153,0.3); }
        #main-header.theme-cyber { background: rgba(255,0,255,0.05); border-bottom: 2px solid #f0f; box-shadow: 0 5px 15px rgba(255,0,255,0.1); backdrop-filter: blur(5px); }
        #main-header.theme-cyber .header-title { font-family: 'Orbitron', sans-serif; color: #0ff; text-shadow: 2px 2px 0px #f0f; }
        #main-header.theme-cyber .deco-text { color: #f0f; font-family: 'Share Tech Mono', monospace; }
        #main-header.theme-cyber .btn-iniciar { background: #0ff; color: #000; border: none; border-radius: 4px !important; font-weight: 900; box-shadow: 2px 2px 0px #f0f; }
        #main-header.theme-cyber .btn-iniciar.connected { background: #10b981; box-shadow: 2px 2px 0px #047857; color: #000; }
        #main-header.theme-neumorph { background: rgba(17,24,39,0.3); border-bottom: 1px solid rgba(255,255,255,0.05); border-radius: 0 0 1.5rem 1.5rem; box-shadow: 0 10px 20px rgba(0,0,0,0.3); backdrop-filter: blur(12px); }
        #main-header.theme-neumorph .header-title { color: #d4d4d8; text-shadow: 2px 2px 4px rgba(0,0,0,0.8); }
        #main-header.theme-neumorph .deco-text { color: #9ca3af; }
        #main-header.theme-neumorph .btn-iniciar { background: #111827; color: #34d399; border: none; box-shadow: 4px 4px 8px rgba(0,0,0,0.6), -4px -4px 8px rgba(255,255,255,0.05); }
        #main-header.theme-neumorph .btn-iniciar.connected { color: #10b981; box-shadow: inset 4px 4px 8px rgba(0,0,0,0.6), inset -4px -4px 8px rgba(255,255,255,0.05); }
        #main-header.theme-minimal { background: transparent; border-bottom: 1px dashed rgba(255,255,255,0.2); }
        #main-header.theme-minimal .header-title { color: #fff; font-weight: 300; letter-spacing: 2px; }
        #main-header.theme-minimal .deco-text { color: #71717a; }
        #main-header.theme-minimal .btn-iniciar { background: white; color: black; border: none; font-weight: bold; }
        #main-header.theme-minimal .btn-iniciar.connected { background: #10b981; color: white; }
        #main-header.theme-arcade { background: rgba(0,0,0,0.4); border-bottom: 1px dashed rgba(255,255,255,0.2); backdrop-filter: blur(4px); }
        #main-header.theme-arcade .header-title { font-size: 18px !important; letter-spacing: 0px !important; font-family: 'Press Start 2P', monospace; color: #ffeb3b; text-shadow: 2px 2px 0px #ef4444; }
        #main-header.theme-arcade .deco-text { color: #fff; font-family: 'Press Start 2P', monospace; }
        #main-header.theme-arcade .btn-iniciar { background: rgba(0,0,0,0.6); color: #0ff; border: 1px solid #0ff; border-radius: 8px !important; font-family: 'Press Start 2P', monospace; box-shadow: 0 0 10px rgba(0,255,255,0.3); }
        #main-header.theme-arcade .btn-iniciar.connected { color: #10b981; border-color: #10b981; box-shadow: 0 0 10px rgba(16,185,129,0.3); }
        #main-header.theme-hologram { background: radial-gradient(circle at top, rgba(34,211,238,0.15) 0%, transparent 80%); border-bottom: 1px solid rgba(34,211,238,0.4); }
        #main-header.theme-hologram .header-title { color: #fff; text-shadow: 0 0 5px #22d3ee, 0 0 15px #22d3ee; font-family: 'Orbitron', sans-serif; }
        #main-header.theme-hologram .deco-text { color: #22d3ee; }
        #main-header.theme-hologram .btn-iniciar { background: rgba(34,211,238,0.1); color: #22d3ee; border: 1px solid #22d3ee; border-radius: 0.5rem !important; box-shadow: inset 0 0 10px rgba(34,211,238,0.3); }
        #main-header.theme-hologram .btn-iniciar.connected { color: #10b981; border-color: #10b981; background: rgba(16,185,129,0.1); box-shadow: inset 0 0 10px rgba(16,185,129,0.3); }
        #main-header.theme-military { background: rgba(26,36,33,0.4); border-bottom: 2px solid #84cc16; backdrop-filter: blur(6px); }
        #main-header.theme-military .header-title { color: #bef264; font-family: 'Share Tech Mono', monospace; text-shadow: 1px 1px 0px #000; }
        #main-header.theme-military .deco-text { color: #84cc16; font-family: 'Share Tech Mono', monospace; }
        #main-header.theme-military .btn-iniciar { background: #4d7c0f; color: #000; border: 1px solid #bef264; border-radius: 4px !important; font-family: 'Share Tech Mono', monospace; font-weight: 900; }
        #main-header.theme-military .btn-iniciar.connected { background: #15803d; border-color: #86efac; color: #fff; }
        #main-header.theme-blood { background: linear-gradient(180deg, rgba(30,0,0,0.8) 0%, transparent 100%); border-bottom: 1px solid #991b1b; backdrop-filter: blur(4px); }
        #main-header.theme-blood .header-title { color: #fca5a5; text-shadow: 0 0 10px red; }
        #main-header.theme-blood .deco-text { color: #ef4444; }
        #main-header.theme-blood .btn-iniciar { background: #7f1d1d; color: #fff; border: 1px solid #f87171; border-radius: 0.5rem !important; box-shadow: 0 0 15px rgba(220,38,38,0.4); }
        #main-header.theme-blood .btn-iniciar.connected { background: #000; border-color: #ef4444; color: #ef4444; box-shadow: none; }
        #main-header.theme-oled { background: rgba(0,0,0,0.5); border-bottom: 1px solid #333; backdrop-filter: blur(10px); }
        #main-header.theme-oled .header-title { color: #fff; text-shadow: none; }
        #main-header.theme-oled .deco-text { color: #666; }
        #main-header.theme-oled .btn-iniciar { background: #111; color: #fff; border: 1px solid #555; border-radius: 2rem !important; }
        #main-header.theme-oled .btn-iniciar.connected { border-color: #10b981; color: #10b981; }
        #main-header.theme-terminal { background: rgba(0,0,0,0.5); border-bottom: 1px dashed #0f0; backdrop-filter: blur(4px); }
        #main-header.theme-terminal .header-title { color: #0f0; font-family: 'Share Tech Mono', monospace; text-shadow: 0 0 5px #0f0; }
        #main-header.theme-terminal .deco-text { color: #0f0; }
        #main-header.theme-terminal .btn-iniciar { background: #000; color: #0f0; border: 1px solid #0f0; border-radius: 4px !important; font-family: 'Share Tech Mono', monospace; }
        #main-header.theme-terminal .btn-iniciar.connected { background: #0f0; color: #000; }
        #main-header.theme-frosted { background: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(15px); }
        #main-header.theme-frosted .header-title { color: #fff; text-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        #main-header.theme-frosted .deco-text { color: #fff; }
        #main-header.theme-frosted .btn-iniciar { background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.3); border-radius: 1rem !important; backdrop-filter: blur(10px); }
        #main-header.theme-frosted .btn-iniciar.connected { background: rgba(16,185,129,0.3); border-color: rgba(16,185,129,0.5); }
        #main-header.theme-pop { background: rgba(139,92,246,0.4); border-bottom: 2px dashed #000; backdrop-filter: blur(8px); }
        #main-header.theme-pop .header-title { color: #fff; font-weight: 900; text-shadow: 2px 2px 0px #000; }
        #main-header.theme-pop .deco-text { color: #fde047; font-weight: 900; text-shadow: 1px 1px 0px #000; }
        #main-header.theme-pop .btn-iniciar { background: #ef4444; color: #fff; border: 2px solid #000; border-radius: 0.5rem !important; font-weight: 900; box-shadow: 2px 2px 0px #34d399; }
        #main-header.theme-pop .btn-iniciar.connected { background: #10b981; box-shadow: 2px 2px 0px #000; }
        #main-header.theme-radar-style { background: radial-gradient(circle at center, rgba(16,185,129,0.15) 0%, transparent 100%); border-bottom: 1px solid #059669; backdrop-filter: blur(4px); }
        #main-header.theme-radar-style .header-title { color: #34d399; font-family: 'Share Tech Mono', monospace; text-shadow: 0 0 8px #10b981; }
        #main-header.theme-radar-style .deco-text { color: #10b981; }
        #main-header.theme-radar-style .btn-iniciar { background: rgba(16,185,129,0.2); color: #6ee7b7; border: 1px solid #10b981; border-radius: 0.5rem !important; font-family: 'Share Tech Mono', monospace; }
        #main-header.theme-radar-style .btn-iniciar.connected { background: #10b981; color: #000; }
        #main-header.theme-translucent { background: transparent; }
        #main-header.theme-translucent .header-title { color: #fff; font-weight: 300; }
        #main-header.theme-translucent .deco-text { color: rgba(255,255,255,0.5); }
        #main-header.theme-translucent .btn-iniciar { background: transparent; color: #fff; border: 1px solid rgba(255,255,255,0.5); border-radius: 2rem !important; }
        #main-header.theme-translucent .btn-iniciar.connected { border-color: #10b981; color: #10b981; }
        #main-header.theme-pureglass { background: rgba(255, 255, 255, 0.05); border-bottom: 1px solid rgba(255, 255, 255, 0.15); backdrop-filter: blur(30px); border-radius: 0 0 2rem 2rem; }
        #main-header.theme-pureglass .header-title { color: #fff; text-shadow: 0 2px 10px rgba(0,0,0,0.5); }
        #main-header.theme-pureglass .deco-text { color: #fff; }
        #main-header.theme-pureglass .btn-iniciar { background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.3); border-radius: 2rem !important; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        #main-header.theme-pureglass .btn-iniciar.connected { background: rgba(16,185,129,0.2); border-color: rgba(16,185,129,0.5); box-shadow: 0 0 15px rgba(16,185,129,0.2); color: #fff; }
        #main-header.theme-neon { background: rgba(255,0,255,0.05); border-bottom: 2px solid #f0f; box-shadow: 0 5px 20px rgba(255,0,255,0.15); backdrop-filter: blur(6px); }
        #main-header.theme-neon .header-title { color: #0ff; text-shadow: 0 0 10px #0ff; }
        #main-header.theme-neon .deco-text { color: #f0f; text-shadow: 0 0 5px #f0f; }
        #main-header.theme-neon .btn-iniciar { background: transparent; color: #f0f; border: 2px solid #f0f; border-radius: 1rem !important; box-shadow: 0 0 10px #f0f, inset 0 0 10px #f0f; }
        #main-header.theme-neon .btn-iniciar.connected { border-color: #0ff; color: #0ff; box-shadow: 0 0 10px #0ff, inset 0 0 10px #0ff; }
        #main-header.theme-brutalism { background: rgba(255,255,255,0.7); border-bottom: 2px solid #000; backdrop-filter: blur(5px); }
        #main-header.theme-brutalism .header-title { color: #000; font-weight: 900; }
        #main-header.theme-brutalism .deco-text { color: #000; font-weight: 900; }
        #main-header.theme-brutalism .btn-iniciar { background: #000; color: #fff; border: 2px solid #000; border-radius: 4px !important; box-shadow: 3px 3px 0px #888; font-weight: 900; }
        #main-header.theme-brutalism .btn-iniciar.connected { background: #10b981; color: #000; box-shadow: 3px 3px 0px #000; }
        #main-header.theme-gold { background: linear-gradient(180deg, #1e1e1e 0%, #0a0a0a 100%); border-bottom: 1px solid #b45309; }
        #main-header.theme-gold .header-title { color: #fde68a; text-shadow: 0 2px 5px rgba(0,0,0,0.8); }
        #main-header.theme-gold .deco-text { color: #d97706; }
        #main-header.theme-gold .btn-iniciar { background: linear-gradient(to bottom, #d97706, #92400e); color: #fff; border: 1px solid #fcd34d; border-radius: 0.5rem !important; text-shadow: 0 1px 2px #000; }
        #main-header.theme-gold .btn-iniciar.connected { background: linear-gradient(to bottom, #15803d, #064e3b); border-color: #86efac; color: #fff; text-shadow: none; }
        #main-header.theme-outline { background: transparent; border-bottom: 1px dashed rgba(255,255,255,0.3); backdrop-filter: blur(2px); }
        #main-header.theme-outline .header-title { color: #fff; font-weight: 100; letter-spacing: 4px; }
        #main-header.theme-outline .deco-text { color: #fff; }
        #main-header.theme-outline .btn-iniciar { background: transparent; color: #fff; border: 1px dotted rgba(255,255,255,0.8); border-radius: 0 !important; }
        #main-header.theme-outline .btn-iniciar.connected { border-color: #10b981; color: #10b981; border-style: solid; }
        .app-layer .bg-\\[\\#0a0f1a\\], .app-layer .bg-\\[\\#02040a\\], .glass-premium { background-color: rgba(10, 15, 26, 0.60) !important; backdrop-filter: blur(8px) !important; border: 1px solid rgba(255,255,255,0.05) !important; }
        .terminal-text { font-family: 'Share Tech Mono', monospace; }
        .hide-scrollbar::-webkit-scrollbar { display: none !important; }
        .hide-scrollbar { -ms-overflow-style: none !important; scrollbar-width: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
        .glass-egg-premium { background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(0, 0, 0, 0.4) 100%); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.15); border-top: 1px solid rgba(255, 255, 255, 0.4); border-left: 1px solid rgba(255, 255, 255, 0.3); box-shadow: 0 30px 60px rgba(0, 0, 0, 0.8), inset 0 0 20px rgba(255, 255, 255, 0.05); transform-style: preserve-3d; }
        .sebas-gold-glitch { background: linear-gradient(to bottom, #fde68a, #f59e0b, #d97706); -webkit-background-clip: text; -webkit-text-fill-color: transparent; filter: drop-shadow(0 0 15px rgba(245, 158, 11, 0.7)); }
        @keyframes scanline-egg { 0% { top: -10%; } 100% { top: 110%; } }
        @keyframes spin-slow-egg { 100% { transform: rotate(360deg); } }
        .scan-bar-egg { position: absolute; left: 0; width: 100%; height: 2px; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent); box-shadow: 0 0 15px #fff; opacity: 0.3; animation: scanline-egg 3s linear infinite; pointer-events: none; }
        .holo-scene-egg { perspective: 1200px; }
        .cyber-card { position: relative; width: 100%; aspect-ratio: 1/1; border-radius: 24px; overflow: visible; display: flex; justify-content: center; align-items: center; padding: 2px; background: rgba(255, 255, 255, 0.02); transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; }
        .cyber-card:active { transform: scale(0.92) translateY(0); transition: transform 0.1s ease; }
        .card-mask { position: absolute; top: 0; left: 0; right: 0; bottom: 0; border-radius: 24px; overflow: hidden; z-index: 0; pointer-events: none; }
        .card-mask::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: conic-gradient(transparent, transparent, transparent, var(--accent-color, #00f0ff)); animation: rotate-border-cyber 3s linear infinite; pointer-events: none; opacity: 0.8; }
        .card-inner { position: relative; background: rgba(10, 15, 30, 0.4); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); width: 100%; height: 100%; border-radius: 22px; z-index: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 15px; box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.5), inset 0 1px 1px rgba(255, 255, 255, 0.1); pointer-events: none; }
        @keyframes rotate-border-cyber { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .cyber-card.clicked { animation: cyber-pulse-fx 0.5s ease-out forwards; }
        @keyframes cyber-pulse-fx { 0% { box-shadow: 0 0 0 0 var(--accent-color, #00f0ff); } 70% { box-shadow: 0 0 0 20px transparent; } 100% { box-shadow: 0 0 0 0 transparent; } }
        .card-cyan { --accent-color: #00f0ff; }
        .card-green { --accent-color: #00e676; }
        .card-purple { --accent-color: #b000ff; }
        .card-yellow { --accent-color: #ffab00; }
        .card-indigo { --accent-color: #6366f1; }
        .card-grey { --accent-color: #cbd5e1; }
        .icon-box { width: 50px; height: 50px; border-radius: 14px; display: flex; justify-content: center; align-items: center; background: rgba(255, 255, 255, 0.05); box-shadow: 0 0 15px rgba(0,0,0,0.2); transition: all 0.3s ease; }
        .cyber-card:active .icon-box { transform: scale(0.95); box-shadow: 0 0 35px var(--accent-color, #00f0ff); }
        .icon-box i { font-size: 24px; color: var(--accent-color, #00f0ff); transition: color 0.2s; }
        .cyber-card:active .icon-box i { color: #ffffff; }
    </style>
</head>
<body class="text-white select-none hide-scrollbar bg-radial-glow">
    <?php include 'modulos/wallpapers.php'; ?>
    <div id="intro-wrapper" class="fixed inset-0 z-[9999] bg-[#05050a] flex items-center justify-center">
        <?php include 'modulos/intros.php'; ?>
    </div>
    <div id="layer-launcher" class="app-layer active flex flex-col h-[100dvh] w-full overflow-hidden z-10">
        <div id="main-header" class="theme-default">
            <div id="header-estado-normal" class="w-full flex flex-col items-center justify-center transition-all duration-300 absolute inset-0 m-auto h-fit z-20">
                <h1 class="header-title">GOLDHEN MANAGER</h1>
                <div class="header-deco">
                    <i class="fa-solid fa-crosshairs text-[10px] deco-text"></i>
                    <div class="h-[1px] w-8 bg-gradient-to-r from-transparent to-current deco-text"></div>
                    <span class="text-[9px] font-mono tracking-widest uppercase font-bold deco-text">V 4.0</span>
                    <div class="h-[1px] w-8 bg-gradient-to-l from-transparent to-current deco-text"></div>
                    <i class="fa-brands fa-playstation text-[12px] deco-text"></i>
                </div>
                <button id="btn-iniciar-principal" onclick="window.abrirPanelConexion()" class="tactile-fx btn-iniciar shadow-lg">
                    <i class="fa-solid fa-power-off text-[11px] icon-btn"></i>
                    <span class="text-[10px] font-black tracking-widest uppercase text-btn mt-0.5">CONECTAR</span>
                </button>
            </div>
            <div id="header-estado-inputs" class="hidden w-full flex-col items-center justify-center transition-all duration-300 opacity-0 absolute inset-0 m-auto h-fit px-4 max-w-sm z-20 scale-95 pb-2">
                <div class="flex w-full gap-2 mb-3 mt-2">
                    <div class="relative flex-1 h-12 bg-[#050810]/80 backdrop-blur-md border border-white/10 rounded-xl overflow-hidden focus-within:border-cyan-500 transition-colors shadow-inner">
                        <i class="fa-solid fa-terminal absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 text-xs"></i>
                        <input type="text" id="ps-ip-full-input" value="192.168.1.28" class="w-full h-full bg-transparent pl-10 pr-4 text-white text-[14px] font-black tracking-wider outline-none text-center">
                    </div>
                    <button onclick="window.cerrarPanelConexion()" class="tactile-fx w-12 h-12 shrink-0 rounded-xl bg-white/5 flex items-center justify-center text-gray-400 hover:text-white border border-white/10"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>
                <div class="flex w-full gap-2 mb-2">
                    <button onclick="window.conectarManual(event)" class="tactile-fx flex-1 h-10 rounded-xl bg-cyan-600/20 border border-cyan-500/50 text-cyan-300 font-black tracking-widest text-[10px] uppercase shadow-[0_0_10px_rgba(34,211,238,0.2)]">MANUAL</button>
                    <button onclick="window.iniciarEscaneoAnimado()" class="tactile-fx flex-1 h-10 rounded-xl bg-purple-600/20 border border-purple-500/50 text-purple-300 font-black tracking-widest text-[10px] uppercase shadow-[0_0_10px_rgba(168,85,247,0.2)]"><i class="fa-solid fa-satellite-dish mr-1 animate-pulse"></i> AUTO</button>
                </div>
            </div>
            <div id="header-estado-scanner" class="hidden w-full h-full flex-col items-center justify-center transition-all duration-300 opacity-0 absolute inset-0 z-10">
                <div id="escaneo-animacion-box" class="absolute inset-0 w-full h-full flex items-center justify-center z-0 overflow-hidden"></div>
                <button onclick="window.abortarEscaneo()" class="absolute top-3 right-4 tactile-fx w-8 h-8 rounded-full bg-black/50 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white backdrop-blur-md z-30 shadow-lg"><i class="fa-solid fa-xmark"></i></button>
                <div class="absolute bottom-2 w-full flex flex-col items-center z-20 pointer-events-none">
                    <h3 id="escaneo-status-title" class="text-[11px] font-black tracking-widest text-cyan-400 uppercase mb-1 text-center drop-shadow-[0_0_5px_rgba(0,0,0,0.8)] bg-black/50 px-3 py-1 rounded-full backdrop-blur-md border border-white/5">ESCANEANDO RED LOCAL</h3>
                    <div id="escaneo-log" class="terminal-text text-[10px] text-cyan-300 font-mono tracking-widest text-center h-[16px] drop-shadow-[0_0_2px_black]"></div>
                </div>
            </div>
        </div>
        <div class="flex-1 w-full max-w-4xl mx-auto z-10 px-4 pt-4 pb-2 overflow-y-auto hide-scrollbar flex items-center">
            <div id="launcher-design-container" class="w-full transition-all duration-300"></div>
        </div>
        
        <div class="w-full flex flex-col items-center justify-center pt-2 pb-4 shrink-0 mt-auto z-10 bg-gradient-to-t from-[#02040a] to-transparent relative z-50">
            <div class="flex items-center gap-3 mb-1 pointer-events-none">
                <div class="w-6 h-[1px] bg-gradient-to-r from-transparent to-cyan-500"></div>
                <i class="fa-solid fa-gamepad text-cyan-400 text-[10px] drop-shadow-[0_0_8px_rgba(34,211,238,0.8)]"></i>
                <i class="fa-brands fa-playstation text-purple-500 text-[10px] drop-shadow-[0_0_8px_rgba(168,85,247,0.8)]"></i>
                <div class="w-6 h-[1px] bg-gradient-to-l from-transparent to-purple-500"></div>
            </div>
            <h1 onmousedown="event.stopPropagation()" ontouchstart="event.stopPropagation()" onclick="window.registrarToqueSecreto(event)" class="text-[11px] font-black tracking-widest text-white uppercase drop-shadow-[0_0_10px_rgba(255,255,255,0.4)] tactile-fx cursor-pointer px-4 py-1">GoldHEN Manager</h1>
            <p class="text-[8px] font-mono text-cyan-300 tracking-widest drop-shadow-[0_0_8px_rgba(34,211,238,0.6)] pointer-events-none mt-0.5">VERSIÓN 4.0 </p>
            <p class="text-[7px] font-bold text-gray-300 tracking-widest mt-1.5 uppercase pointer-events-none drop-shadow-md bg-white/5 px-2 py-0.5 rounded-full border border-white/10">Developed By SeBaS</p>
        </div>
    </div>
    <div id="toast-secreto" class="fixed bottom-[140px] left-1/2 -translate-x-1/2 z-40 px-4 py-2 rounded-full bg-[#0d1321]/95 border border-cyan-500/40 text-[10px] font-mono text-cyan-300 tracking-widest uppercase shadow-[0_0_20px_rgba(34,211,238,0.25)] transition-all duration-200 opacity-0 scale-90 pointer-events-none"></div>
    <div id="trofeo-banner" class="fixed bottom-6 left-1/2 -translate-x-1/2 translate-y-[200%] opacity-0 transition-all duration-500 ease-out z-[999999] flex items-center gap-4 px-5 py-3 rounded-[1.5rem] bg-[#0a0f1d]/95 border border-amber-400/50 shadow-[0_10px_40px_rgba(251,191,36,0.4)] w-[90%] max-w-sm pointer-events-none">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-300 to-amber-600 flex items-center justify-center text-black text-xl shadow-[0_0_15px_rgba(251,191,36,0.6)] shrink-0"><i class="fa-solid fa-trophy"></i></div>
        <div class="flex flex-col min-w-0"><span class="text-[8px] font-mono font-bold text-amber-400 uppercase tracking-widest">Trofeo Desbloqueado • Platino</span><span class="text-[11px] font-black text-white uppercase tracking-wider truncate mt-0.5">Mente Maestra del Sistema</span></div>
    </div>
    <div id="modal-easter-egg" class="fixed inset-0 z-[99990] bg-[#03050a]/90 hidden flex-col items-center justify-center opacity-0 transition-opacity duration-500">
        <canvas id="particle-canvas" class="absolute inset-0 z-0 pointer-events-auto"></canvas>
        <div class="holo-scene-egg w-full px-4 max-w-[360px] relative z-10 flex justify-center pointer-events-none mb-6">
            <div id="dev-holo-card" class="glass-egg-premium w-full rounded-[2rem] p-5 flex flex-col items-center text-center relative overflow-hidden pointer-events-auto transition-transform duration-100 scale-90">
                <div class="scan-bar-egg"></div>
                <div class="w-full flex items-center justify-between mb-4 z-10">
                    <span class="px-2.5 py-1 rounded-full bg-red-500/20 border border-red-500/40 text-[8px] font-mono font-bold text-red-300 uppercase tracking-widest flex items-center gap-1.5 shadow-inner"><span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-ping"></span> ROOT ACCESS</span>
                    <span class="text-[9px] font-mono text-cyan-200 tracking-widest font-bold drop-shadow-[0_0_5px_#22d3ee]">ID #001-VIP</span>
                </div>
                <div class="relative w-20 h-20 flex items-center justify-center my-1">
                    <div class="absolute inset-0 rounded-full border-2 border-dashed border-amber-400/60" style="animation: spin-slow-egg 10s linear infinite;"></div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-300/50 flex items-center justify-center text-amber-400 backdrop-blur-md shadow-[0_0_20px_rgba(245,158,11,0.5)]"><i class="fa-solid fa-crown text-3xl drop-shadow-[0_0_10px_rgba(245,158,11,1)]"></i></div>
                </div>
                <span class="text-[8px] font-mono text-gray-300 uppercase tracking-[0.3em] mt-4">CREATED & DEVELOPED BY</span>
                <h2 class="text-4xl font-orbitron font-black tracking-wider uppercase sebas-gold-glitch mt-1 mb-2">SeBaS</h2>
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-[8px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-md bg-cyan-500/20 border border-cyan-400/40 text-cyan-200 shadow-inner">Lead Architect</span>
                    <span class="text-[8px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-md bg-purple-500/20 border border-purple-400/40 text-purple-200 shadow-inner">PS4 Scene Dev</span>
                </div>
                <p class="text-[10px] text-white font-medium bg-black/30 border border-white/10 rounded-[1rem] p-3 mb-5 w-full shadow-inner leading-relaxed">"Diseñado línea por línea para llevar el control total de <span class="font-bold text-amber-400 drop-shadow-[0_0_5px_rgba(245,158,11,0.8)]">GoldHEN</span> al siguiente nivel."</p>
                <div class="grid grid-cols-3 gap-2 w-full mb-5">
                    <div class="bg-black/30 border border-white/10 rounded-[1rem] p-2 flex flex-col items-center shadow-inner"><i class="fa-solid fa-microchip text-cyan-400 text-sm mb-1"></i><span class="text-[11px] font-black text-white">CORE</span><span class="text-[7px] font-mono text-gray-500 uppercase mt-0.5">Native Build</span></div>
                    <div class="bg-black/30 border border-white/10 rounded-[1rem] p-2 flex flex-col items-center shadow-inner"><i class="fa-solid fa-bolt text-amber-400 text-sm mb-1"></i><span class="text-[11px] font-black text-white">100%</span><span class="text-[7px] font-mono text-gray-500 uppercase mt-0.5">Custom UI</span></div>
                    <div class="bg-black/30 border border-white/10 rounded-[1rem] p-2 flex flex-col items-center shadow-inner"><i class="fa-brands fa-playstation text-purple-400 text-sm mb-1"></i><span class="text-[11px] font-black text-white">PRO</span><span class="text-[7px] font-mono text-gray-500 uppercase mt-0.5">Edition</span></div>
                </div>
                <button onclick="window.cerrarEasterEgg()" class="tactile-fx w-full py-3.5 rounded-xl bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 text-white font-black text-[11px] uppercase tracking-widest shadow-[0_0_20px_rgba(59,130,246,0.4)] flex items-center justify-center gap-2 border border-white/20"><i class="fa-solid fa-power-off"></i> Volver al Sistema</button>
            </div>
        </div>
    </div>

    <?php include 'modulos/biblioteca.php'; ?>
    <?php include 'modulos/modding.php'; ?>
    <?php include 'modulos/explorador.php'; ?>
    <?php include 'modulos/transferir.php'; ?>
    <?php include 'modulos/roms.php'; ?>
    <?php include 'modulos/mods.php'; ?>
    <?php include 'modulos/ajustes.php'; ?>
    <script src="js/app.js"></script>
    <script src="js/biblioteca.js"></script>
    <script src="js/modding.js"></script>
    <script src="js/explorador.js"></script>
    <script src="js/transferir.js"></script>
    <script src="js/roms.js"></script>
    <script src="js/mods.js"></script>
    <script src="js/afr_mods.js"></script>
    <script>
        window.abrirModuloRetrasado = function(id, nativo, element) {
            if(window.playSynth) window.playSynth('select');
            let card = element.closest('.cyber-card') || element;
            card.classList.remove('clicked');
            void card.offsetWidth;
            card.classList.add('clicked');
            
            const launcher = document.getElementById('layer-launcher');
            setTimeout(() => {
                launcher.style.transform = 'scale(0.95)';
                launcher.style.opacity = '0';
                launcher.style.transition = 'all 0.2s ease-in-out';
            }, 250); 
            
            setTimeout(() => {
                if(nativo) window.abrirModuloNativo(id);
                else window.abrirModulo(id);
                
                setTimeout(() => {
                    launcher.style.transform = '';
                    launcher.style.opacity = '';
                    launcher.style.transition = '';
                }, 500);
            }, 450); 
        };

        if (typeof bootSelectedIntro === 'function') {
            bootSelectedIntro();
        } else {
            setTimeout(() => { const w = document.getElementById('intro-wrapper'); if(w) w.style.display = 'none'; }, 4000);
            setTimeout(() => { document.body.style.display = 'block'; }, 2000); 
        }

        history.replaceState({ page: 'launcher' }, "Launcher", "");
        window.abrirModuloNativo = function(idModulo) {
            history.pushState({ page: idModulo, ruta: '/' }, "Modulo", "");
            activarCapaVisual(idModulo);
            if (idModulo === 'explorador' && typeof cargarRutaFtp === 'function') { cargarRutaFtp('/', true); if (typeof renderizarAccesosRapidos === 'function') renderizarAccesosRapidos(); }
        };
        function activarCapaVisual(idModulo) {
            document.querySelectorAll('.app-layer').forEach(layer => { layer.classList.remove('active', 'flex'); layer.classList.add('hidden'); });
            const target = document.getElementById('layer-' + idModulo);
            if (target) { target.classList.remove('hidden'); setTimeout(() => { target.classList.add('active', 'flex'); }, 10); }
        }
        window.addEventListener('popstate', function(event) {
            let modals = Array.from(document.querySelectorAll('.fixed.inset-0:not(.hidden)'));
            modals = modals.filter(m => m.style.display !== 'none' && m.id !== 'layer-launcher' && !m.classList.contains('app-layer') && m.id !== 'intro-wrapper');
            if (modals.length > 0) {
                history.pushState(event.state, "", ""); 
                let topModal = modals[modals.length - 1];
                let closeBtn = topModal.querySelector('[onclick*="cerrar"], [onclick*="cancelar"]');
                if (closeBtn) { closeBtn.click(); } else { topModal.classList.remove('opacity-100'); setTimeout(() => topModal.classList.add('hidden'), 300); }
                return; 
            }
            if (event.state && event.state.page) {
                if (event.state.page === 'launcher') { activarCapaVisual('launcher'); } 
                else if (event.state.page === 'explorador' || event.state.page === 'ftp_folder') { activarCapaVisual('explorador'); let rutaDestino = event.state.ruta || '/'; if (typeof cargarRutaFtp === 'function') { cargarRutaFtp(rutaDestino, true); } } 
                else { activarCapaVisual(event.state.page); }
            } else { activarCapaVisual('launcher'); }
        });
        window.volverAlLauncher = function() { history.back(); };
    </script>
</body>
</html>
<?php // Developed By SeBaS ?>
