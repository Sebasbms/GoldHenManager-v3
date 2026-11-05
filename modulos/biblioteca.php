<?php
/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: modulos/biblioteca.php
 * ====================================================================
 */
?>
<style>
    .carousel-viewport-master { position: relative; width: 100%; max-width: 800px; flex: 1; min-height: 240px; display: flex; justify-content: center; align-items: center; perspective: 1200px; -webkit-perspective: 1200px; overflow: hidden; touch-action: none; }
    .glow-background-master { position: absolute; width: 280px; height: 280px; background: radial-gradient(circle, rgba(0, 162, 255, 0.6) 0%, rgba(0, 114, 230, 0.2) 40%, rgba(0,0,0,0) 70%); border-radius: 50%; z-index: 1; pointer-events: none; mix-blend-mode: screen; filter: blur(25px); opacity: 0.85; transition: background 0.5s ease; }
    .carousel-track-master { position: relative; width: 145px; height: 200px; transform-style: preserve-3d; -webkit-transform-style: preserve-3d; z-index: 2; }
    .ps4-box-case { position: absolute; width: 145px; height: 200px; background-color: #12151e; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5); transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1), opacity 0.6s ease, box-shadow 0.5s ease, border 0.5s ease; cursor: pointer; display: flex; flex-direction: column; border: 1px solid rgba(255, 255, 255, 0.05); transform-style: preserve-3d; -webkit-transform-style: preserve-3d; will-change: transform, opacity; -webkit-box-reflect: below 2px linear-gradient(transparent, transparent 65%, rgba(255,255,255,0.25)); }
    .ps4-box-case.active { border: 1px solid rgba(0, 210, 255, 0.5); box-shadow: 0 0 35px rgba(0, 162, 255, 0.8), 0 15px 40px rgba(0, 0, 0, 0.7); }
    .ps4-top-ribbon { background: linear-gradient(180deg, #0056c6 0%, #003780 100%); height: 22px; display: flex; align-items: center; padding: 0 8px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); z-index: 10; position: relative; }
    .ps4-ribbon-text { font-size: 8px; font-weight: 800; font-family: 'Outfit'; text-transform: uppercase; margin-left: 4px; letter-spacing: 1px; color: white; }
    .ps4-img-art { flex: 1; width: 100%; object-fit: cover; background-color: #0f121a; position: relative; }
    .sinc-overlay { background: rgba(3, 5, 11, 0.9); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(34, 211, 238, 0.3); border-radius: 4px; }
    input[type=range].custom-video-range { -webkit-appearance: none; width: 100%; background: transparent; }
    input[type=range].custom-video-range::-webkit-slider-thumb { -webkit-appearance: none; height: 14px; width: 14px; border-radius: 50%; background: #22d3ee; cursor: pointer; margin-top: -5px; box-shadow: 0 0 10px rgba(34,211,238,0.8); }
    input[type=range].custom-video-range::-webkit-slider-runnable-track { width: 100%; height: 4px; cursor: pointer; background: rgba(255,255,255,0.2); border-radius: 2px; }

    .theme-glass { font-family: 'Outfit', sans-serif; background: transparent !important; color: white !important; }
    .theme-glass .btn-icon { background: rgba(255,255,255,0.02) !important; border: 1px solid rgba(255,255,255,0.05) !important; border-radius: 0.75rem; color: white !important;}
    .theme-glass .search-box, .theme-glass .filter-btn { background: rgba(0,0,0,0.4) !important; border: 1px solid rgba(255,255,255,0.05) !important; border-radius: 0.75rem; color: white !important;}
    .theme-glass .game-card { background: rgba(255,255,255,0.02) !important; border: 1px solid rgba(255,255,255,0.05) !important; border-radius: 1.5rem; padding: 0.5rem; aspect-ratio: 3/4.2; backdrop-filter: blur(10px); }
    .theme-glass .game-img-wrap { border-radius: 1rem; overflow: hidden; height: 80%; }
    .theme-glass .game-title { font-size: 10px; font-weight: 900; text-align: center; margin-top: 0.5rem; color: #d1d5db !important; text-shadow: 0 2px 4px rgba(0,0,0,0.8); }
    
    .theme-liquid { font-family: 'Outfit', sans-serif; background: transparent !important; color: white !important; }
    .theme-liquid .btn-icon, .theme-liquid .search-box, .theme-liquid .filter-btn { background: rgba(34,211,238,0.05) !important; border: 1px solid rgba(34,211,238,0.2) !important; border-radius: 2rem; backdrop-filter: blur(15px); color: white !important;}
    .theme-liquid .game-card { background: rgba(255,255,255,0.03) !important; border: 1px solid rgba(255,255,255,0.1) !important; border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; padding: 0.75rem; aspect-ratio: 1/1.1; backdrop-filter: blur(25px) saturate(150%); box-shadow: inset 0 0 20px rgba(255,255,255,0.05), 0 10px 30px rgba(0,0,0,0.5); transition: border-radius 0.3s ease; }
    .theme-liquid .game-card:active { border-radius: 50%; }
    .theme-liquid .game-img-wrap { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; overflow: hidden; height: 75%; }
    .theme-liquid .game-title { font-size: 9px; font-weight: 900; text-align: center; margin-top: 0.75rem; color: #a5f3fc !important; text-shadow: 0 2px 4px rgba(0,0,0,0.8); }

    .theme-frosted { font-family: 'Outfit', sans-serif; background: transparent !important; color: white !important; }
    .theme-frosted .btn-icon, .theme-frosted .search-box, .theme-frosted .filter-btn { background: rgba(255,255,255,0.05) !important; border: 1px solid rgba(255,255,255,0.1) !important; border-radius: 1rem; backdrop-filter: blur(20px); color: white !important;}
    .theme-frosted .game-card { background: rgba(255,255,255,0.03) !important; border: 1px solid rgba(255,255,255,0.1) !important; border-radius: 1rem; padding: 0.5rem; aspect-ratio: 3/4.2; backdrop-filter: blur(30px); overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.3); }
    .theme-frosted .game-img-wrap { border-radius: 0.5rem; overflow: hidden; height: 80%; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .theme-frosted .game-title { font-size: 10px; font-weight: 700; text-align: center; padding: 0.5rem 0 0 0; color: #e2e8f0 !important; text-shadow: 0 2px 4px rgba(0,0,0,0.8); }

    .theme-translucent { font-family: 'Outfit', sans-serif; background: transparent !important; color: #fff !important; }
    .theme-translucent .btn-icon, .theme-translucent .search-box, .theme-translucent .filter-btn { background: rgba(0,0,0,0.3) !important; border: none !important; border-bottom: 1px solid rgba(255,255,255,0.2) !important; border-radius: 0; color: white !important;}
    .theme-translucent .game-card { background: rgba(0,0,0,0.4) !important; border: none !important; border-radius: 2rem; padding: 0.5rem; aspect-ratio: 3/4.2; backdrop-filter: blur(20px); box-shadow: 0 20px 40px rgba(0,0,0,0.4); }
    .theme-translucent .game-img-wrap { border-radius: 1.5rem; overflow: hidden; height: 100%; position: relative; }
    .theme-translucent .game-title { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); font-size: 11px; font-weight: 900; text-align: center; padding: 2rem 0.5rem 0.5rem; color: #fff !important; }

    .theme-holographic { font-family: 'Orbitron', sans-serif; background: transparent !important; color: #22d3ee !important; }
    .theme-holographic .btn-icon, .theme-holographic .search-box, .theme-holographic .filter-btn { background: rgba(34,211,238,0.05) !important; border: 1px solid rgba(34,211,238,0.3) !important; border-radius: 0.5rem; color: #22d3ee !important; box-shadow: 0 0 10px rgba(34,211,238,0.2); backdrop-filter: blur(10px); }
    .theme-holographic .game-card { background: linear-gradient(135deg, rgba(34,211,238,0.1), rgba(168,85,247,0.05)) !important; border: 1px solid rgba(34,211,238,0.4) !important; border-radius: 1rem; padding: 0.25rem; aspect-ratio: 3/4.2; box-shadow: inset 0 0 20px rgba(34,211,238,0.2), 0 10px 20px rgba(0,0,0,0.8); backdrop-filter: blur(5px); }
    .theme-holographic .game-img-wrap { border-radius: 0.75rem; overflow: hidden; height: 82%; filter: contrast(1.1) brightness(1.2) drop-shadow(0 0 5px rgba(34,211,238,0.5)); mix-blend-mode: screen; }
    .theme-holographic .game-title { font-size: 8px; font-weight: 900; text-align: center; margin-top: 0.5rem; color: #22d3ee !important; text-shadow: 0 0 5px #22d3ee; }

    .theme-cyber { font-family: 'Orbitron', sans-serif; background: transparent !important; color: #0ff !important; }
    .theme-cyber .btn-icon, .theme-cyber .search-box, .theme-cyber .filter-btn { background: rgba(0,0,0,0.5) !important; border: 1px solid #0ff !important; transform: skewX(-10deg); border-radius: 0; color: #0ff !important; backdrop-filter: blur(5px); }
    .theme-cyber .game-card { background: rgba(0,0,0,0.6) !important; border: 1px solid #0ff !important; clip-path: polygon(15px 0, 100% 0, 100% calc(100% - 15px), calc(100% - 15px) 100%, 0 100%, 0 15px); padding: 2px; aspect-ratio: 3/4.2; backdrop-filter: blur(5px); }
    .theme-cyber .game-img-wrap { height: 75%; clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px); }
    .theme-cyber .game-title { font-size: 8px; font-weight: 700; text-align: left; padding: 0.5rem; color: #0ff !important; }

    .theme-neumorph { font-family: 'Outfit', sans-serif; background: transparent !important; color: #a1a1aa !important; }
    .theme-neumorph .btn-icon, .theme-neumorph .search-box, .theme-neumorph .filter-btn { background: rgba(26,27,30,0.8) !important; border: none !important; border-radius: 1rem; box-shadow: 4px 4px 8px rgba(0,0,0,0.4), -4px -4px 8px rgba(255,255,255,0.03); color: #a1a1aa !important; }
    .theme-neumorph .game-card { background: rgba(26,27,30,0.8) !important; border-radius: 1.5rem; padding: 0.75rem; aspect-ratio: 3/4.2; box-shadow: 6px 6px 12px rgba(0,0,0,0.5), -6px -6px 12px rgba(255,255,255,0.02) !important; border: none !important; backdrop-filter: blur(10px);}
    .theme-neumorph .game-img-wrap { height: 78%; border-radius: 1rem; overflow: hidden; box-shadow: inset 2px 2px 5px rgba(0,0,0,0.5); }
    .theme-neumorph .game-title { font-size: 10px; font-weight: 800; text-align: center; margin-top: 0.75rem; color: #d4d4d8 !important; }

    .theme-minimal { font-family: 'Outfit', sans-serif; background: transparent !important; color: #ececec !important; }
    .theme-minimal .btn-icon, .theme-minimal .search-box, .theme-minimal .filter-btn { background: rgba(30,30,30,0.8) !important; border: none !important; border-radius: 9999px; color: #a0a0a0 !important; backdrop-filter: blur(5px);}
    .theme-minimal .game-card { background: transparent !important; border: none !important; padding: 0; aspect-ratio: 3/4.5; box-shadow: none !important;}
    .theme-minimal .game-img-wrap { height: 85%; border-radius: 1.5rem; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.5); }
    .theme-minimal .game-title { font-size: 11px; font-weight: 600; text-align: center; margin-top: 0.5rem; color: #ffffff !important; text-shadow: 0 2px 4px rgba(0,0,0,0.8);}

    .theme-arcade { font-family: 'Press Start 2P', monospace; background: transparent !important; color: #ffeb3b !important; }
    .theme-arcade .btn-icon, .theme-arcade .search-box, .theme-arcade .filter-btn { background: rgba(0,0,0,0.7) !important; border: 2px solid #00ffff !important; border-radius: 0; color: #ffeb3b !important; box-shadow: 3px 3px 0px #ff0055 !important; font-size: 8px; backdrop-filter: blur(5px); }
    .theme-arcade .game-card { background: rgba(0,0,0,0.8) !important; border: 2px solid #00ffff !important; padding: 4px; aspect-ratio: 3/4.2; box-shadow: 4px 4px 0px #ff0055 !important; backdrop-filter: blur(5px); }
    .theme-arcade .game-img-wrap { height: 75%; border: 2px solid #fff; }
    .theme-arcade .game-title { font-size: 6px; font-weight: normal; text-align: center; margin-top: 0.5rem; color: #fff !important; line-height: 1.4; text-shadow: 1px 1px 0px #000; }

    .theme-gold { font-family: 'Outfit', sans-serif; background: transparent !important; color: #fbbf24 !important; }
    .theme-gold .btn-icon, .theme-gold .search-box, .theme-gold .filter-btn { background: rgba(23,23,23,0.8) !important; border: 1px solid #d97706 !important; border-radius: 0.5rem; color: #fbbf24 !important; backdrop-filter: blur(5px);}
    .theme-gold .game-card { background: linear-gradient(to bottom, rgba(23,23,23,0.9), rgba(10,10,10,0.9)) !important; border: 1px solid #d97706 !important; border-radius: 0.5rem; padding: 0.25rem; aspect-ratio: 3/4.2; box-shadow: 0 4px 15px rgba(217, 119, 6, 0.2) !important; backdrop-filter: blur(5px);}
    .theme-gold .game-img-wrap { height: 80%; border: 1px solid #92400e; border-radius: 0.25rem; overflow: hidden; }
    .theme-gold .game-title { font-size: 10px; font-weight: 700; text-align: center; margin-top: 0.5rem; color: #fde68a !important; }

    .theme-neon { font-family: 'Outfit', sans-serif; background: transparent !important; color: #f0f !important; }
    .theme-neon .btn-icon, .theme-neon .search-box, .theme-neon .filter-btn { background: rgba(0,0,0,0.5) !important; border: 2px solid #f0f !important; border-radius: 1rem; color: #0ff !important; box-shadow: 0 0 10px #f0f, inset 0 0 5px #f0f !important; backdrop-filter: blur(5px);}
    .theme-neon .game-card { background: rgba(255,0,255,0.05) !important; border: 2px solid #0ff !important; border-radius: 1rem; padding: 0; aspect-ratio: 3/4.2; overflow: hidden; box-shadow: 0 0 15px #0ff !important; backdrop-filter: blur(5px);}
    .theme-neon .game-img-wrap { height: 100%; opacity: 0.8; mix-blend-mode: hard-light; border-radius: 0;}
    .theme-neon .game-title { position: absolute; bottom: 0; width: 100%; background: rgba(0,0,0,0.8); font-size: 11px; font-weight: 900; text-align: center; padding: 0.5rem; color: #f0f !important; text-shadow: 0 0 5px #f0f; border-top: 2px solid #f0f; }

    .theme-military { font-family: 'Share Tech Mono', monospace; background: transparent !important; color: #84cc16 !important; }
    .theme-military .btn-icon, .theme-military .search-box, .theme-military .filter-btn { background: rgba(47,62,54,0.8) !important; border: 2px solid #4d7c0f !important; border-radius: 0; color: #a3e635 !important; backdrop-filter: blur(5px); }
    .theme-military .game-card { background: rgba(39,53,43,0.9) !important; border: 2px solid #3f6212 !important; padding: 0.5rem; aspect-ratio: 3/4.2; border-left: 4px solid #84cc16 !important; border-radius: 0; backdrop-filter: blur(5px); }
    .theme-military .game-img-wrap { height: 75%; border: 1px solid #1a2421; filter: sepia(0.5) hue-rotate(60deg) saturate(1.5); border-radius: 0; }
    .theme-military .game-title { font-size: 10px; font-weight: bold; text-align: left; margin-top: 0.5rem; color: #bef264 !important; text-transform: uppercase; text-shadow: 1px 1px 0px #000; }

    .theme-blood { font-family: 'Outfit', sans-serif; background: transparent !important; color: #ef4444 !important; }
    .theme-blood .btn-icon, .theme-blood .search-box, .theme-blood .filter-btn { background: rgba(24,0,0,0.8) !important; border: 1px solid #991b1b !important; border-radius: 0.5rem; color: #fca5a5 !important; backdrop-filter: blur(5px); }
    .theme-blood .game-card { background: rgba(10,0,0,0.8) !important; border: 1px solid #7f1d1d !important; border-radius: 0.5rem; padding: 0; aspect-ratio: 3/4.2; overflow: hidden; box-shadow: 0 0 20px rgba(220, 38, 38, 0.2) !important; backdrop-filter: blur(5px); }
    .theme-blood .game-img-wrap { height: 100%; opacity: 0.7; filter: grayscale(50%) contrast(1.2); border-radius: 0; }
    .theme-blood .game-card:hover .game-img-wrap { opacity: 1; filter: grayscale(0%); }
    .theme-blood .game-title { position: absolute; top: 0; width: 100%; background: linear-gradient(to bottom, rgba(127,29,29,0.9), transparent); font-size: 12px; font-weight: 900; text-align: center; padding: 0.5rem; color: #fecaca !important; text-shadow: 2px 2px 4px #000; }

    .theme-outline { font-family: 'Outfit', sans-serif; background: transparent !important; color: #fff !important; }
    .theme-outline .btn-icon, .theme-outline .search-box, .theme-outline .filter-btn { background: rgba(255,255,255,0.05) !important; border: 1px solid rgba(255,255,255,0.2) !important; border-radius: 1rem; color: #fff !important; backdrop-filter: blur(10px); }
    .theme-outline .game-card { background: transparent !important; border: 1px solid rgba(255,255,255,0.3) !important; padding: 0.5rem; aspect-ratio: 3/4.2; box-shadow: none !important; border-radius: 1.2rem; transition: border-color 0.3s ease; }
    .theme-outline .game-card:hover { border-color: rgba(255,255,255,0.8) !important; }
    .theme-outline .game-img-wrap { height: 80%; border-radius: 0.8rem; overflow: hidden; filter: grayscale(20%); transition: filter 0.3s ease; }
    .theme-outline .game-card:hover .game-img-wrap { filter: grayscale(0%); }
    .theme-outline .game-title { font-size: 11px; font-weight: 300; text-align: center; margin-top: 0.5rem; color: #fff !important; letter-spacing: 1px; text-transform: uppercase; }

    .theme-radar { font-family: 'Share Tech Mono', monospace; background: transparent !important; color: #00ff00 !important; }
    .theme-radar .btn-icon, .theme-radar .search-box, .theme-radar .filter-btn { background: rgba(0,34,0,0.8) !important; border: 1px solid #00ff00 !important; border-radius: 0; color: #00ff00 !important; backdrop-filter: blur(5px);}
    .theme-radar .game-card { background: rgba(0,34,0,0.8) !important; border: 1px solid #00ff00 !important; border-radius: 0.5rem; padding: 0.25rem; aspect-ratio: 3/4.2; overflow: hidden; box-shadow: none !important; backdrop-filter: blur(5px);}
    .theme-radar .game-img-wrap { width: 100%; height: 80%; opacity: 0.7; filter: sepia(1) hue-rotate(70deg) saturate(3); border-radius: 0.25rem; overflow: hidden;}
    .theme-radar .game-title { font-size: 9px; font-weight: bold; text-align: center; color: #00ff00 !important; margin-top: 0.5rem; text-shadow: 1px 1px 0px #000;}

    .theme-pureglass { font-family: 'Outfit', sans-serif; background: transparent !important; color: #fff !important; }
    .theme-pureglass .app-header h2, .theme-pureglass .app-header span, .theme-pureglass .app-header i { color: #fff !important; text-shadow: 0 2px 10px rgba(0,0,0,0.5); }
    .theme-pureglass .search-box input { color: #fff !important; text-shadow: 0 1px 5px rgba(0,0,0,0.5); }
    .theme-pureglass .btn-icon, .theme-pureglass .search-box, .theme-pureglass .filter-btn { background: rgba(255, 255, 255, 0.1) !important; border: 1px solid rgba(255, 255, 255, 0.25) !important; border-radius: 2rem; backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px); box-shadow: inset 0 0 15px rgba(255,255,255,0.1), 0 4px 10px rgba(0,0,0,0.2); color: #fff !important;}
    .theme-pureglass .game-card { background: rgba(255, 255, 255, 0.05) !important; border: 1px solid rgba(255, 255, 255, 0.2) !important; border-radius: 2rem; padding: 0.5rem; aspect-ratio: 3/4.2; backdrop-filter: blur(30px) saturate(150%); -webkit-backdrop-filter: blur(30px) saturate(150%); box-shadow: 0 10px 30px rgba(0,0,0,0.4), inset 0 1px 15px rgba(255,255,255,0.2) !important; }
    .theme-pureglass .game-img-wrap { border-radius: 1.5rem; overflow: hidden; height: 80%; border-bottom: 1px solid rgba(255,255,255,0.3); }
    .theme-pureglass .game-title { font-size: 11px; font-weight: 800; text-align: center; margin-top: 0.5rem; color: #fff !important; text-shadow: 0 2px 5px rgba(0,0,0,0.8); }

    .theme-oled { font-family: 'Outfit', sans-serif; background: transparent !important; color: #ffffff !important; }
    .theme-oled .app-header h2, .theme-oled .app-header span, .theme-oled .app-header i { text-shadow: 0 2px 4px #000; }
    .theme-oled .btn-icon, .theme-oled .search-box, .theme-oled .filter-btn { background: rgba(0,0,0,0.8) !important; border: 1px solid #333 !important; border-radius: 0.5rem; color: #fff !important; }
    .theme-oled .game-card { background: rgba(0,0,0,0.9) !important; border: 1px solid #222 !important; border-radius: 0.5rem; padding: 0; aspect-ratio: 3/4.2; overflow: hidden; box-shadow: none !important;}
    .theme-oled .game-img-wrap { height: 85%; border-bottom: 1px solid #222; border-radius: 0;}
    .theme-oled .game-title { font-size: 10px; font-weight: 400; text-align: left; padding: 0.5rem; color: #999 !important; }

    .theme-pop { font-family: 'Outfit', sans-serif; background: transparent !important; color: #fff !important; }
    .theme-pop .app-header h2, .theme-pop .app-header span, .theme-pop .app-header i { color: #fff !important; text-shadow: 2px 2px 0px #000;}
    .theme-pop .btn-icon, .theme-pop .search-box, .theme-pop .filter-btn { background: rgba(255,255,255,0.9) !important; border: 3px solid #000 !important; border-radius: 0.5rem; color: #000 !important; box-shadow: 3px 3px 0px #000 !important; font-weight: 900; }
    .theme-pop .search-box input { color: #000 !important; }
    .theme-pop .game-card { background: #a855f7 !important; border: 3px solid #000 !important; border-radius: 1rem; padding: 0.5rem; aspect-ratio: 3/4.2; box-shadow: 5px 5px 0px #34d399 !important; transition: transform 0.1s; }
    .theme-pop .game-card:active { transform: translate(5px, 5px); box-shadow: 0px 0px 0px #34d399 !important; }
    .theme-pop .game-img-wrap { height: 75%; border: 3px solid #000 !important; border-radius: 0.5rem; overflow: hidden; }
    .theme-pop .game-title { font-size: 11px; font-weight: 900; text-align: center; margin-top: 0.5rem; color: #fff !important; text-shadow: 2px 2px 0px #000; text-transform: uppercase; }

    .theme-lightglass { font-family: 'Outfit', sans-serif; background: transparent !important; color: #fff !important; }
    .theme-lightglass .app-header h2, .theme-lightglass .app-header span, .theme-lightglass .app-header i { text-shadow: 0 2px 4px #000; }
    .theme-lightglass .btn-icon, .theme-lightglass .search-box, .theme-lightglass .filter-btn { background: rgba(255,255,255,0.2) !important; border: 1px solid rgba(255,255,255,0.3) !important; border-radius: 1rem; color: #fff !important; box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important; backdrop-filter: blur(10px);}
    .theme-lightglass .game-card { background: rgba(255,255,255,0.15) !important; border: 1px solid rgba(255,255,255,0.4) !important; border-radius: 1.5rem; padding: 0.5rem; aspect-ratio: 3/4.2; box-shadow: 0 10px 20px rgba(0,0,0,0.2) !important; backdrop-filter: blur(15px); }
    .theme-lightglass .game-img-wrap { height: 80%; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
    .theme-lightglass .game-title { font-size: 10px; font-weight: 900; text-align: center; margin-top: 0.5rem; color: #fff !important; text-shadow: 0 2px 4px #000; }

    .theme-glitch { font-family: 'Share Tech Mono', monospace; background: transparent !important; color: #fff !important; }
    .theme-glitch .btn-icon, .theme-glitch .search-box, .theme-glitch .filter-btn { background: rgba(0,0,0,0.8) !important; border: 1px solid #fff !important; border-radius: 0; color: #fff !important; text-shadow: 2px 0px 0px red, -2px 0px 0px cyan; box-shadow: inset 0 0 10px rgba(255,255,255,0.1) !important; backdrop-filter: blur(5px);}
    .theme-glitch .game-card { background: rgba(0,0,0,0.8) !important; border: 1px solid #333 !important; padding: 0.25rem; aspect-ratio: 3/4.2; position: relative; border-radius: 0; backdrop-filter: blur(5px);}
    .theme-glitch .game-card:hover { border-color: #fff !important; box-shadow: 4px 0 0 red, -4px 0 0 cyan !important; }
    .theme-glitch .game-img-wrap { height: 80%; filter: contrast(1.5) saturate(0.5); border-radius: 0;}
    .theme-glitch .game-title { font-size: 10px; font-weight: bold; text-align: center; margin-top: 0.5rem; color: #fff !important; text-shadow: 1px 0px 0px red, -1px 0px 0px cyan; text-transform: uppercase; }
</style>

<div id="layer-biblioteca" class="app-layer flex flex-col p-4 h-screen w-full overflow-hidden theme-glass">
    <div class="app-header w-full flex flex-col gap-3 shrink-0 pt-1 z-30">
        <div class="flex justify-between items-center w-full">
            <h2 class="text-2xl font-black tracking-tighter uppercase text-inherit">Biblioteca</h2>
            <div class="flex items-center gap-2">
                <button onclick="abrirGaleriaGlobal()" class="btn-icon tactile-fx w-8 h-8 flex items-center justify-center transition-all">
                    <i class="fa-solid fa-images text-xs"></i>
                </button>
                <button onclick="alternarColumnasGrid()" class="btn-icon tactile-fx w-8 h-8 flex items-center justify-center transition-all">
                    <i class="fa-solid fa-border-all text-xs" id="icono-columnas-grid"></i>
                </button>
                <button onclick="conmutarModoVista()" class="btn-icon tactile-fx w-8 h-8 flex items-center justify-center transition-all">
                    <i class="fa-solid fa-cube text-xs" id="icono-vista"></i>
                </button>
                <button onclick="volverAlLauncher()" class="btn-icon tactile-fx w-8 h-8 flex items-center justify-center transition-all">
                    <i class="fa-solid fa-house text-xs"></i>
                </button>
            </div>
        </div>

        <div class="search-box tactile-fx relative w-full h-10 flex items-center">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 text-[11px] opacity-60"></i>
            <input type="text" id="engine-search" oninput="ejecutarFiltroGlobal()" placeholder="Buscar por título o CUSA..." class="w-full h-full bg-transparent pl-9 pr-12 text-[12px] font-bold outline-none text-inherit pointer-events-none">
            <button onclick="forzarSincronizacionManual()" class="absolute right-2 w-7 h-7 flex items-center justify-center active:scale-95 transition-all opacity-80 hover:opacity-100 pointer-events-auto">
                <i class="fa-solid fa-rotate-right text-[10px]"></i>
            </button>
        </div>

        <div class="flex items-center justify-between w-full gap-2 mt-1">
            <button onclick="abrirModalFiltro()" class="filter-btn tactile-fx flex-1 h-9 flex items-center justify-between px-3 transition-all">
                <div class="flex items-center gap-2 overflow-hidden">
                    <i class="fa-solid fa-layer-group text-[10px] shrink-0 opacity-80"></i>
                    <span id="label-filtro-actual" class="text-[9px] font-black uppercase tracking-widest truncate text-inherit">TODOS</span>
                </div>
                <i class="fa-solid fa-chevron-down text-[9px] shrink-0 ml-1 opacity-60"></i>
            </button>
            
            <button onclick="abrirModalOrden()" class="filter-btn tactile-fx flex-1 h-9 flex items-center justify-between px-3 transition-all">
                <div class="flex items-center gap-2 overflow-hidden">
                    <i id="icono-orden-actual" class="fa-solid fa-arrow-down-a-z text-[10px] shrink-0 opacity-80"></i>
                    <span id="label-orden-actual" class="text-[9px] font-black uppercase tracking-widest truncate text-inherit">Nombre A-Z</span>
                </div>
                <i class="fa-solid fa-chevron-down text-[9px] shrink-0 ml-1 opacity-60"></i>
            </button>
        </div>
        
        <div class="flex items-center justify-start w-full px-1">
            <span class="text-[8px] font-black uppercase tracking-widest opacity-60">Estado: <span id="badge-total-txt" class="opacity-100" style="color: inherit;">0 Títulos</span></span>
        </div>
    </div>

    <div class="flex-grow w-full relative overflow-hidden mt-2 flex flex-col">
        <div id="container-view-grid" class="absolute inset-0 overflow-y-auto hide-scrollbar w-full h-full pb-28 hidden">
            <div id="dom-grid-target" class="grid grid-cols-3 gap-4 w-full px-1"></div>
        </div>

        <div id="container-view-3d" class="absolute inset-0 w-full h-full flex flex-col justify-start pt-6">
            <div class="carousel-viewport-master tactile-fx shrink-0" id="swipe-touch-zone">
                <div class="glow-background-master" id="dom-glow-target"></div>
                <div id="dom-3d-target" class="carousel-track-master"></div>
            </div>
            
            <div class="w-full shrink-0 flex flex-col items-center mt-6 mb-20 z-20" style="color: inherit;">
                <h3 id="text-title-3d" class="text-[16px] font-black uppercase truncate w-full px-4 text-center tracking-widest drop-shadow-md">---</h3>
                <div class="flex gap-2 mt-1.5">
                    <span id="text-cusa-3d" class="px-2 py-0.5 rounded bg-black/40 border border-white/10 text-[9px] font-mono font-bold tracking-widest text-white">---</span>
                    <span id="text-version-3d" class="px-2 py-0.5 rounded bg-black/40 border border-white/10 text-[9px] font-mono font-bold tracking-widest opacity-70 text-white">---</span>
                </div>
                <div class="w-[80%] h-px bg-white/10 my-4"></div>
                <div class="grid grid-cols-4 gap-2 w-full max-w-md px-2 text-white">
                    <button onclick="ejecutarAccionRapidaJuego('saves')" class="tactile-fx flex flex-col items-center justify-center p-3 rounded-2xl bg-black/40 border border-white/10 shadow-lg backdrop-blur-sm">
                        <i class="fa-solid fa-floppy-disk text-lg mb-1.5 opacity-90"></i>
                        <span class="text-[7.5px] font-black tracking-widest uppercase opacity-70">Saves</span>
                    </button>
                    <button onclick="ejecutarAccionRapidaJuego('galeria')" class="tactile-fx relative flex flex-col items-center justify-center p-3 rounded-2xl bg-black/40 border border-white/10 shadow-lg backdrop-blur-sm">
                        <i class="fa-solid fa-images text-lg mb-1.5 opacity-90"></i>
                        <span class="text-[7.5px] font-black tracking-widest uppercase opacity-70">Galería</span>
                        <span id="sheet-count-capturas-badge-3d" class="absolute top-1 right-1 bg-white text-black text-[7px] font-black px-1 rounded-md hidden">--</span>
                    </button>
                    <button onclick="ejecutarAccionRapidaJuego('editar_portada')" class="tactile-fx flex flex-col items-center justify-center p-3 rounded-2xl bg-black/40 border border-white/10 shadow-lg backdrop-blur-sm">
                        <i class="fa-solid fa-wand-magic-sparkles text-lg mb-1.5 opacity-90"></i>
                        <span class="text-[7.5px] font-black tracking-widest uppercase opacity-70">Portada</span>
                    </button>
                    <button onclick="ejecutarAccionRapidaJuego('dlcs')" class="tactile-fx flex flex-col items-center justify-center p-3 rounded-2xl bg-black/40 border border-white/10 shadow-lg backdrop-blur-sm">
                        <i class="fa-solid fa-puzzle-piece text-lg mb-1.5 opacity-90"></i>
                        <span class="text-[7.5px] font-black tracking-widest uppercase opacity-70">DLCs</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-sincronizacion-progreso" class="fixed inset-0 z-[500] sinc-overlay hidden flex items-center justify-center p-6 opacity-0 transition-opacity duration-300">
    <div class="glass-premium w-full max-w-sm rounded-[2.5rem] p-6 shadow-2xl flex flex-col items-center border border-white/10 bg-[#070b14]/90 modal-pop">
        <div class="w-12 h-12 rounded-full border border-cyan-500/20 bg-cyan-500/5 flex items-center justify-center text-cyan-400 text-lg mb-3"><i class="fa-brands fa-playstation animate-pulse"></i></div>
        <h3 id="sinc-modal-title" class="text-xs font-black tracking-widest text-cyan-400 uppercase">Sincronizando</h3>
        <p id="sinc-modal-text" class="text-[9px] font-mono text-gray-400 uppercase tracking-wider text-center mt-1 truncate max-w-xs">Estableciendo Socket...</p>
        <div class="w-full bg-white/5 border border-white/5 h-2 rounded-full overflow-hidden mt-4 relative"><div id="sinc-modal-bar" class="absolute left-0 top-0 h-full bg-gradient-to-r from-cyan-500 to-purple-500 rounded-full transition-all duration-200" style="width: 0%;"></div></div>
        <div class="flex justify-between items-center w-full mt-2 text-[8px] font-black uppercase tracking-widest text-gray-500"><span id="sinc-modal-percentage">0%</span><span id="sinc-modal-bytes">0 / 0 Analizados</span></div>
        <button onclick="abortarSincronizacionDesdeBoton()" class="w-full py-2 rounded-xl border border-red-500/20 bg-red-500/10 text-red-400 font-black tracking-widest text-[9px] uppercase mt-4 active:scale-95 transition-all">Cancelar</button>
    </div>
</div>

<div id="modal-nueva-categoria" class="fixed inset-0 z-[800] sinc-overlay hidden flex items-center justify-center p-6 opacity-0 transition-opacity duration-300">
    <div class="glass-premium w-full max-w-xs rounded-[2rem] p-5 shadow-2xl flex flex-col bg-[#070b14]/90 modal-pop">
        <h3 class="text-xs font-black tracking-widest text-cyan-400 uppercase mb-3">Nueva Categoría</h3>
        <div class="w-full h-10 bg-black/60 border border-white/10 rounded-xl overflow-hidden shadow-inner flex items-center px-3 focus-within:border-cyan-500/40"><input type="text" id="input-nueva-cat" placeholder="EJ: FAVORITOS..." class="w-full h-full bg-transparent text-[11px] font-black tracking-widest uppercase text-white outline-none"></div>
        <div class="flex gap-2 w-full mt-4 text-[9px] font-black tracking-widest uppercase"><button onclick="cerrarModalNuevaCategoria()" class="flex-1 py-2.5 rounded-xl border border-white/5 bg-white/5 text-gray-400">Cerrar</button><button onclick="crearCategoriaProcesar()" class="flex-1 py-2.5 rounded-xl bg-cyan-600 text-white shadow-lg">Crear</button></div>
    </div>
</div>

<div id="modal-filtro-categorias" onclick="cerrarModalFiltro()" class="fixed inset-0 z-[600] bg-black/60 backdrop-blur-sm hidden flex items-end justify-center opacity-0 transition-opacity duration-300">
    <div id="modal-filtro-content" onclick="event.stopPropagation()" class="w-full max-w-[400px] bg-[#0d131f] rounded-t-[2rem] border-t border-white/10 p-6 transform translate-y-full transition-transform duration-300 flex flex-col shadow-[0_-10px_40px_rgba(0,0,0,0.9)] pb-8">
        <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-5"></div>
        <h3 class="text-[11px] font-black tracking-widest text-gray-400 uppercase mb-4 text-center">Filtrar por Categoría</h3>
        <div id="lista-filtros-custom" class="flex flex-col gap-2 max-h-[40vh] overflow-y-auto custom-scrollbar pr-2"></div>
        <button onclick="abrirModalNuevaCategoria()" class="mt-4 w-full h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center gap-2 text-cyan-400 text-[10px] font-black uppercase tracking-widest active:scale-[0.98] transition-all hover:bg-cyan-500/20"><i class="fa-solid fa-plus"></i> Añadir Categoría</button>
    </div>
</div>

<div id="modal-orden-custom" onclick="cerrarModalOrden()" class="fixed inset-0 z-[600] bg-black/60 backdrop-blur-sm hidden flex items-end justify-center opacity-0 transition-opacity duration-300">
    <div id="modal-orden-content" onclick="event.stopPropagation()" class="w-full max-w-[400px] bg-[#0d131f] rounded-t-[2rem] border-t border-white/10 p-6 transform translate-y-full transition-transform duration-300 flex flex-col shadow-[0_-10px_40px_rgba(0,0,0,0.9)] pb-8">
        <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-5"></div>
        <h3 class="text-[11px] font-black tracking-widest text-gray-400 uppercase mb-4 text-center">Criterio de Orden</h3>
        <div id="lista-orden-custom" class="flex flex-col gap-2 pr-2"></div>
    </div>
</div>

<div id="modal-confirmar-eliminar" onclick="cerrarModalConfirmarEliminar()" class="fixed inset-0 z-[999] bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-6 opacity-0 transition-opacity duration-300">
    <div onclick="event.stopPropagation()" class="glass-premium w-full max-w-xs rounded-[2rem] p-6 shadow-2xl flex flex-col items-center bg-[#070b14]/95 border border-red-500/20 transform scale-95 transition-transform duration-300" id="modal-confirmar-content">
        <div class="w-12 h-12 rounded-full bg-red-500/10 flex items-center justify-center text-red-400 text-xl mb-4 border border-red-500/20"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <h3 class="text-[13px] font-black tracking-widest text-white uppercase text-center mb-2">Eliminar Categoría</h3>
        <p id="texto-confirmar-eliminar" class="text-[10px] text-gray-400 text-center mb-6 leading-relaxed px-2">¿Seguro?</p>
        <div class="flex gap-3 w-full">
            <button onclick="cerrarModalConfirmarEliminar()" class="flex-1 py-3 rounded-xl border border-white/10 bg-white/5 text-[10px] font-black tracking-widest uppercase text-gray-300 active:scale-95 transition-all hover:bg-white/10">Cancelar</button>
            <button onclick="ejecutarEliminacionCategoriaPendiente()" class="flex-1 py-3 rounded-xl bg-red-600/80 border border-red-500/50 text-[10px] font-black tracking-widest uppercase text-white active:scale-95 transition-all shadow-[0_0_15px_rgba(220,38,38,0.4)]">Eliminar</button>
        </div>
    </div>
</div>

<div id="sheet-detalles-juego" onclick="cerrarBottomSheet(event)" class="fixed inset-0 z-[500] bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div id="sheet-content-card" onclick="event.stopPropagation()" class="relative w-full max-w-[400px] rounded-[2rem] border border-white/10 overflow-hidden shadow-[0_20px_60px_rgba(0,0,0,0.9)] transform scale-95 transition-transform duration-300 flex flex-col bg-[#050810]">
        <div id="panel-bg-blur" class="absolute top-0 left-0 right-0 h-[220px] bg-cover bg-center opacity-30 blur-md pointer-events-none transition-all duration-500"></div>
        <div class="absolute top-0 left-0 right-0 h-[220px] bg-gradient-to-b from-[#050810]/10 via-[#050810]/70 to-[#050810] pointer-events-none"></div>

        <div class="relative z-10 flex-1 overflow-y-auto px-6 py-7 hide-scrollbar flex flex-col gap-6">
            <div class="flex gap-5 items-start relative">
                <button onclick="cerrarBottomSheet(null)" class="tactile-fx absolute top-0 right-0 w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:text-white transition-colors z-20">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <div id="panel-avatar-art" class="w-[120px] h-[160px] rounded-2xl border border-white/10 bg-cover bg-center shrink-0 shadow-[0_15px_30px_rgba(0,0,0,0.6)]"></div>
                <div class="flex flex-col flex-1 min-w-0 pr-6 mt-1">
                    <h2 id="panel-game-title" class="text-[18px] font-black uppercase text-white leading-tight drop-shadow-md">---</h2>
                    <span id="panel-game-cusa" class="text-[13px] font-mono font-black text-cyan-400 tracking-widest mt-1.5 uppercase">---</span>
                    <p id="panel-game-version" class="text-[12px] font-medium text-gray-300 uppercase tracking-widest mt-1">---</p>
                </div>
            </div>
            <div class="w-full flex flex-col gap-2 mt-1">
                <span class="text-[9px] font-black tracking-widest text-gray-500 uppercase">Ubicado en categoría:</span>
                <button onclick="abrirSelectorCategoriasCustom()" class="tactile-fx w-full h-12 bg-[#0b121c]/80 border border-white/10 rounded-xl flex items-center justify-between px-4 focus-within:border-cyan-500/30 transition-colors shadow-lg">
                    <span id="label-categoria-actual" class="text-[12px] font-black uppercase tracking-widest text-cyan-400">---</span>
                    <i class="fa-solid fa-chevron-down text-gray-500 text-[11px]"></i>
                </button>
            </div>
            <div class="flex flex-col gap-3 w-full">
                <button onclick="ejecutarAccionRapidaJuego('saves')" class="tactile-fx group flex items-center gap-4 p-4 rounded-2xl bg-[#0b121c]/80 border border-cyan-500/20 hover:bg-cyan-500/10 transition-all relative overflow-hidden shadow-lg text-left backdrop-blur-sm">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-cyan-500/90 shadow-[0_0_15px_rgba(34,211,238,0.5)]"></div>
                    <div class="w-11 h-11 rounded-xl bg-cyan-500/10 flex items-center justify-center border border-cyan-500/20 shrink-0">
                        <i class="fa-solid fa-floppy-disk text-cyan-400 text-lg"></i>
                    </div>
                    <div class="flex flex-col flex-1">
                        <span class="text-[12px] font-black uppercase tracking-widest text-gray-100 group-active:text-cyan-300 transition-colors">Backup de Partidas</span>
                        <span class="text-[10px] text-gray-400 mt-1 leading-tight">Respaldar y restaurar tus partidas.</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-cyan-500/50 text-xs mr-1 group-hover:translate-x-1 transition-transform"></i>
                </button>
                <button onclick="ejecutarAccionRapidaJuego('galeria')" class="tactile-fx group flex items-center gap-4 p-4 rounded-2xl bg-[#0b1414]/80 border border-emerald-500/20 hover:bg-emerald-500/10 transition-all relative overflow-hidden shadow-lg text-left backdrop-blur-sm">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-emerald-500/90 shadow-[0_0_15px_rgba(16,185,129,0.5)]"></div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20 shrink-0 relative">
                        <i class="fa-solid fa-images text-emerald-400 text-lg"></i>
                        <span id="sheet-count-capturas-badge" class="absolute -top-1 -right-1 bg-emerald-500 text-black text-[8px] font-black px-1.5 py-0.5 rounded-md hidden">--</span>
                    </div>
                    <div class="flex flex-col flex-1">
                        <span class="text-[12px] font-black uppercase tracking-widest text-gray-100 group-active:text-emerald-300 transition-colors">Galería Multimedia</span>
                        <span class="text-[10px] text-gray-400 mt-1 leading-tight">Ver capturas y videos guardados.</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-emerald-500/50 text-xs mr-1 group-hover:translate-x-1 transition-transform"></i>
                </button>
                <button onclick="ejecutarAccionRapidaJuego('editar_portada')" class="tactile-fx group flex items-center gap-4 p-4 rounded-2xl bg-[#110b1a]/80 border border-purple-500/20 hover:bg-purple-500/10 transition-all relative overflow-hidden shadow-lg text-left backdrop-blur-sm">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-purple-500/90 shadow-[0_0_15px_rgba(168,85,247,0.5)]"></div>
                    <div class="w-11 h-11 rounded-xl bg-purple-500/10 flex items-center justify-center border border-purple-500/20 shrink-0">
                        <i class="fa-solid fa-wand-magic-sparkles text-purple-400 text-lg"></i>
                    </div>
                    <div class="flex flex-col flex-1">
                        <span class="text-[12px] font-black uppercase tracking-widest text-gray-100 group-active:text-purple-300 transition-colors">Editar Portada</span>
                        <span class="text-[10px] text-gray-400 mt-1 leading-tight">Ir a la sección Modding.</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-purple-500/50 text-xs mr-1 group-hover:translate-x-1 transition-transform"></i>
                </button>
                <button onclick="ejecutarAccionRapidaJuego('dlcs')" class="tactile-fx group flex items-center gap-4 p-4 rounded-2xl bg-[#0b101f]/80 border border-blue-500/20 hover:bg-blue-500/10 transition-all relative overflow-hidden shadow-lg text-left backdrop-blur-sm">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-blue-500/90 shadow-[0_0_15px_rgba(59,130,246,0.5)]"></div>
                    <div class="w-11 h-11 rounded-xl bg-blue-500/10 flex items-center justify-center border border-blue-500/20 shrink-0">
                        <i class="fa-solid fa-puzzle-piece text-blue-400 text-lg"></i>
                    </div>
                    <div class="flex flex-col flex-1">
                        <span class="text-[12px] font-black uppercase tracking-widest text-gray-100 group-active:text-blue-300 transition-colors">Gestionar DLCs / Updates</span>
                        <span class="text-[10px] text-gray-400 mt-1 leading-tight">Administrar contenido adicional.</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-blue-500/50 text-xs mr-1 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div id="modal-selector-categorias-custom" onclick="cerrarSelectorCategoriasCustom(event)" class="fixed inset-0 z-[700] bg-black/60 backdrop-blur-sm hidden flex items-end justify-center opacity-0 transition-opacity duration-300">
    <div id="modal-selector-categorias-content" onclick="event.stopPropagation()" class="w-full max-w-[400px] bg-[#0d131f] rounded-t-[2rem] border-t border-white/10 p-6 transform translate-y-full transition-transform duration-300 flex flex-col shadow-[0_-10px_40px_rgba(0,0,0,0.9)] pb-8">
        <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-5"></div>
        <h3 class="text-[11px] font-black tracking-widest text-gray-400 uppercase mb-4 text-center">Mover a Categoría</h3>
        <div id="lista-categorias-custom-game" class="flex flex-col gap-2 max-h-[40vh] overflow-y-auto custom-scrollbar pr-2"></div>
    </div>
</div>

<div id="modal-galeria-juego" class="fixed inset-0 z-[800] bg-[#050810] hidden flex-col opacity-0 transition-opacity duration-300 transform translate-y-full">
    <div class="w-full flex items-center justify-between p-4 bg-[#0a0e14]/90 backdrop-blur-md border-b border-white/5 z-20 shrink-0 shadow-lg">
        <div class="flex items-center gap-3">
            <button onclick="cerrarGaleriaJuego()" class="tactile-fx w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-300 hover:bg-white/10"><i class="fa-solid fa-arrow-left"></i></button>
            <div class="flex flex-col">
                <h3 id="galeria-title" class="text-[13px] font-black uppercase text-white tracking-widest truncate max-w-[200px]">---</h3>
                <span id="galeria-count" class="text-[9px] font-mono text-emerald-400">0 Capturas</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="alternarColumnasGaleria()" class="tactile-fx w-10 h-10 rounded-full bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400"><i id="icono-columnas-galeria" class="fa-solid fa-border-all text-xs"></i></button>
            <button onclick="forzarRecargaGaleria()" class="tactile-fx w-10 h-10 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400"><i class="fa-solid fa-rotate-right text-xs"></i></button>
        </div>
    </div>
    <div id="galeria-grid-container" class="flex-1 w-full h-full overflow-y-auto p-4 pb-28 hide-scrollbar touch-pan-y" style="-webkit-overflow-scrolling: touch;">
        <div id="galeria-loader" class="w-full h-full flex flex-col items-center justify-center text-emerald-500 opacity-60">
            <i class="fa-solid fa-satellite-dish animate-pulse text-4xl mb-3"></i>
            <p class="text-[10px] font-mono tracking-widest uppercase">Cargando...</p>
        </div>
        <div id="galeria-grid" class="grid grid-cols-2 gap-4 hidden"></div>
    </div>
</div>

<div id="lightbox-visor" onclick="cerrarLightbox()" class="fixed inset-0 z-[900] bg-black/60 backdrop-blur-md hidden flex flex-col items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div id="lightbox-modal" onclick="event.stopPropagation()" class="w-full max-w-3xl bg-[#0a0f1a]/90 backdrop-blur-2xl border border-white/10 rounded-[2rem] shadow-2xl flex flex-col transform scale-95 transition-transform duration-300 overflow-hidden">
        
        <div class="w-full flex items-center justify-between p-3 bg-black/50 border-b border-white/5 z-10 shrink-0">
            <p id="lightbox-name" class="text-[10px] font-mono text-cyan-400 tracking-widest uppercase truncate max-w-[60%] pl-3"></p>
            <div class="flex items-center gap-2 pr-1 shrink-0">
                <button onclick="borrarMediaLightbox()" class="tactile-fx w-8 h-8 rounded-full bg-red-500/10 text-red-400 border border-red-500/20"><i class="fa-solid fa-trash-can text-sm"></i></button>
                <button onclick="compartirImagenLightbox()" class="tactile-fx w-8 h-8 rounded-full bg-cyan-500/10 text-cyan-400 border border-cyan-500/20"><i class="fa-solid fa-share-nodes text-sm"></i></button>
                <button onclick="cerrarLightbox()" class="tactile-fx w-8 h-8 rounded-full bg-white/5 text-white border border-white/10"><i class="fa-solid fa-xmark text-sm"></i></button>
            </div>
        </div>

        <div id="lightbox-media-container" class="w-full flex items-center justify-center bg-black relative min-h-[25vh] max-h-[60vh] overflow-hidden">
            <img id="lightbox-img" src="" class="max-w-full max-h-[60vh] object-contain hidden" onclick="event.stopPropagation()">
            <video id="lightbox-video" src="" playsinline class="max-w-full max-h-[60vh] object-contain hidden"></video>
        </div>

        <div id="video-custom-controls" onclick="event.stopPropagation()" class="w-full bg-[#050810] border-t border-white/5 p-3 flex flex-col gap-3 hidden shrink-0">
            <div class="flex items-center gap-3 w-full px-2">
                <span id="video-current-time" class="text-[10px] font-mono text-cyan-400">00:00</span>
                <input type="range" id="video-progress" class="custom-video-range flex-1" value="0" min="0" step="0.1">
                <span id="video-duration" class="text-[10px] font-mono text-gray-400">00:00</span>
            </div>
            <div class="flex items-center justify-between w-full px-2">
                <div class="flex items-center gap-6">
                    <button id="video-play-btn" class="tactile-fx text-white hover:text-cyan-400 transition-colors"><i class="fa-solid fa-play text-xl"></i></button>
                    <button id="video-mute-btn" class="tactile-fx text-gray-300 hover:text-white transition-colors"><i class="fa-solid fa-volume-high text-sm"></i></button>
                </div>
                <button id="video-fullscreen-btn" class="tactile-fx text-gray-300 hover:text-white transition-colors"><i class="fa-solid fa-expand text-sm"></i></button>
            </div>
        </div>
    </div>
</div>

<div id="modal-confirmar-borrar-galeria" class="fixed inset-0 z-[999] bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-6 opacity-0 transition-opacity duration-300">
    <div class="glass-premium w-full max-w-xs rounded-[2rem] p-6 shadow-2xl flex flex-col items-center bg-[#070b14]/95 border border-red-500/20 transform scale-95 transition-transform duration-300" id="modal-content-borrar-galeria">
        <div class="w-14 h-14 rounded-full bg-red-500/10 flex items-center justify-center text-red-400 text-2xl mb-4 border border-red-500/20 shadow-[0_0_15px_rgba(239,68,68,0.2)]"><i class="fa-solid fa-trash-can"></i></div>
        <h3 class="text-[14px] font-black tracking-widest text-white uppercase text-center mb-1">Eliminar Archivo</h3>
        <p class="text-[10px] text-gray-400 text-center font-mono mb-6 leading-relaxed px-2">¿Borrar de la consola permanentemente?</p>
        <div class="flex gap-3 w-full">
            <button onclick="cerrarModalBorrarGaleria()" class="tactile-fx flex-1 py-3 rounded-xl border border-white/10 bg-white/5 text-[10px] font-black tracking-widest uppercase text-gray-300 hover:bg-white/10">Cancelar</button>
            <button onclick="ejecutarBorradoMediaConfirmado()" class="tactile-fx flex-1 py-3 rounded-xl bg-gradient-to-r from-red-600 to-red-800 border border-red-500/50 text-[10px] font-black tracking-widest uppercase text-white shadow-[0_0_15px_rgba(220,38,38,0.4)]">Borrar</button>
        </div>
    </div>
</div>

<div id="modal-dlc-update" onclick="cerrarModalDLC()" class="fixed inset-0 z-[800] bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div id="modal-dlc-content" onclick="event.stopPropagation()" class="relative w-full max-w-[400px] rounded-[2rem] border border-white/10 overflow-hidden shadow-[0_20px_60px_rgba(0,0,0,0.9)] transform scale-95 transition-transform duration-300 flex flex-col bg-[#050810]">
        <div id="dlc-bg-blur" class="absolute top-0 left-0 right-0 h-[150px] bg-cover bg-center opacity-30 blur-md pointer-events-none transition-all duration-500"></div>
        <div class="absolute top-0 left-0 right-0 h-[150px] bg-gradient-to-b from-[#050810]/10 via-[#050810]/80 to-[#050810] pointer-events-none"></div>
        <div class="relative z-10 flex flex-col p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="flex flex-col">
                    <h3 class="text-[10px] font-black tracking-widest text-blue-400 uppercase mb-1">Gestor de Contenido</h3>
                    <h2 id="dlc-game-title" class="text-[16px] font-black uppercase text-white leading-tight drop-shadow-md truncate max-w-[250px]">---</h2>
                    <span id="dlc-game-cusa" class="text-[10px] font-mono font-black text-gray-400 tracking-widest mt-1 uppercase">---</span>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="forzarRecargaDLCs()" class="tactile-fx w-8 h-8 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-400 backdrop-blur-md border border-blue-500/20 shadow-lg shrink-0"><i class="fa-solid fa-rotate-right text-xs"></i></button>
                    <button onclick="cerrarModalDLC()" class="tactile-fx w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white backdrop-blur-md border border-white/20 shadow-lg shrink-0"><i class="fa-solid fa-xmark text-sm"></i></button>
                </div>
            </div>
            <div id="dlc-loader" class="w-full py-10 flex flex-col items-center justify-center text-blue-500 opacity-60">
                <i class="fa-solid fa-satellite-dish animate-pulse text-3xl mb-3"></i>
                <p class="text-[10px] font-mono tracking-widest uppercase">Calculando pesos...</p>
            </div>
            <div id="dlc-data-container" class="hidden flex flex-col gap-5 overflow-y-auto custom-scrollbar max-h-[50vh] pr-2">
                <div class="flex flex-col gap-2">
                    <span class="text-[9px] font-black tracking-widest text-gray-500 uppercase">Estado del Update:</span>
                    <div id="dlc-update-box" class="flex items-center gap-4 p-4 rounded-2xl bg-[#0b121c]/80 border border-white/5 shadow-lg"></div>
                </div>
                <div class="flex flex-col gap-2">
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-black tracking-widest text-gray-500 uppercase">DLCs Instalados:</span>
                        <span id="dlc-count-badge" class="text-[9px] font-black tracking-widest text-blue-400 bg-blue-500/10 px-2 py-0.5 rounded-md">0</span>
                    </div>
                    <div id="dlc-list-box" class="flex flex-col gap-2"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-confirmar-eliminar-contenido" onclick="cerrarModalEliminarContenido()" class="fixed inset-0 z-[999] bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-6 opacity-0 transition-opacity duration-300">
    <div onclick="event.stopPropagation()" class="glass-premium w-full max-w-xs rounded-[2rem] p-6 shadow-2xl flex flex-col items-center bg-[#070b14]/95 border border-red-500/30 transform scale-95 transition-transform duration-300" id="modal-content-eliminar-contenido">
        <div class="relative w-16 h-16 rounded-full bg-red-500/10 flex items-center justify-center text-red-500 text-2xl mb-4 border border-red-500/30 shadow-[0_0_20px_rgba(239,68,68,0.2)]">
            <div class="absolute inset-0 rounded-full border border-red-500/50 animate-ping opacity-20"></div>
            <i class="fa-solid fa-fire-flame-curved"></i>
        </div>
        <h3 class="text-[14px] font-black tracking-widest text-white uppercase text-center mb-1">Peligro Crítico</h3>
        <p class="text-[10px] text-red-400 font-mono tracking-widest uppercase text-center mb-3">Borrado de Consola</p>
        <p id="texto-confirmar-contenido" class="text-[11px] text-gray-400 text-center mb-6 leading-relaxed px-2">
            ¿Estás seguro de eliminar el archivo <br>
            <span id="nombre-contenido-eliminar" class="text-white font-black text-[10px] bg-white/10 px-2 py-1 rounded-md mt-2 inline-block border border-white/10">---</span><br>
            <span class="text-[8px] text-red-500/70 mt-2 block tracking-widest">Esta acción es irreversible por FTP.</span>
        </p>
        <div class="flex gap-3 w-full">
            <button onclick="cerrarModalEliminarContenido()" class="tactile-fx flex-1 py-3 rounded-xl border border-white/10 bg-white/5 text-[10px] font-black tracking-widest uppercase text-gray-300 hover:bg-white/10">Cancelar</button>
            <button onclick="ejecutarEliminacionContenidoFTP()" class="tactile-fx flex-1 py-3 rounded-xl bg-gradient-to-r from-red-600 to-red-800 border border-red-500/50 text-[10px] font-black tracking-widest uppercase text-white shadow-[0_0_20px_rgba(220,38,38,0.5)] flex items-center justify-center gap-2">
                <i id="icono-btn-eliminar" class="fa-solid fa-skull"></i> Borrar
            </button>
        </div>
    </div>
</div>
<?php
// Developed By SeBaS
?>
