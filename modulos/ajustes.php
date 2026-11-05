<?php
/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: modulos/ajustes.php
 * ====================================================================
 */
error_reporting(0);
@ini_set('display_errors', 0);
@ini_set('memory_limit', '512M'); 

$local_ip = '127.0.0.1';
$sock = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
if ($sock) {
    @socket_connect($sock, "8.8.8.8", 53);
    @socket_getsockname($sock, $name);
    @socket_close($sock);
    if ($name) $local_ip = $name;
}
$port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : '8080';
$real_host_url = "http://" . $local_ip . ":" . $port;
?>
<div id="layer-ajustes" class="app-layer flex flex-col p-4 h-[100dvh] w-full overflow-hidden bg-[#02040a] relative hidden">
    
    <div class="w-full flex items-center justify-between z-30 shrink-0 pt-1 mb-5">
        <div class="flex items-center gap-3">
            <button onclick="window.volverAlLauncher()" class="tactile-fx w-10 h-10 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-center hover:bg-white/5">
                <i class="fas fa-arrow-left text-gray-300"></i>
            </button>
            <div class="flex flex-col">
                <h2 class="text-[17px] font-black tracking-tighter uppercase text-white leading-none">Ajustes</h2>
                <span class="text-[9px] font-mono text-cyan-400 tracking-widest mt-0.5">Personalización y Sistema</span>
            </div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 shadow-[0_0_15px_rgba(34,211,238,0.2)]">
            <i class="fas fa-sliders text-lg"></i>
        </div>
    </div>

    <div class="w-full flex bg-[#0a0f1a] rounded-xl p-1 mb-4 border border-white/5 shrink-0 shadow-lg relative z-30">
        <button id="btn-tab-ui" onclick="switchAjustesTab('ui')" class="tactile-fx flex-1 py-2.5 rounded-lg bg-cyan-600 text-white text-[9px] font-black uppercase tracking-widest shadow-[0_0_15px_rgba(6,182,212,0.4)]">UI Visual</button>
        <button id="btn-tab-fx" onclick="switchAjustesTab('fx')" class="tactile-fx flex-1 py-2.5 rounded-lg bg-transparent text-gray-500 text-[9px] font-black uppercase tracking-widest">Sensores</button>
        <button id="btn-tab-sys" onclick="switchAjustesTab('sys')" class="tactile-fx flex-1 py-2.5 rounded-lg bg-transparent text-gray-500 text-[9px] font-black uppercase tracking-widest">Sistema</button>
    </div>

    <div class="flex-1 overflow-y-auto hide-scrollbar flex flex-col gap-4 pb-10 z-20">

        <div id="tab-ui" class="flex flex-col gap-3 w-full">
            
            <div class="w-full bg-[#0a0f1a] border border-white/5 rounded-[1.2rem] p-3 shadow-lg flex flex-col gap-2">
                <div class="flex flex-col border-b border-white/5 pb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm shadow-inner shrink-0">
                            <i class="fas fa-heading"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] font-black uppercase text-white tracking-widest leading-none">1. Cabecera</span>
                            <span class="text-[8px] font-bold text-emerald-400 uppercase tracking-widest mt-0.5">Diseño del panel superior</span>
                        </div>
                    </div>
                </div>
                <div class="relative w-full">
                    <div onclick="window.toggleCustomSelectHeader()" class="tactile-fx w-full bg-[#111827] border border-white/10 rounded-xl px-3 py-2.5 text-[11px] font-bold text-white flex justify-between items-center cursor-pointer shadow-inner">
                        <span id="custom-select-header-label" class="uppercase tracking-wider">1. Default</span>
                        <i id="custom-select-header-icon" class="fas fa-chevron-down text-emerald-400 transition-transform duration-300"></i>
                    </div>
                    <div id="custom-select-header-options" class="absolute w-full mt-1 bg-[#0a0f1a] border border-white/10 rounded-xl shadow-2xl overflow-hidden z-[90] origin-top transform scale-y-0 opacity-0 transition-all duration-300 max-h-[180px] overflow-y-auto custom-scrollbar flex flex-col pointer-events-none">
                        <div onclick="window.seleccionarHeaderCustom('theme-default', '1. Default')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">1. Default</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-liquid', '2. Liquid Glass')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">2. Liquid Glass</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-cyber', '3. Cyberpunk')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">3. Cyberpunk</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-neumorph', '4. Neumorfismo')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">4. Neumorfismo</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-minimal', '5. Minimalista')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">5. Minimalista</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-arcade', '6. Arcade Retro')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">6. Arcade Retro</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-hologram', '7. Holograma 3D')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">7. Holograma 3D</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-military', '8. Military')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">8. Military</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-blood', '9. Blood Vampire')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">9. Blood Vampire</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-oled', '10. OLED Black')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">10. OLED Black</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-terminal', '11. Terminal DOS')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">11. Terminal DOS</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-frosted', '12. Frosted Glass')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">12. Frosted Glass</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-pop', '13. Pop Art')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">13. Pop Art</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-radar-style', '14. Radar Console')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">14. Radar Console</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-translucent', '15. Translucent')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">15. Translucent</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-pureglass', '16. Pure Glass')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">16. Pure Glass</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-neon', '17. Neon Synth')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">17. Neon Synth</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-brutalism', '18. Brutalism')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">18. Brutalism</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-gold', '19. GoldHEN')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">19. GoldHEN</div>
                        <div onclick="window.seleccionarHeaderCustom('theme-outline', '20. Outline')" class="p-3 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">20. Outline</div>
                    </div>
                </div>
            </div>

            <div class="w-full bg-[#0a0f1a] border border-white/5 rounded-[1.2rem] p-3 shadow-lg flex flex-col gap-2">
                <div class="flex flex-col border-b border-white/5 pb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-sm shadow-inner shrink-0">
                            <i class="fas fa-satellite-dish"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] font-black uppercase text-white tracking-widest leading-none">2. Radar</span>
                            <span class="text-[8px] font-bold text-cyan-400 uppercase tracking-widest mt-0.5">Animación al buscar IP</span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2 w-full items-center">
                    <div class="relative flex-1">
                        <div onclick="window.toggleCustomSelectRadar()" class="tactile-fx w-full bg-[#111827] border border-white/10 rounded-xl px-3 py-2.5 text-[11px] font-bold text-white flex justify-between items-center cursor-pointer shadow-inner">
                            <span id="custom-select-radar-label" class="uppercase tracking-wider truncate w-[130px]">14. Digital Glitch</span>
                            <i id="custom-select-radar-icon" class="fas fa-chevron-down text-cyan-400 transition-transform duration-300"></i>
                        </div>
                        <div id="custom-select-radar-options" class="absolute w-full mt-1 bg-[#0a0f1a] border border-white/10 rounded-xl shadow-2xl overflow-hidden z-[85] origin-top transform scale-y-0 opacity-0 transition-all duration-300 max-h-[180px] overflow-y-auto custom-scrollbar flex flex-col pointer-events-none">
                            <div onclick="window.seleccionarRadarCustom('anim_1', '1. Pulse Matrix')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">1. Pulse Matrix</div>
                            <div onclick="window.seleccionarRadarCustom('anim_2', '2. Cyber Rift')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">2. Cyber Rift</div>
                            <div onclick="window.seleccionarRadarCustom('anim_3', '3. Neon Fluid')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">3. Neon Fluid</div>
                            <div onclick="window.seleccionarRadarCustom('anim_4', '4. Quantum Tunnel')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">4. Quantum Tunnel</div>
                            <div onclick="window.seleccionarRadarCustom('anim_5', '5. Retro Grid')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">5. Retro Grid</div>
                            <div onclick="window.seleccionarRadarCustom('anim_6', '6. Plasma Core')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">6. Plasma Core</div>
                            <div onclick="window.seleccionarRadarCustom('anim_7', '7. Data Stream')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">7. Data Stream</div>
                            <div onclick="window.seleccionarRadarCustom('anim_8', '8. Hologram Wave')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">8. Hologram Wave</div>
                            <div onclick="window.seleccionarRadarCustom('anim_9', '9. Energy Storm')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">9. Energy Storm</div>
                            <div onclick="window.seleccionarRadarCustom('anim_10', '10. Cosmic Dust')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">10. Cosmic Dust</div>
                            <div onclick="window.seleccionarRadarCustom('anim_11', '11. Hexagon Web')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">11. Hexagon Web</div>
                            <div onclick="window.seleccionarRadarCustom('anim_12', '12. Sonar Ping')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">12. Sonar Ping</div>
                            <div onclick="window.seleccionarRadarCustom('anim_13', '13. Laser Net')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">13. Laser Net</div>
                            <div onclick="window.seleccionarRadarCustom('anim_14', '14. Digital Glitch')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">14. Digital Glitch</div>
                            <div onclick="window.seleccionarRadarCustom('anim_15', '15. Synthwave Sun')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">15. Synthwave Sun</div>
                            <div onclick="window.seleccionarRadarCustom('anim_16', '16. Binary Code')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">16. Binary Code</div>
                            <div onclick="window.seleccionarRadarCustom('anim_17', '17. Echolocation')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">17. Echolocation</div>
                            <div onclick="window.seleccionarRadarCustom('anim_18', '18. Hyperspace')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">18. Hyperspace</div>
                            <div onclick="window.seleccionarRadarCustom('anim_19', '19. Radar Sweep')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">19. Radar Sweep</div>
                            <div onclick="window.seleccionarRadarCustom('anim_20', '20. Aurora Flow')" class="p-3 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">20. Aurora Flow</div>
                        </div>
                    </div>
                    <button onclick="window.previewRadarActual()" class="tactile-fx h-9 px-4 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 font-black tracking-widest text-[9px] uppercase flex items-center justify-center shadow-[0_0_15px_rgba(34,211,238,0.15)] hover:bg-cyan-500/20 shrink-0">
                        PROBAR
                    </button>
                </div>
            </div>

            <div class="w-full bg-[#0a0f1a] border border-white/5 rounded-[1.2rem] p-3 shadow-lg flex flex-col gap-2">
                <div class="flex flex-col border-b border-white/5 pb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center text-sm shadow-inner shrink-0">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] font-black uppercase text-white tracking-widest leading-none">3. Menú Principal</span>
                            <span class="text-[8px] font-bold text-blue-400 uppercase tracking-widest mt-0.5">Estructura del Launcher</span>
                        </div>
                    </div>
                </div>
                <div class="relative w-full">
                    <div onclick="window.toggleCustomSelectLauncher()" class="tactile-fx w-full bg-[#111827] border border-white/10 rounded-xl px-3 py-2.5 text-[11px] font-bold text-white flex justify-between items-center cursor-pointer shadow-inner">
                        <span id="custom-select-launcher-label" class="uppercase tracking-wider">5. Holograma 3D (Cuadrícula)</span>
                        <i id="custom-select-launcher-icon" class="fas fa-chevron-down text-blue-400 transition-transform duration-300"></i>
                    </div>
                    <div id="custom-select-launcher-options" class="absolute w-full mt-1 bg-[#0a0f1a] border border-white/10 rounded-xl shadow-2xl overflow-hidden z-[70] origin-top transform scale-y-0 opacity-0 transition-all duration-300 max-h-[180px] overflow-y-auto custom-scrollbar flex flex-col pointer-events-none">
                        <div onclick="window.seleccionarDisenoLauncher('0-grid', '0. Default (Cuadrícula)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">0. Default (Cuadrícula)</div>
                        <div onclick="window.seleccionarDisenoLauncher('0-list', '0. Default (Lista)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">0. Default (Lista)</div>
                        <div onclick="window.seleccionarDisenoLauncher('1-grid', '1. Glass UI (Cuadrícula)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-cyan-400 uppercase tracking-wider cursor-pointer">1. Glass UI (Cuadrícula)</div>
                        <div onclick="window.seleccionarDisenoLauncher('1-list', '1. Glass UI (Lista)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-cyan-400 uppercase tracking-wider cursor-pointer">1. Glass UI (Lista)</div>
                        <div onclick="window.seleccionarDisenoLauncher('2-grid', '2. Cyber Neón (Cuadrícula)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-purple-400 uppercase tracking-wider cursor-pointer">2. Cyber Neón (Cuadrícula)</div>
                        <div onclick="window.seleccionarDisenoLauncher('2-list', '2. Cyber Neón (Lista)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-purple-400 uppercase tracking-wider cursor-pointer">2. Cyber Neón (Lista)</div>
                        <div onclick="window.seleccionarDisenoLauncher('3-grid', '3. Minimalista (Cuadrícula)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-white uppercase tracking-wider cursor-pointer">3. Minimalista (Cuadrícula)</div>
                        <div onclick="window.seleccionarDisenoLauncher('3-list', '3. Minimalista (Lista)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-white uppercase tracking-wider cursor-pointer">3. Minimalista (Lista)</div>
                        <div onclick="window.seleccionarDisenoLauncher('4-grid', '4. Neumorfismo (Cuadrícula)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-cyan-500 uppercase tracking-wider cursor-pointer">4. Neumorfismo (Cuadrícula)</div>
                        <div onclick="window.seleccionarDisenoLauncher('4-list', '4. Neumorfismo (Lista)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-cyan-500 uppercase tracking-wider cursor-pointer">4. Neumorfismo (Lista)</div>
                        <div onclick="window.seleccionarDisenoLauncher('5-grid', '5. Holograma 3D (Cuadrícula)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-purple-500 uppercase tracking-wider cursor-pointer">5. Holograma 3D (Cuadrícula)</div>
                        <div onclick="window.seleccionarDisenoLauncher('5-list', '5. Holograma 3D (Lista)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-purple-500 uppercase tracking-wider cursor-pointer">5. Holograma 3D (Lista)</div>
                        <div onclick="window.seleccionarDisenoLauncher('6-grid', '6. Mecánico Retro (Cuadrícula)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-emerald-400 uppercase tracking-wider cursor-pointer">6. Mecánico Retro (Cuadrícula)</div>
                        <div onclick="window.seleccionarDisenoLauncher('6-list', '6. Mecánico Retro (Lista)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-emerald-400 uppercase tracking-wider cursor-pointer">6. Mecánico Retro (Lista)</div>
                        <div onclick="window.seleccionarDisenoLauncher('7-grid', '7. Aura (Cuadrícula)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-blue-400 uppercase tracking-wider cursor-pointer">7. Aura (Cuadrícula)</div>
                        <div onclick="window.seleccionarDisenoLauncher('7-list', '7. Aura (Lista)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-blue-400 uppercase tracking-wider cursor-pointer">7. Aura (Lista)</div>
                        <div onclick="window.seleccionarDisenoLauncher('8-grid', '8. Sci-Fi Angular (Cuadrícula)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-indigo-400 uppercase tracking-wider cursor-pointer">8. Sci-Fi Angular (Cuadrícula)</div>
                        <div onclick="window.seleccionarDisenoLauncher('8-list', '8. Sci-Fi Angular (Lista)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-indigo-400 uppercase tracking-wider cursor-pointer">8. Sci-Fi Angular (Lista)</div>
                        <div onclick="window.seleccionarDisenoLauncher('9-grid', '9. Brutalismo (Cuadrícula)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-pink-500 uppercase tracking-wider cursor-pointer">9. Brutalismo (Cuadrícula)</div>
                        <div onclick="window.seleccionarDisenoLauncher('9-list', '9. Brutalismo (Lista)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-pink-500 uppercase tracking-wider cursor-pointer">9. Brutalismo (Lista)</div>
                        <div onclick="window.seleccionarDisenoLauncher('10-grid', '10. Acrílico (Cuadrícula)')" class="p-3 hover:bg-white/5 text-[10px] font-bold text-white uppercase tracking-wider cursor-pointer">10. Acrílico (Cuadrícula)</div>
                        <div onclick="window.seleccionarDisenoLauncher('10-list', '10. Acrílico (Lista)')" class="p-3 hover:bg-white/5 text-[10px] font-bold text-white uppercase tracking-wider cursor-pointer">10. Acrílico (Lista)</div>
                    </div>
                </div>
            </div>
            
            <div class="w-full bg-[#0a0f1a] border border-white/5 rounded-[1.2rem] p-3 shadow-lg flex flex-col gap-2">
                <div class="flex flex-col border-b border-white/5 pb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-purple-500/20 text-purple-400 flex items-center justify-center text-sm shadow-inner shrink-0">
                            <i class="fas fa-swatchbook"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] font-black uppercase text-white tracking-widest leading-none">4. Biblioteca de Juegos</span>
                            <span class="text-[8px] font-bold text-purple-400 uppercase tracking-widest mt-0.5">Apariencia visual</span>
                        </div>
                    </div>
                </div>
                <div class="relative w-full">
                    <div onclick="window.toggleCustomSelectBiblio()" class="tactile-fx w-full bg-[#111827] border border-white/10 rounded-xl px-3 py-2.5 text-[11px] font-bold text-white flex justify-between items-center cursor-pointer shadow-inner">
                        <span id="custom-select-biblio-label" class="uppercase tracking-wider">5. Holograma 3D</span>
                        <i id="custom-select-biblio-icon" class="fas fa-chevron-down text-purple-400 transition-transform duration-300"></i>
                    </div>
                    <div id="custom-select-biblio-options" class="absolute w-full mt-1 bg-[#0a0f1a] border border-white/10 rounded-xl shadow-2xl overflow-hidden z-[65] origin-top transform scale-y-0 opacity-0 transition-all duration-300 max-h-[180px] overflow-y-auto custom-scrollbar flex flex-col pointer-events-none">
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-glass', '1. Premium Glass')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">1. Premium Glass</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-liquid', '2. Liquid Glass')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">2. Liquid Glass</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-frosted', '3. Frosted (Dark)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">3. Frosted (Dark)</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-translucent', '4. Translucent')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">4. Translucent</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-holographic', '5. Holograma 3D')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">5. Holograma 3D</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-cyber', '6. Cyberpunk Tech')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">6. Cyberpunk Tech</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-neumorph', '7. Neumorfismo Dark')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">7. Neumorfismo Dark</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-minimal', '8. Minimalista Flat')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">8. Minimalista Flat</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-arcade', '9. Arcade Retro')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">9. Arcade Retro</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-gold', '10. GoldHEN Luxury')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">10. GoldHEN Luxury</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-neon', '11. Synthwave Neon')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">11. Synthwave Neon</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-military', '12. Military Tactical')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">12. Military Tactical</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-blood', '13. Blood Vampire')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">13. Blood Vampire</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-outline', '14. Outline Minimal')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">14. Outline Minimal</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-radar', '15. Radar Console')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">15. Radar Console</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-pureglass', '16. Pure Liquid Glass')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-cyan-400 uppercase tracking-wider cursor-pointer">16. Pure Liquid Glass</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-oled', '17. OLED True Black')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">17. OLED True Black</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-pop', '18. Pop Art Comic')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">18. Pop Art Comic</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-lightglass', '19. Light Glass (Claro)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">19. Light Glass (Claro)</div>
                        <div onclick="window.seleccionarDisenoBiblioteca('theme-glitch', '20. Glitch Hacker')" class="p-3 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">20. Glitch Hacker</div>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-1 bg-[#111827] border border-white/5 rounded-xl p-2.5 shadow-inner">
                    <span class="text-[10px] font-bold text-gray-200 uppercase w-full">Columnas (Grilla)</span>
                    <button onclick="window.alternarColumnasBiblioAjustes()" id="btn-toggle-cols" class="tactile-fx px-4 py-2 rounded-lg bg-purple-600/20 border border-purple-500/50 text-purple-400 text-[9px] font-bold tracking-widest uppercase shadow-[0_0_10px_rgba(168,85,247,0.2)] shrink-0">
                        3 COLS
                    </button>
                </div>
            </div>

            <div class="w-full bg-[#0a0f1a] border border-white/5 rounded-[1.2rem] p-3 shadow-lg flex flex-col gap-2">
                <div class="flex flex-col border-b border-white/5 pb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-sm shadow-inner shrink-0">
                            <i class="fas fa-desktop"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] font-black uppercase text-white tracking-widest leading-none">5. Fondo Animado</span>
                            <span class="text-[8px] font-bold text-cyan-400 uppercase tracking-widest mt-0.5">Wallpaper interactivo</span>
                        </div>
                    </div>
                </div>
                <div class="relative w-full" id="custom-select-container">
                    <div onclick="window.toggleCustomSelect()" class="tactile-fx w-full bg-[#111827] border border-white/10 rounded-xl px-3 py-2.5 text-[11px] font-bold text-white flex justify-between items-center cursor-pointer shadow-inner">
                        <span id="custom-select-label" class="uppercase tracking-wider">System Default (PS5)</span>
                        <i id="custom-select-icon" class="fas fa-chevron-down text-cyan-400 transition-transform duration-300"></i>
                    </div>
                    <div id="custom-select-options" class="absolute w-full mt-1 bg-[#0a0f1a] border border-white/10 rounded-xl shadow-2xl overflow-hidden z-[50] origin-top transform scale-y-0 opacity-0 transition-all duration-300 max-h-[180px] overflow-y-auto custom-scrollbar flex flex-col pointer-events-none">
                        <div onclick="window.seleccionarFondoCustom('none', 'Apagar Fondos')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">Apagar Fondos</div>
                        <div onclick="window.seleccionarFondoCustom('bg-ps5', 'System Default (PS5)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-cyan-400 uppercase tracking-wider cursor-pointer">System Default (PS5)</div>
                        <div onclick="window.seleccionarFondoCustom('bg-ps4', 'Olas Líquidas (PS4)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-blue-400 uppercase tracking-wider cursor-pointer">Olas Líquidas (PS4)</div>
                        <div onclick="window.seleccionarFondoCustom('bg-ps2', 'Cubos 3D (PS2)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-indigo-400 uppercase tracking-wider cursor-pointer">Cubos 3D (PS2)</div>
                        <div onclick="window.seleccionarFondoCustom('bg-matrix', 'Lluvia de Código (Matrix)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-emerald-400 uppercase tracking-wider cursor-pointer">Lluvia de Código (Matrix)</div>
                        <div onclick="window.seleccionarFondoCustom('bg-warp', 'Velocidad Warp (Espacio)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-white uppercase tracking-wider cursor-pointer">Velocidad Warp (Espacio)</div>
                        <div onclick="window.seleccionarFondoCustom('bg-plasma', 'Fluido Plasma')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-pink-400 uppercase tracking-wider cursor-pointer">Fluido Plasma</div>
                        <div onclick="window.seleccionarFondoCustom('bg-network', 'Red Neuronal (Network)')" class="p-3 hover:bg-white/5 text-[10px] font-bold text-sky-400 uppercase tracking-wider cursor-pointer">Red Neuronal (Network)</div>
                    </div>
                </div>
            </div>

            <div class="w-full bg-[#0a0f1a] border border-white/5 rounded-[1.2rem] p-3 shadow-lg flex flex-col gap-2">
                <div class="flex flex-col border-b border-white/5 pb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-green-500/20 text-green-400 flex items-center justify-center text-sm shadow-inner shrink-0">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] font-black uppercase text-white tracking-widest leading-none">6. Animación Inicio</span>
                            <span class="text-[8px] font-bold text-green-400 uppercase tracking-widest mt-0.5">Splash Screen (Boot)</span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2 w-full items-center">
                    <div class="relative flex-1">
                        <div onclick="window.toggleCustomSelectIntro()" class="tactile-fx w-full bg-[#111827] border border-white/10 rounded-xl px-3 py-2.5 text-[11px] font-bold text-white flex justify-between items-center cursor-pointer shadow-inner">
                            <span id="custom-select-intro-label" class="uppercase tracking-wider truncate w-[130px]">Sin Intro (Rápido)</span>
                            <i id="custom-select-intro-icon" class="fas fa-chevron-down text-green-400 transition-transform duration-300"></i>
                        </div>
                        <div id="custom-select-intro-options" class="absolute w-full mt-1 bg-[#0a0f1a] border border-white/10 rounded-xl shadow-2xl overflow-hidden z-[60] origin-top transform scale-y-0 opacity-0 transition-all duration-300 max-h-[180px] overflow-y-auto custom-scrollbar flex flex-col pointer-events-none">
                            <div onclick="window.seleccionarIntroCustom('none', 'Sin Intro (Rápido)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">Sin Intro (Rápido)</div>
                            <div onclick="window.seleccionarIntroCustom('intro-ps5', 'PlayStation 5')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">PlayStation 5</div>
                            <div onclick="window.seleccionarIntroCustom('intro-ps4', 'PlayStation 4')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">PlayStation 4</div>
                            <div onclick="window.seleccionarIntroCustom('intro-ps2', 'PlayStation 2 Clásica')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">PlayStation 2 Clásica</div>
                            <div onclick="window.seleccionarIntroCustom('intro-glitch', 'Glitch Hacker')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">Glitch Hacker</div>
                            <div onclick="window.seleccionarIntroCustom('intro-hud', 'Sci-Fi HUD')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">Sci-Fi HUD</div>
                            <div onclick="window.seleccionarIntroCustom('intro-neon', 'Cyberpunk Neon')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">Cyberpunk Neon</div>
                            <div onclick="window.seleccionarIntroCustom('intro-decrypt', 'Decrypt System')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">Decrypt System</div>
                            <div onclick="window.seleccionarIntroCustom('intro-arcade', 'Retro Arcade')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">Retro Arcade</div>
                            <div onclick="window.seleccionarIntroCustom('intro-matrix-rain', 'Matrix Rain')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">Matrix Rain</div>
                            <div onclick="window.seleccionarIntroCustom('intro-crt', 'Terminal CRT (Boot)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">Terminal CRT (Boot)</div>
                            <div onclick="window.seleccionarIntroCustom('intro-gb', 'Game Boy Clásica')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">Game Boy Clásica</div>
                            <div onclick="window.seleccionarIntroCustom('intro-breach', 'System Breach')" class="p-3 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">System Breach</div>
                        </div>
                    </div>
                    <button onclick="window.previewIntroActual()" class="tactile-fx h-9 px-4 rounded-xl bg-green-500/10 border border-green-500/30 text-green-400 font-black tracking-widest text-[9px] uppercase flex items-center justify-center shadow-[0_0_15px_rgba(34,211,238,0.15)] hover:bg-green-500/20 shrink-0">
                        PROBAR
                    </button>
                </div>
            </div>
        </div>

        <div id="tab-fx" class="flex flex-col gap-4 w-full hidden">
            
            <div class="w-full bg-[#0a0f1a] border border-white/5 rounded-[1.2rem] p-3 shadow-lg flex flex-col gap-2">
                <div class="flex flex-col border-b border-white/5 pb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm shadow-inner shrink-0">
                            <i class="fas fa-hand-sparkles"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] font-black uppercase text-white tracking-widest leading-none">Partículas Táctiles</span>
                            <span class="text-[8px] font-bold text-emerald-400 uppercase tracking-widest mt-0.5">Efectos visuales al tocar</span>
                        </div>
                    </div>
                </div>
                <div class="relative w-full">
                    <div onclick="window.toggleCustomSelectParticulas()" class="tactile-fx w-full bg-[#111827] border border-white/10 rounded-xl px-3 py-2.5 text-[11px] font-bold text-white flex justify-between items-center cursor-pointer shadow-inner">
                        <span id="custom-select-particulas-label" class="uppercase tracking-wider">10. Burbujas</span>
                        <i id="custom-select-particulas-icon" class="fas fa-chevron-down text-emerald-400 transition-transform duration-300"></i>
                    </div>
                    <div id="custom-select-particulas-options" class="absolute w-full mt-1 bg-[#0a0f1a] border border-white/10 rounded-xl shadow-2xl overflow-hidden z-[80] origin-top transform scale-y-0 opacity-0 transition-all duration-300 max-h-[180px] overflow-y-auto custom-scrollbar flex flex-col pointer-events-none">
                        <div onclick="window.seleccionarParticulaCustom('moleculas', '1. Moléculas')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">1. Moléculas</div>
                        <div onclick="window.seleccionarParticulaCustom('plasma', '2. Plasma')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">2. Plasma</div>
                        <div onclick="window.seleccionarParticulaCustom('sparks', '3. Chispas')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">3. Chispas</div>
                        <div onclick="window.seleccionarParticulaCustom('fuego', '4. Fuego Fatuo')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">4. Fuego Fatuo</div>
                        <div onclick="window.seleccionarParticulaCustom('estrella', '5. Estrellas')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">5. Estrellas</div>
                        <div onclick="window.seleccionarParticulaCustom('glitch', '6. Glitch Data')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">6. Glitch Data</div>
                        <div onclick="window.seleccionarParticulaCustom('magic', '7. Magic Boost')" class="p-3 border-b border-white/5 hover:bg-emerald-500/10 text-[10px] font-bold text-emerald-400 uppercase tracking-wider cursor-pointer">7. Magic Boost</div>
                        <div onclick="window.seleccionarParticulaCustom('nieve', '8. Nieve')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">8. Nieve</div>
                        <div onclick="window.seleccionarParticulaCustom('matrix', '9. Matrix Rain')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">9. Matrix Rain</div>
                        <div onclick="window.seleccionarParticulaCustom('burbuja', '10. Burbujas')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">10. Burbujas</div>
                        <div onclick="window.seleccionarParticulaCustom('humo', '11. Humo')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">11. Humo</div>
                        <div onclick="window.seleccionarParticulaCustom('corazon', '12. Corazones')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">12. Corazones</div>
                        <div onclick="window.seleccionarParticulaCustom('polvo', '13. Polvo Cósmico')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">13. Polvo Cósmico</div>
                        <div onclick="window.seleccionarParticulaCustom('hoja', '14. Otoño')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">14. Otoño</div>
                        <div onclick="window.seleccionarParticulaCustom('hexagono', '15. Hexágonos')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">15. Hexágonos</div>
                        <div onclick="window.seleccionarParticulaCustom('laser', '16. Rayos Láser')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">16. Rayos Láser</div>
                        <div onclick="window.seleccionarParticulaCustom('confeti', '17. Confeti')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">17. Confeti</div>
                        <div onclick="window.seleccionarParticulaCustom('moneda', '18. Monedas Oro')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">18. Monedas Oro</div>
                        <div onclick="window.seleccionarParticulaCustom('notas', '19. Notas Musicales')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">19. Notas Musicales</div>
                        <div onclick="window.seleccionarParticulaCustom('sonar', '20. Sonar Rings')" class="p-3 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">20. Sonar Rings</div>
                    </div>
                </div>
                <div class="flex flex-col gap-2 mt-1">
                    <div class="flex items-center gap-3 w-full bg-[#111827] border border-white/5 rounded-xl p-2.5 shadow-inner">
                        <span class="text-[9px] font-mono text-gray-400 uppercase tracking-widest w-16">Cantidad</span>
                        <input type="range" id="cfg-slider-intensidad" min="10" max="300" value="27" oninput="window.cambiarIntensidadParticulas(this.value)" class="flex-1 h-1 bg-gray-800 rounded-lg appearance-none cursor-pointer accent-emerald-500">
                        <span id="lbl-intensidad-part" class="text-[9px] font-black text-emerald-400 w-8 text-right">27%</span>
                    </div>
                    <div class="flex items-center gap-3 w-full bg-[#111827] border border-white/5 rounded-xl p-2.5 shadow-inner">
                        <span class="text-[9px] font-mono text-gray-400 uppercase tracking-widest w-16">Tamaño</span>
                        <input type="range" id="cfg-slider-tamano" min="50" max="200" value="90" oninput="window.cambiarTamanoParticulas(this.value)" class="flex-1 h-1 bg-gray-800 rounded-lg appearance-none cursor-pointer accent-emerald-500">
                        <span id="lbl-tamano-part" class="text-[9px] font-black text-emerald-400 w-8 text-right">90%</span>
                    </div>
                </div>
            </div>

            <div class="w-full bg-[#0a0f1a] border border-white/5 rounded-[1.2rem] p-3 shadow-lg flex flex-col gap-2">
                <div class="flex flex-col border-b border-white/5 pb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center text-sm shadow-inner shrink-0">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] font-black uppercase text-white tracking-widest leading-none">Alertas del Sistema</span>
                            <span class="text-[8px] font-bold text-blue-400 uppercase tracking-widest mt-0.5">Estilo de notificaciones</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between bg-[#111827] border border-white/5 rounded-xl p-2.5 shadow-inner mt-1">
                    <span class="text-[10px] font-bold text-gray-200 uppercase w-full">Alertas Flotantes</span>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" id="cfg-switch-notificaciones" class="sr-only peer" checked onchange="window.guardarAjustesNotificaciones(this)">
                        <div class="w-10 h-5 bg-gray-800 border border-white/5 rounded-full peer peer-checked:after:translate-x-full peer-checked:peer-checked:after:border-white after:absolute after:top-[2px] after:left-[2px] after:bg-gray-400 after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600 peer-checked:after:bg-white"></div>
                    </label>
                </div>
                <div class="relative w-full">
                    <div onclick="window.toggleCustomSelectNotificaciones()" class="tactile-fx w-full bg-[#111827] border border-white/10 rounded-xl px-3 py-2.5 text-[11px] font-bold text-white flex justify-between items-center cursor-pointer shadow-inner">
                        <span id="custom-select-notificaciones-label" class="uppercase tracking-wider">3. Liquid Glass (Abajo)</span>
                        <i id="custom-select-notificaciones-icon" class="fas fa-chevron-down text-blue-400 transition-transform duration-300"></i>
                    </div>
                    <div id="custom-select-notificaciones-options" class="absolute w-full mt-1 bg-[#0a0f1a] border border-white/10 rounded-xl shadow-2xl overflow-hidden z-[80] origin-top transform scale-y-0 opacity-0 transition-all duration-300 max-h-[180px] overflow-y-auto custom-scrollbar flex flex-col pointer-events-none">
                        <div onclick="window.seleccionarNotificacionCustom('toast-default', '1. Premium Default')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">1. Premium Default</div>
                        <div onclick="window.seleccionarNotificacionCustom('toast-ps4', '2. PS4 Clásico')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">2. PS4 Clásico</div>
                        <div onclick="window.seleccionarNotificacionCustom('toast-liquid', '3. Liquid Glass (Abajo)')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">3. Liquid Glass (Abajo)</div>
                        <div onclick="window.seleccionarNotificacionCustom('toast-glitch', '4. Glitch Alert')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">4. Glitch Alert</div>
                        <div onclick="window.seleccionarNotificacionCustom('toast-cyber', '5. Cyberpunk Neon')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">5. Cyberpunk Neon</div>
                        <div onclick="window.seleccionarNotificacionCustom('toast-holographic', '6. Holograma 3D')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">6. Holograma 3D</div>
                        <div onclick="window.seleccionarNotificacionCustom('toast-arcade', '7. Arcade Retro')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">7. Arcade Retro</div>
                        <div onclick="window.seleccionarNotificacionCustom('toast-minimal', '8. Minimalista Outline')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">8. Minimalista Outline</div>
                        <div onclick="window.seleccionarNotificacionCustom('toast-military', '9. Military Tactical')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">9. Military Tactical</div>
                        <div onclick="window.seleccionarNotificacionCustom('toast-neumorph', '10. Neumorfismo Dark')" class="p-3 border-b border-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">10. Neumorfismo Dark</div>
                    </div>
                </div>
            </div>

            <div class="w-full bg-[#0a0f1a] border border-white/5 rounded-[1.2rem] p-3 shadow-lg flex flex-col gap-2">
                <div class="flex flex-col border-b border-white/5 pb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm shadow-inner shrink-0">
                            <i class="fas fa-volume-up"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] font-black uppercase text-white tracking-widest leading-none">Motor de Audio (SFX)</span>
                            <span class="text-[8px] font-bold text-amber-400 uppercase tracking-widest mt-0.5">Sonidos de la Interfaz</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between bg-[#111827] border border-white/5 rounded-xl p-2.5 shadow-inner mt-1">
                    <span class="text-[10px] font-bold text-gray-200 uppercase w-full">Efectos Activos</span>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" id="cfg-switch-sonidos" class="sr-only peer" checked onchange="window.guardarAjustesSonidos(this)">
                        <div class="w-10 h-5 bg-gray-800 border border-white/5 rounded-full peer peer-checked:after:translate-x-full peer-checked:peer-checked:after:border-white after:absolute after:top-[2px] after:left-[2px] after:bg-gray-400 after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500 peer-checked:after:bg-white"></div>
                    </label>
                </div>
                <div class="relative w-full">
                    <div onclick="window.toggleCustomSelectSonido()" class="tactile-fx w-full bg-[#111827] border border-white/10 rounded-xl px-3 py-2.5 text-[11px] font-bold text-white flex justify-between items-center cursor-pointer shadow-inner">
                        <span id="custom-select-sonido-label" class="uppercase tracking-wider">11. Bubble Pop</span>
                        <i id="custom-select-sonido-icon" class="fas fa-chevron-down text-amber-400 transition-transform duration-300"></i>
                    </div>
                    <div id="custom-select-sonido-options" class="absolute w-full mt-1 bg-[#0a0f1a] border border-white/10 rounded-xl shadow-2xl overflow-hidden z-[85] origin-top transform scale-y-0 opacity-0 transition-all duration-300 max-h-[180px] overflow-y-auto custom-scrollbar flex flex-col pointer-events-none">
                        <div onclick="window.seleccionarSonidoCustom('select', '1. UI Default')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">1. UI Default</div>
                        <div onclick="window.seleccionarSonidoCustom('liquid', '2. Liquid Drop')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">2. Liquid Drop</div>
                        <div onclick="window.seleccionarSonidoCustom('cyber', '3. Cyber Glitch')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">3. Cyber Glitch</div>
                        <div onclick="window.seleccionarSonidoCustom('brutal', '4. Mech Heavy')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">4. Mech Heavy</div>
                        <div onclick="window.seleccionarSonidoCustom('spark', '5. Spark Ping')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">5. Spark Ping</div>
                        <div onclick="window.seleccionarSonidoCustom('magic', '6. Magic Chime')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">6. Magic Chime</div>
                        <div onclick="window.seleccionarSonidoCustom('coin', '7. Coin Collect')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">7. Coin Collect</div>
                        <div onclick="window.seleccionarSonidoCustom('hover', '8. Sci-Fi Hover')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">8. Sci-Fi Hover</div>
                        <div onclick="window.seleccionarSonidoCustom('bass', '9. Deep Bass')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">9. Deep Bass</div>
                        <div onclick="window.seleccionarSonidoCustom('retro', '10. Retro 8-bit')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">10. Retro 8-bit</div>
                        <div onclick="window.seleccionarSonidoCustom('bubble', '11. Bubble Pop')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">11. Bubble Pop</div>
                        <div onclick="window.seleccionarSonidoCustom('laser', '12. Laser Pew')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">12. Laser Pew</div>
                        <div onclick="window.seleccionarSonidoCustom('tap', '13. Soft Tap')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">13. Soft Tap</div>
                        <div onclick="window.seleccionarSonidoCustom('glass', '14. Crystal Glass')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">14. Crystal Glass</div>
                        <div onclick="window.seleccionarSonidoCustom('echo', '15. Echo Ping')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">15. Echo Ping</div>
                        <div onclick="window.seleccionarSonidoCustom('sonar', '16. Sub Sonar')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">16. Sub Sonar</div>
                        <div onclick="window.seleccionarSonidoCustom('zap', '17. Electric Zap')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">17. Electric Zap</div>
                        <div onclick="window.seleccionarSonidoCustom('whoosh', '18. Space Whoosh')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">18. Space Whoosh</div>
                        <div onclick="window.seleccionarSonidoCustom('type', '19. Typewriter')" class="p-3 border-b border-white/5 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">19. Typewriter</div>
                        <div onclick="window.seleccionarSonidoCustom('alien', '20. Alien Blaster')" class="p-3 hover:bg-white/5 text-[10px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer">20. Alien Blaster</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 w-full bg-[#111827] border border-white/5 rounded-xl p-2.5 shadow-inner mt-1">
                    <span class="text-[9px] font-mono text-gray-400 uppercase tracking-widest w-16">Volumen</span>
                    <input type="range" id="cfg-slider-volumen" min="0" max="100" value="11" oninput="window.cambiarVolumenSFX(this.value)" class="flex-1 h-1 bg-gray-800 rounded-lg appearance-none cursor-pointer accent-amber-500">
                    <span id="lbl-volumen-sfx" class="text-[9px] font-black text-amber-400 w-8 text-right">11%</span>
                </div>
            </div>
        </div>

        <div id="tab-sys" class="flex flex-col gap-4 w-full hidden">
            
            <div class="w-full bg-[#0a0f1a] border border-white/5 rounded-[1.5rem] p-4 shadow-lg flex flex-col gap-3">
                <div class="flex items-center justify-between border-b border-white/5 pb-2">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-network-wired text-blue-400 text-sm"></i>
                        <span class="text-[10px] font-black uppercase text-gray-400 tracking-wider">Acceso Local</span>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[8px] font-black bg-blue-500/20 text-blue-400 border border-blue-500/30">SERVER HOST</span>
                </div>
                <p class="text-[9px] text-gray-400 font-mono mt-1 leading-relaxed">Este celular es el servidor. Cualquier dispositivo en tu misma red Wi-Fi puede abrir la app ingresando a esta dirección:</p>
                
                <div class="flex items-center justify-between bg-black/40 border border-blue-500/30 rounded-xl px-3 py-3 mt-1">
                    <span class="text-[11px] font-black text-blue-300 tracking-widest truncate w-full" id="host-url-display"><?php echo $real_host_url; ?></span>
                    <button onclick="copiarUrlHost()" class="text-blue-400 active:scale-90 transition-transform ml-2 shrink-0"><i class="fa-solid fa-copy"></i></button>
                </div>
                
                <div class="grid grid-cols-2 gap-3 mt-2">
                    <button onclick="compartirAppHost()" class="tactile-fx w-full py-3 rounded-xl bg-blue-600/20 border border-blue-500/40 text-blue-300 font-black tracking-widest text-[10px] uppercase flex flex-col items-center justify-center gap-1 shadow-[0_0_15px_rgba(59,130,246,0.15)]">
                        <i class="fa-solid fa-share-nodes text-sm"></i> Compartir Link
                    </button>
                    <button onclick="mostrarModalQR()" class="tactile-fx w-full py-3 rounded-xl bg-purple-600/20 border border-purple-500/40 text-purple-300 font-black tracking-widest text-[10px] uppercase flex flex-col items-center justify-center gap-1 shadow-[0_0_15px_rgba(168,85,247,0.15)]">
                        <i class="fa-solid fa-qrcode text-sm"></i> Mostrar QR
                    </button>
                </div>
            </div>

            <div class="w-full bg-[#0a0f1a] border border-white/5 rounded-[1.5rem] p-4 shadow-lg flex flex-col gap-3">
                <div class="flex items-center gap-2 border-b border-white/5 pb-2">
                    <i class="fas fa-cloud-download-alt text-cyan-400 text-sm"></i>
                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-wider">Actualizaciones OTA</span>
                </div>
                <p class="text-[9px] text-gray-400 font-mono mt-1">Busca e instala nuevas versiones de GoldHEN Manager directamente desde GitHub.</p>
                
                <button onclick="buscarActualizacionOTA()" class="mt-2 tactile-fx w-full py-3 rounded-xl bg-cyan-600/20 border border-cyan-500/40 text-cyan-300 font-black tracking-widest text-[10px] uppercase flex items-center justify-center gap-2 shadow-[0_0_15px_rgba(6,182,212,0.15)]">
                    <i class="fa-solid fa-sync text-sm"></i> Buscar Actualización
                </button>
            </div>

            <div class="w-full bg-[#0a0f1a] border border-white/5 rounded-[1.5rem] p-4 shadow-lg flex flex-col gap-3">
                <div class="flex items-center gap-2 border-b border-white/5 pb-2">
                    <i class="fas fa-save text-emerald-400 text-sm"></i>
                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-wider">Copias de Seguridad</span>
                </div>
                <p class="text-[9px] text-gray-400 font-mono mt-1">Exporta un respaldo completo de tu personalización, rutas e IPs por si borras los datos del navegador.</p>
                
                <div class="grid grid-cols-2 gap-3 mt-2">
                    <button onclick="exportarConfiguracionJSON()" class="tactile-fx w-full py-3 rounded-xl bg-emerald-600/20 border border-emerald-500/40 text-emerald-300 font-black tracking-widest text-[10px] uppercase flex flex-col items-center justify-center gap-1 shadow-[0_0_15px_rgba(16,185,129,0.15)]">
                        <i class="fa-solid fa-download text-sm"></i> Exportar
                    </button>
                    <button onclick="document.getElementById('input-import-backup').click()" class="tactile-fx w-full py-3 rounded-xl bg-gray-800/50 border border-gray-600/40 text-gray-300 font-black tracking-widest text-[10px] uppercase flex flex-col items-center justify-center gap-1">
                        <i class="fa-solid fa-upload text-sm"></i> Restaurar
                    </button>
                    <input type="file" id="input-import-backup" accept=".json" class="hidden" onchange="importarConfiguracionJSON(event)">
                </div>
            </div>

            <div class="w-full bg-[#0a0f1a] border border-red-500/20 rounded-[1.5rem] p-4 shadow-lg flex flex-col gap-3">
                <div class="flex items-center justify-between border-b border-red-500/20 pb-2">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-trash-alt text-red-500 text-sm"></i>
                        <span class="text-[10px] font-black uppercase text-red-400 tracking-wider">Limpieza de Archivos</span>
                    </div>
                    <i class="fa-solid fa-triangle-exclamation text-red-500 animate-pulse"></i>
                </div>
                <p class="text-[9px] text-gray-400 font-mono mt-1">Esta acción borrará todas las portadas descargadas, capturas de pantalla y caché temporal de la carpeta <b class="text-red-300">/user</b> del servidor.</p>
                
                <button onclick="iniciarBorradoCaché()" class="mt-2 tactile-fx w-full py-3.5 rounded-xl bg-red-600/20 border border-red-500/50 text-red-400 font-black tracking-widest text-[10px] uppercase flex items-center justify-center gap-2 shadow-[0_0_15px_rgba(239,68,68,0.2)]">
                    <i class="fa-solid fa-broom"></i> Vaciar Caché / Portadas
                </button>
            </div>
        </div>

        <div class="w-full flex flex-col items-center justify-center pt-8 pb-4 opacity-80 shrink-0">
            <div class="flex items-center gap-4 mb-3">
                <div class="w-12 h-[1px] bg-gradient-to-r from-transparent to-cyan-500"></div>
                <i class="fa-solid fa-gamepad text-cyan-400 text-lg drop-shadow-[0_0_8px_rgba(34,211,238,0.8)]"></i>
                <i class="fa-brands fa-playstation text-purple-500 text-lg drop-shadow-[0_0_8px_rgba(168,85,247,0.8)]"></i>
                <div class="w-12 h-[1px] bg-gradient-to-l from-transparent to-purple-500"></div>
            </div>
            <h1 class="text-[14px] font-black tracking-widest text-white uppercase shadow-black drop-shadow-md">GoldHEN Manager</h1>
            <p class="text-[10px] font-mono text-cyan-400 tracking-widest mt-0.5 shadow-black drop-shadow-md">VERSIÓN V4.0</p>
            <p class="text-[8px] font-bold text-gray-500 tracking-widest mt-2 uppercase">Developed By SeBaS</p>
        </div>

    </div>
</div>

<div id="modal-qr-share" class="fixed inset-0 z-[9999] bg-black/90 backdrop-blur-md hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="w-full max-w-sm rounded-[2rem] border border-purple-500/50 bg-[#0a0f1a] p-6 shadow-[0_0_30px_rgba(168,85,247,0.2)] flex flex-col items-center text-center transform scale-95 transition-transform duration-300" id="content-qr-share">
        <button onclick="cerrarModalQR()" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-white/10 text-white flex items-center justify-center hover:bg-red-500/50 transition-colors"><i class="fa-solid fa-xmark"></i></button>
        <h3 class="text-[15px] font-black tracking-widest text-purple-400 uppercase mb-2 mt-2"><i class="fa-solid fa-qrcode mr-2"></i> Escanear QR</h3>
        <p class="text-[10px] text-gray-400 font-mono mb-5 leading-relaxed">Escanea este código desde otro dispositivo conectado a tu red Wi-Fi.</p>
        <div class="w-48 h-48 bg-white p-3 rounded-[1.5rem] shadow-[0_0_25px_rgba(168,85,247,0.4)] mb-5 flex items-center justify-center">
            <img id="qr-image" src="" alt="QR Code" class="w-full h-full object-contain">
        </div>
        <span id="qr-url-text" class="text-[11px] font-black text-purple-300 tracking-widest bg-black/40 border border-purple-500/30 rounded-xl px-4 py-2.5 w-full truncate"></span>
    </div>
</div>

<div id="modal-borrado-1" class="fixed inset-0 z-[9999] bg-black/90 backdrop-blur-md hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="w-full max-w-sm rounded-[2rem] border-2 border-orange-500/50 bg-[#0a0f1a] p-6 shadow-[0_0_30px_rgba(249,115,22,0.2)] flex flex-col items-center text-center transform scale-95 transition-transform duration-300" id="content-borrado-1">
        <div class="w-16 h-16 rounded-full bg-orange-500/20 text-orange-500 flex items-center justify-center text-3xl mb-4"><i class="fa-solid fa-circle-exclamation"></i></div>
        <h3 class="text-[15px] font-black tracking-widest text-white uppercase mb-2">¿Estás Seguro?</h3>
        <p class="text-[10px] text-gray-400 font-mono mb-6 leading-relaxed">Se vaciará el contenido de las carpetas <b>user/portadas_custom</b> y <b>user/capturas</b> para liberar espacio en tu celular.</p>
        <div class="flex gap-3 w-full">
            <button onclick="cerrarModalesBorrado()" class="flex-1 py-3 rounded-xl border border-white/10 bg-white/5 text-[10px] font-black tracking-widest uppercase text-gray-300 active:scale-95 transition-all">Cancelar</button>
            <button onclick="avanzarBorradoPaso2()" class="flex-1 py-3 rounded-xl bg-orange-600 text-[10px] font-black tracking-widest uppercase text-white active:scale-95 transition-all">Continuar</button>
        </div>
    </div>
</div>

<div id="modal-borrado-2" class="fixed inset-0 z-[9999] bg-red-950/90 backdrop-blur-md hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="w-full max-w-sm rounded-[2rem] border-2 border-red-500 bg-[#0a0f1a] p-6 shadow-[0_0_50px_rgba(239,68,68,0.5)] flex flex-col items-center text-center transform scale-95 transition-transform duration-300" id="content-borrado-2">
        <div class="w-16 h-16 rounded-full bg-red-500/20 text-red-500 flex items-center justify-center text-3xl mb-4 animate-pulse"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <h3 class="text-[15px] font-black tracking-widest text-red-400 uppercase mb-2">Paso Final</h3>
        <p class="text-[10px] text-red-200 font-mono mb-6 leading-relaxed">Esta acción es irreversible y eliminará los archivos físicos del servidor en tu dispositivo. ¿Vaciar ahora?</p>
        <div class="flex gap-3 w-full">
            <button onclick="cerrarModalesBorrado()" class="flex-1 py-3 rounded-xl border border-white/10 bg-white/5 text-[10px] font-black tracking-widest uppercase text-gray-300 active:scale-95 transition-all">Abortar</button>
            <button onclick="ejecutarLimpiezaBackend()" id="btn-ejecutar-limpieza" class="flex-1 py-3 rounded-xl bg-red-600 text-[10px] font-black tracking-widest uppercase text-white active:scale-95 transition-all shadow-[0_0_20px_rgba(239,68,68,0.6)]">VACIAR TODO</button>
        </div>
    </div>
</div>

<!-- MODAL OTA UPDATE -->
<div id="modal-ota-update" class="fixed inset-0 z-[9999] bg-black/90 backdrop-blur-md hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="w-full max-w-sm rounded-[2rem] border-2 border-cyan-500/50 bg-[#0a0f1a] p-6 shadow-[0_0_30px_rgba(6,182,212,0.2)] flex flex-col items-center transform scale-95 transition-transform duration-300" id="content-ota-update">
        <div class="w-16 h-16 rounded-full bg-cyan-500/20 text-cyan-500 flex items-center justify-center text-3xl mb-4 shadow-[0_0_15px_rgba(6,182,212,0.4)]" id="ota-icon-container">
            <i class="fa-solid fa-cloud-arrow-down" id="ota-icon"></i>
        </div>
        <h3 class="text-[15px] font-black tracking-widest text-white uppercase mb-2" id="ota-title">Buscando...</h3>
        
        <div class="w-full flex justify-between items-center bg-black/40 border border-white/10 rounded-xl p-3 mb-4">
            <div class="flex flex-col items-center">
                <span class="text-[8px] font-mono text-gray-500 uppercase tracking-widest">Actual</span>
                <span class="text-[12px] font-black text-gray-300" id="ota-version-local">v4.0</span>
            </div>
            <i class="fa-solid fa-arrow-right text-cyan-500 text-sm"></i>
            <div class="flex flex-col items-center">
                <span class="text-[8px] font-mono text-gray-500 uppercase tracking-widest">Nueva</span>
                <span class="text-[12px] font-black text-cyan-400" id="ota-version-nueva">--</span>
            </div>
        </div>

        <div id="ota-changelog-container" class="w-full bg-[#111827] border border-white/5 rounded-xl p-3 mb-4 hidden max-h-[120px] overflow-y-auto custom-scrollbar">
            <span class="text-[9px] font-black tracking-widest text-cyan-400 uppercase block mb-1">Novedades:</span>
            <p id="ota-changelog" class="text-[9px] text-gray-300 font-mono leading-relaxed whitespace-pre-line"></p>
        </div>

        <div id="ota-progress-container" class="w-full hidden flex-col gap-2 mb-4">
            <div class="flex justify-between items-center">
                <span class="text-[9px] font-bold text-cyan-400 uppercase tracking-widest" id="ota-progress-status">Descargando...</span>
                <span class="text-[11px] font-black text-cyan-400 font-mono" id="ota-percent">0%</span>
            </div>
            <div class="w-full h-2 bg-black rounded-full overflow-hidden border border-white/5">
                <div id="ota-bar" class="h-full bg-gradient-to-r from-cyan-600 to-blue-400 w-0 transition-all"></div>
            </div>
        </div>

        <div class="flex gap-3 w-full mt-2" id="ota-buttons">
            <button onclick="cerrarModalOTA()" id="btn-ota-cancel" class="flex-1 py-3.5 rounded-xl border border-white/10 bg-white/5 text-[10px] font-black tracking-widest uppercase text-gray-300 active:scale-95 transition-all">Cancelar</button>
            <button id="btn-ota-install" class="flex-1 py-3.5 rounded-xl bg-cyan-600 text-[10px] font-black tracking-widest uppercase text-black active:scale-95 transition-all shadow-[0_0_15px_rgba(6,182,212,0.4)] hidden">Instalar</button>
        </div>
    </div>
</div>

<script>
    const realHostUrl = "<?php echo $real_host_url; ?>";

    document.addEventListener("DOMContentLoaded", () => {
        const urlDisplay = document.getElementById('host-url-display');
        if(urlDisplay) {
            urlDisplay.innerText = realHostUrl;
        }
    });

    function switchAjustesTab(tab) {
        if(typeof window.playSynth === 'function') window.playSynth('select');
        const btns = {
            'ui': document.getElementById('btn-tab-ui'),
            'fx': document.getElementById('btn-tab-fx'),
            'sys': document.getElementById('btn-tab-sys')
        };
        const tabs = {
            'ui': document.getElementById('tab-ui'),
            'fx': document.getElementById('tab-fx'),
            'sys': document.getElementById('tab-sys')
        };
        
        Object.keys(btns).forEach(k => {
            btns[k].className = "tactile-fx flex-1 py-3 rounded-lg bg-transparent text-gray-500 text-[10px] font-black uppercase tracking-widest";
            tabs[k].classList.add('hidden');
        });

        if(tab === 'ui') btns[tab].className = "tactile-fx flex-1 py-3 rounded-lg bg-cyan-600 text-white text-[10px] font-black uppercase tracking-widest shadow-[0_0_15px_rgba(6,182,212,0.4)]";
        if(tab === 'fx') btns[tab].className = "tactile-fx flex-1 py-3 rounded-lg bg-purple-600 text-white text-[10px] font-black uppercase tracking-widest shadow-[0_0_15px_rgba(168,85,247,0.4)]";
        if(tab === 'sys') btns[tab].className = "tactile-fx flex-1 py-3 rounded-lg bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest shadow-[0_0_15px_rgba(59,130,246,0.4)]";
        
        tabs[tab].classList.remove('hidden');
    }

    function copiarUrlHost() {
        if(typeof window.playSynth === 'function') window.playSynth('select');
        navigator.clipboard.writeText(realHostUrl).then(() => {
            if(typeof window.sysNotification === 'function') window.sysNotification("COPIADO", "IP del servidor copiada al portapapeles", "fa-copy");
        }).catch(err => {
            if(typeof window.sysNotification === 'function') window.sysNotification("ERROR", "No se pudo copiar", "fa-times");
        });
    }

    function compartirAppHost() {
        if(typeof window.playSynth === 'function') window.playSynth('select');
        if (navigator.share) {
            navigator.share({
                title: 'GoldHEN Manager',
                text: 'Ingresa a esta IP desde la misma red Wi-Fi para acceder al servidor:',
                url: realHostUrl
            }).catch(console.error);
        } else {
            copiarUrlHost();
        }
    }

    function mostrarModalQR() {
        if(typeof window.playSynth === 'function') window.playSynth('magic');
        const modal = document.getElementById('modal-qr-share');
        const content = document.getElementById('content-qr-share');
        
        document.getElementById('qr-url-text').innerText = realHostUrl;
        document.getElementById('qr-image').src = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&color=000000&bgcolor=ffffff&data=" + encodeURIComponent(realHostUrl);
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.add('opacity-100');
            content.classList.remove('scale-95');
        }, 10);
    }

    function cerrarModalQR() {
        if(typeof window.playSynth === 'function') window.playSynth('select');
        const modal = document.getElementById('modal-qr-share');
        const content = document.getElementById('content-qr-share');
        
        content.classList.add('scale-95');
        modal.classList.remove('opacity-100');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function exportarConfiguracionJSON() {
        if(typeof window.playSynth === 'function') window.playSynth('select');
        let backupData = {};
        
        for (let i = 0; i < localStorage.length; i++) {
            let key = localStorage.key(i);
            if (key.startsWith('cfg_sebas_') || key.startsWith('ps4_') || key.startsWith('sebas_')) {
                backupData[key] = localStorage.getItem(key);
            }
        }

        let dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(backupData, null, 2));
        let dlElement = document.createElement('a');
        dlElement.setAttribute("href", dataStr);
        dlElement.setAttribute("download", "GoldHEN_Manager_Backup.json");
        dlElement.click();
        
        if(typeof window.sysNotification === 'function') window.sysNotification("BACKUP EXITOSO", "Configuración guardada en descargas", "fa-save");
    }

    function importarConfiguracionJSON(event) {
        if(typeof window.playSynth === 'function') window.playSynth('select');
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = JSON.parse(e.target.result);
                Object.keys(data).forEach(key => {
                    if (key.startsWith('cfg_sebas_') || key.startsWith('ps4_') || key.startsWith('sebas_')) {
                        localStorage.setItem(key, data[key]);
                    }
                });
                if(typeof window.sysNotification === 'function') window.sysNotification("RESTAURADO", "Aplicando configuración y reiniciando...", "fa-rotate");
                setTimeout(() => window.location.reload(), 1500);
            } catch (err) {
                if(typeof window.sysNotification === 'function') window.sysNotification("ERROR", "Archivo de copia de seguridad inválido", "fa-times-circle");
            }
        };
        reader.readAsText(file);
        event.target.value = ''; 
    }

    function iniciarBorradoCaché() {
        if(typeof window.playSynth === 'function') window.playSynth('brutal');
        const modal = document.getElementById('modal-borrado-1');
        const content = document.getElementById('content-borrado-1');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.add('opacity-100');
            content.classList.remove('scale-95');
        }, 10);
    }

    function cerrarModalesBorrado() {
        if(typeof window.playSynth === 'function') window.playSynth('select');
        const m1 = document.getElementById('modal-borrado-1');
        const m2 = document.getElementById('modal-borrado-2');
        const c1 = document.getElementById('content-borrado-1');
        const c2 = document.getElementById('content-borrado-2');
        
        if(c1) c1.classList.add('scale-95');
        if(c2) c2.classList.add('scale-95');
        
        if(m1) m1.classList.remove('opacity-100');
        if(m2) m2.classList.remove('opacity-100');
        
        setTimeout(() => {
            if(m1) m1.classList.add('hidden');
            if(m2) m2.classList.add('hidden');
        }, 300);
    }

    function avanzarBorradoPaso2() {
        if(typeof window.playSynth === 'function') window.playSynth('brutal');
        
        const m1 = document.getElementById('modal-borrado-1');
        const c1 = document.getElementById('content-borrado-1');
        c1.classList.add('scale-95');
        m1.classList.remove('opacity-100');
        
        setTimeout(() => {
            m1.classList.add('hidden');
            const m2 = document.getElementById('modal-borrado-2');
            const c2 = document.getElementById('content-borrado-2');
            m2.classList.remove('hidden');
            setTimeout(() => {
                m2.classList.add('opacity-100');
                c2.classList.remove('scale-95');
            }, 10);
        }, 300);
    }

    async function ejecutarLimpiezaBackend() {
        if(typeof window.playSynth === 'function') window.playSynth('laser');
        const btn = document.getElementById('btn-ejecutar-limpieza');
        const oldText = btn.innerHTML;
        btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> BORRANDO...`;
        btn.classList.add('pointer-events-none', 'opacity-80');

        try {
            const fd = new FormData();
            fd.append('action', 'limpiar_user');
            
            let res = await fetch('api/limpiar_cache.php', { method: 'POST', body: fd });
            let data = await res.json();
            
            if (data.status === 'success') {
                if(typeof window.sysNotification === 'function') window.sysNotification("SISTEMA LIMPIO", "Archivos temporales y portadas eliminados", "fa-broom");
                cerrarModalesBorrado();
            } else {
                if(typeof window.sysNotification === 'function') window.sysNotification("ERROR", "No se pudo limpiar la carpeta", "fa-bug");
            }
        } catch (e) {
            if(typeof window.sysNotification === 'function') window.sysNotification("ERROR CRÍTICO", "Fallo de comunicación con servidor local", "fa-server");
        } finally {
            btn.innerHTML = oldText;
            btn.classList.remove('pointer-events-none', 'opacity-80');
        }
    }

    function cerrarModalOTA() {
        const modal = document.getElementById('modal-ota-update');
        const content = document.getElementById('content-ota-update');
        if(content) content.classList.add('scale-95');
        if(modal) modal.classList.remove('opacity-100');
        setTimeout(() => {
            if(modal) modal.classList.add('hidden');
        }, 300);
    }
</script>
<?php
// Developed By SeBaS
?>
