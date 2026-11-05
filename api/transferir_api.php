<?php
/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: api/transferir_api.php
 * ====================================================================
 */
?>
<div id="layer-transferir" class="app-layer flex flex-col p-4 h-[100dvh] w-full overflow-hidden bg-[#02040a] relative hidden">
    
    <div class="w-full flex items-center justify-between z-30 shrink-0 pt-1 mb-6">
        <div class="flex items-center gap-3">
            <button onclick="volverAlLauncher()" class="w-10 h-10 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-center active:scale-90 transition-all hover:bg-white/5">
                <i class="fa-solid fa-arrow-left text-gray-300"></i>
            </button>
            <div class="flex flex-col">
                <h2 class="text-[18px] font-black tracking-tighter uppercase text-white leading-none">FTP Manual</h2>
                <span class="text-[9px] font-mono text-cyan-400 tracking-widest mt-0.5">Transferencia de Archivos</span>
            </div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 shadow-[0_0_15px_rgba(6,182,212,0.2)]">
            <i class="fa-solid fa-sitemap text-lg"></i>
        </div>
    </div>

    <div class="flex-1 flex flex-col gap-4 overflow-y-auto hide-scrollbar pb-10">
        
        <div id="transfer-dropzone" onclick="document.getElementById('input-archivo-pesado').click()" class="w-full h-32 rounded-[2rem] border-2 border-dashed border-white/10 bg-[#0a0f1a] flex flex-col items-center justify-center cursor-pointer hover:border-cyan-500/40 hover:bg-cyan-500/5 transition-all active:scale-[0.98] shrink-0">
            <div class="w-12 h-12 rounded-full bg-cyan-500/10 text-cyan-500 flex items-center justify-center text-2xl mb-2 shadow-inner"><i class="fa-solid fa-file-upload"></i></div>
            <span class="text-[12px] font-black uppercase text-white tracking-widest text-center px-4 truncate w-full" id="transfer-filename">Elegir Archivos...</span>
            <span class="text-[9px] font-mono text-gray-500 mt-1 uppercase" id="transfer-queue-status">Cola de Envío Vacía</span>
        </div>
        <input type="file" id="input-archivo-pesado" multiple class="hidden" onchange="prepararArchivoTransferencia(event)">

        <div class="w-full bg-[#0a0f1a] rounded-[1.5rem] p-4 border border-white/5 shadow-md flex flex-col gap-2 shrink-0">
            <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Ruta Destino FTP:</label>
            <div class="flex items-center gap-2">
                <div class="flex-1 flex items-center bg-black/40 border border-white/10 rounded-xl px-3 py-2.5">
                    <span class="text-cyan-500 mr-2"><i class="fa-solid fa-folder-open"></i></span>
                    <input type="text" id="transfer-target-path" value="/data/" placeholder="/data/" class="w-full bg-transparent text-[11px] font-mono text-white outline-none">
                </div>
                <!-- BOTÓN DE HISTORIAL DE RUTAS -->
                <button onclick="abrirHistorialRutasFTP()" class="w-11 h-11 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/30 flex items-center justify-center active:scale-90 transition-all hover:bg-purple-500/20 shadow-inner shrink-0">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </button>
                <button onclick="abrirExploradorFTP()" class="w-11 h-11 rounded-xl bg-cyan-500/10 text-cyan-500 border border-cyan-500/30 flex items-center justify-center active:scale-90 transition-all hover:bg-cyan-500/20 shadow-inner shrink-0">
                    <i class="fa-solid fa-sitemap"></i>
                </button>
            </div>
        </div>

        <div class="w-full bg-[#0a0f1a] rounded-[2rem] p-5 border border-white/5 shadow-xl relative shrink-0">
            <div class="flex justify-between items-center mb-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Progreso</span>
                <span class="text-[12px] font-black text-cyan-400 font-mono" id="transfer-percent">0%</span>
            </div>
            <div class="w-full h-2.5 bg-black rounded-full overflow-hidden mb-4 border border-white/5">
                <div id="transfer-bar" class="h-full bg-gradient-to-r from-cyan-600 to-blue-400 w-0 transition-all"></div>
            </div>
            <div class="grid grid-cols-2 gap-3 text-center">
                <div class="flex flex-col bg-black/40 p-2 rounded-xl border border-white/5">
                    <span class="text-[8px] font-bold text-gray-500 uppercase">Velocidad</span>
                    <span class="text-[11px] font-mono text-white" id="transfer-speed">0.00 MB/s</span>
                </div>
                <div class="flex flex-col bg-black/40 p-2 rounded-xl border border-white/5">
                    <span class="text-[8px] font-bold text-gray-500 uppercase">Restante</span>
                    <span class="text-[11px] font-mono text-white" id="transfer-eta">--:--</span>
                </div>
            </div>
            <div class="flex justify-between items-center mt-3 px-1">
                <span class="text-[8px] font-black tracking-widest text-gray-500 uppercase">Enviado: <span id="transfer-sent" class="text-white ml-1 font-mono">0 B</span></span>
                <span class="text-[8px] font-black tracking-widest text-gray-500 uppercase">Total: <span id="transfer-total" class="text-white ml-1 font-mono">0 B</span></span>
            </div>
        </div>

        <div class="flex gap-3 mt-auto shrink-0 mb-2">
            <button id="btn-iniciar-transferencia" onclick="iniciarGestorDeCola()" disabled class="flex-1 py-4 rounded-[1.5rem] bg-gray-800 text-gray-500 text-[12px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-3 pointer-events-none">
                <i class="fa-solid fa-rocket"></i> Enviar por FTP
            </button>
            <button id="btn-abortar-transferencia" onclick="abortarTransferenciaActiva()" class="hidden w-16 h-[56px] rounded-[1.5rem] border border-red-500/30 bg-red-500/10 text-red-500 flex items-center justify-center text-xl active:scale-95 transition-all hover:bg-red-500/20">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
    </div>
</div>

<!-- MODAL EXPLORADOR FTP -->
<div id="modal-explorador-ftp" class="fixed inset-0 z-[990] bg-black/90 backdrop-blur-md hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div id="modal-content-explorador" class="w-full max-w-sm rounded-[2rem] border border-white/10 bg-[#0a0f1a] p-5 shadow-2xl flex flex-col h-[75vh] transform translate-y-full transition-transform duration-300 relative">
        <div class="flex items-center justify-between mb-4 shrink-0">
            <h3 class="text-[13px] font-black tracking-widest text-cyan-400 uppercase"><i class="fa-solid fa-network-wired mr-2"></i> Elegir Destino</h3>
            <button onclick="cerrarExploradorFTP()" class="w-8 h-8 rounded-full bg-white/5 text-gray-400 flex items-center justify-center hover:text-white active:scale-90 transition-all"><i class="fa-solid fa-times"></i></button>
        </div>
        <div class="flex items-center bg-[#111827] border border-white/5 rounded-xl px-3 py-2.5 mb-3 shrink-0">
            <span class="text-cyan-500 mr-2 text-[12px]"><i class="fa-solid fa-map-marker-alt"></i></span>
            <span id="explorador-ruta-actual" class="text-[11px] font-mono text-gray-200 w-full overflow-hidden" style="direction: rtl; text-align: left;">/data/</span>
        </div>
        <div id="explorador-lista" class="flex-1 overflow-y-auto custom-scrollbar flex flex-col gap-1.5 mb-4 bg-[#060913] rounded-xl p-2 border border-white/5 shadow-inner"></div>
        <button onclick="confirmarRutaFTP()" class="w-full py-4 rounded-xl bg-cyan-600 text-[11px] font-black tracking-widest uppercase text-black active:scale-95 transition-all shadow-[0_0_15px_rgba(6,182,212,0.4)] shrink-0">
            <i class="fa-solid fa-check mr-1"></i> Fijar Esta Carpeta
        </button>
        <!-- BOTÓN FLOTANTE CREAR CARPETA -->
        <button onclick="preguntarNuevaCarpeta()" class="absolute bottom-24 right-8 w-12 h-12 rounded-full bg-green-500 text-white shadow-[0_0_20px_rgba(34,197,94,0.4)] flex items-center justify-center text-xl hover:bg-green-400 active:scale-90 transition-transform">
            <i class="fa-solid fa-folder-plus"></i>
        </button>
    </div>
</div>

<!-- MODAL HISTORIAL DE RUTAS -->
<div id="modal-history-rutas" class="fixed inset-0 z-[995] bg-black/90 backdrop-blur-md hidden flex flex-col justify-end md:justify-center transition-opacity duration-300 opacity-0 pointer-events-none p-4">
    <div class="w-full max-w-sm mx-auto rounded-[2rem] p-5 shadow-2xl transition-transform duration-300 translate-y-full md:scale-95 border border-purple-500/30 bg-[#0a0f1a] flex flex-col max-h-[70vh]" id="modal-content-history-rutas">
        <div class="flex justify-between items-center mb-4 shrink-0 border-b border-purple-500/30 pb-3">
            <h2 class="text-[12px] font-black uppercase tracking-widest flex items-center gap-2 text-purple-400"><i class="fa-solid fa-clock-rotate-left"></i> RUTAS RECIENTES</h2>
            <button onclick="cerrarHistorialRutas()" class="w-7 h-7 bg-white/5 rounded-full flex items-center justify-center active:scale-90 text-gray-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="history-rutas-list" class="flex-1 overflow-y-auto custom-scrollbar font-mono space-y-2 pr-2 mb-4"></div>
        <button onclick="limpiarHistorialRutas()" class="w-full shrink-0 border border-red-500/30 bg-red-900/20 active:scale-95 text-red-400 font-black text-[10px] py-3 rounded-xl tracking-widest uppercase transition-transform shadow-lg flex justify-center items-center gap-2">
            <i class="fa-solid fa-trash"></i> BORRAR TODO EL HISTORIAL
        </button>
    </div>
</div>

<!-- MODAL NUEVA CARPETA -->
<div id="modal-nueva-carpeta" class="fixed inset-0 z-[999] bg-black/90 backdrop-blur-md hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="w-full max-w-sm rounded-[2rem] border-2 border-green-500/50 bg-[#0a0f1a] p-6 shadow-2xl flex flex-col items-center">
        <h3 class="text-[14px] font-black tracking-widest text-green-400 uppercase text-center mb-4"><i class="fa-solid fa-folder-plus mr-2"></i> Crear Carpeta</h3>
        <input type="text" id="input-nueva-carpeta" class="w-full bg-[#111827] border border-white/10 rounded-xl px-4 py-3 mb-5 text-[12px] font-mono text-white outline-none focus:border-green-500/50 transition-colors" placeholder="nombre_de_carpeta">
        <div class="flex gap-2 w-full">
            <button onclick="cerrarNuevaCarpeta()" class="flex-1 py-3.5 rounded-xl border border-white/10 bg-white/5 text-[10px] font-black tracking-widest uppercase text-gray-300 active:scale-95 transition-all">Cancelar</button>
            <button onclick="ejecutarCrearCarpeta()" class="flex-1 py-3.5 rounded-xl bg-green-600 text-[10px] font-black tracking-widest uppercase text-black active:scale-95 transition-all shadow-[0_0_15px_rgba(34,197,94,0.4)]">Crear</button>
        </div>
    </div>
</div>

<!-- MODAL COLISIÓN -->
<div id="modal-colision-ftp" class="fixed inset-0 z-[990] bg-black/90 backdrop-blur-md hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <div class="w-full max-w-sm rounded-[2rem] border-2 border-cyan-500/50 bg-[#0a0f1a] p-6 shadow-2xl flex flex-col items-center">
        <div class="w-16 h-16 rounded-full bg-cyan-500/20 text-cyan-500 flex items-center justify-center text-3xl mb-4 shadow-[0_0_15px_rgba(6,182,212,0.3)]">
            <i class="fa-solid fa-clone"></i>
        </div>
        <h3 class="text-[15px] font-black tracking-widest text-white uppercase text-center mb-2">Archivo Existente</h3>
        <p class="text-[10px] text-gray-400 text-center font-mono mb-4 px-2 leading-relaxed">
            El archivo <b class="text-cyan-400" id="colision-filename">nombre.pkg</b> ya existe en la consola. ¿Qué deseas hacer con él?
        </p>
        <input type="text" id="colision-input-rename" class="w-full bg-[#111827] border border-white/10 rounded-xl px-4 py-3 mb-5 text-[11px] font-bold text-white outline-none focus:border-cyan-500/50 transition-colors" placeholder="Nuevo nombre...">
        <div class="flex flex-col gap-2 w-full">
            <button onclick="accionColision('renombrar')" class="w-full py-3.5 rounded-xl bg-cyan-600 text-[10px] font-black tracking-widest uppercase text-black active:scale-95 transition-all shadow-[0_0_15px_rgba(6,182,212,0.4)]">
                <i class="fa-solid fa-edit mr-1"></i> Renombrar y Subir
            </button>
            <button onclick="accionColision('reemplazar')" class="w-full py-3.5 rounded-xl border border-red-500/30 bg-red-500/10 text-[10px] font-black tracking-widest uppercase text-red-400 active:scale-95 transition-all hover:bg-red-500/20">
                <i class="fa-solid fa-exclamation-triangle mr-1"></i> Sobrescribir Original
            </button>
            <button onclick="accionColision('omitir')" class="w-full py-3.5 rounded-xl border border-white/10 bg-white/5 text-[10px] font-black tracking-widest uppercase text-gray-300 active:scale-95 transition-all">
                <i class="fa-solid fa-step-forward mr-1"></i> Omitir Archivo
            </button>
        </div>
    </div>
</div>

<!-- MODAL ÉXITO CENTRAL + CONFETI -->
<div id="modal-exito-anim" class="fixed inset-0 z-[1000] bg-black/80 backdrop-blur-sm hidden flex flex-col items-center justify-center p-4 opacity-0 transition-opacity duration-500 overflow-hidden pointer-events-none">
    <div id="confetti-container" class="absolute inset-0 pointer-events-none overflow-hidden"></div>
    <div id="modal-content-exito" class="w-full max-w-sm rounded-[2rem] bg-gradient-to-b from-[#0a0f1a] to-[#02040a] p-8 shadow-[0_0_50px_rgba(16,185,129,0.2)] border border-emerald-500/50 flex flex-col items-center transform scale-50 transition-transform duration-500 pointer-events-auto">
        <div class="w-20 h-20 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-5xl mb-6 shadow-[0_0_30px_rgba(16,185,129,0.5)]">
            <i class="fa-solid fa-check-circle drop-shadow-[0_0_10px_rgba(16,185,129,0.8)]"></i>
        </div>
        <h2 class="text-[22px] font-orbitron font-black tracking-widest uppercase text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-cyan-400 mb-2">¡ENVÍO EXITOSO!</h2>
        <p class="text-[12px] font-tech text-gray-300 mb-8 text-center">Todos los archivos han sido transferidos a la consola satisfactoriamente.</p>
        <button onclick="cerrarModalExito()" class="w-full py-4 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-400 text-[12px] font-black tracking-widest uppercase text-black active:scale-95 transition-all shadow-[0_0_20px_rgba(16,185,129,0.5)]">
            ¡GENIAL!
        </button>
    </div>
</div>
