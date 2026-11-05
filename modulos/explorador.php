<?php
/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: modulos/explorador.php
 * ====================================================================
 */
header('Content-Type: text/html; charset=utf-8');
$firma = chr(83).chr(101).chr(66).chr(97).chr(83); 
header('X-Author: ' . $firma);
?>
<style>
    .theme-glass .header { border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(6,9,19,0.8); backdrop-filter: blur(10px); }
    .theme-glass .search-bar { background: rgba(10, 13, 24, 0.55); border: 1px solid rgba(255,255,255,0.05); backdrop-filter: blur(12px); border-radius: 1rem; }
    .icon-dir { color: #10b981; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.2); box-shadow: 0 0 15px rgba(16,185,129,0.1); }
    .icon-pkg { color: #22d3ee; background: rgba(34,211,238,0.15); border: 1px solid rgba(34,211,238,0.2); box-shadow: 0 0 15px rgba(34,211,238,0.1); }
    .icon-txt { color: #a855f7; background: rgba(168,85,247,0.15); border: 1px solid rgba(168,85,247,0.2); box-shadow: 0 0 15px rgba(168,85,247,0.1); }
    .icon-img { color: #f59e0b; background: rgba(245,158,11,0.15); border: 1px solid rgba(245,158,11,0.2); box-shadow: 0 0 15px rgba(245,158,11,0.1); }
    .icon-default { color: #9ca3af; background: rgba(156,163,175,0.15); border: 1px solid rgba(156,163,175,0.2); box-shadow: 0 0 15px rgba(156,163,175,0.1); }
    #ftp-list-container { transition: all 0.3s ease; -webkit-overflow-scrolling: touch; position: relative; }
    .view-list { display: flex; flex-direction: column; gap: 0.5rem; }
    .view-list .file-card { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; border-radius: 1rem; background: rgba(10, 13, 24, 0.4); border: 1px solid transparent; }
    .view-list .file-info { display: flex; flex-direction: column; flex: 1; min-width: 0; padding-left: 0.75rem; }
    .view-list .file-meta { display: flex; flex-direction: column; align-items: flex-end; flex-shrink: 0; }
    .view-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.5rem; padding-top: 0.5rem; }
    .view-grid .file-card { background: transparent !important; border: none !important; padding: 0.25rem; box-shadow: none !important; justify-content: flex-start; }
    .view-grid .file-card > div:first-child { flex-direction: column !important; justify-content: flex-start !important; align-items: center !important; gap: 0.4rem !important; }
    .view-grid .file-icon-wrap { width: 4.2rem !important; height: 4.2rem !important; font-size: 2.3rem !important; border-radius: 1.2rem !important; margin: 0 auto; }
    .view-grid .file-info { padding-left: 0 !important; width: 100%; align-items: center; }
    .view-grid .file-title { font-size: 10px !important; text-align: center; white-space: normal; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-height: 1.2; word-break: break-all; }
    .view-grid .file-date { display: none !important; }
    .view-grid .file-meta { display: none !important; }
    .view-grid .ctx-btn { display: none !important; }
    .view-grid .select-check { top: 0 !important; left: 0 !important; transform: none !important; width: 1.25rem; height: 1.25rem; }
    .file-card { position: relative; transition: all 0.2s ease; cursor: pointer; }
    .view-list .file-card.selected { border-color: #22d3ee; background: rgba(34, 211, 238, 0.1); }
    .view-grid .file-card.selected .file-icon-wrap { opacity: 0.5; border: 2px solid #22d3ee; }
    .file-card .select-check { transition: all 0.2s; opacity: 0; transform: scale(0.5); pointer-events: none; }
    .mode-select .file-card .select-check { opacity: 1; transform: scale(1); }
    .mode-select .file-card .ctx-btn { opacity: 0; pointer-events: none; }
    .hide-scrollbar::-webkit-scrollbar { display: none; }
</style>

<div id="layer-explorador" class="app-layer flex flex-col h-[100dvh] w-full overflow-hidden bg-transparent relative hidden theme-glass">
    <header class="header w-full pt-5 pb-3 px-4 flex items-center justify-between z-20 shrink-0">
        <div class="flex items-center gap-3">
            <button onclick="navegarArribaOLauncher()" class="tactile-fx w-10 h-10 flex items-center justify-center rounded-[1rem] bg-white/5 border border-white/10 text-gray-300 hover:text-white">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <div class="flex flex-col">
                <div class="flex items-center gap-2">
                    <h2 class="text-[17px] font-black uppercase tracking-widest leading-none text-white drop-shadow-md">Explorador</h2>
                    <i id="loader-explorador" class="fas fa-circle-notch fa-spin text-cyan-400 text-xs hidden"></i>
                </div>
                <span class="text-[9px] font-mono text-cyan-400 mt-1 uppercase tracking-widest">Root / Consola</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="cargarRutaFtp(rutaActualExplorador, true)" class="tactile-fx w-9 h-9 flex items-center justify-center rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400">
                <i class="fa-solid fa-rotate-right text-sm"></i>
            </button>
            <button onclick="window.volverAlLauncher()" class="tactile-fx w-9 h-9 flex items-center justify-center rounded-xl bg-red-500/10 border border-red-500/30 text-red-400">
                <i class="fa-solid fa-house text-sm"></i>
            </button>
        </div>
    </header>

    <div class="px-4 py-4 shrink-0 z-10 w-full flex flex-col gap-3">
        <div class="flex items-center gap-2 w-full">
            <div class="search-bar flex-1 h-11 flex items-center px-4 gap-3 focus-within:border-cyan-500/50 transition-colors">
                <i class="fa-solid fa-magnifying-glass text-gray-500 text-xs"></i>
                <input type="text" id="buscador-explorador" oninput="filtrarExploradorActivo()" placeholder="Buscar en carpeta actual..." class="bg-transparent border-none outline-none w-full text-[12px] font-medium placeholder-gray-500 text-white">
            </div>
            <button onclick="toggleExploradorViewMode()" id="btn-view-mode" class="tactile-fx w-11 h-11 shrink-0 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center shadow-[0_0_15px_rgba(34,211,238,0.15)]">
                <i class="fa-solid fa-border-all text-lg"></i>
            </button>
            <button onclick="abrirHubFavoritos()" class="tactile-fx w-11 h-11 shrink-0 rounded-2xl bg-gradient-to-br from-amber-500/20 to-yellow-600/20 border border-amber-500/30 text-amber-400 flex items-center justify-center shadow-[0_0_15px_rgba(245,158,11,0.15)]">
                <i class="fa-solid fa-star text-lg"></i>
            </button>
        </div>
        <div id="ftp-ruta-actual" class="flex items-center w-full px-1 overflow-x-auto hide-scrollbar gap-2 h-7"></div>
    </div>

    <div id="list-headers" class="px-5 py-2 flex items-center justify-between shrink-0 opacity-50 border-b border-white/5 mx-4">
        <span class="text-[9px] font-black uppercase tracking-widest">Nombre</span>
        <span class="text-[9px] font-black uppercase tracking-widest">Tamaño</span>
    </div>

    <div id="ftp-list-container" class="view-list flex-1 overflow-y-auto px-4 pb-28 hide-scrollbar z-10 w-full relative"></div>

    <div id="clipboard-bar" class="fixed bottom-6 right-[88px] z-[80] w-[260px] bg-indigo-950/95 backdrop-blur-xl border border-indigo-500/50 p-2 rounded-2xl flex items-center justify-between shadow-[0_10px_40px_rgba(79,70,229,0.4)] transition-all duration-300 opacity-0 translate-x-10 scale-95 pointer-events-none">
        <div class="flex items-center gap-2 pl-2">
            <div class="bg-indigo-500/20 text-indigo-400 w-8 h-8 flex items-center justify-center rounded-xl"><i class="fa-solid fa-clipboard text-sm"></i></div>
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-white uppercase tracking-widest">Portapapeles</span>
                <span id="clipboard-count" class="text-[8px] font-mono text-indigo-300 mt-0.5">0 items</span>
            </div>
        </div>
        <div class="flex items-center gap-1 pr-1">
            <button onclick="ejecutarPegado()" class="tactile-fx px-4 py-2 bg-indigo-500 text-white font-black text-[9px] uppercase tracking-widest rounded-xl shadow-[0_0_15px_rgba(99,102,241,0.5)]">Pegar</button>
            <button onclick="cancelarPegado()" class="tactile-fx w-8 h-8 flex items-center justify-center bg-red-500/20 text-red-400 border border-red-500/30 rounded-xl"><i class="fa-solid fa-times text-xs"></i></button>
        </div>
    </div>

    <div id="header-multi-select" class="fixed bottom-6 right-[88px] z-[80] w-[260px] bg-cyan-950/95 backdrop-blur-xl border border-cyan-500/50 p-2 rounded-2xl flex items-center justify-between shadow-[0_10px_40px_rgba(6,182,212,0.4)] transition-all duration-300 opacity-0 translate-x-10 scale-95 pointer-events-none">
        <div class="flex items-center gap-2 pl-2">
            <button onclick="cancelarSeleccionMultiple()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-black/40 text-white tactile-fx"><i class="fa-solid fa-times text-xs"></i></button>
            <span id="txt-multi-count" class="text-[10px] font-black text-white tracking-widest uppercase">0 Archivos</span>
        </div>
        <div class="flex gap-1 pr-1">
            <button onclick="ejecutarAccionMultiple('copiar')" class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-300 flex items-center justify-center tactile-fx border border-blue-500/30"><i class="fa-solid fa-copy"></i></button>
            <button onclick="ejecutarAccionMultiple('cortar')" class="w-9 h-9 rounded-xl bg-orange-500/20 text-orange-300 flex items-center justify-center tactile-fx border border-orange-500/30"><i class="fa-solid fa-scissors"></i></button>
            <button onclick="ejecutarAccionMultiple('eliminar')" class="w-9 h-9 rounded-xl bg-red-500/50 text-white flex items-center justify-center tactile-fx shadow-[0_0_15px_rgba(239,68,68,0.5)]"><i class="fa-solid fa-trash"></i></button>
        </div>
    </div>

    <div class="fixed bottom-6 right-6 z-40">
        <div id="fab-menu-ex" class="absolute bottom-[4.5rem] right-0 flex flex-col items-end gap-3 transition-all duration-200 origin-bottom transform scale-0 opacity-0 pointer-events-none">
            <button onclick="document.getElementById('explorador-upload-folder').click()" class="tactile-fx flex items-center gap-3">
                <span class="text-[10px] font-bold uppercase tracking-widest text-white bg-black/70 px-3 py-1.5 rounded-lg backdrop-blur-md whitespace-nowrap">Subir Carpeta</span>
                <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center shadow-lg shrink-0"><i class="fa-solid fa-folder-open text-sm"></i></div>
            </button>
            <button onclick="document.getElementById('explorador-upload-file').click()" class="tactile-fx flex items-center gap-3">
                <span class="text-[10px] font-bold uppercase tracking-widest text-white bg-black/70 px-3 py-1.5 rounded-lg backdrop-blur-md whitespace-nowrap">Subir Archivo</span>
                <div class="w-10 h-10 rounded-full bg-cyan-600 text-white flex items-center justify-center shadow-lg shrink-0"><i class="fa-solid fa-cloud-arrow-up text-sm"></i></div>
            </button>
            <button onclick="promptCrear('carpeta')" class="tactile-fx flex items-center gap-3">
                <span class="text-[10px] font-bold uppercase tracking-widest text-white bg-black/70 px-3 py-1.5 rounded-lg backdrop-blur-md whitespace-nowrap">Crear Carpeta</span>
                <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow-lg shrink-0"><i class="fa-solid fa-folder-plus text-sm"></i></div>
            </button>
        </div>
        <button onclick="toggleExploradorFAB()" id="fab-main-ex" class="tactile-fx w-14 h-14 rounded-full bg-gradient-to-r from-cyan-600 to-blue-600 text-white flex items-center justify-center text-xl shadow-[0_10px_20px_rgba(6,182,212,0.4)] transition-transform duration-300 relative z-10">
            <i class="fa-solid fa-plus transition-transform duration-300" id="fab-icon-ex"></i>
        </button>
        <input type="file" id="explorador-upload-file" class="hidden" onchange="procesarSubidaExplorador(event)">
        <input type="file" id="explorador-upload-folder" class="hidden" webkitdirectory directory multiple onchange="procesarSubidaCarpetaEstructurada(event)">
    </div>

    <div id="modal-hub-favs" class="fixed inset-0 z-[9999] bg-[#02040a]/90 backdrop-blur-sm hidden flex-col transition-opacity duration-300 opacity-0">
        <div class="absolute inset-0 z-0" onclick="cerrarHubFavoritos()"></div>
        <div id="modal-hub-favs-content" class="absolute bottom-0 left-0 w-full bg-[#0a0d18] border-t border-white/10 rounded-t-[2rem] p-6 shadow-[0_-20px_50px_rgba(0,0,0,0.8)] z-10 transform translate-y-full transition-transform duration-300">
            <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-5"></div>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-[15px] font-black tracking-widest uppercase text-white"><i class="fa-solid fa-star text-amber-400 mr-2"></i> Mis Accesos</h3>
                <button onclick="abrirModalNuevoAccesoFtp()" class="px-3 py-1.5 rounded-lg bg-cyan-500/10 text-cyan-400 text-[9px] font-bold uppercase tracking-widest border border-cyan-500/30 tactile-fx hover:bg-cyan-500/20">
                    <i class="fa-solid fa-plus mr-1"></i> Añadir Actual
                </button>
            </div>
            <div id="ftp-shortcuts-grid" class="grid grid-cols-3 gap-2.5 mb-4 max-h-[40vh] overflow-y-auto custom-scrollbar"></div>
        </div>
    </div>

    <div id="modal-ctx-explorador" class="fixed inset-0 z-[9999] bg-[#02040a]/80 backdrop-blur-sm hidden flex-col transition-opacity duration-300 opacity-0">
        <div class="absolute inset-0 z-0" onclick="cerrarContextoExplorador()"></div>
        <div id="modal-ctx-content" class="absolute bottom-0 left-0 w-full bg-[#0a0d18] border-t border-cyan-500/30 rounded-t-[2rem] p-5 shadow-[0_-20px_50px_rgba(0,0,0,0.8)] z-10 transform translate-y-full transition-transform duration-300 pb-8">
            <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-4"></div>
            <div class="flex items-center gap-4 bg-white/5 p-4 rounded-2xl mb-5 border border-white/10">
                <div id="ctx-icon-display" class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0"></div>
                <div class="flex flex-col min-w-0">
                    <span id="ctx-filename" class="text-[13px] font-black text-white truncate w-full"></span>
                    <span id="ctx-filetype" class="text-[10px] font-mono text-cyan-400 uppercase mt-0.5"></span>
                </div>
            </div>
            <div class="grid grid-cols-4 gap-2 mb-2">
                <button onclick="accionDirectaContexto('copiar')" class="tactile-fx flex flex-col items-center justify-center gap-2 p-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 text-gray-300 h-[70px]">
                    <i class="fa-solid fa-copy text-lg"></i>
                    <span class="text-[8px] font-bold uppercase tracking-widest text-center leading-none">Copiar</span>
                </button>
                <button onclick="accionDirectaContexto('cortar')" class="tactile-fx flex flex-col items-center justify-center gap-2 p-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 text-gray-300 h-[70px]">
                    <i class="fa-solid fa-scissors text-lg"></i>
                    <span class="text-[8px] font-bold uppercase tracking-widest text-center leading-none">Cortar</span>
                </button>
                <button onclick="accionDirectaContexto('renombrar')" class="tactile-fx flex flex-col items-center justify-center gap-2 p-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 text-gray-300 h-[70px]">
                    <i class="fa-solid fa-pen text-lg"></i>
                    <span class="text-[8px] font-bold uppercase tracking-widest text-center leading-none">Nombre</span>
                </button>
                <button onclick="accionDirectaContexto('duplicar')" class="tactile-fx flex flex-col items-center justify-center gap-2 p-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 text-gray-300 h-[70px]">
                    <i class="fa-solid fa-clone text-lg"></i>
                    <span class="text-[8px] font-bold uppercase tracking-widest text-center leading-none">Duplicar</span>
                </button>
            </div>
            <button id="ctx-btn-bajar" onclick="accionDirectaContexto('descargar')" class="hidden tactile-fx w-full p-4 mt-2 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-400 flex items-center justify-between">
                <div class="flex items-center gap-3"><i class="fa-solid fa-download text-lg"></i><span class="text-[11px] font-bold uppercase tracking-widest">Bajar al Celular</span></div>
            </button>
            <button onclick="accionDirectaContexto('eliminar')" class="tactile-fx w-full p-4 mt-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-500 flex items-center justify-between shadow-[0_0_15px_rgba(239,68,68,0.15)]">
                <div class="flex items-center gap-3"><i class="fa-solid fa-trash-can text-lg"></i><span class="text-[11px] font-black uppercase tracking-widest">Eliminar Permanentemente</span></div>
            </button>
        </div>
    </div>

    <div id="modal-prompt-ftp" class="fixed inset-0 z-[960] bg-black/90 backdrop-blur-md hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="w-full max-w-sm rounded-[2rem] border border-cyan-500/30 bg-[#0a0f1a] p-6 shadow-2xl flex flex-col">
            <h3 id="prompt-title" class="text-[14px] font-black tracking-widest text-cyan-400 uppercase text-center mb-4">Acción</h3>
            <input type="text" id="prompt-input" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3.5 mb-6 text-[12px] font-bold text-white outline-none focus:border-cyan-400 transition-colors">
            <div class="flex gap-3 w-full">
                <button onclick="cerrarPromptFtp()" class="flex-1 py-3.5 rounded-xl border border-white/10 bg-white/5 text-[10px] font-black tracking-widest uppercase text-gray-300 active:scale-95 transition-all">Cancelar</button>
                <button id="prompt-btn-confirm" class="flex-1 py-3.5 rounded-xl bg-cyan-600 text-[10px] font-black tracking-widest uppercase text-black active:scale-95 transition-all shadow-[0_0_15px_rgba(6,182,212,0.4)]">Confirmar</button>
            </div>
        </div>
    </div>

    <div id="modal-delete-confirm" class="fixed inset-0 z-[970] bg-black/90 backdrop-blur-md hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="w-full max-w-sm rounded-[2rem] border border-red-500/50 bg-[#0a0202]/95 p-6 shadow-2xl flex flex-col items-center text-center">
            <div class="w-16 h-16 rounded-full bg-red-500/20 text-red-500 flex items-center justify-center text-3xl mb-4 shadow-[0_0_15px_rgba(239,68,68,0.4)]"><i class="fa-solid fa-trash-can"></i></div>
            <h3 id="del-modal-title" class="text-[16px] font-black tracking-widest text-red-400 uppercase mb-2">¿Eliminar Permanentemente?</h3>
            <p id="del-modal-desc" class="text-[10px] text-gray-300 mb-6 px-2 leading-relaxed font-mono">...</p>
            <div class="flex flex-col gap-3 w-full">
                <button id="btn-real-delete" class="w-full py-4 rounded-xl bg-red-600 text-[11px] font-black tracking-widest uppercase text-white active:scale-95 transition-all shadow-[0_0_15px_rgba(239,68,68,0.4)]">Sí, Eliminar</button>
                <button onclick="cerrarDeleteConfirm()" class="w-full py-3.5 rounded-xl border border-white/10 bg-white/5 text-[10px] font-black tracking-widest uppercase text-gray-300 active:scale-95 transition-all">Cancelar</button>
            </div>
        </div>
    </div>

    <div id="modal-nuevo-acceso-ftp" class="fixed inset-0 z-[950] bg-black/90 backdrop-blur-md hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="w-full max-w-sm rounded-[2rem] border border-amber-500/30 bg-[#0a0f1a] p-6 shadow-2xl flex flex-col">
            <div class="w-14 h-14 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/30 flex items-center justify-center text-2xl mx-auto mb-3 shadow-[0_0_15px_rgba(245,158,11,0.3)]"><i class="fa-solid fa-star"></i></div>
            <h3 class="text-[14px] font-black tracking-widest text-white uppercase text-center mb-1">Añadir a Favoritos</h3>
            <p class="text-[9px] text-gray-400 text-center font-mono mb-5 px-2 truncate" id="txt-ruta-a-guardar"></p>
            <div class="flex justify-between w-full mb-6 px-2">
                <button onclick="seleccionarIconoFav('fa-folder', 'cyan', this)" class="btn-fav-icon tactile-fx w-11 h-11 rounded-xl bg-cyan-500/20 text-cyan-400 border border-cyan-500/50 text-xl"><i class="fa-solid fa-folder"></i></button>
                <button onclick="seleccionarIconoFav('fa-gamepad', 'purple', this)" class="btn-fav-icon tactile-fx w-11 h-11 rounded-xl bg-purple-500/20 text-purple-400 border border-transparent text-xl"><i class="fa-solid fa-gamepad"></i></button>
                <button onclick="seleccionarIconoFav('fa-compact-disc', 'emerald', this)" class="btn-fav-icon tactile-fx w-11 h-11 rounded-xl bg-emerald-500/20 text-emerald-400 border border-transparent text-xl"><i class="fa-solid fa-compact-disc"></i></button>
                <button onclick="seleccionarIconoFav('fa-box-open', 'amber', this)" class="btn-fav-icon tactile-fx w-11 h-11 rounded-xl bg-amber-500/20 text-amber-400 border border-transparent text-xl"><i class="fa-solid fa-box-open"></i></button>
            </div>
            <input type="hidden" id="fav-selected-icon" value="fa-folder">
            <input type="hidden" id="fav-selected-color" value="cyan">
            <input type="text" id="ftp-shortcut-btn-name" placeholder="Nombre (Ej: Mods GTA V)" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3.5 mb-6 text-[12px] font-bold text-white outline-none placeholder:text-gray-700 uppercase tracking-wider focus:border-amber-500/50 transition-colors">
            <div class="flex gap-3 w-full">
                <button onclick="cerrarModalNuevoAccesoFtp()" class="flex-1 py-3.5 rounded-xl border border-white/10 bg-white/5 text-[10px] font-black tracking-widest uppercase text-gray-300 active:scale-95 transition-all">Cancelar</button>
                <button onclick="guardarNuevoAccesoFtp()" class="flex-1 py-3.5 rounded-xl bg-amber-600 text-[10px] font-black tracking-widest uppercase text-black active:scale-95 transition-all shadow-[0_0_15px_rgba(245,158,11,0.4)]">Guardar</button>
            </div>
        </div>
    </div>

    <div id="modal-upload-progress" class="fixed inset-0 z-[980] bg-black/90 backdrop-blur-md hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="w-full max-w-sm rounded-[2rem] border border-cyan-500/30 bg-[#0a0f1a] p-6 shadow-[0_0_50px_rgba(6,182,212,0.15)] flex flex-col">
            <div class="w-16 h-16 rounded-full bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-3xl mx-auto mb-4 border border-cyan-500/30"><i id="progress-icon-explorador" class="fa-solid fa-cloud-arrow-up animate-bounce"></i></div>
            <h3 class="text-[14px] font-black tracking-widest text-white uppercase text-center mb-1" id="up-modal-title">Subiendo al FTP</h3>
            <p class="text-[10px] text-gray-400 text-center font-mono mb-5 truncate px-2" id="up-modal-filename">archivo.pkg</p>
            <div class="flex justify-between items-end mb-2 px-1">
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest" id="up-modal-speed">0 MB/s</span>
                <span class="text-[16px] font-black text-cyan-400" id="up-modal-percent">0%</span>
            </div>
            <div class="w-full h-3 bg-black rounded-full overflow-hidden border border-white/10 mb-4 shadow-inner">
                <div id="up-modal-bar" class="h-full bg-gradient-to-r from-blue-600 to-cyan-400 w-0 transition-all duration-300 relative"><div class="absolute inset-0 bg-white/20 w-full animate-[pulse_1.5s_ease-in-out_infinite]"></div></div>
            </div>
            <div class="flex justify-between items-center text-[9px] font-mono text-gray-500 mb-8 px-1">
                <span id="up-modal-sent">0 MB / 0 MB</span>
                <span id="up-modal-eta" class="text-white font-bold">ETA: --:--</span>
            </div>
            <button onclick="cancelarSubidaExplorador()" class="w-full py-4 rounded-xl border border-red-500/30 bg-red-500/10 text-[11px] font-black tracking-widest uppercase text-red-400 active:scale-95 transition-all hover:bg-red-500/20"><i class="fa-solid fa-ban mr-1"></i> Abortar Transferencia</button>
        </div>
    </div>
</div>
