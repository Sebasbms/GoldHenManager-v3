/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: js/roms.js
 * ====================================================================
 */
var ROMS_CHUNK_SIZE = 1.5 * 1024 * 1024;
var dbEmuladoresRoms = [];
var emuladorActivoRoms = null;
var rutaActivaRoms = null;
var drawerEmuladoresAbierto = false;
var rutaActualExploradorRoms = '/data/';
var cacheJuegosBibliotecaRoms = [];
var audioCtxRoms = null;
var modoBorrarRutasRoms = false;

var colaSubidaRoms = [];
var totalRomsEnCola = 0;
var romActualBlob = null;
var romsAbortController = null;
var subiendoRomsActivo = false;
var romStartTime = 0;

function initModuloRoms() {
    try {
        dbEmuladoresRoms = JSON.parse(localStorage.getItem('sebas_roms_emuladores_v1')) || [];
    } catch (e) {
        dbEmuladoresRoms = [];
    }
    const capaRoms = document.getElementById('layer-roms');
    if (capaRoms) {
        const obs = new MutationObserver(() => {
            if (capaRoms.classList.contains('active')) {
                window.evaluarEstadoUIRoms();
            }
        });
        obs.observe(capaRoms, { attributes: true, attributeFilter: ['class'] });
    }
    window.evaluarEstadoUIRoms();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initModuloRoms);
} else {
    initModuloRoms();
}

function guardarEmuladoresRomsStorage() {
    localStorage.setItem('sebas_roms_emuladores_v1', JSON.stringify(dbEmuladoresRoms));
}

function formatearBytesRoms(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function playExitoSoundRoms() {
    try {
        if (!audioCtxRoms) { audioCtxRoms = new (window.AudioContext || window.webkitAudioContext)(); }
        if (audioCtxRoms.state === 'suspended') { audioCtxRoms.resume(); }
        const now = audioCtxRoms.currentTime;
        const freqs = [523.25, 659.25, 783.99, 1046.50]; 
        freqs.forEach((f, i) => {
            const osc = audioCtxRoms.createOscillator();
            const gain = audioCtxRoms.createGain();
            osc.connect(gain); gain.connect(audioCtxRoms.destination);
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

function dispararConfetiRoms() {
    const container = document.getElementById('confetti-container-roms');
    if(!container) return;
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
            
            conf.style.transform = `translate(${x - (conf.dataset.left === '20%' ? window.innerWidth*0.2 : window.innerWidth*0.8)}px, ${y - window.innerHeight}px) rotate(${rot}deg)`;
            if (y < window.innerHeight + 100) actvos = true;
        }
        if(actvos && frame < 300) requestAnimationFrame(animar);
    }
    requestAnimationFrame(animar);
}

window.mostrarModalExitoRoms = function() {
    const modal = document.getElementById('modal-exito-anim-roms');
    const content = document.getElementById('content-exito-roms');
    const txt = document.getElementById('roms-txt-modal-exito');
    if(txt) txt.innerText = `${totalRomsEnCola} archivo(s) ROM inyectados correctamente en ${emuladorActivoRoms.nombre}.`;
    
    modal.classList.remove('hidden');
    playExitoSoundRoms();
    
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modal.classList.remove('pointer-events-none');
        content.classList.remove('scale-50');
        content.classList.add('scale-100');
        dispararConfetiRoms();
    }, 50);
};

window.cerrarModalExitoRoms = function() {
    if(typeof window.playSynth === 'function') window.playSynth('select');
    const modal = document.getElementById('modal-exito-anim-roms');
    const content = document.getElementById('content-exito-roms');
    modal.classList.remove('opacity-100');
    content.classList.remove('scale-100');
    content.classList.add('scale-50');
    setTimeout(() => {
        modal.classList.add('hidden', 'pointer-events-none');
        window.cerrarPanelSubidaRoms();
    }, 500);
};

window.toggleDrawerEmuladoresRoms = function(forzarEstado) {
    if (typeof window.playSynth === 'function') window.playSynth('select');
    const drawer = document.getElementById('roms-drawer-emuladores');
    const flecha = document.getElementById('roms-icono-flecha-drawer');
    if (!drawer || !flecha) return;

    drawerEmuladoresAbierto = typeof forzarEstado === 'boolean' ? forzarEstado : !drawerEmuladoresAbierto;

    if (drawerEmuladoresAbierto) {
        drawer.classList.remove('roms-drawer-closed');
        drawer.classList.add('roms-drawer-open');
        flecha.style.transform = 'rotate(180deg)';
    } else {
        drawer.classList.remove('roms-drawer-open');
        drawer.classList.add('roms-drawer-closed');
        flecha.style.transform = 'rotate(0deg)';
    }
};

function actualizarTarjetaHeroRoms() {
    if (!emuladorActivoRoms) return;
    const imgEl = document.getElementById('roms-hero-emu-cover');
    const nomEl = document.getElementById('roms-hero-emu-nombre');
    const infoEl = document.getElementById('roms-hero-emu-info');
    const badgeEl = document.getElementById('roms-badge-total-emus');

    if (imgEl) imgEl.src = emuladorActivoRoms.cover;
    if (nomEl) nomEl.innerText = emuladorActivoRoms.nombre;
    const cantRutas = (emuladorActivoRoms.carpetas || []).length;
    if (infoEl) infoEl.innerText = `${emuladorActivoRoms.id} • ${cantRutas} ${cantRutas === 1 ? 'RUTA' : 'RUTAS'}`;
    if (badgeEl) badgeEl.innerText = `${dbEmuladoresRoms.length} VINCULADOS`;
}

window.evaluarEstadoUIRoms = function() {
    const vacio = document.getElementById('roms-estado-vacio');
    const contenido = document.getElementById('roms-contenido-principal');
    if (!vacio || !contenido) return;

    if (dbEmuladoresRoms.length === 0) {
        vacio.classList.remove('hidden');
        vacio.classList.add('flex');
        contenido.classList.add('hidden');
        contenido.classList.remove('flex');
    } else {
        vacio.classList.add('hidden');
        vacio.classList.remove('flex');
        contenido.classList.remove('hidden');
        contenido.classList.add('flex');
        if (!emuladorActivoRoms && dbEmuladoresRoms.length > 0) {
            emuladorActivoRoms = dbEmuladoresRoms[0];
        }
        renderizarEmuladoresRoms();
        if (emuladorActivoRoms) window.seleccionarEmuladorRoms(emuladorActivoRoms.id, false);
    }
};

function renderizarEmuladoresRoms() {
    const container = document.getElementById('roms-contenedor-emuladores');
    if (!container) return;
    let html = '';

    dbEmuladoresRoms.forEach(emu => {
        const isActive = emuladorActivoRoms && emuladorActivoRoms.id === emu.id ? 'active' : '';
        html += `
        <div onclick="window.seleccionarEmuladorRoms('${emu.id}', true)" class="roms-emu-card ${isActive} tactile-fx flex flex-col items-center p-2 w-full rounded-[1.2rem] gap-1.5 cursor-pointer relative">
            <button onclick="window.eliminarEmuladorRoms(event, '${emu.id}')" class="absolute top-3 right-3 z-20 w-5 h-5 rounded-full bg-black/75 text-gray-300 hover:text-red-400 flex items-center justify-center text-[9px] border border-white/10">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <img src="${emu.cover}" class="w-full aspect-square object-cover rounded-[0.8rem] shadow-md bg-black/40">
            <span class="text-[9px] font-black tracking-widest text-center uppercase truncate w-full px-1">${emu.nombre}</span>
        </div>`;
    });
    container.innerHTML = html;
}

window.eliminarEmuladorRoms = function(e, id) {
    e.stopPropagation();
    if (typeof window.playSynth === 'function') window.playSynth('select');
    dbEmuladoresRoms = dbEmuladoresRoms.filter(emu => emu.id !== id);
    if (emuladorActivoRoms && emuladorActivoRoms.id === id) {
        emuladorActivoRoms = dbEmuladoresRoms[0] || null;
    }
    guardarEmuladoresRomsStorage();
    window.evaluarEstadoUIRoms();
};

window.seleccionarEmuladorRoms = function(id, cerrarDrawer = true) {
    emuladorActivoRoms = dbEmuladoresRoms.find(e => e.id === id);
    if (!emuladorActivoRoms) return;

    if (!Array.isArray(emuladorActivoRoms.carpetas)) emuladorActivoRoms.carpetas = [];
    
    let savedRuta = localStorage.getItem('sebas_roms_last_ruta_' + emuladorActivoRoms.id);
    
    if (!rutaActivaRoms || !emuladorActivoRoms.carpetas.includes(rutaActivaRoms)) {
        if (savedRuta && emuladorActivoRoms.carpetas.includes(savedRuta)) {
            rutaActivaRoms = savedRuta;
        } else {
            rutaActivaRoms = emuladorActivoRoms.carpetas.length > 0 ? emuladorActivoRoms.carpetas[0] : null;
        }
    }

    modoBorrarRutasRoms = false;

    actualizarTarjetaHeroRoms();
    renderizarEmuladoresRoms();

    if (cerrarDrawer && drawerEmuladoresAbierto) {
        window.toggleDrawerEmuladoresRoms(false);
    }

    const lblNom = document.getElementById('roms-nombre-emu-activo');
    if (lblNom) lblNom.innerText = emuladorActivoRoms.nombre;

    const secCarpetas = document.getElementById('roms-seccion-carpetas');
    if (secCarpetas) {
        secCarpetas.classList.remove('hidden');
        secCarpetas.classList.add('flex');
        setTimeout(() => { secCarpetas.classList.remove('opacity-0', 'translate-y-4'); }, 30);
    }

    renderizarCarpetasRoms();

    if (rutaActivaRoms) {
        window.seleccionarCarpetaRoms(rutaActivaRoms);
    } else {
        ocultarSeccionSubidaRoms();
    }
};

window.toggleModoBorrarRutasRoms = function() {
    if (typeof window.playSynth === 'function') window.playSynth('select');
    modoBorrarRutasRoms = !modoBorrarRutasRoms;
    renderizarCarpetasRoms();
    if (modoBorrarRutasRoms) {
        ocultarSeccionSubidaRoms();
    } else {
        if (rutaActivaRoms) window.seleccionarCarpetaRoms(rutaActivaRoms);
    }
};

function renderizarCarpetasRoms() {
    const container = document.getElementById('roms-contenedor-rutas');
    const btnEditar = document.getElementById('btn-toggle-borrar-rutas');
    if (!container || !emuladorActivoRoms) return;
    let html = '';

    if (emuladorActivoRoms.carpetas.length === 0) {
        modoBorrarRutasRoms = false;
        if (btnEditar) btnEditar.style.display = 'none';
        
        container.className = "flex w-full px-1 transition-all duration-300";
        html = `
        <div onclick="window.abrirExploradorFTPRoms()" class="tactile-fx w-full py-4 px-4 rounded-xl border border-dashed border-white/10 bg-white/[0.01] flex items-center justify-center gap-3 text-gray-400 hover:text-cyan-400 hover:border-cyan-500/30">
            <i class="fa-solid fa-folder-tree text-lg text-cyan-400"></i>
            <span class="text-[11px] font-mono uppercase tracking-widest">Sin rutas guardadas. Toca para explorar FTP</span>
        </div>`;
    } else {
        if (btnEditar) {
            btnEditar.style.display = 'flex';
            if (modoBorrarRutasRoms) {
                btnEditar.className = "tactile-fx px-3 py-1.5 rounded-full bg-red-500/20 border border-red-500/50 text-red-400 text-[9px] font-black uppercase tracking-widest flex items-center gap-1.5 shadow-[0_0_10px_rgba(239,68,68,0.2)] shrink-0 transition-all";
                btnEditar.innerHTML = `<i class="fa-solid fa-check text-[10px]"></i> Listo`;
            } else {
                btnEditar.className = "tactile-fx px-3 py-1.5 rounded-full bg-gray-500/10 border border-gray-500/30 text-gray-400 text-[9px] font-black uppercase tracking-widest flex items-center gap-1.5 hover:bg-gray-500/20 shrink-0 transition-all";
                btnEditar.innerHTML = `<i class="fa-solid fa-pen text-[10px]"></i> Editar`;
            }
        }

        if (modoBorrarRutasRoms) {
            container.className = "grid grid-cols-2 gap-2.5 w-full px-1 pb-4 transition-all duration-300";
        } else {
            container.className = "flex w-full overflow-x-auto hide-scrollbar gap-3 snap-x px-1 transition-all duration-300";
        }

        const anchoTarjeta = modoBorrarRutasRoms ? 'w-full' : (emuladorActivoRoms.carpetas.length === 1 ? 'w-full' : 'w-[200px] shrink-0');
        const snapClass = modoBorrarRutasRoms ? '' : 'snap-center';

        emuladorActivoRoms.carpetas.forEach(ruta => {
            const isActive = (!modoBorrarRutasRoms && rutaActivaRoms === ruta) ? 'active text-emerald-400' : 'text-gray-300 hover:border-emerald-500/30';
            const iconActive = (!modoBorrarRutasRoms && rutaActivaRoms === ruta) ? 'text-emerald-500' : 'text-gray-500';
            const nombreCorto = ruta.split('/').filter(Boolean).pop() || 'Raíz';

            let accionClick = modoBorrarRutasRoms ? '' : `onclick="window.seleccionarCarpetaRoms('${ruta}')"`;
            let claseBorde = modoBorrarRutasRoms ? 'border-red-500/40 bg-red-900/10' : '';
            let clasePuntero = modoBorrarRutasRoms ? '' : 'cursor-pointer';
            
            let btnBorrar = '';
            if (modoBorrarRutasRoms) {
                btnBorrar = `
                <button onclick="window.eliminarCarpetaRoms(event, '${ruta}')" class="w-8 h-8 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center text-[11px] shrink-0 active:scale-90 hover:bg-red-500/40 transition-all ml-2 border border-red-500/40 shadow-[0_0_10px_rgba(239,68,68,0.2)]">
                    <i class="fa-solid fa-trash-can"></i>
                </button>`;
            }

            html += `
            <div ${accionClick} class="roms-folder-chip ${isActive} ${claseBorde} tactile-fx ${anchoTarjeta} p-3 rounded-2xl flex items-center justify-between gap-3 ${snapClass} ${clasePuntero}">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-xl bg-black/40 flex items-center justify-center ${iconActive} text-lg shrink-0 shadow-inner">
                        <i class="fa-solid fa-folder"></i>
                    </div>
                    <span class="text-[12px] font-black tracking-widest uppercase truncate">${nombreCorto}</span>
                </div>
                ${btnBorrar}
            </div>`;
        });
    }

    container.innerHTML = html;
    actualizarTarjetaHeroRoms();
}

window.eliminarCarpetaRoms = function(e, ruta) {
    e.stopPropagation();
    if (!emuladorActivoRoms) return;
    emuladorActivoRoms.carpetas = emuladorActivoRoms.carpetas.filter(r => r !== ruta);
    guardarEmuladoresRomsStorage();
    if (rutaActivaRoms === ruta) {
        rutaActivaRoms = emuladorActivoRoms.carpetas[0] || null;
    }
    if (emuladorActivoRoms.carpetas.length === 0) {
        modoBorrarRutasRoms = false;
    }
    renderizarCarpetasRoms();
    if (rutaActivaRoms && !modoBorrarRutasRoms) window.seleccionarCarpetaRoms(rutaActivaRoms);
    else ocultarSeccionSubidaRoms();
};

window.seleccionarCarpetaRoms = function(ruta) {
    if (typeof window.playSynth === 'function') window.playSynth('tap');
    rutaActivaRoms = ruta;
    
    if (emuladorActivoRoms) {
        localStorage.setItem('sebas_roms_last_ruta_' + emuladorActivoRoms.id, ruta);
    }
    
    renderizarCarpetasRoms();
    const txtRuta = document.getElementById('roms-ruta-final-txt');
    if (txtRuta) txtRuta.innerText = ruta;

    const secSubida = document.getElementById('roms-seccion-subida');
    if (secSubida) {
        secSubida.classList.remove('hidden');
        secSubida.classList.add('flex');
        setTimeout(() => { secSubida.classList.remove('opacity-0', 'translate-y-4'); }, 30);
    }
};

function ocultarSeccionSubidaRoms() {
    const secSubida = document.getElementById('roms-seccion-subida');
    if (!secSubida) return;
    secSubida.classList.add('opacity-0', 'translate-y-4');
    setTimeout(() => { secSubida.classList.add('hidden'); secSubida.classList.remove('flex'); }, 200);
}

window.abrirModalBibliotecaRoms = async function() {
    if (typeof window.playSynth === 'function') window.playSynth('select');
    const modal = document.getElementById('roms-modal-biblioteca');
    const grid = document.getElementById('roms-grid-modal-biblioteca');
    const inputBuscar = document.getElementById('roms-buscar-biblio');
    if (inputBuscar) inputBuscar.value = '';

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => { modal.classList.remove('opacity-0'); }, 10);

    grid.innerHTML = `<div class="col-span-3 py-12 text-center text-cyan-400"><i class="fa-solid fa-spinner fa-spin text-2xl mb-2"></i><p class="text-[10px] font-mono uppercase tracking-widest">Cargando Biblioteca...</p></div>`;

    if (typeof listadoJuegos !== 'undefined' && Array.isArray(listadoJuegos) && listadoJuegos.length > 0) {
        cacheJuegosBibliotecaRoms = listadoJuegos;
    } else {
        try {
            let res = await fetch('api/library_biblioteca.php?action=get_cached_games');
            let json = await res.json();
            if (json && json.status === 'success' && Array.isArray(json.data)) {
                cacheJuegosBibliotecaRoms = json.data;
            }
        } catch (e) {
            cacheJuegosBibliotecaRoms = [];
        }
    }

    window.filtrarModalBibliotecaRoms();
};

window.filtrarModalBibliotecaRoms = function() {
    const grid = document.getElementById('roms-grid-modal-biblioteca');
    const query = (document.getElementById('roms-buscar-biblio')?.value || '').trim().toLowerCase();
    if (!grid) return;

    if (!cacheJuegosBibliotecaRoms || cacheJuegosBibliotecaRoms.length === 0) {
        grid.innerHTML = `<div class="col-span-3 py-10 text-center px-4"><i class="fa-solid fa-triangle-exclamation text-amber-400 text-2xl mb-2"></i><p class="text-[10px] font-mono text-gray-400 uppercase tracking-widest">No hay juegos en caché. Abre primero el módulo Biblioteca para sincronizar tus títulos de PS4.</p></div>`;
        return;
    }

    let html = '';
    cacheJuegosBibliotecaRoms.forEach(juego => {
        if (dbEmuladoresRoms.find(e => e.id === juego.id)) return;
        if (query && !juego.nombre.toLowerCase().includes(query) && !juego.id.toLowerCase().includes(query)) return;

        const safeNombre = (juego.nombre || juego.id).replace(/'/g, "\\'");
        const safeImg = (juego.img || '').replace(/'/g, "\\'");

        html += `
        <div onclick="window.vincularEmuladorRoms('${juego.id}', '${safeNombre}', '${safeImg}')" class="tactile-fx flex flex-col items-center bg-[#0a0f1a] border border-white/5 rounded-2xl p-2.5 gap-2 hover:border-purple-500/50">
            <img src="${juego.img}" class="w-full aspect-square object-cover rounded-xl shadow-md bg-black/40">
            <span class="text-[10px] font-black uppercase text-center text-white tracking-widest truncate w-full">${juego.nombre}</span>
            <span class="text-[8px] font-mono text-gray-500 -mt-1">${juego.id}</span>
        </div>`;
    });

    if (html === '') {
        html = `<p class="col-span-3 text-center text-[10px] text-gray-500 font-mono py-10 uppercase tracking-widest">No se encontraron aplicaciones disponibles</p>`;
    }

    grid.innerHTML = html;
};

window.cerrarModalBibliotecaRoms = function() {
    const modal = document.getElementById('roms-modal-biblioteca');
    if (!modal) return;
    modal.classList.add('opacity-0');
    setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); }, 250);
};

window.vincularEmuladorRoms = function(id, nombre, cover) {
    if (typeof window.playSynth === 'function') window.playSynth('success');
    dbEmuladoresRoms.push({ id: id, nombre: nombre, cover: cover, carpetas: [] });
    guardarEmuladoresRomsStorage();
    window.cerrarModalBibliotecaRoms();
    emuladorActivoRoms = dbEmuladoresRoms.find(e => e.id === id);
    window.evaluarEstadoUIRoms();
    window.toggleDrawerEmuladoresRoms(false);
    if (typeof window.sysNotification === 'function') {
        window.sysNotification("EMULADOR VINCULADO", `${nombre} añadido a tus perfiles.`, "fa-gamepad");
    }
};

window.abrirExploradorFTPRoms = function() {
    if (!emuladorActivoRoms) return;
    const ip = localStorage.getItem('sebas_ip_final_libre');
    if (!ip) {
        if (typeof window.sysNotification === 'function') window.sysNotification("SIN CONEXIÓN", "Conecta tu consola PS4 primero.", "fa-wifi");
        return;
    }

    rutaActualExploradorRoms = rutaActivaRoms || '/data/';
    if (!rutaActualExploradorRoms.endsWith('/')) rutaActualExploradorRoms += '/';

    const modal = document.getElementById('roms-modal-ftp');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => { modal.classList.remove('opacity-0'); }, 10);

    cargarDirectoriosFTPRoms(rutaActualExploradorRoms);
};

window.cerrarModalFTPRoms = function() {
    const modal = document.getElementById('roms-modal-ftp');
    if (!modal) return;
    modal.classList.add('opacity-0');
    setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); }, 250);
};

window.irRutaDirectaFTPRoms = function(ruta) {
    rutaActualExploradorRoms = ruta;
    cargarDirectoriosFTPRoms(rutaActualExploradorRoms);
};

async function cargarDirectoriosFTPRoms(ruta) {
    const ip = localStorage.getItem('sebas_ip_final_libre');
    const container = document.getElementById('roms-lista-carpetas-ftp');
    const lblRuta = document.getElementById('roms-ruta-ftp-actual');
    if (!container) return;

    if (!ruta.endsWith('/')) ruta += '/';
    rutaActualExploradorRoms = ruta;
    if (lblRuta) lblRuta.innerText = rutaActualExploradorRoms;

    container.innerHTML = `<div class="w-full py-10 text-center text-cyan-400 opacity-70"><i class="fa-solid fa-spinner fa-spin text-2xl mb-2"></i><p class="text-[10px] font-mono uppercase tracking-widest">Leyendo carpetas de PS4...</p></div>`;

    try {
        let fd = new FormData();
        fd.append('action', 'listar_directorio');
        fd.append('host_ip', ip);
        fd.append('path', rutaActualExploradorRoms);

        let res = await fetch('api/explorador_api.php', { method: 'POST', body: fd });
        let data = await res.json();

        if (data && data.status === 'success') {
            container.innerHTML = '';

            if (!data.data || !data.data.carpetas || data.data.carpetas.length === 0) {
                container.innerHTML = `<div class="text-center py-10 opacity-50"><i class="fa-solid fa-folder-open text-2xl text-gray-500 mb-2"></i><p class="text-[10px] font-mono uppercase tracking-widest text-gray-400">Sin subcarpetas aquí. Puedes guardar esta ruta o crear una carpeta con el botón superior.</p></div>`;
                return;
            }

            data.data.carpetas.forEach(carpetaObj => {
                const dir = carpetaObj.name;
                const div = document.createElement('div');
                div.className = "tactile-fx flex items-center gap-3 p-3 rounded-xl bg-white/[0.02] hover:bg-cyan-500/10 border border-white/5 hover:border-cyan-500/30 cursor-pointer shrink-0";
                div.onclick = () => {
                    rutaActualExploradorRoms = rutaActualExploradorRoms + dir + '/';
                    cargarDirectoriosFTPRoms(rutaActualExploradorRoms);
                };
                div.innerHTML = `
                    <i class="fa-solid fa-folder text-cyan-400 text-base"></i>
                    <span class="text-[12px] font-bold text-gray-200 tracking-wider truncate">${dir}</span>
                    <i class="fa-solid fa-chevron-right text-gray-600 text-[10px] ml-auto"></i>
                `;
                container.appendChild(div);
            });
        } else {
            container.innerHTML = `<div class="text-center py-10 text-red-400 text-[10px] font-mono uppercase">No se pudo leer este directorio</div>`;
        }
    } catch (e) {
        container.innerHTML = `<div class="text-center py-10 text-red-400 text-[10px] font-mono uppercase">Error de conexión FTP con la consola</div>`;
    }
}

window.subirNivelFTPRoms = function() {
    if (rutaActualExploradorRoms === '/' || rutaActualExploradorRoms === '') return;
    let partes = rutaActualExploradorRoms.split('/').filter(Boolean);
    partes.pop();
    rutaActualExploradorRoms = partes.length > 0 ? '/' + partes.join('/') + '/' : '/';
    cargarDirectoriosFTPRoms(rutaActualExploradorRoms);
};

window.crearCarpetaFTPRoms = async function() {
    const ip = localStorage.getItem('sebas_ip_final_libre');
    const nombre = prompt("Nombre de la nueva carpeta para ROMs:");
    if (!nombre || !nombre.trim()) return;

    try {
        let fd = new FormData();
        fd.append('action', 'crear_carpeta');
        fd.append('host_ip', ip);
        fd.append('target', rutaActualExploradorRoms + nombre.trim());

        let res = await fetch('api/explorador_api.php', { method: 'POST', body: fd });
        let data = await res.json();
        if (data && data.status === 'success') {
            if (typeof window.sysNotification === 'function') window.sysNotification("CARPETA CREADA", `Se creó ${nombre.trim()}`, "fa-folder-plus");
            cargarDirectoriosFTPRoms(rutaActualExploradorRoms);
        } else {
            if (typeof window.sysNotification === 'function') window.sysNotification("ERROR", "No se pudo crear la carpeta", "fa-circle-xmark");
        }
    } catch (e) {}
};

window.confirmarRutaFTPRoms = function() {
    if (!emuladorActivoRoms) return;
    let rutaFinal = rutaActualExploradorRoms;
    if (!rutaFinal.endsWith('/')) rutaFinal += '/';

    if (!emuladorActivoRoms.carpetas.includes(rutaFinal)) {
        emuladorActivoRoms.carpetas.push(rutaFinal);
        guardarEmuladoresRomsStorage();
    }
    window.cerrarModalFTPRoms();
    modoBorrarRutasRoms = false;
    renderizarCarpetasRoms();
    window.seleccionarCarpetaRoms(rutaFinal);
    if (typeof window.sysNotification === 'function') {
        window.sysNotification("RUTA GUARDADA", `Fijada en ${emuladorActivoRoms.nombre}`, "fa-check-circle");
    }
};

window.iniciarSubidaRomsFTP = async function(input) {
    if (!input.files || input.files.length === 0 || subiendoRomsActivo) return;
    const ip = localStorage.getItem('sebas_ip_final_libre');
    if (!ip) {
        if (typeof window.sysNotification === 'function') window.sysNotification("ERROR", "No hay IP conectada.", "fa-wifi");
        input.value = '';
        return;
    }
    if (!rutaActivaRoms) {
        if (typeof window.sysNotification === 'function') window.sysNotification("AVISO", "Selecciona una carpeta de destino.", "fa-folder");
        input.value = '';
        return;
    }

    colaSubidaRoms = Array.from(input.files);
    totalRomsEnCola = colaSubidaRoms.length;
    subiendoRomsActivo = true;

    const panel = document.getElementById('roms-panel-progreso');
    if (panel) {
        panel.classList.remove('hidden');
        panel.classList.add('flex');
        document.getElementById('btn-pausar-rom-top').classList.remove('hidden');
        document.getElementById('roms-panel-acciones').classList.add('hidden');
        document.getElementById('roms-panel-acciones').classList.remove('flex');
    }

    procesarSiguienteRom();
};

async function procesarSiguienteRom() {
    if (colaSubidaRoms.length === 0) {
        subiendoRomsActivo = false; 
        romActualBlob = null;
        window.mostrarModalExitoRoms();
        return;
    }

    romActualBlob = colaSubidaRoms.shift();
    ejecutarSubidaRomUnica();
}

async function ejecutarSubidaRomUnica() {
    const ip = localStorage.getItem('sebas_ip_final_libre');
    let targetDir = rutaActivaRoms;
    if (!targetDir.endsWith('/')) targetDir += '/';

    romsAbortController = new AbortController();
    
    let offsetBytes = 0;
    
    document.getElementById('roms-txt-cola-status').innerText = `ENVIANDO ROM (${(totalRomsEnCola - colaSubidaRoms.length)}/${totalRomsEnCola})`;
    document.getElementById('roms-txt-nombre-archivo').innerText = romActualBlob.name;
    document.getElementById('roms-txt-porcentaje').innerText = '0%';
    document.getElementById('roms-barra-progreso').style.width = '0%';

    let fdSize = new FormData();
    fdSize.append('action', 'get_file_size');
    fdSize.append('host_ip', ip);
    fdSize.append('file_path', targetDir + romActualBlob.name);
    
    try {
        let resSize = await fetch('api/explorador_api.php', { method: 'POST', body: fdSize });
        let dataSize = await resSize.json();
        
        if (dataSize.status === 'success' && dataSize.size > 0 && dataSize.size < romActualBlob.size) {
            offsetBytes = dataSize.size;
            if (typeof window.sysNotification === 'function') window.sysNotification("REANUDANDO", `Continuando desde ${formatearBytesRoms(offsetBytes)}`, "fa-play");
        } else if (dataSize.size >= romActualBlob.size) {
            procesarSiguienteRom();
            return;
        }
    } catch(e) {}

    let bytesSent = offsetBytes;
    romStartTime = Date.now() - (offsetBytes > 0 ? 1000 : 0); 
    
    let chunkIndex = (offsetBytes > 0) ? 1 : 0; 

    while (offsetBytes < romActualBlob.size) {
        if (romsAbortController.signal.aborted) return;

        let start = offsetBytes;
        let end = Math.min(start + ROMS_CHUNK_SIZE, romActualBlob.size);
        let chunkBlob = romActualBlob.slice(start, end);

        let fd = new FormData();
        fd.append('action', 'upload_explorer_chunk');
        fd.append('host_ip', ip);
        fd.append('chunk_index', chunkIndex); 
        fd.append('filename', romActualBlob.name); 
        fd.append('target_dir', targetDir);
        fd.append('file_chunk', chunkBlob, romActualBlob.name);

        try {
            let res = await fetch('api/explorador_api.php', { method: 'POST', body: fd, signal: romsAbortController.signal });
            let data = await res.json();
            
            if (data.status === 'success') {
                offsetBytes += (end - start);
                bytesSent = offsetBytes;
                actualizarMetricasRomsUI(bytesSent, romActualBlob.size, romStartTime);
                chunkIndex++;
            } else { throw new Error((data && data.message) || "Error al escribir en consola"); }
        } catch (err) {
            if (err.name !== 'AbortError') {
                window.pausarSubidaRom(true, err.message);
            }
            return;
        }
    }
    
    procesarSiguienteRom();
}

function actualizarMetricasRomsUI(bytesSent, totalBytes, startTime) {
    const percent = ((bytesSent / totalBytes) * 100).toFixed(1);
    const elapsedSec = (Date.now() - startTime) / 1000;
    const speedBps = elapsedSec > 0 ? (bytesSent / elapsedSec) : 0;
    const remBytes = totalBytes - bytesSent;
    const remSec = speedBps > 0 ? Math.round(remBytes / speedBps) : 0;

    let etaStr = "--:--";
    if (remSec > 0 && isFinite(remSec)) {
        const m = Math.floor(remSec / 60).toString().padStart(2, '0');
        const s = (remSec % 60).toString().padStart(2, '0');
        etaStr = `${m}:${s}`;
    }

    document.getElementById('roms-txt-porcentaje').innerText = `${percent}%`;
    document.getElementById('roms-barra-progreso').style.width = `${percent}%`;
    document.getElementById('roms-txt-bytes').innerText = `${formatearBytesRoms(bytesSent)} / ${formatearBytesRoms(totalBytes)}`;
    document.getElementById('roms-txt-speed').innerText = `${(speedBps / (1024 * 1024)).toFixed(2)} MB/s`;
    document.getElementById('roms-txt-eta').innerText = `ETA: ${etaStr}`;
}

window.pausarSubidaRom = function(porError = false, errorMsg = "") {
    if (romsAbortController) romsAbortController.abort();
    subiendoRomsActivo = false;
    
    if (romActualBlob) {
        colaSubidaRoms.unshift(romActualBlob);
        romActualBlob = null;
    }
    
    document.getElementById('roms-txt-cola-status').innerText = "Transferencia Pausada";
    document.getElementById('btn-pausar-rom-top').classList.add('hidden');
    document.getElementById('roms-panel-acciones').classList.remove('hidden');
    document.getElementById('roms-panel-acciones').classList.add('flex');

    if (porError && typeof window.sysNotification === 'function') {
        window.sysNotification("ERROR DE TRANSFERENCIA", errorMsg || "Conexión perdida. Transferencia pausada.", "fa-triangle-exclamation");
    } else if (typeof window.sysNotification === 'function') {
        window.sysNotification("PAUSADO", "Transferencia detenida.", "fa-pause");
    }
};

window.reanudarSubidaRom = function() {
    if(typeof window.playSynth === 'function') window.playSynth('select');
    subiendoRomsActivo = true;
    document.getElementById('roms-txt-cola-status').innerText = "Reanudando conexión...";
    document.getElementById('btn-pausar-rom-top').classList.remove('hidden');
    document.getElementById('roms-panel-acciones').classList.add('hidden');
    document.getElementById('roms-panel-acciones').classList.remove('flex');
    
    procesarSiguienteRom();
};

window.cancelarSubidaRomsFTP = function() {
    if (romsAbortController) romsAbortController.abort();
    subiendoRomsActivo = false;
    colaSubidaRoms = [];
    romActualBlob = null;
    if (typeof window.sysNotification === 'function') {
        window.sysNotification("CANCELADO", "Envío de ROM detenido.", "fa-ban");
    }
    window.cerrarPanelSubidaRoms();
};

window.cerrarPanelSubidaRoms = function() {
    const panel = document.getElementById('roms-panel-progreso');
    const input = document.getElementById('roms-input-file');
    if (input) input.value = '';
    if (panel) {
        panel.classList.add('opacity-0');
        setTimeout(() => {
            panel.classList.add('hidden');
            panel.classList.remove('flex', 'opacity-0');
        }, 300);
    }
};
