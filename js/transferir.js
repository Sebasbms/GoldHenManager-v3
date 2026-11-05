/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: js/transferir.js
 * ====================================================================
 */
const CHUNK_SIZE = 1.5 * 1024 * 1024; 

let isTransferring = false;
let transferAbortController = null;

let colaDeArchivos = [];
let totalArchivosEnCola = 0;
let archivoActualBlob = null;
let archivoActualNombreFinal = "";
let fileStartTime = 0; 
let windowAudioCtx = null;

function formatearTamanoBytes(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function playChidoSound() {
    try {
        if (!windowAudioCtx) { windowAudioCtx = new (window.AudioContext || window.webkitAudioContext)(); }
        if (windowAudioCtx.state === 'suspended') { windowAudioCtx.resume(); }
        const now = windowAudioCtx.currentTime;
        
        const freqs = [523.25, 659.25, 783.99, 1046.50]; 
        freqs.forEach((f, i) => {
            const osc = windowAudioCtx.createOscillator();
            const gain = windowAudioCtx.createGain();
            osc.connect(gain); gain.connect(windowAudioCtx.destination);
            osc.type = 'square';
            osc.frequency.setValueAtTime(f, now + (i * 0.1));
            gain.gain.setValueAtTime(0, now + (i * 0.1));
            gain.gain.linearRampToValueAtTime(0.05, now + (i * 0.1) + 0.05);
            gain.gain.exponentialRampToValueAtTime(0.001, now + (i * 0.1) + 0.5);
            osc.start(now + (i * 0.1));
            osc.stop(now + (i * 0.1) + 0.6);
        });
    } catch(e) {}
}

function dispararConfeti() {
    const container = document.getElementById('confetti-container');
    container.innerHTML = '';
    const colors = ['#00ffff', '#ff00ff', '#39ff14', '#ffff00', '#ff0000', '#10b981'];
    
    for (let i = 0; i < 80; i++) {
        const conf = document.createElement('div');
        conf.className = 'absolute rounded-sm';
        const size = Math.random() * 8 + 4;
        conf.style.width = `${size}px`;
        conf.style.height = `${size * (Math.random() > 0.5 ? 2 : 1)}px`;
        conf.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        
        const isLeft = Math.random() > 0.5;
        conf.style.bottom = '-20px';
        conf.style.left = isLeft ? '20%' : '80%';
        
        const angulo = isLeft ? (Math.random() * (Math.PI/3)) : -(Math.random() * (Math.PI/3)); 
        const velocidad = 15 + Math.random() * 20;
        
        conf.dataset.vx = Math.sin(angulo) * velocidad + (isLeft ? 2 : -2);
        conf.dataset.vy = -Math.cos(angulo) * velocidad - (Math.random() * 10);
        conf.dataset.x = isLeft ? window.innerWidth * 0.2 : window.innerWidth * 0.8;
        conf.dataset.y = window.innerHeight;
        conf.dataset.rot = Math.random() * 360;
        conf.dataset.rotS = (Math.random() - 0.5) * 20;
        
        container.appendChild(conf);
    }
    
    let frame = 0;
    function animar() {
        frame++;
        let actvos = false;
        for(let conf of container.children) {
            let vx = parseFloat(conf.dataset.vx);
            let vy = parseFloat(conf.dataset.vy);
            let x = parseFloat(conf.dataset.x);
            let y = parseFloat(conf.dataset.y);
            let rot = parseFloat(conf.dataset.rot);
            
            vy += 0.4; 
            vx *= 0.98; 
            x += vx;
            y += vy;
            rot += parseFloat(conf.dataset.rotS);
            
            conf.dataset.vy = vy;
            conf.dataset.vx = vx;
            conf.dataset.x = x;
            conf.dataset.y = y;
            conf.dataset.rot = rot;
            
            conf.style.transform = `translate(${x - (conf.dataset.left === '20%' ? window.innerWidth*0.2 : window.innerWidth*0.8)}px, ${y - window.innerHeight}px) rotate(${rot}deg)`;
            if (y < window.innerHeight + 100) actvos = true;
        }
        if(actvos && frame < 300) requestAnimationFrame(animar);
    }
    requestAnimationFrame(animar);
}

function mostrarModalExito() {
    const modal = document.getElementById('modal-exito-anim');
    const content = document.getElementById('modal-content-exito');
    modal.classList.remove('hidden');
    
    playChidoSound();
    
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modal.classList.remove('pointer-events-none');
        content.classList.remove('scale-50');
        content.classList.add('scale-100');
        dispararConfeti();
    }, 50);
}

function cerrarModalExito() {
    const modal = document.getElementById('modal-exito-anim');
    const content = document.getElementById('modal-content-exito');
    modal.classList.remove('opacity-100');
    content.classList.remove('scale-100');
    content.classList.add('scale-50');
    setTimeout(() => {
        modal.classList.add('hidden', 'pointer-events-none');
    }, 500);
    resetTransferUI(true);
}

function prepararArchivoTransferencia(event) {
    colaDeArchivos = Array.from(event.target.files);
    totalArchivosEnCola = colaDeArchivos.length;
    
    if (totalArchivosEnCola === 0) return;

    if(totalArchivosEnCola === 1) {
        document.getElementById('transfer-filename').innerText = colaDeArchivos[0].name;
    } else {
        document.getElementById('transfer-filename').innerText = `${totalArchivosEnCola} Archivos en Cola`;
    }
    document.getElementById('transfer-queue-status').innerText = "Listo para iniciar";

    document.getElementById('transfer-total').innerText = '0 B';
    document.getElementById('transfer-sent').innerText = '0 B';
    document.getElementById('transfer-percent').innerText = '0%';
    document.getElementById('transfer-bar').style.width = '0%';
    document.getElementById('transfer-speed').innerText = '0.00 MB/s';
    document.getElementById('transfer-eta').innerText = '--:--';

    const btn = document.getElementById('btn-iniciar-transferencia');
    btn.disabled = false;
    btn.innerHTML = `<i class="fa-solid fa-rocket"></i> Enviar por FTP`;
    btn.className = "flex-1 py-4 rounded-[1.5rem] bg-gradient-to-r from-cyan-600 to-blue-500 text-white text-[12px] font-black uppercase tracking-widest transition-all active:scale-95 shadow-[0_0_20px_rgba(6,182,212,0.4)] flex items-center justify-center gap-3";
}

function iniciarGestorDeCola() {
    if (colaDeArchivos.length === 0 || isTransferring) return;
    
    isTransferring = true;
    guardarRutaHistorialFTP(document.getElementById('transfer-target-path').value.trim() || '/data/');
    
    const btn = document.getElementById('btn-iniciar-transferencia');
    btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-lg"></i> Transfiriendo...`;
    btn.className = "flex-1 py-4 rounded-[1.5rem] bg-cyan-900/50 text-cyan-500 text-[12px] font-black uppercase tracking-widest flex items-center justify-center gap-3 cursor-not-allowed border border-cyan-500/30";
    
    document.getElementById('btn-abortar-transferencia').classList.remove('hidden');

    procesarSiguienteEnLaCola();
}

function abortarTransferenciaActiva() {
    if (transferAbortController) { transferAbortController.abort(); }
    isTransferring = false;
    
    if (archivoActualBlob && !colaDeArchivos.includes(archivoActualBlob)) {
        colaDeArchivos.unshift(archivoActualBlob);
    }
    
    if(typeof window.sysNotification === 'function') {
        window.sysNotification("PAUSADO", "Transferencia detenida.", "fa-pause");
    }
    
    document.getElementById('transfer-queue-status').innerText = "Transferencia Pausada";
    
    const btn = document.getElementById('btn-iniciar-transferencia');
    btn.innerHTML = `<i class="fa-solid fa-play"></i> REANUDAR ENVÍO`;
    btn.disabled = false;
    btn.className = "flex-1 py-4 rounded-[1.5rem] bg-gradient-to-r from-orange-500 to-red-500 text-white text-[12px] font-black uppercase tracking-widest transition-all active:scale-95 shadow-[0_0_20px_rgba(249,115,22,0.4)] flex items-center justify-center gap-3";
    
    document.getElementById('btn-abortar-transferencia').classList.add('hidden');
}

function procesarSiguienteEnLaCola() {
    if (colaDeArchivos.length === 0) {
        document.getElementById('transfer-queue-status').innerText = "Cola Finalizada";
        document.getElementById('transfer-filename').innerText = "Transferencia Completada";
        mostrarModalExito();
        return;
    }

    archivoActualBlob = colaDeArchivos.shift();
    archivoActualNombreFinal = archivoActualBlob.name;

    const faltantes = totalArchivosEnCola - colaDeArchivos.length;
    document.getElementById('transfer-queue-status').innerText = `Subiendo ${faltantes} de ${totalArchivosEnCola}...`;
    document.getElementById('transfer-filename').innerText = archivoActualNombreFinal;

    verificarColisionConsola(archivoActualNombreFinal);
}

async function verificarColisionConsola(nombreAProbar) {
    const ip = localStorage.getItem('sebas_ip_final_libre');
    if (!ip) { 
        if(typeof window.sysNotification === 'function') window.sysNotification("ERROR", "No hay IP conectada.", "fa-wifi"); 
        abortarTransferenciaActiva(); 
        return; 
    }

    let targetDir = document.getElementById('transfer-target-path').value.trim() || '/data/';
    if (!targetDir.endsWith('/')) targetDir += '/';

    try {
        let fd = new FormData();
        fd.append('action', 'get_file_size');
        fd.append('host_ip', ip);
        fd.append('file_path', targetDir + nombreAProbar);

        let res = await fetch('api/explorador_api.php', { method: 'POST', body: fd });
        let data = await res.json();

        const isResuming = document.getElementById('btn-iniciar-transferencia').innerHTML.includes('REANUDAR');

        if (data.status === 'success' && data.size > 0 && !isResuming) {
            document.getElementById('colision-filename').innerText = nombreAProbar;
            document.getElementById('colision-input-rename').value = nombreAProbar;
            
            const modal = document.getElementById('modal-colision-ftp');
            modal.classList.remove('hidden');
            setTimeout(() => modal.classList.add('opacity-100'), 10);
        } else {
            ejecutarSubidaUnica();
        }
    } catch (e) {
        if(typeof window.sysNotification === 'function') window.sysNotification("ERROR", "Fallo al verificar el archivo.", "fa-bug");
        abortarTransferenciaActiva();
    }
}

function accionColision(accion) {
    const modal = document.getElementById('modal-colision-ftp');
    modal.classList.remove('opacity-100');
    setTimeout(() => modal.classList.add('hidden'), 300);

    if (accion === 'omitir') {
        if(typeof window.sysNotification === 'function') window.sysNotification("OMITIDO", `Saltando ${archivoActualNombreFinal}...`, "fa-step-forward");
        procesarSiguienteEnLaCola();
    } 
    else if (accion === 'reemplazar') {
        ejecutarSubidaUnica(); 
    } 
    else if (accion === 'renombrar') {
        const nuevoNombre = document.getElementById('colision-input-rename').value.trim();
        if(nuevoNombre === "") {
            if(typeof window.sysNotification === 'function') window.sysNotification("ERROR", "Debes escribir un nombre válido.", "fa-times");
            procesarSiguienteEnLaCola(); return;
        }
        archivoActualNombreFinal = nuevoNombre;
        document.getElementById('transfer-filename').innerText = archivoActualNombreFinal;
        verificarColisionConsola(archivoActualNombreFinal);
    }
}

async function ejecutarSubidaUnica() {
    const ip = localStorage.getItem('sebas_ip_final_libre');
    let targetDir = document.getElementById('transfer-target-path').value.trim() || '/data/';
    if (!targetDir.endsWith('/')) targetDir += '/';

    transferAbortController = new AbortController();

    let offsetBytes = 0;

    let fdSize = new FormData();
    fdSize.append('action', 'get_file_size');
    fdSize.append('host_ip', ip);
    fdSize.append('file_path', targetDir + archivoActualNombreFinal);
    try {
        let resSize = await fetch('api/explorador_api.php', { method: 'POST', body: fdSize });
        let dataSize = await resSize.json();
        if (dataSize.status === 'success' && dataSize.size > 0 && dataSize.size < archivoActualBlob.size) {
            offsetBytes = dataSize.size;
            if(typeof window.sysNotification === 'function') window.sysNotification("REANUDANDO", `Desde ${formatearTamanoBytes(offsetBytes)}`, "fa-play");
        } else if (dataSize.size >= archivoActualBlob.size) {
            procesarSiguienteEnLaCola(); 
            return;
        }
    } catch(e) {}

    let bytesSent = offsetBytes;
    fileStartTime = Date.now() - (offsetBytes > 0 ? 1000 : 0); 
    document.getElementById('transfer-total').innerText = formatearTamanoBytes(archivoActualBlob.size);

    let chunkIndex = (offsetBytes > 0) ? 1 : 0; 

    while (offsetBytes < archivoActualBlob.size) {
        if (transferAbortController.signal.aborted) { return; }

        let start = offsetBytes;
        let end = Math.min(start + CHUNK_SIZE, archivoActualBlob.size);
        let chunkBlob = archivoActualBlob.slice(start, end);

        let fd = new FormData();
        fd.append('action', 'upload_explorer_chunk');
        fd.append('host_ip', ip);
        fd.append('chunk_index', chunkIndex); 
        fd.append('filename', archivoActualNombreFinal); 
        fd.append('target_dir', targetDir);
        fd.append('file_chunk', chunkBlob, archivoActualNombreFinal);

        try {
            let res = await fetch('api/explorador_api.php', { method: 'POST', body: fd, signal: transferAbortController.signal });
            let data = await res.json();
            
            if (data.status === 'success') {
                offsetBytes += (end - start);
                bytesSent = offsetBytes;
                actualizarMetricas(bytesSent, archivoActualBlob.size);
                chunkIndex++;
            } else { throw new Error(data.message); }
        } catch (err) {
            if (err.name !== 'AbortError') {
                if(typeof window.sysNotification === 'function') window.sysNotification("ERROR FTP", "Desconexión detectada. Pausando...", "fa-exclamation-triangle");
                abortarTransferenciaActiva(); 
            }
            return;
        }
    }
    procesarSiguienteEnLaCola();
}

function actualizarMetricas(bytesSent, totalBytes) {
    const percent = ((bytesSent / totalBytes) * 100).toFixed(1);
    const timeElapsedSec = (Date.now() - fileStartTime) / 1000;
    const speedBytesPerSec = timeElapsedSec > 0 ? (bytesSent / timeElapsedSec) : 0;
    
    const bytesRemaining = totalBytes - bytesSent;
    const secondsRemaining = speedBytesPerSec > 0 ? Math.round(bytesRemaining / speedBytesPerSec) : 0;
    
    let etaString = "--:--";
    if (secondsRemaining > 0 && isFinite(secondsRemaining)) {
        const m = Math.floor(secondsRemaining / 60).toString().padStart(2, '0');
        const s = (secondsRemaining % 60).toString().padStart(2, '0');
        etaString = `${m}:${s}`;
    }

    document.getElementById('transfer-percent').innerText = `${percent}%`;
    document.getElementById('transfer-bar').style.width = `${percent}%`;
    document.getElementById('transfer-sent').innerText = formatearTamanoBytes(bytesSent);
    document.getElementById('transfer-speed').innerText = `${(speedBytesPerSec / (1024*1024)).toFixed(2)} MB/s`;
    document.getElementById('transfer-eta').innerText = etaString;
}

function resetTransferUI(success = false) {
    isTransferring = false;
    document.getElementById('input-archivo-pesado').value = '';

    const btn = document.getElementById('btn-iniciar-transferencia');
    const btnAbort = document.getElementById('btn-abortar-transferencia');
    
    btnAbort.classList.add('hidden'); 

    if (success) {
        btn.innerHTML = `<i class="fa-solid fa-check"></i> Transferencia Completa`;
        btn.className = "flex-1 py-4 rounded-[1.5rem] bg-emerald-600/20 text-emerald-400 border border-emerald-500/50 text-[12px] font-black uppercase tracking-widest flex items-center justify-center gap-3 cursor-not-allowed";
    } else if(!colaDeArchivos.length) {
        btn.innerHTML = `<i class="fa-solid fa-rocket"></i> Enviar por FTP`;
        btn.disabled = true;
        btn.className = "flex-1 py-4 rounded-[1.5rem] bg-gray-800 text-gray-500 text-[12px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-3 pointer-events-none";
    }
}

// ============================================
// EXPLORADOR Y CREACIÓN DE CARPETAS
// ============================================

window.exploradorRutaActual = "/";

function abrirExploradorFTP() {
    var ps4Ip = localStorage.getItem('sebas_ip_final_libre');
    if (!ps4Ip) { 
        if(typeof window.sysNotification === 'function') window.sysNotification("ERROR", "No hay conexión FTP.", "fa-wifi"); 
        return; 
    }

    window.exploradorRutaActual = document.getElementById('transfer-target-path').value.trim() || '/'; 
    if(!window.exploradorRutaActual.endsWith('/')) window.exploradorRutaActual += '/';
    
    var modal = document.getElementById('modal-explorador-ftp');
    if (modal) {
        modal.classList.remove('hidden');
        setTimeout(function() {
            modal.classList.add('opacity-100');
            var content = document.getElementById('modal-content-explorador');
            if (content) content.classList.remove('translate-y-full');
        }, 10);
    }
    cargarCarpetasFTP(window.exploradorRutaActual);
}

function cerrarExploradorFTP() {
    var modal = document.getElementById('modal-explorador-ftp');
    var content = document.getElementById('modal-content-explorador');
    if (content) content.classList.add('translate-y-full');
    if (modal) {
        modal.classList.remove('opacity-100');
        setTimeout(function() { modal.classList.add('hidden'); }, 300);
    }
}

async function cargarCarpetasFTP(ruta) {
    var ip = localStorage.getItem('sebas_ip_final_libre');
    var lista = document.getElementById('explorador-lista');
    if (!lista) return;

    var lblRuta = document.getElementById('explorador-ruta-actual');
    if (lblRuta) lblRuta.innerText = ruta;

    lista.innerHTML = '<div class="w-full py-10 text-center text-cyan-500 opacity-50"><i class="fa-solid fa-spinner fa-spin text-3xl mb-3"></i><br><span class="text-[10px] font-bold uppercase tracking-widest">Conectando...</span></div>';

    try {
        var fd = new FormData();
        fd.append('action', 'listar_directorio');
        fd.append('host_ip', ip);
        fd.append('path', ruta);

        var res = await fetch('api/explorador_api.php', { method: 'POST', body: fd });
        var data = await res.json();

        if (data.status === 'success') {
            lista.innerHTML = '';
            
            if (ruta !== '/' && ruta !== '') {
                var btnBack = document.createElement('div');
                btnBack.className = "flex items-center gap-3 p-3 bg-cyan-500/10 rounded-xl cursor-pointer hover:bg-cyan-500/20 active:scale-95 transition-all border border-cyan-500/20 shrink-0";
                btnBack.onclick = function() { subirNivelCarpetaFTP(); };
                btnBack.innerHTML = '<div class="w-8 h-8 rounded-lg bg-black/40 flex items-center justify-center text-cyan-400"><i class="fa-solid fa-level-up-alt"></i></div> <span class="text-[11px] font-black text-cyan-400 uppercase tracking-widest">NIVEL ANTERIOR</span>';
                lista.appendChild(btnBack);
            }

            if (!data.data.carpetas || data.data.carpetas.length === 0) {
                var divVacia = document.createElement('div');
                divVacia.className = "text-center py-10 opacity-50";
                divVacia.innerHTML = '<i class="fa-solid fa-folder-open text-3xl text-gray-500 mb-3"></i><br><span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Carpeta Vacía</span>';
                lista.appendChild(divVacia);
            } else {
                data.data.carpetas.forEach(function(carpetaObj) {
                    var dir = carpetaObj.name;
                    var div = document.createElement('div');
                    div.className = "flex items-center gap-3 p-3 bg-black/30 rounded-xl cursor-pointer hover:border-cyan-500/50 hover:bg-cyan-500/5 active:scale-95 transition-all border border-white/5 shrink-0";
                    div.onclick = function() { navegarAdentroCarpetaFTP(dir); };
                    div.innerHTML = '<i class="fa-solid fa-folder text-gray-500 text-lg group-hover:text-cyan-500 transition-colors"></i> <span class="text-[11px] font-bold text-gray-300 uppercase truncate">' + dir + '</span>';
                    lista.appendChild(div);
                });
            }
        } else {
            lista.innerHTML = '<div class="text-center py-10"><span class="text-red-400 text-[10px] font-bold uppercase tracking-widest">Error al leer consola</span></div>';
        }
    } catch (e) {
        lista.innerHTML = '<div class="text-center py-10"><span class="text-red-400 text-[10px] font-bold uppercase tracking-widest">Se perdió conexión</span></div>';
    }
}

function navegarAdentroCarpetaFTP(carpeta) {
    window.exploradorRutaActual += carpeta + '/';
    cargarCarpetasFTP(window.exploradorRutaActual);
}

function subirNivelCarpetaFTP() {
    var partes = window.exploradorRutaActual.split('/');
    var partesLimpias = [];
    
    for(var i = 0; i < partes.length; i++) {
        if(partes[i] !== '') {
            partesLimpias.push(partes[i]);
        }
    }
    
    partesLimpias.pop(); 
    
    if (partesLimpias.length === 0) {
        window.exploradorRutaActual = '/'; 
    } else {
        window.exploradorRutaActual = '/' + partesLimpias.join('/') + '/';
    }
    
    cargarCarpetasFTP(window.exploradorRutaActual);
}

function confirmarRutaFTP() {
    var inputRuta = document.getElementById('transfer-target-path');
    if (inputRuta) inputRuta.value = window.exploradorRutaActual;
    cerrarExploradorFTP();
    if(typeof window.sysNotification === 'function') window.sysNotification("SISTEMA", "Ruta de destino fijada.", "fa-crosshairs");
}

function preguntarNuevaCarpeta() {
    const modal = document.getElementById('modal-nueva-carpeta');
    if(modal) {
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.add('opacity-100'); }, 10);
        document.getElementById('input-nueva-carpeta').focus();
    }
}

function cerrarNuevaCarpeta() {
    const modal = document.getElementById('modal-nueva-carpeta');
    if(modal) {
        modal.classList.remove('opacity-100');
        setTimeout(() => { modal.classList.add('hidden'); document.getElementById('input-nueva-carpeta').value = ''; }, 300);
    }
}

async function ejecutarCrearCarpeta() {
    const ip = localStorage.getItem('sebas_ip_final_libre');
    const folderName = document.getElementById('input-nueva-carpeta').value.trim();
    if(!folderName) return;

    cerrarNuevaCarpeta();

    const fd = new FormData();
    fd.append('action', 'crear_carpeta');
    fd.append('host_ip', ip);
    fd.append('target', window.exploradorRutaActual + folderName);

    try {
        const res = await fetch('api/explorador_api.php', { method: 'POST', body: fd });
        const data = await res.json();
        if(data.status === 'success') {
            if(typeof window.sysNotification === 'function') window.sysNotification("ÉXITO", "Carpeta creada.", "fa-folder-plus");
            cargarCarpetasFTP(window.exploradorRutaActual);
        } else {
            if(typeof window.sysNotification === 'function') window.sysNotification("ERROR", "No se pudo crear.", "fa-times");
        }
    } catch(e) {}
}

// ============================================
// HISTORIAL DE RUTAS
// ============================================

function guardarRutaHistorialFTP(ruta) {
    if (!ruta || ruta === '/data/') return;
    let hist = JSON.parse(localStorage.getItem('ps4_ruta_history') || '[]');
    hist = hist.filter(h => h !== ruta); 
    hist.unshift(ruta); 
    if (hist.length > 10) hist.pop(); 
    localStorage.setItem('ps4_ruta_history', JSON.stringify(hist));
}

function abrirHistorialRutasFTP() {
    const modal = document.getElementById('modal-history-rutas');
    const content = document.getElementById('modal-content-history-rutas');
    const lista = document.getElementById('history-rutas-list');
    
    let hist = JSON.parse(localStorage.getItem('ps4_ruta_history') || '[]');
    
    if (hist.length === 0) {
        lista.innerHTML = '<div class="text-center py-6 text-gray-500 text-[10px] font-mono">No hay rutas guardadas aún.</div>';
    } else {
        lista.innerHTML = hist.map(r => `
            <div class="flex items-center gap-3 p-3 bg-purple-900/10 rounded-xl border border-purple-500/20 mb-2 group">
                <div class="flex-1 flex items-center gap-3 cursor-pointer active:scale-95 transition-all" onclick="seleccionarRutaFTP('${r}')">
                    <i class="fa-solid fa-folder text-purple-400 shrink-0"></i>
                    <span class="text-[11px] text-purple-200 truncate w-full" style="direction: rtl; text-align: left;">${r}</span>
                </div>
                <button onclick="borrarRutaIndividualFTP('${r}')" class="w-8 h-8 rounded-full bg-red-500/10 text-red-500 border border-red-500/20 flex items-center justify-center shrink-0 active:scale-90 hover:bg-red-500/20 transition-all">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        `).join('');
    }

    modal.classList.remove('hidden', 'pointer-events-none');
    setTimeout(() => {
        modal.classList.add('opacity-100');
        content.classList.remove('translate-y-full', 'scale-95');
    }, 10);
}

function borrarRutaIndividualFTP(rutaABorrar) {
    let hist = JSON.parse(localStorage.getItem('ps4_ruta_history') || '[]');
    hist = hist.filter(h => h !== rutaABorrar);
    localStorage.setItem('ps4_ruta_history', JSON.stringify(hist));
    abrirHistorialRutasFTP(); 
    if(typeof window.sysNotification === 'function') window.sysNotification("SISTEMA", "Ruta eliminada del historial", "fa-trash");
}

function cerrarHistorialRutas() {
    const modal = document.getElementById('modal-history-rutas');
    const content = document.getElementById('modal-content-history-rutas');
    content.classList.add('translate-y-full', 'scale-95');
    modal.classList.remove('opacity-100');
    setTimeout(() => modal.classList.add('hidden', 'pointer-events-none'), 300);
}

function seleccionarRutaFTP(ruta) {
    document.getElementById('transfer-target-path').value = ruta;
    cerrarHistorialRutas();
}

function limpiarHistorialRutas() {
    localStorage.removeItem('ps4_ruta_history');
    abrirHistorialRutasFTP();
}
