/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: js/explorador.js
 * ====================================================================
 */
let rutaActualExplorador = "/";
let contenidoActual = [];
let clipboardFtp = { accion: null, items: [], origen: null };
let controllerSubidaExplorador = null;
let seleccionMultipleExplorador = new Set();
let modoSeleccionActivo = false;
let isGridMode = false;
let itemContextualActual = null;
let deleteConfirmStage = 0;

const shortcutsPredeterminados = [
    { nombre: "Raíz (/)", ruta: "/", icon: "fa-hard-drive", color: "blue", locked: true },
    { nombre: "Montajes", ruta: "/mnt", icon: "fa-server", color: "cyan", locked: true },
    { nombre: "USB", ruta: "/mnt/usb0", icon: "fa-plug", color: "emerald", locked: true },
    { nombre: "GoldHEN", ruta: "/data/GoldHEN", icon: "fa-crown", color: "amber", locked: true },
    { nombre: "PS4 Data", ruta: "/user/app", icon: "fa-gamepad", color: "purple", locked: true }
];

document.addEventListener('DOMContentLoaded', () => {
    cargarRutaFtp('/');
    let saved = JSON.parse(localStorage.getItem('sebas_ftp_shortcuts'));
    if (!saved || !saved.find(s => s.ruta === '/mnt')) {
        saved = shortcutsPredeterminados;
        localStorage.setItem('sebas_ftp_shortcuts', JSON.stringify(saved));
    }
    renderizarAccesosRapidos();
});

function navegarArribaOLauncher() {
    if(window.playSynth) window.playSynth('select');
    if (rutaActualExplorador === "/" || rutaActualExplorador === "") {
        window.volverAlLauncher();
    } else {
        navegarArribaFtp();
    }
}

function toggleExploradorViewMode() {
    if(window.playSynth) window.playSynth('select');
    const cont = document.getElementById('ftp-list-container');
    const btn = document.getElementById('btn-view-mode');
    const headers = document.getElementById('list-headers');
    if(isGridMode) {
        cont.classList.remove('view-grid');
        cont.classList.add('view-list');
        btn.innerHTML = '<i class="fa-solid fa-border-all text-lg"></i>';
        if(headers) headers.style.display = 'flex';
        isGridMode = false;
    } else {
        cont.classList.remove('view-list');
        cont.classList.add('view-grid');
        btn.innerHTML = '<i class="fa-solid fa-list text-lg"></i>';
        if(headers) headers.style.display = 'none';
        isGridMode = true;
    }
}

async function cargarRutaFtp(ruta, ignoreCache = false) {
    const cont = document.getElementById('ftp-list-container');
    if (!window.globalAppConfig || !window.globalAppConfig.statusConexion) {
        if(cont) cont.innerHTML = `<div class="absolute inset-0 flex flex-col items-center justify-center opacity-50 z-50"><i class="fa-solid fa-unlink text-5xl mb-4 text-red-500"></i><p class="text-[12px] font-mono tracking-widest uppercase text-red-400">CONSOLA DESCONECTADA</p></div>`;
        return;
    }
    rutaActualExplorador = ruta.replace(/\/+/g, '/');
    if (rutaActualExplorador === "") rutaActualExplorador = "/";

    const loader = document.getElementById('loader-explorador');
    if(loader) { loader.classList.remove('hidden'); loader.classList.add('inline-block'); }
    if(cont) cont.innerHTML = `<div class="absolute inset-0 flex flex-col items-center justify-center opacity-80 z-50"><i class="fa-solid fa-circle-notch fa-spin text-5xl mb-4 text-cyan-500"></i><p class="text-[12px] font-mono tracking-widest uppercase text-cyan-400">CARGANDO...</p></div>`;
    
    renderizarRutaBar();
    limpiarSeleccionMultiple();

    try {
        let fd = new FormData();
        fd.append('action', 'listar_directorio');
        fd.append('path', rutaActualExplorador);
        fd.append('host_ip', window.globalAppConfig.ipConsola);

        let res = await fetch('api/explorador_api.php', { method: 'POST', body: fd });
        let data = await res.json();
        
        if (data.status === 'success') {
            let items = [];
            if(data.data.carpetas) data.data.carpetas.forEach(c => items.push({name: c.name, size: c.size, date: c.date, type: 'dir'}));
            if(data.data.archivos) data.data.archivos.forEach(a => items.push({name: a.name, size: a.size, date: a.date, type: 'file'}));
            contenidoActual = items;
            renderizarListaFtp();
        } else {
            if(window.sysNotification) window.sysNotification("ERROR FTP", data.message, "fa-exclamation-triangle");
            if(rutaActualExplorador !== "/") { navegarArribaFtp(); }
            else {
                if(cont) cont.innerHTML = `<div class="absolute inset-0 flex flex-col items-center justify-center opacity-50 z-50"><i class="fa-solid fa-folder-open text-5xl mb-4"></i><p class="text-[12px] font-mono tracking-widest uppercase">Carpeta Vacía</p></div>`;
            }
        }
    } catch (e) {
        if(window.sysNotification) window.sysNotification("ERROR", "Fallo de conexión.", "fa-wifi");
        if(cont) cont.innerHTML = `<div class="absolute inset-0 flex flex-col items-center justify-center opacity-50 z-50"><i class="fa-solid fa-unlink text-5xl mb-4 text-red-500"></i><p class="text-[12px] font-mono tracking-widest uppercase text-red-400">ERROR DE RED</p></div>`;
    } finally {
        if(loader) { loader.classList.add('hidden'); loader.classList.remove('inline-block'); }
    }
}

function renderizarRutaBar() {
    const cont = document.getElementById('ftp-ruta-actual');
    if (!cont) return;
    cont.innerHTML = '';
    
    if (rutaActualExplorador === '/') {
        cont.innerHTML += `<span class="text-cyan-500 font-black text-xs shrink-0 pl-2">/ ROOT</span>`;
        return;
    }

    let partes = rutaActualExplorador.split('/').filter(p => p.trim() !== '');
    let rutaAcumulada = '';
    
    cont.innerHTML += `<span class="text-cyan-500 font-black text-xs shrink-0 pl-2">/</span>`;
    
    partes.forEach((p, idx) => {
        rutaAcumulada += '/' + p;
        if (idx === partes.length - 1) {
            cont.innerHTML += `<span class="text-white font-black text-xs shrink-0 px-2 py-0.5 bg-white/10 rounded-md truncate max-w-[100px]">${p}</span>`;
        } else {
            cont.innerHTML += `<span class="text-gray-400 text-[10px] shrink-0">›</span><span class="text-cyan-500 font-bold text-xs shrink-0 cursor-pointer hover:underline" onclick="cargarRutaFtp('${rutaAcumulada}')">${p}</span>`;
        }
    });
}

function navegarArribaFtp() {
    if(window.playSynth) window.playSynth('select');
    if (rutaActualExplorador === "/") return;
    let partes = rutaActualExplorador.split('/').filter(p => p.trim() !== '');
    partes.pop();
    let parent = "/" + partes.join('/');
    cargarRutaFtp(parent);
}

function formatearTamano(bytes) {
    if (bytes === 0 || bytes === "-" || bytes === undefined) return "";
    const k = 1024; const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function getIconData(nombre, type) {
    if (type === 'dir') return { clase: 'icon-dir', icon: 'fa-folder' };
    const ext = nombre.split('.').pop().toLowerCase();
    const imgs = ['jpg','jpeg','png','gif','webp'];
    const pkgs = ['pkg','bin','iso'];
    const txts = ['txt','json','ini','xml','log'];
    if (imgs.includes(ext)) return { clase: 'icon-img', icon: 'fa-image' };
    if (pkgs.includes(ext)) return { clase: 'icon-pkg', icon: 'fa-box-open' };
    if (txts.includes(ext)) return { clase: 'icon-txt', icon: 'fa-file-code' };
    return { clase: 'icon-default', icon: 'fa-file' };
}

function renderizarListaFtp(filtroTexto = "") {
    const cont = document.getElementById('ftp-list-container');
    if (!cont) return;
    cont.innerHTML = '';
    let arr = contenidoActual;
    if (filtroTexto !== "") {
        let t = filtroTexto.toLowerCase();
        arr = arr.filter(f => f.name.toLowerCase().includes(t));
    }
    arr.sort((a, b) => {
        if (a.type === 'dir' && b.type !== 'dir') return -1;
        if (a.type !== 'dir' && b.type === 'dir') return 1;
        return a.name.localeCompare(b.name);
    });

    const fragment = document.createDocumentFragment();

    if (rutaActualExplorador !== '/' && rutaActualExplorador !== '') {
        const backCard = document.createElement('div');
        backCard.className = `file-card tactile-fx border-dashed border-cyan-500/30 bg-cyan-900/10`;
        backCard.onclick = navegarArribaFtp;
        backCard.innerHTML = `
            <div class="flex items-center gap-3.5 flex-1 min-w-0 w-full h-full relative z-10 cursor-pointer">
                <div class="file-icon-wrap text-cyan-400 bg-transparent w-11 h-11 rounded-xl flex items-center justify-center shrink-0 pointer-events-none">
                    <i class="fa-solid fa-arrow-turn-up text-3xl"></i>
                </div>
                <div class="file-info pointer-events-none">
                    <span class="file-title text-[13px] font-bold text-cyan-400 truncate w-full">.. Volver Atrás</span>
                    <span class="file-date text-[9px] font-mono text-cyan-600 mt-0.5 uppercase">Carpeta Anterior</span>
                </div>
            </div>
        `;
        fragment.appendChild(backCard);
    }

    if (arr.length === 0 && (rutaActualExplorador === '/' || rutaActualExplorador === '')) {
        cont.innerHTML = `<div class="absolute inset-0 flex flex-col items-center justify-center opacity-50 z-50"><i class="fa-solid fa-folder-open text-5xl mb-4"></i><p class="text-[12px] font-mono tracking-widest uppercase">Carpeta Vacía</p></div>`;
        return;
    } else if (arr.length === 0) {
        const emptyMsg = document.createElement('div');
        emptyMsg.className = "absolute inset-0 flex flex-col items-center justify-center opacity-50 z-50";
        emptyMsg.innerHTML = `<i class="fa-solid fa-folder-open text-5xl mb-4"></i><p class="text-[12px] font-mono tracking-widest uppercase">Carpeta Vacía</p>`;
        fragment.appendChild(emptyMsg);
    }

    arr.forEach(item => {
        const isDir = item.type === 'dir';
        const ic = getIconData(item.name, item.type);
        const isSelected = seleccionMultipleExplorador.has(item.name);
        let fechaCortada = item.date ? item.date.substring(0,10) : "";
        let sizeText = isDir ? "DIR" : formatearTamano(item.size);
        const card = document.createElement('div');
        card.className = `file-card tactile-fx ${isSelected ? 'selected' : ''}`;
        card.innerHTML = `
            <div class="flex items-center gap-3.5 flex-1 min-w-0 w-full h-full relative z-10 cursor-pointer" onclick="handleFileClick(event, '${item.name}', '${item.type}')">
                <div class="file-icon-wrap ${ic.clase} w-11 h-11 rounded-xl flex items-center justify-center shrink-0 pointer-events-auto" onclick="handleIconClick(event, '${item.name}')">
                    <i class="fa-solid ${ic.icon} text-xl drop-shadow-md pointer-events-none"></i>
                </div>
                <div class="file-info pointer-events-none">
                    <span class="file-title text-[13px] font-bold text-gray-200 truncate w-full">${item.name}</span>
                    <span class="file-date text-[9px] font-mono text-gray-500 mt-0.5 uppercase">${fechaCortada}</span>
                </div>
            </div>
            <div class="file-meta pointer-events-none z-10">
                ${isDir ? `<span class="text-[10px] font-black text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20 uppercase tracking-widest">DIR</span>` : `<span class="text-[10px] font-mono font-black text-cyan-400 shrink-0">${sizeText}</span>`}
            </div>
            <button onclick="abrirContextoExplorador(event, '${item.name}', '${item.type}', ${item.size})" class="ctx-btn tactile-fx p-2 text-gray-400 hover:text-white z-20">
                <i class="fa-solid fa-ellipsis-vertical pointer-events-none"></i>
            </button>
            <div class="select-check absolute left-2 top-1/2 -translate-y-1/2 w-5 h-5 rounded-full border-2 border-cyan-500/50 flex items-center justify-center bg-black/50 z-20">
                <i class="fa-solid fa-check text-[10px] ${isSelected ? 'text-cyan-400' : 'text-transparent'}"></i>
            </div>
        `;
        fragment.appendChild(card);
    });

    const spacer = document.createElement('div');
    spacer.className = isGridMode ? "col-span-4 h-24 w-full shrink-0 pointer-events-none" : "h-24 w-full shrink-0 pointer-events-none";
    fragment.appendChild(spacer);

    cont.appendChild(fragment);
}

function filtrarExploradorActivo() {
    let t = document.getElementById('buscador-explorador').value;
    renderizarListaFtp(t);
}

function handleFileClick(e, nombre, type) {
    if (modoSeleccionActivo) {
        toggleItemSeleccion(nombre);
        return;
    }
    if(window.playSynth) window.playSynth('select');
    if (type === 'dir') {
        cargarRutaFtp(rutaActualExplorador + (rutaActualExplorador.endsWith('/') ? '' : '/') + nombre);
    } else {
        abrirContextoExplorador(e, nombre, type, 0);
    }
}

function handleIconClick(e, nombre) {
    e.stopPropagation();
    if(window.playSynth) window.playSynth('tap');
    if (!modoSeleccionActivo) { activarModoSeleccion(); }
    toggleItemSeleccion(nombre);
}

function activarModoSeleccion() {
    modoSeleccionActivo = true;
    document.getElementById('ftp-list-container').classList.add('mode-select');
    const header = document.getElementById('header-multi-select');
    header.classList.remove('opacity-0', 'translate-x-10', 'scale-95', 'pointer-events-none');
    header.classList.add('opacity-100', 'translate-x-0', 'scale-100', 'pointer-events-auto');
}

function cancelarSeleccionMultiple() {
    if(window.playSynth) window.playSynth('select');
    modoSeleccionActivo = false;
    seleccionMultipleExplorador.clear();
    document.getElementById('ftp-list-container').classList.remove('mode-select');
    const header = document.getElementById('header-multi-select');
    header.classList.remove('opacity-100', 'translate-x-0', 'scale-100', 'pointer-events-auto');
    header.classList.add('opacity-0', 'translate-x-10', 'scale-95', 'pointer-events-none');
    renderizarListaFtp(document.getElementById('buscador-explorador').value);
}

function limpiarSeleccionMultiple() {
    modoSeleccionActivo = false;
    seleccionMultipleExplorador.clear();
    const cont = document.getElementById('ftp-list-container');
    if(cont) cont.classList.remove('mode-select');
    const header = document.getElementById('header-multi-select');
    if(header) {
        header.classList.remove('opacity-100', 'translate-x-0', 'scale-100', 'pointer-events-auto');
        header.classList.add('opacity-0', 'translate-x-10', 'scale-95', 'pointer-events-none');
    }
}

function toggleItemSeleccion(nombre) {
    if (seleccionMultipleExplorador.has(nombre)) { seleccionMultipleExplorador.delete(nombre); } 
    else { seleccionMultipleExplorador.add(nombre); }
    document.getElementById('txt-multi-count').innerText = `${seleccionMultipleExplorador.size} Archivos`;
    renderizarListaFtp(document.getElementById('buscador-explorador').value);
    if (seleccionMultipleExplorador.size === 0) { cancelarSeleccionMultiple(); }
}

function abrirContextoExplorador(e, name, type, sizeBytes) {
    if(e) e.stopPropagation();
    if(window.playSynth) window.playSynth('select');
    itemContextualActual = { name: name, type: type, size: sizeBytes };
    const modal = document.getElementById('modal-ctx-explorador');
    const content = document.getElementById('modal-ctx-content');
    document.getElementById('ctx-filename').innerText = name;
    const iconBox = document.getElementById('ctx-icon-display');
    const btnBajar = document.getElementById('ctx-btn-bajar');
    let ic = getIconData(name, type);
    iconBox.className = `w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0 ${ic.clase}`;
    iconBox.innerHTML = `<i class="fa-solid ${ic.icon}"></i>`;
    if(type === 'dir') {
        document.getElementById('ctx-filetype').innerText = "Directorio de Sistema";
        btnBajar.classList.add('hidden');
    } else {
        document.getElementById('ctx-filetype').innerText = formatearTamano(sizeBytes) + " • Archivo Binario";
        btnBajar.classList.remove('hidden');
    }
    modal.classList.remove('hidden'); modal.classList.add('flex');
    setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('translate-y-full'); }, 10);
}

function cerrarContextoExplorador() {
    const modal = document.getElementById('modal-ctx-explorador');
    const content = document.getElementById('modal-ctx-content');
    if(!modal) return;
    content.classList.add('translate-y-full'); modal.classList.add('opacity-0');
    setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); }, 300);
}

function accionDirectaContexto(accion) {
    cerrarContextoExplorador();
    if(!itemContextualActual) return;
    let obj = itemContextualActual;
    if (accion === 'eliminar') {
        seleccionMultipleExplorador.clear();
        seleccionMultipleExplorador.add(obj.name);
        ejecutarAccionMultiple('eliminar');
    } else if (accion === 'copiar' || accion === 'cortar' || accion === 'duplicar') {
        seleccionMultipleExplorador.clear();
        seleccionMultipleExplorador.add(obj.name);
        ejecutarAccionMultiple(accion);
    } else if (accion === 'renombrar') {
        promptRenombrar(obj.name);
    } else if (accion === 'descargar') {
        const urlDescarga = `api/explorador_api.php?action=descargar_directo&host_ip=${window.globalAppConfig.ipConsola}&path=${encodeURIComponent(rutaActualExplorador + (rutaActualExplorador.endsWith('/') ? '' : '/') + obj.name)}`;
        window.location.href = urlDescarga;
        if(window.sysNotification) window.sysNotification("DESCARGANDO", "Iniciando transferencia al dispositivo...", "fa-download");
    }
}

function ejecutarAccionMultiple(accion) {
    if (seleccionMultipleExplorador.size === 0) return;
    if(window.playSynth) window.playSynth('select');
    let items = Array.from(seleccionMultipleExplorador);
    if (accion === 'copiar' || accion === 'cortar') {
        clipboardFtp = { accion: accion, items: items, origen: rutaActualExplorador };
        document.getElementById('clipboard-count').innerText = `${items.length} items`;
        document.getElementById('clipboard-bar').classList.remove('opacity-0', 'translate-x-10', 'scale-95', 'pointer-events-none');
        document.getElementById('clipboard-bar').classList.add('opacity-100', 'translate-x-0', 'scale-100', 'pointer-events-auto');
        cancelarSeleccionMultiple();
    } 
    else if (accion === 'duplicar') {
        if(items.length > 1) { if(window.sysNotification) window.sysNotification("ERROR", "Duplica de a uno.", "fa-xmark"); return; }
        promptDuplicar(items[0]);
    }
    else if (accion === 'eliminar') {
        deleteConfirmStage = 0;
        document.getElementById('del-modal-desc').innerText = items.length === 1 ? `¿Borrar "${items[0]}"?` : `¿Borrar ${items.length} elementos?`;
        document.getElementById('del-modal-title').innerText = "1º ADVERTENCIA";
        document.getElementById('btn-real-delete').innerText = "SÍ, ELIMINAR";
        document.getElementById('btn-real-delete').className = "w-full py-4 rounded-xl bg-red-600 text-[11px] font-black tracking-widest uppercase text-white active:scale-95 transition-all shadow-[0_0_15px_rgba(239,68,68,0.4)]";
        
        let btnReal = document.getElementById('btn-real-delete');
        btnReal.onclick = async () => {
            if (deleteConfirmStage === 0) {
                deleteConfirmStage = 1;
                document.getElementById('del-modal-title').innerText = "2º ADVERTENCIA (IRREVERSIBLE)";
                document.getElementById('del-modal-desc').innerText = "Esta acción borrará los archivos de la consola de forma permanente.";
                document.getElementById('btn-real-delete').innerText = "CONFIRMAR ELIMINACIÓN";
                document.getElementById('btn-real-delete').className = "w-full py-4 rounded-xl bg-red-900 border border-red-500 text-[11px] font-black tracking-widest uppercase text-white active:scale-95 transition-all shadow-[0_0_30px_rgba(239,68,68,0.8)] animate-pulse";
                if(window.playSynth) window.playSynth('brutal');
                return;
            }

            if(window.playSynth) window.playSynth('brutal');
            cerrarDeleteConfirm();
            if(window.sysNotification) window.sysNotification("ELIMINANDO", "Borrando archivos...", "fa-trash");
            for (let name of items) {
                let p = (rutaActualExplorador.endsWith('/') ? rutaActualExplorador : rutaActualExplorador + '/') + name;
                let itemObj = contenidoActual.find(x => x.name === name);
                let isDir = itemObj && itemObj.type === 'dir';
                let fd = new FormData();
                fd.append('action', 'eliminar'); 
                fd.append('target', p);
                fd.append('is_dir', isDir ? 'true' : 'false');
                fd.append('host_ip', window.globalAppConfig.ipConsola);
                try { await fetch('api/explorador_api.php', { method: 'POST', body: fd }); } catch(e) {}
            }
            if(window.sysNotification) window.sysNotification("ÉXITO", "Archivos eliminados.", "fa-check");
            cancelarSeleccionMultiple();
            cargarRutaFtp(rutaActualExplorador);
        };
        abrirDeleteConfirm();
    }
}

function cancelarPegado() {
    clipboardFtp = { accion: null, items: [], origen: null };
    document.getElementById('clipboard-bar').classList.remove('opacity-100', 'translate-x-0', 'scale-100', 'pointer-events-auto');
    document.getElementById('clipboard-bar').classList.add('opacity-0', 'translate-x-10', 'scale-95', 'pointer-events-none');
    if(window.playSynth) window.playSynth('select');
}

async function ejecutarPegado() {
    if (!clipboardFtp.accion || clipboardFtp.items.length === 0) return;
    if(window.playSynth) window.playSynth('select');
    if(window.sysNotification) window.sysNotification("PEGANDO", `Procesando ${clipboardFtp.items.length} elementos...`, "fa-paste");
    for (let name of clipboardFtp.items) {
        let pOri = (clipboardFtp.origen.endsWith('/') ? clipboardFtp.origen : clipboardFtp.origen + '/') + name;
        let pDes = (rutaActualExplorador.endsWith('/') ? rutaActualExplorador : rutaActualExplorador + '/') + name;
        if (pOri === pDes) continue; 
        let fd = new FormData();
        fd.append('action', clipboardFtp.accion === 'copiar' ? 'copiar' : 'renombrar_mover');
        if(clipboardFtp.accion === 'copiar') {
            fd.append('source_path', pOri); fd.append('dest_path', pDes);
        } else {
            fd.append('old_path', pOri); fd.append('new_path', pDes);
        }
        fd.append('host_ip', window.globalAppConfig.ipConsola);
        try { await fetch('api/explorador_api.php', { method: 'POST', body: fd }); } catch(e) {}
    }
    if (clipboardFtp.accion === 'cortar') { 
        clipboardFtp = { accion: null, items: [], origen: null }; 
        document.getElementById('clipboard-bar').classList.remove('opacity-100', 'translate-x-0', 'scale-100', 'pointer-events-auto');
        document.getElementById('clipboard-bar').classList.add('opacity-0', 'translate-x-10', 'scale-95', 'pointer-events-none');
    }
    if(window.sysNotification) window.sysNotification("ÉXITO", "Pegado finalizado.", "fa-check");
    cargarRutaFtp(rutaActualExplorador);
}

function abrirDeleteConfirm() {
    const m = document.getElementById('modal-delete-confirm');
    m.classList.remove('hidden'); m.classList.add('flex');
    setTimeout(() => m.classList.remove('opacity-0'), 10);
}

function cerrarDeleteConfirm() {
    const m = document.getElementById('modal-delete-confirm');
    m.classList.add('opacity-0');
    setTimeout(() => { m.classList.add('hidden'); m.classList.remove('flex'); }, 300);
}

function abrirPromptFtp(titulo, placeholder, callback, valorDefecto = "") {
    const m = document.getElementById('modal-prompt-ftp');
    document.getElementById('prompt-title').innerText = titulo;
    const inp = document.getElementById('prompt-input');
    inp.placeholder = placeholder; 
    inp.value = valorDefecto;
    const btn = document.getElementById('prompt-btn-confirm');
    btn.onclick = () => { if(inp.value.trim() !== "") { cerrarPromptFtp(); callback(inp.value.trim()); } };
    m.classList.remove('hidden'); m.classList.add('flex');
    setTimeout(() => { m.classList.remove('opacity-0'); inp.focus(); inp.select(); }, 10);
}

function cerrarPromptFtp() {
    const m = document.getElementById('modal-prompt-ftp');
    m.classList.add('opacity-0');
    setTimeout(() => { m.classList.add('hidden'); m.classList.remove('flex'); }, 300);
}

function promptCrear(tipo) {
    cerrarFabMenu();
    abrirPromptFtp(`Crear Nuevo ${tipo}`, `Nombre del ${tipo}...`, async (nombre) => {
        let p = (rutaActualExplorador.endsWith('/') ? rutaActualExplorador : rutaActualExplorador + '/') + nombre;
        let fd = new FormData();
        fd.append('action', tipo === 'carpeta' ? 'crear_carpeta' : 'crear_archivo');
        fd.append('target', p); fd.append('host_ip', window.globalAppConfig.ipConsola);
        try {
            await fetch('api/explorador_api.php', { method: 'POST', body: fd });
            if(window.sysNotification) window.sysNotification("ÉXITO", `${tipo} creado.`, "fa-check");
            cargarRutaFtp(rutaActualExplorador);
        } catch(e) {}
    });
}

function promptRenombrar(viejoName) {
    abrirPromptFtp("Renombrar Elemento", "Nuevo nombre...", async (nuevoName) => {
        if (viejoName === nuevoName) return;
        let pViejo = (rutaActualExplorador.endsWith('/') ? rutaActualExplorador : rutaActualExplorador + '/') + viejoName;
        let pNuevo = (rutaActualExplorador.endsWith('/') ? rutaActualExplorador : rutaActualExplorador + '/') + nuevoName;
        let fd = new FormData();
        fd.append('action', 'renombrar_mover'); fd.append('old_path', pViejo); fd.append('new_path', pNuevo);
        fd.append('host_ip', window.globalAppConfig.ipConsola);
        try {
            await fetch('api/explorador_api.php', { method: 'POST', body: fd });
            if(window.sysNotification) window.sysNotification("ÉXITO", `Renombrado a ${nuevoName}`, "fa-check");
            cargarRutaFtp(rutaActualExplorador);
        } catch(e) {}
    }, viejoName);
}

function promptDuplicar(name) {
    let nuevoDesc = name;
    if(name.includes('.')) { 
        let parts = name.split('.'); 
        let ext = parts.pop();
        nuevoDesc = parts.join('.') + "_copia." + ext; 
    } 
    else { nuevoDesc += "_copia"; }
    
    abrirPromptFtp("Duplicar Archivo", "Nombre de la copia...", async (nuevoName) => {
        let pOri = (rutaActualExplorador.endsWith('/') ? rutaActualExplorador : rutaActualExplorador + '/') + name;
        let pDes = (rutaActualExplorador.endsWith('/') ? rutaActualExplorador : rutaActualExplorador + '/') + nuevoName;
        let fd = new FormData();
        fd.append('action', 'copiar'); fd.append('source_path', pOri); fd.append('dest_path', pDes);
        fd.append('host_ip', window.globalAppConfig.ipConsola);
        try {
            await fetch('api/explorador_api.php', { method: 'POST', body: fd });
            if(window.sysNotification) window.sysNotification("ÉXITO", `Duplicado a ${nuevoName}`, "fa-check");
            cancelarSeleccionMultiple(); cargarRutaFtp(rutaActualExplorador);
        } catch(e) {}
    }, nuevoDesc);
}

let fabExOpen = false;
function toggleExploradorFAB() {
    if(window.playSynth) window.playSynth('select');
    const menu = document.getElementById('fab-menu-ex');
    const icon = document.getElementById('fab-icon-ex');
    fabExOpen = !fabExOpen;
    if(fabExOpen) { 
        menu.classList.remove('scale-0', 'opacity-0', 'pointer-events-none'); 
        menu.classList.add('pointer-events-auto');
        icon.style.transform = 'rotate(45deg)'; 
    } else { 
        menu.classList.add('scale-0', 'opacity-0', 'pointer-events-none'); 
        menu.classList.remove('pointer-events-auto');
        icon.style.transform = 'rotate(0deg)'; 
    }
}
function cerrarFabMenu() { if(fabExOpen) toggleExploradorFAB(); }

function seleccionarIconoFav(icon, color, btn) {
    document.querySelectorAll('.btn-fav-icon').forEach(b => {
        b.classList.remove('border-cyan-500/50', 'border-purple-500/50', 'border-emerald-500/50', 'border-amber-500/50');
        b.classList.add('border-transparent');
    });
    btn.classList.remove('border-transparent');
    btn.classList.add(`border-${color}-500/50`);
    document.getElementById('fav-selected-icon').value = icon;
    document.getElementById('fav-selected-color').value = color;
}

function renderizarAccesosRapidos() {
    const grid = document.getElementById('ftp-shortcuts-grid');
    if(!grid) return;
    grid.innerHTML = '';
    let saved = JSON.parse(localStorage.getItem('sebas_ftp_shortcuts'));
    if (!saved || saved.length === 0) {
        saved = shortcutsPredeterminados;
        localStorage.setItem('sebas_ftp_shortcuts', JSON.stringify(saved));
    }
    
    saved.forEach(s => {
        const isDefault = shortcutsPredeterminados.find(d => d.ruta === s.ruta && d.nombre === s.nombre);
        if(isDefault) s.locked = true;
    });

    const s = { 'blue': 'bg-blue-500/20 text-blue-400 border-blue-500/30', 'amber': 'bg-amber-500/20 text-amber-400 border-amber-500/30', 'emerald': 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30', 'purple': 'bg-purple-500/20 text-purple-400 border-purple-500/30', 'cyan': 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30' };
    saved.forEach((sc, index) => {
        let cls = s[sc.color] || s['cyan'];
        let trashBtn = sc.locked ? '' : `<button onclick="eliminarAccesoRapido(event, ${index})" class="absolute top-1 right-1 w-5 h-5 rounded-full bg-red-500/20 text-red-500 flex items-center justify-center text-[8px] opacity-100 transition-opacity"><i class="fa-solid fa-trash"></i></button>`;
        
        grid.innerHTML += `
            <div class="glass-panel p-3 rounded-2xl flex flex-col items-center justify-center gap-2 tactile-fx cursor-pointer relative group border-white/5" onclick="navegarAccesoRapido('${sc.ruta}')">
                ${trashBtn}
                <div class="w-8 h-8 rounded-xl flex items-center justify-center border ${cls}"><i class="fa-solid ${sc.icon} text-sm"></i></div>
                <span class="text-[9px] font-black tracking-widest uppercase text-white truncate w-full text-center px-1">${sc.nombre}</span>
            </div>
        `;
    });
}

function abrirHubFavoritos() {
    if(window.playSynth) window.playSynth('select');
    renderizarAccesosRapidos();
    const modal = document.getElementById('modal-hub-favs');
    const content = document.getElementById('modal-hub-favs-content');
    modal.classList.remove('hidden'); modal.classList.add('flex');
    setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('translate-y-full'); }, 10);
}

function cerrarHubFavoritos() {
    const modal = document.getElementById('modal-hub-favs');
    const content = document.getElementById('modal-hub-favs-content');
    content.classList.add('translate-y-full'); modal.classList.add('opacity-0');
    setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); }, 300);
}

function navegarAccesoRapido(ruta) { cerrarHubFavoritos(); cargarRutaFtp(ruta); }

function eliminarAccesoRapido(e, index) {
    e.stopPropagation();
    let saved = JSON.parse(localStorage.getItem('sebas_ftp_shortcuts')) || shortcutsPredeterminados;
    saved.splice(index, 1);
    localStorage.setItem('sebas_ftp_shortcuts', JSON.stringify(saved));
    renderizarAccesosRapidos();
}

function abrirModalNuevoAccesoFtp() {
    cerrarFabMenu(); cerrarHubFavoritos();
    if(window.playSynth) window.playSynth('select');
    const m = document.getElementById('modal-nuevo-acceso-ftp');
    document.getElementById('txt-ruta-a-guardar').innerText = rutaActualExplorador;
    
    document.getElementById('ftp-shortcut-btn-name').value = '';
    document.getElementById('fav-selected-icon').value = 'fa-folder';
    document.getElementById('fav-selected-color').value = 'cyan';
    document.querySelectorAll('.btn-fav-icon').forEach((b, idx) => {
        b.classList.remove('border-cyan-500/50', 'border-purple-500/50', 'border-emerald-500/50', 'border-amber-500/50');
        b.classList.add('border-transparent');
        if(idx === 0) b.classList.add('border-cyan-500/50');
    });

    m.classList.remove('hidden'); m.classList.add('flex');
    setTimeout(() => m.classList.remove('opacity-0'), 10);
}

function cerrarModalNuevoAccesoFtp() {
    const m = document.getElementById('modal-nuevo-acceso-ftp');
    m.classList.add('opacity-0');
    setTimeout(() => { m.classList.add('hidden'); m.classList.remove('flex'); }, 300);
}

function guardarNuevoAccesoFtp() {
    let nom = document.getElementById('ftp-shortcut-btn-name').value.trim();
    if(nom === "") { if(window.sysNotification) window.sysNotification("ERROR", "Asigna un nombre al acceso.", "fa-xmark"); return; }
    let icon = document.getElementById('fav-selected-icon').value;
    let color = document.getElementById('fav-selected-color').value;
    
    let saved = JSON.parse(localStorage.getItem('sebas_ftp_shortcuts')) || shortcutsPredeterminados;
    saved.push({ nombre: nom, ruta: rutaActualExplorador, icon: icon, color: color });
    localStorage.setItem('sebas_ftp_shortcuts', JSON.stringify(saved));
    cerrarModalNuevoAccesoFtp();
    if(window.sysNotification) window.sysNotification("ÉXITO", "Acceso guardado.", "fa-star");
}

async function procesarSubidaExplorador(event) {
    cerrarFabMenu();
    let files = event.target.files;
    if (!files || files.length === 0) return;
    let file = files[0];
    document.getElementById('modal-upload-progress').classList.remove('hidden');
    setTimeout(() => document.getElementById('modal-upload-progress').classList.remove('opacity-0'), 10);
    document.getElementById('up-modal-title').innerText = "SUBIENDO ARCHIVO";
    document.getElementById('up-modal-filename').innerText = file.name;
    controllerSubidaExplorador = new AbortController();
    const CHUNK_SIZE = 1 * 1024 * 1024;
    const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
    let chunksUploaded = 0;
    let startTime = Date.now();
    for (let i = 0; i < totalChunks; i++) {
        if(controllerSubidaExplorador.signal.aborted) break;
        let start = i * CHUNK_SIZE;
        let end = Math.min(start + CHUNK_SIZE, file.size);
        let chunk = file.slice(start, end);
        let fd = new FormData();
        fd.append('action', 'upload_explorer_chunk'); fd.append('file_chunk', chunk); fd.append('chunk_index', i);
        fd.append('filename', file.name); fd.append('target_dir', rutaActualExplorador);
        fd.append('host_ip', window.globalAppConfig.ipConsola);
        try {
            await fetch('api/explorador_api.php', { method: 'POST', body: fd, signal: controllerSubidaExplorador.signal });
            chunksUploaded++;
            let percent = Math.round((chunksUploaded / totalChunks) * 100);
            let timeElapsed = (Date.now() - startTime) / 1000;
            let speed = (chunksUploaded * CHUNK_SIZE) / timeElapsed;
            let remainingBytes = file.size - (chunksUploaded * CHUNK_SIZE);
            let eta = speed > 0 ? remainingBytes / speed : 0;
            document.getElementById('up-modal-percent').innerText = percent + "%";
            document.getElementById('up-modal-bar').style.width = percent + "%";
            document.getElementById('up-modal-speed').innerText = formatearTamano(speed) + "/s";
            document.getElementById('up-modal-sent').innerText = `${formatearTamano(chunksUploaded * CHUNK_SIZE)} / ${formatearTamano(file.size)}`;
            document.getElementById('up-modal-eta').innerText = `ETA: ${Math.ceil(eta)}s`;
        } catch (e) { break; }
    }
    if(!controllerSubidaExplorador.signal.aborted) {
        if(window.sysNotification) window.sysNotification("SUBIDA EXITOSA", "Archivo transferido a la consola.", "fa-check");
        cargarRutaFtp(rutaActualExplorador);
    }
    cerrarUploadProgress();
    document.getElementById('explorador-upload-file').value = "";
}

async function procesarSubidaCarpetaEstructurada(event) {
    cerrarFabMenu();
    let files = Array.from(event.target.files);
    if (!files || files.length === 0) return;

    let totalSize = files.reduce((acc, file) => acc + file.size, 0);
    let nombreCarpetaRaiz = files[0].webkitRelativePath.split('/')[0];
    
    document.getElementById('modal-upload-progress').classList.remove('hidden');
    setTimeout(() => document.getElementById('modal-upload-progress').classList.remove('opacity-0'), 10);
    document.getElementById('up-modal-title').innerText = "SUBIENDO CARPETA";
    document.getElementById('up-modal-filename').innerText = `Carpeta: ${nombreCarpetaRaiz} (${files.length} items)`;
    
    controllerSubidaExplorador = new AbortController();
    const CHUNK_SIZE = 1 * 1024 * 1024;
    let totalBytesUploaded = 0;
    let startTime = Date.now();

    for (let file of files) {
        if(controllerSubidaExplorador.signal.aborted) break;
        
        let fileRelativePath = file.webkitRelativePath;
        let fileTotalChunks = Math.ceil(file.size / CHUNK_SIZE);
        let fileBytesUploaded = 0;

        for (let i = 0; i < fileTotalChunks; i++) {
            if(controllerSubidaExplorador.signal.aborted) break;
            
            let start = i * CHUNK_SIZE;
            let end = Math.min(start + CHUNK_SIZE, file.size);
            let chunk = file.slice(start, end);
            
            let fd = new FormData();
            fd.append('action', 'upload_explorer_chunk'); 
            fd.append('file_chunk', chunk); 
            fd.append('chunk_index', i);
            fd.append('filename', file.name); 
            fd.append('target_dir', rutaActualExplorador);
            fd.append('is_folder_upload', 'true');
            fd.append('relative_path', fileRelativePath);
            fd.append('host_ip', window.globalAppConfig.ipConsola);

            try {
                await fetch('api/explorador_api.php', { method: 'POST', body: fd, signal: controllerSubidaExplorador.signal });
                fileBytesUploaded += chunk.size;
                totalBytesUploaded += chunk.size;
                
                let percent = Math.round((totalBytesUploaded / totalSize) * 100);
                let timeElapsed = (Date.now() - startTime) / 1000;
                let speed = totalBytesUploaded / timeElapsed;
                let remainingBytes = totalSize - totalBytesUploaded;
                let eta = speed > 0 ? remainingBytes / speed : 0;
                
                document.getElementById('up-modal-percent').innerText = percent + "%";
                document.getElementById('up-modal-bar').style.width = percent + "%";
                document.getElementById('up-modal-speed').innerText = formatearTamano(speed) + "/s";
                document.getElementById('up-modal-sent').innerText = `${formatearTamano(totalBytesUploaded)} / ${formatearTamano(totalSize)}`;
                document.getElementById('up-modal-eta').innerText = `ETA: ${Math.ceil(eta)}s`;
            } catch (e) { break; }
        }
    }

    if(!controllerSubidaExplorador.signal.aborted) {
        if(window.sysNotification) window.sysNotification("SUBIDA EXITOSA", `Carpeta ${nombreCarpetaRaiz} transferida.`, "fa-folder-open");
        cargarRutaFtp(rutaActualExplorador);
    }
    cerrarUploadProgress();
    document.getElementById('explorador-upload-folder').value = "";
}

function cancelarSubidaExplorador() {
    if(controllerSubidaExplorador) {
        controllerSubidaExplorador.abort();
        if(window.sysNotification) window.sysNotification("ABORTADO", "Transferencia cancelada.", "fa-ban");
    }
    cerrarUploadProgress();
}

function cerrarUploadProgress() {
    const modal = document.getElementById('modal-upload-progress');
    modal.classList.add('opacity-0');
    setTimeout(() => { modal.classList.add('hidden'); }, 300);
}
