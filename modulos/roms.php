<?php
/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: modulos/roms.php
 * ====================================================================
 */
?>
<style>
    #layer-roms .roms-emu-card { transition: all 0.25s ease; border: 1px solid rgba(255,255,255,0.06); background: rgba(10,15,26,0.85); }
    #layer-roms .roms-emu-card.active { border-color: #22d3ee; background: rgba(34,211,238,0.14); box-shadow: 0 0 15px rgba(34,211,238,0.25); }
    
    #layer-roms .roms-folder-chip { transition: all 0.25s ease; border: 1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.03); }
    #layer-roms .roms-folder-chip.active { border-color: #10b981; background: rgba(16,185,129,0.15); box-shadow: 0 0 15px rgba(16,185,129,0.2); }
    
    #layer-roms .roms-drawer-closed { max-height: 0px; opacity: 0; overflow: hidden; padding-top: 0; padding-bottom: 0; margin-top: 0; border-width: 0px; pointer-events: none; }
    #layer-roms .roms-drawer-open { max-height: 330px; opacity: 1; padding: 12px; margin-top: 8px; border-width: 1px; pointer-events: auto; }
</style>

<div id="layer-roms" class="app-layer hidden flex-col h-[100dvh] w-full overflow-hidden z-10 select-none bg-[#02040a]">

    <div class="w-full flex items-center justify-between z-30 shrink-0 p-4 pt-6 mb-1 border-b border-white/5 bg-[#060913]/80 backdrop-blur-md shadow-lg">
        <div class="flex items-center gap-3">
            <button onclick="window.volverAlLauncher()" class="tactile-fx w-10 h-10 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-center hover:bg-white/5">
                <i class="fa-solid fa-arrow-left text-gray-300"></i>
            </button>
            <div class="flex flex-col">
                <h2 class="text-[17px] font-black tracking-tighter uppercase text-purple-400 leading-none">Inyector ROMs</h2>
                <span class="text-[9px] font-mono text-gray-400 tracking-widest mt-0.5">Gestor de Emuladores PS4</span>
            </div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 shadow-[0_0_15px_rgba(168,85,247,0.2)]">
            <i class="fa-solid fa-ghost text-lg"></i>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto hide-scrollbar flex flex-col p-4 gap-5 pb-12 relative">
        
        <div id="roms-estado-vacio" class="hidden flex-col items-center justify-center w-full h-[65dvh] transition-opacity duration-500">
            <div class="glass-panel p-8 rounded-[2rem] flex flex-col items-center justify-center max-w-xs text-center border-t border-purple-500/30 shadow-[0_0_40px_rgba(168,85,247,0.1)] relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-b from-purple-500/10 to-transparent"></div>
                <div class="w-20 h-20 rounded-full bg-[#0a0f1a] border border-purple-500/30 flex items-center justify-center text-purple-400 text-3xl mb-4 shadow-[0_0_20px_rgba(168,85,247,0.4)] relative z-10 animate-pulse">
                    <i class="fa-solid fa-gamepad"></i>
                </div>
                <h3 class="text-[14px] font-black uppercase tracking-widest text-white relative z-10">Sin Emuladores</h3>
                <p class="text-[10px] text-gray-400 mt-2 mb-6 font-mono tracking-widest uppercase relative z-10 leading-relaxed">Selecciona un emulador de tu biblioteca instalada para configurar sus carpetas de ROMs.</p>
                <button onclick="window.abrirModalBibliotecaRoms()" class="tactile-fx bg-gradient-to-r from-purple-600 to-cyan-500 text-white font-black text-[11px] px-6 py-3 rounded-full uppercase tracking-widest shadow-[0_0_15px_rgba(34,211,238,0.4)] relative z-10 w-full flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> Vincular Emulador
                </button>
            </div>
        </div>

        <div id="roms-contenido-principal" class="flex flex-col gap-5 w-full hidden">
            
            <div class="flex flex-col">
                <div class="flex items-center justify-between px-1 mb-2.5">
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 rounded-full bg-purple-500/20 text-purple-400 flex items-center justify-center text-[10px] font-black shrink-0">1</div>
                        <span class="text-[11px] font-black uppercase text-gray-300 tracking-wider">Emulador Activo</span>
                        <span id="roms-badge-total-emus" class="text-[9px] font-mono bg-white/5 border border-white/10 px-2 py-0.5 rounded-full text-gray-400">0</span>
                    </div>
                    <button onclick="window.abrirModalBibliotecaRoms()" class="tactile-fx px-3 py-1 rounded-full bg-purple-500/10 border border-purple-500/30 text-purple-300 text-[9px] font-black uppercase tracking-widest flex items-center gap-1.5 hover:bg-purple-500/20 shadow-[0_0_10px_rgba(168,85,247,0.15)]">
                        <i class="fa-solid fa-plus text-[8px]"></i> Vincular
                    </button>
                </div>
                
                <div onclick="window.toggleDrawerEmuladoresRoms()" id="roms-hero-emu-card" class="tactile-fx w-full p-3 rounded-[1.5rem] bg-gradient-to-r from-[#0a101f] via-[#0d1527] to-[#0a101f] border border-cyan-500/40 shadow-[0_0_25px_rgba(34,211,238,0.12)] flex items-center justify-between gap-3.5 relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-cyan-500/10 rounded-full blur-2xl pointer-events-none"></div>
                    
                    <div class="flex items-center gap-3.5 min-w-0 relative z-10">
                        <img id="roms-hero-emu-cover" src="" class="w-16 h-16 rounded-2xl object-cover shadow-lg border border-white/10 shrink-0 bg-black/40">
                        <div class="flex flex-col min-w-0">
                            <span class="text-[8px] font-mono text-cyan-400 uppercase tracking-widest flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span> Seleccionado
                            </span>
                            <h3 id="roms-hero-emu-nombre" class="text-[15px] font-black uppercase tracking-wider text-white truncate mt-0.5">EMULADOR</h3>
                            <span id="roms-hero-emu-info" class="text-[9px] font-mono text-gray-400 tracking-widest mt-0.5">CUSA00000 • 0 RUTAS</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white/[0.04] border border-white/10 shrink-0 relative z-10">
                        <span class="text-[9px] font-black uppercase tracking-widest text-cyan-300">Cambiar</span>
                        <i id="roms-icono-flecha-drawer" class="fa-solid fa-chevron-down text-cyan-400 text-xs transition-transform duration-300"></i>
                    </div>
                </div>

                <div id="roms-drawer-emuladores" class="roms-drawer-closed transition-all duration-300 rounded-[1.5rem] bg-[#04070f]/95 border-cyan-500/20 shadow-2xl overflow-y-auto custom-scrollbar">
                    <div class="flex items-center justify-between px-1 mb-2.5">
                        <span class="text-[9px] font-mono text-gray-400 uppercase tracking-widest">Toca un emulador para seleccionarlo:</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2.5" id="roms-contenedor-emuladores"></div>
                </div>
            </div>

            <div id="roms-seccion-carpetas" class="hidden flex-col gap-3 transition-all duration-300 opacity-0 transform translate-y-4">
                <div class="w-full h-[1px] bg-white/5"></div>
                
                <div class="flex items-center justify-between px-1">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-[10px] font-black shrink-0">2</div>
                        <span class="text-[11px] font-black uppercase text-gray-300 tracking-wider truncate">Rutas (<span id="roms-nombre-emu-activo" class="text-cyan-400"></span>)</span>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button id="btn-toggle-borrar-rutas" onclick="window.toggleModoBorrarRutasRoms()" style="display:none;" class="tactile-fx px-3 py-1.5 rounded-full bg-gray-500/10 border border-gray-500/30 text-gray-400 text-[9px] font-black uppercase tracking-widest flex items-center gap-1.5 hover:bg-gray-500/20 shrink-0 transition-all">
                            <i class="fa-solid fa-pen text-[10px]"></i> Editar
                        </button>
                        <button onclick="window.abrirExploradorFTPRoms()" class="tactile-fx px-3 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-[9px] font-black uppercase tracking-widest flex items-center gap-1.5 hover:bg-cyan-500/20 shrink-0 shadow-[0_0_10px_rgba(34,211,238,0.15)]">
                            <i class="fa-solid fa-folder-plus text-[10px]"></i> Ruta FTP
                        </button>
                    </div>
                </div>

                <div class="flex w-full overflow-x-auto hide-scrollbar gap-3 snap-x px-1 transition-all duration-300" id="roms-contenedor-rutas"></div>
            </div>

            <div id="roms-seccion-subida" class="hidden flex-col gap-3 transition-all duration-300 opacity-0 transform translate-y-4">
                <div class="w-full h-[1px] bg-white/5 mb-0.5"></div>

                <div class="flex items-center gap-2 px-1">
                    <div class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-[10px] font-black shrink-0">3</div>
                    <span class="text-[11px] font-black uppercase text-gray-300 tracking-wider">Enviar ROM a la PS4</span>
                </div>

                <div class="w-full bg-emerald-950/30 border border-emerald-500/25 rounded-xl px-3 py-2.5 flex items-center gap-2.5 shadow-inner">
                    <i class="fa-solid fa-folder-tree text-emerald-400 text-sm shrink-0"></i>
                    <span class="text-[10px] font-mono text-emerald-300 tracking-wider break-all leading-tight" id="roms-ruta-final-txt">/</span>
                </div>

                <label for="roms-input-file" id="roms-drop-zone" class="tactile-fx flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-emerald-500/40 rounded-[1.5rem] bg-[#0a0f1a]/60 hover:bg-emerald-900/10 transition-colors cursor-pointer shadow-[0_0_15px_rgba(16,185,129,0.08)]">
                    <div class="flex flex-col items-center justify-center pointer-events-none">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-emerald-400 mb-2 animate-bounce"></i>
                        <p class="text-[12px] font-black tracking-widest uppercase text-white">Seleccionar archivo(s) ROM</p>
                        <p class="text-[9px] text-gray-500 font-mono tracking-widest uppercase mt-1">.ZIP, .ISO, .SMC, .BIN, .WBFS, .CHD, .GBA</p>
                    </div>
                    <input id="roms-input-file" type="file" multiple class="hidden" onchange="window.iniciarSubidaRomsFTP(this)" />
                </label>

                <div id="roms-panel-progreso" class="hidden glass-panel rounded-[1.2rem] p-4 flex-col gap-3 border border-emerald-500/20">
                    <div class="flex justify-between items-center">
                        <div class="flex flex-col min-w-0 pr-2">
                            <span id="roms-txt-cola-status" class="text-[9px] font-black text-emerald-400 tracking-widest uppercase">Subiendo vía FTP...</span>
                            <span id="roms-txt-nombre-archivo" class="text-[12px] font-bold text-white truncate">juego.zip</span>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <span id="roms-txt-porcentaje" class="text-[16px] font-black text-emerald-400">0%</span>
                            <button id="btn-pausar-rom-top" onclick="window.pausarSubidaRom()" class="w-8 h-8 rounded-lg bg-red-500/10 border border-red-500/30 text-red-400 flex items-center justify-center tactile-fx">
                                <i class="fa-solid fa-pause text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <div class="w-full h-2 bg-gray-800 rounded-full overflow-hidden">
                        <div id="roms-barra-progreso" class="h-full bg-gradient-to-r from-cyan-400 to-emerald-400 w-0 transition-all duration-150 shadow-[0_0_10px_#34d399]"></div>
                    </div>
                    <div class="flex justify-between items-center text-[10px] font-mono text-gray-400">
                        <span id="roms-txt-bytes">0 B / 0 B</span>
                        <span id="roms-txt-speed" class="text-cyan-400">0.00 MB/s</span>
                        <span id="roms-txt-eta">ETA: --:--</span>
                    </div>
                    
                    <div id="roms-panel-acciones" class="hidden gap-2 mt-1">
                        <button onclick="window.reanudarSubidaRom()" class="flex-1 py-3 rounded-xl bg-orange-600/20 text-orange-400 border border-orange-500/30 text-[10px] font-black uppercase tracking-widest shadow-[0_0_15px_rgba(249,115,22,0.2)] tactile-fx flex items-center justify-center gap-1.5"><i class="fa-solid fa-play"></i> Reanudar</button>
                        <button onclick="window.cancelarSubidaRomsFTP()" class="flex-1 py-3 rounded-xl bg-red-600/20 text-red-400 border border-red-500/30 text-[10px] font-black uppercase tracking-widest tactile-fx flex items-center justify-center gap-1.5"><i class="fa-solid fa-xmark"></i> Abortar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="roms-modal-biblioteca" class="fixed inset-0 z-[9999] bg-[#02040a]/95 backdrop-blur-md hidden flex-col transition-opacity duration-300 opacity-0">
        <div class="w-full p-4 pt-6 border-b border-white/5 flex items-center justify-between shrink-0 bg-[#060913]">
            <div class="flex flex-col">
                <h3 class="text-[14px] font-black tracking-widest text-white uppercase">Biblioteca PS4</h3>
                <span class="text-[9px] font-mono text-purple-400 tracking-widest">TOCA EL EMULADOR PARA VINCULARLO</span>
            </div>
            <button onclick="window.cerrarModalBibliotecaRoms()" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:text-white tactile-fx">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="px-4 py-2.5 bg-[#060913]/60 border-b border-white/5 shrink-0">
            <div class="relative w-full h-10 bg-white/[0.03] border border-white/10 rounded-xl flex items-center px-3">
                <i class="fa-solid fa-magnifying-glass text-gray-500 text-sm mr-2.5"></i>
                <input type="text" id="roms-buscar-biblio" oninput="window.filtrarModalBibliotecaRoms()" placeholder="Buscar aplicación o CUSA..." class="w-full bg-transparent text-sm text-white outline-none font-medium">
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 grid grid-cols-3 gap-3.5 custom-scrollbar content-start" id="roms-grid-modal-biblioteca"></div>
    </div>

    <div id="roms-modal-ftp" class="fixed inset-0 z-[9999] bg-[#02040a]/95 backdrop-blur-md hidden flex-col transition-opacity duration-300 opacity-0">
        <div class="w-full p-4 pt-6 border-b border-white/5 flex items-center justify-between shrink-0 bg-[#060913]">
            <div class="flex items-center gap-2.5 min-w-0">
                <button onclick="window.subirNivelFTPRoms()" class="w-9 h-9 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-gray-300 hover:text-white tactile-fx shrink-0">
                    <i class="fa-solid fa-turn-up text-xs"></i>
                </button>
                <div class="flex flex-col min-w-0">
                    <h3 class="text-[13px] font-black tracking-widest text-white uppercase">Explorar PS4 (FTP)</h3>
                    <span id="roms-ruta-ftp-actual" class="text-[10px] font-mono text-cyan-400 tracking-wider break-all leading-tight">/data/</span>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button onclick="window.crearCarpetaFTPRoms()" class="w-8 h-8 rounded-full bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 tactile-fx" title="Nueva Carpeta">
                    <i class="fa-solid fa-folder-plus text-xs"></i>
                </button>
                <button onclick="window.cerrarModalFTPRoms()" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:text-white tactile-fx">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <div class="flex gap-2 px-4 py-2.5 bg-[#060913]/60 border-b border-white/5 shrink-0 overflow-x-auto hide-scrollbar">
            <button onclick="window.irRutaDirectaFTPRoms('/')" class="tactile-fx px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-[9px] font-mono text-gray-300 uppercase shrink-0"><i class="fa-solid fa-hard-drive mr-1 text-cyan-400"></i> Raíz (/)</button>
            <button onclick="window.irRutaDirectaFTPRoms('/data/')" class="tactile-fx px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-[9px] font-mono text-gray-300 uppercase shrink-0"><i class="fa-solid fa-database mr-1 text-purple-400"></i> /data/</button>
            <button onclick="window.irRutaDirectaFTPRoms('/mnt/usb0/')" class="tactile-fx px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-[9px] font-mono text-gray-300 uppercase shrink-0"><i class="fa-brands fa-usb mr-1 text-emerald-400"></i> USB 0</button>
            <button onclick="window.irRutaDirectaFTPRoms('/mnt/usb1/')" class="tactile-fx px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-[9px] font-mono text-gray-300 uppercase shrink-0"><i class="fa-brands fa-usb mr-1 text-amber-400"></i> USB 1</button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-3 custom-scrollbar flex flex-col gap-1.5" id="roms-lista-carpetas-ftp"></div>

        <div class="w-full p-4 border-t border-white/5 bg-[#060913] shrink-0">
            <button onclick="window.confirmarRutaFTPRoms()" class="w-full h-12 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-black text-[12px] uppercase tracking-widest tactile-fx shadow-[0_0_20px_rgba(34,211,238,0.3)] flex items-center justify-center gap-2">
                <i class="fa-solid fa-check text-lg"></i> Guardar Carpeta Seleccionada
            </button>
        </div>
    </div>

    <div id="modal-exito-anim-roms" class="fixed inset-0 z-[9999] bg-black/80 backdrop-blur-sm hidden flex flex-col items-center justify-center p-4 opacity-0 transition-opacity duration-500 overflow-hidden pointer-events-none">
        <div id="confetti-container-roms" class="absolute inset-0 pointer-events-none overflow-hidden"></div>
        <div id="content-exito-roms" class="w-full max-w-sm rounded-[2rem] bg-gradient-to-b from-[#0a0f1a] to-[#02040a] p-8 shadow-[0_0_50px_rgba(16,185,129,0.2)] border border-emerald-500/50 flex flex-col items-center transform scale-50 transition-transform duration-500 pointer-events-auto">
            <div class="w-20 h-20 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-5xl mb-6 shadow-[0_0_30px_rgba(16,185,129,0.5)]">
                <i class="fa-solid fa-check-circle drop-shadow-[0_0_10px_rgba(16,185,129,0.8)]"></i>
            </div>
            <h2 class="text-[22px] font-orbitron font-black tracking-widest uppercase text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-cyan-400 mb-2 text-center">¡ENVÍO EXITOSO!</h2>
            <p class="text-[12px] font-tech text-gray-300 mb-8 text-center" id="roms-txt-modal-exito">Todos los archivos han sido transferidos a la consola satisfactoriamente.</p>
            <button onclick="window.cerrarModalExitoRoms()" class="w-full py-4 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-400 text-[12px] font-black tracking-widest uppercase text-black active:scale-95 transition-all shadow-[0_0_20px_rgba(16,185,129,0.5)]">
                ¡GENIAL!
            </button>
        </div>
    </div>
</div>
<?php
// Developed By SeBaS
?>
