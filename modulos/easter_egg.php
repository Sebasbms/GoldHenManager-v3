<?php
/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: modulos/easter_egg.php
 * ====================================================================
 */
?>
<style>
    .glass-gold {
        background: rgba(12, 8, 4, 0.65);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-top: 1px solid rgba(255, 255, 255, 0.15);
        border-left: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.9), inset 0 0 30px rgba(245, 158, 11, 0.1);
        transform-style: preserve-3d;
    }
    .sebas-gold-glitch {
        background: linear-gradient(to bottom, #fde68a, #f59e0b, #d97706);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        filter: drop-shadow(0 0 10px rgba(245, 158, 11, 0.6));
    }
    @keyframes scanline-egg { 0% { top: -10%; } 100% { top: 110%; } }
    @keyframes spin-slow-egg { 100% { transform: rotate(360deg); } }
    @keyframes pulse-glow-egg { 0%, 100% { box-shadow: 0 0 20px rgba(245, 158, 11, 0.4); } 50% { box-shadow: 0 0 40px rgba(245, 158, 11, 0.8); } }
    .scan-bar-egg { position: absolute; left: 0; width: 100%; height: 2px; background: linear-gradient(90deg, transparent, #fbbf24, transparent); box-shadow: 0 0 15px #fbbf24; opacity: 0.4; animation: scanline-egg 3s linear infinite; pointer-events: none; }
    .holo-scene-egg { perspective: 1200px; }
</style>

<!-- TOAST DE CONTEO -->
<div id="toast-secreto" class="fixed bottom-32 left-1/2 -translate-x-1/2 z-[9999] px-4 py-2 rounded-full bg-[#0d1321]/95 border border-cyan-500/40 text-[10px] font-mono text-cyan-300 tracking-widest uppercase shadow-[0_0_20px_rgba(34,211,238,0.25)] transition-all duration-200 opacity-0 scale-90 pointer-events-none">
    Faltan 3 toques...
</div>

<!-- BANNER TROFEO PLATINO -->
<div id="trofeo-banner" class="fixed bottom-6 left-1/2 -translate-x-1/2 translate-y-[200%] opacity-0 transition-all duration-500 ease-out z-[999999] flex items-center gap-4 px-5 py-3.5 rounded-[1.5rem] bg-[#0a0f1d]/95 border border-amber-400/50 shadow-[0_10px_40px_rgba(251,191,36,0.4)] w-[90%] max-w-sm pointer-events-none">
    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-300 to-amber-600 flex items-center justify-center text-black text-2xl shadow-[0_0_20px_rgba(251,191,36,0.6)] shrink-0">
        <i class="fa-solid fa-trophy"></i>
    </div>
    <div class="flex flex-col min-w-0">
        <span class="text-[9px] font-mono font-bold text-amber-400 uppercase tracking-widest">Trofeo Desbloqueado • Platino</span>
        <span class="text-[13px] font-black text-white uppercase tracking-wider truncate mt-0.5">Mente Maestra del Sistema</span>
    </div>
</div>

<!-- MODAL EASTER EGG COMPLETO -->
<div id="modal-easter-egg" class="fixed inset-0 z-[99990] bg-[#020308]/90 hidden flex-col items-center justify-center opacity-0 transition-opacity duration-500">
    <!-- FONDO INTERACTIVO -->
    <canvas id="particle-canvas" class="absolute inset-0 z-0 pointer-events-auto"></canvas>

    <!-- TARJETA HOLOGRÁFICA DORADA -->
    <div class="holo-scene-egg w-full px-5 max-w-[380px] relative z-10 flex justify-center pointer-events-none">
        <div id="dev-holo-card" class="glass-gold w-full rounded-[2rem] p-7 flex flex-col items-center text-center relative overflow-hidden pointer-events-auto transition-transform duration-100 scale-90">
            <div class="scan-bar-egg text-amber-400"></div>
            
            <div class="w-full flex items-center justify-between mb-6 z-10">
                <span class="px-3 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-[8px] font-mono font-bold text-amber-400 uppercase tracking-widest flex items-center gap-1.5 shadow-inner">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> ROOT ACCESS
                </span>
                <span class="text-[10px] font-mono text-gray-500 tracking-widest font-bold">ID #001-VIP</span>
            </div>
            
            <div class="relative w-28 h-28 flex items-center justify-center my-2">
                <div class="absolute inset-0 rounded-full border-[3px] border-dashed border-amber-500/40" style="animation: spin-slow-egg 12s linear infinite;"></div>
                <div class="w-20 h-20 rounded-[1.2rem] bg-amber-950/60 border border-amber-500/40 flex items-center justify-center text-amber-400 backdrop-blur-md" style="animation: pulse-glow-egg 3s infinite;">
                    <i class="fa-solid fa-crown text-4xl drop-shadow-[0_0_10px_rgba(245,158,11,0.8)]"></i>
                </div>
            </div>
            
            <span class="text-[9px] font-mono text-gray-400 uppercase tracking-[0.3em] mt-6">CREATED & DEVELOPED BY</span>
            <h2 class="text-[2.7rem] font-orbitron font-black tracking-wider uppercase sebas-gold-glitch mt-1 mb-2">SeBaS</h2>
            
            <div class="flex items-center gap-2 mb-6">
                <span class="text-[9px] font-bold uppercase tracking-widest px-3 py-1 rounded bg-amber-500/10 border border-amber-500/30 text-amber-400 shadow-inner">Lead Architect</span>
                <span class="text-[9px] font-bold uppercase tracking-widest px-3 py-1 rounded bg-amber-500/10 border border-amber-500/30 text-amber-400 shadow-inner">PS4 Scene Dev</span>
            </div>
            
            <p class="text-[11px] text-gray-300 font-medium bg-[#050301]/60 border border-amber-500/20 rounded-2xl p-4 mb-6 w-full shadow-inner leading-relaxed">
                "Diseñado línea por línea para llevar el control total de <span class="font-bold text-amber-400 drop-shadow-[0_0_5px_rgba(245,158,11,0.5)]">GoldHEN</span> al siguiente nivel."
            </p>
            
            <!-- ESTADÍSTICAS -->
            <div class="grid grid-cols-3 gap-2.5 w-full mb-6">
                <div class="bg-[#050301]/60 border border-amber-500/20 rounded-[1rem] p-3 flex flex-col items-center shadow-inner">
                    <i class="fa-solid fa-microchip text-amber-400 text-sm mb-1.5 opacity-80"></i>
                    <span class="text-[12px] font-black text-white">CORE</span>
                    <span class="text-[7px] font-mono text-gray-500 uppercase mt-0.5">Native Build</span>
                </div>
                <div class="bg-[#050301]/60 border border-amber-500/20 rounded-[1rem] p-3 flex flex-col items-center shadow-inner">
                    <i class="fa-solid fa-bolt text-amber-400 text-sm mb-1.5 opacity-80"></i>
                    <span class="text-[12px] font-black text-white">100%</span>
                    <span class="text-[7px] font-mono text-gray-500 uppercase mt-0.5">Custom UI</span>
                </div>
                <div class="bg-[#050301]/60 border border-amber-500/20 rounded-[1rem] p-3 flex flex-col items-center shadow-inner">
                    <i class="fa-brands fa-playstation text-amber-400 text-sm mb-1.5 opacity-80"></i>
                    <span class="text-[12px] font-black text-white">PRO</span>
                    <span class="text-[7px] font-mono text-gray-500 uppercase mt-0.5">Edition</span>
                </div>
            </div>

            <!-- Botón Cerrar -->
            <button onclick="window.cerrarEasterEgg()" class="tactile-fx w-full py-4 rounded-[1.2rem] bg-gradient-to-r from-amber-600 to-yellow-500 text-black font-black text-[12px] uppercase tracking-widest shadow-[0_0_20px_rgba(245,158,11,0.4)] flex items-center justify-center gap-2 border border-yellow-300/50">
                <i class="fa-solid fa-power-off"></i> Volver al Sistema
            </button>
        </div>
    </div>
</div>
<?php
// Developed By SeBaS
?>
