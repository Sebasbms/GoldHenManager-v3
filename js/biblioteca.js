/**
 * ====================================================================
 * GOLDHENMANAGER 🚀 - DEVELOPED By SeBaS - RUTA: js/biblioteca.js
 * ====================================================================
 */
window.DEFAULT_COVER_IMG = "data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 560%22%3E%3Crect width=%22400%22 height=%22560%22 fill=%22%230a0f1a%22/%3E%3Crect x=%2220%22 y=%2220%22 width=%22360%22 height=%22520%22 rx=%2220%22 fill=%22none%22 stroke=%22%231e293b%22 stroke-width=%224%22 stroke-dasharray=%2210,10%22/%3E%3Cpath d=%22M100,260 C100,200 130,180 180,180 L220,180 C270,180 300,200 300,260 L300,320 C300,380 260,380 240,340 L160,340 C140,380 100,380 100,320 Z%22 fill=%22%23111827%22 stroke=%22%23334155%22 stroke-width=%224%22/%3E%3Ccircle cx=%22140%22 cy=%22250%22 r=%2214%22 fill=%22%23334155%22/%3E%3Ccircle cx=%22140%22 cy=%22290%22 r=%2214%22 fill=%22%23334155%22/%3E%3Ccircle cx=%22120%22 cy=%22270%22 r=%2214%22 fill=%22%23334155%22/%3E%3Ccircle cx=%22160%22 cy=%22270%22 r=%2214%22 fill=%22%23334155%22/%3E%3Ccircle cx=%22260%22 cy=%22250%22 r=%2210%22 fill=%22%23a855f7%22/%3E%3Ccircle cx=%22260%22 cy=%22290%22 r=%2210%22 fill=%22%2310b981%22/%3E%3Ccircle cx=%22240%22 cy=%22270%22 r=%2210%22 fill=%22%23ec4899%22/%3E%3Ccircle cx=%22280%22 cy=%22270%22 r=%2210%22 fill=%22%23f59e0b%22/%3E%3Ctext x=%22200%22 y=%22430%22 font-family=%22sans-serif%22 font-size=%2224%22 font-weight=%22900%22 fill=%22%23334155%22 text-anchor=%22middle%22 letter-spacing=%224%22%3EPS4 TITLE%3C/text%3E%3C/svg%3E";

let catalogoActual = { juegos: [], carpetas: [], ocultos: [], sistema: [] };
let vistaModo = 'grid'; 
let index3DActivo = 0;
let filtroActivo = 'MENU';
let criterioOrdenGlobal = 'nombre_az'; 
let juegoSeleccionadoLocal = null;
let isSyncing = false;
let startX = 0;
let currentX = 0;
let isDragging = false;
let galeriaCacheData = [];
let urlImagenActualLightbox = "";
let nombreImagenActualLightbox = "";
let indexLightboxActual = -1;
let isGlobalGallery = false; 

function initBiblio() {
    const btnSync = document.querySelector('.search-box button');
    if (btnSync) {
        btnSync.innerHTML = '<i class="fa-solid fa-xmark text-lg"></i>';
        btnSync.style.display = 'none'; 
        btnSync.onclick = () => {
            const input = document.getElementById('engine-search');
            if (input) {
                input.value = '';
                ejecutarFiltroGlobal();
                btnSync.style.display = 'none';
            }
        };
    }

    const searchInput = document.getElementById('engine-search');
    if(searchInput) {
        searchInput.classList.remove('pointer-events-none');
        
        searchInput.addEventListener('input', (e) => {
            if (btnSync) {
                if (e.target.value.trim().length > 0) {
                    btnSync.style.display = 'flex';
                } else {
                    btnSync.style.display = 'none';
                }
            }
        });
    }

    const capaBib = document.getElementById('layer-biblioteca');
    if (capaBib) {
        const observadorCapa = new MutationObserver((mutations) => {
            let currentIsActive = capaBib.classList.contains('active');
            if (currentIsActive && !window.biblioWasActive) {
                window.biblioWasActive = true;
                vistaModo = 'grid';
                filtroActivo = 'MENU';
                
                if (searchInput) searchInput.value = "";
                if (btnSync) btnSync.style.display = 'none';
                
                const icono = document.getElementById('icono-vista');
                if (icono) icono.className = 'fa-solid fa-cube text-cyan-400 text-xs';
                const target3D = document.getElementById('dom-3d-target');
                if (target3D) target3D.innerHTML = '';
                index3DActivo = 0;
                
                actualizarInterfazFiltros();
                
                if (typeof window.aplicarTemaBiblioteca === 'function') window.aplicarTemaBiblioteca();
                sincronizarCatalogoSilencioso(); 
            } else if (!currentIsActive) {
                window.biblioWasActive = false;
            }
        });
        observadorCapa.observe(capaBib, { attributes: true, attributeFilter: ['class'] });
        
        if (capaBib.classList.contains('active')) { 
            window.biblioWasActive = true;
            vistaModo = 'grid';
            filtroActivo = 'MENU';
            actualizarInterfazFiltros();
            if (typeof window.aplicarTemaBiblioteca === 'function') window.aplicarTemaBiblioteca();
            sincronizarCatalogoSilencioso(); 
        }
    }
    setupCustomVideoPlayer();
}

if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initBiblio); } 
else { initBiblio(); }

async function sincronizarCatalogoSilencioso() {
    if (isSyncing) return;
    isSyncing = true;
    const ip = localStorage.getItem('sebas_ip_final_libre') || '192.168.1.28';
    const port = localStorage.getItem('sebas_port_libre') || '2121';
    const badgeText = document.getElementById('badge-total-txt');
    if (badgeText) badgeText.innerText = `Sincronizando DB...`;
    
    if (catalogoActual.juegos.length === 0 && catalogoActual.sistema.length === 0) {
        recompilarTodo(); 
    }
    
    try {
        let fd = new FormData();
        fd.append('action', 'sync_catalog');
        fd.append('host_ip', ip);
        fd.append('port', port);
        let response = await fetch('api/library_biblioteca.php', { method: 'POST', body: fd });
        let json = await response.json();
        if (json && json.status === 'success') {
            catalogoActual = json.catalog;
            
            window.listadoJuegos = [];
            catalogoActual.juegos.forEach(j => { window.listadoJuegos.push({...j, is_moddable: true, is_hidden_forced: false}); });
            catalogoActual.carpetas.forEach(c => {
                if(c.items) c.items.forEach(j => { window.listadoJuegos.push({...j, is_moddable: true, is_hidden_forced: false}); });
            });
            catalogoActual.sistema.forEach(j => { window.listadoJuegos.push({...j, is_moddable: true, is_hidden_forced: false}); });
            catalogoActual.ocultos.forEach(j => { window.listadoJuegos.push({...j, is_moddable: false, is_hidden_forced: true}); });
            
            window.listadoJuegos = Array.from(new Set(window.listadoJuegos.map(a => JSON.stringify(a)))).map(a => JSON.parse(a));

            actualizarInterfazFiltros();
            recompilarTodo();
            
            if (json.queue && json.queue.length > 0) {
                procesarColaIconos(json.queue, ip, port);
            }
        } else {
            if (badgeText) badgeText.innerText = `Error DB`;
        }
    } catch (e) {
        if (badgeText) badgeText.innerText = `Desconectado`;
    } finally {
        isSyncing = false;
    }
}

async function procesarColaIconos(queue, ip, port) {
    const chunkSize = 10;
    for (let i = 0; i < queue.length; i += chunkSize) {
        const chunk = queue.slice(i, i + chunkSize);
        await Promise.all(chunk.map(item => {
            let fd = new FormData();
            fd.append('action', 'download_icon');
            fd.append('host_ip', ip);
            fd.append('port', port);
            fd.append('titleId', item.id);
            fd.append('meta', item.meta);
            return fetch('api/library_biblioteca.php', { method: 'POST', body: fd }).catch(e=>e);
        }));
    }
    
    const timestamp = new Date().getTime();
    const updateImg = (j) => {
        if (j.img && !j.img.startsWith('data:')) {
            j.img = j.img.split('?v=')[0] + '?v=' + timestamp;
        }
    };
    
    catalogoActual.juegos.forEach(updateImg);
    catalogoActual.sistema.forEach(updateImg);
    catalogoActual.ocultos.forEach(updateImg);
    catalogoActual.carpetas.forEach(c => { if (c.items) c.items.forEach(updateImg); });
    if (window.listadoJuegos) window.listadoJuegos.forEach(updateImg);
    
    recompilarTodo();
}

function ordenarLista(lista) {
    if (criterioOrdenGlobal === 'nombre_az') { 
        lista.sort((a, b) => a.nombre.localeCompare(b.nombre)); 
    } else if (criterioOrdenGlobal === 'nombre_za') { 
        lista.sort((a, b) => b.nombre.localeCompare(a.nombre)); 
    } else if (criterioOrdenGlobal === 'recientes') { 
        lista.sort((a, b) => b.id.localeCompare(a.id)); 
    }
    return lista;
}

function obtenerVisiblesFiltrados() {
    const searchInput = document.getElementById('engine-search');
    const query = searchInput ? searchInput.value.toLowerCase().trim() : "";

    if (query !== "") {
        let resultados = [];
        catalogoActual.carpetas.forEach(c => {
            if (c.nombre.toLowerCase().includes(query) || c.id.toLowerCase().includes(query)) {
                resultados.push(c);
            }
            if (c.items) {
                c.items.forEach(j => {
                    if (j.nombre.toLowerCase().includes(query) || j.id.toLowerCase().includes(query)) resultados.push(j);
                });
            }
        });
        catalogoActual.juegos.forEach(j => {
            if (j.nombre.toLowerCase().includes(query) || j.id.toLowerCase().includes(query)) resultados.push(j);
        });
        catalogoActual.sistema.forEach(j => {
            if (j.nombre.toLowerCase().includes(query) || j.id.toLowerCase().includes(query)) resultados.push(j);
        });
        catalogoActual.ocultos.forEach(j => {
            if (j.nombre.toLowerCase().includes(query) || j.id.toLowerCase().includes(query)) resultados.push(j);
        });
        
        const unicos = Array.from(new Set(resultados.map(a => JSON.stringify(a)))).map(a => JSON.parse(a));
        return ordenarLista(unicos);
    }

    let listaCarpetas = [];
    let listaJuegos = [];
    let listaSistema = [];

    if (filtroActivo === 'MENU') {
        listaCarpetas = [...catalogoActual.carpetas];
        listaJuegos = [...catalogoActual.juegos];
    } else if (filtroActivo === 'SISTEMA') {
        listaSistema = [...catalogoActual.sistema];
    } else if (filtroActivo === 'OCULTOS') {
        listaJuegos = [...catalogoActual.ocultos];
    } else {
        const carpeta = catalogoActual.carpetas.find(c => c.id === filtroActivo);
        if (carpeta) {
            listaJuegos = [...carpeta.items];
        }
    }

    if (filtroActivo === 'MENU') {
        return [...ordenarLista(listaCarpetas), ...ordenarLista(listaJuegos)];
    } else {
        return ordenarLista([...listaCarpetas, ...listaJuegos, ...listaSistema]);
    }
}

function recompilarTodo() {
    const visibles = obtenerVisiblesFiltrados();
    const badgeText = document.getElementById('badge-total-txt');
    const gridBox = document.getElementById('container-view-grid');
    const box3D = document.getElementById('container-view-3d');
    const gridDom = document.getElementById('dom-grid-target');
    
    if (isSyncing && catalogoActual.juegos.length === 0 && catalogoActual.sistema.length === 0) {
        if(gridBox) gridBox.classList.remove('hidden');
        if(box3D) box3D.classList.add('hidden');
        if (gridDom) {
            gridDom.className = `w-full h-full relative`;
            gridDom.innerHTML = `
                <div class="absolute inset-0 m-auto flex flex-col items-center justify-center h-fit w-fit transform -translate-y-12">
                    <i class="fa-solid fa-satellite-dish animate-pulse text-5xl text-cyan-500 mb-4 drop-shadow-[0_0_15px_rgba(34,211,238,0.5)]"></i>
                    <p class="text-[11px] font-black uppercase tracking-widest text-cyan-400 text-center">Analizando<br>Base de Datos...</p>
                </div>
            `;
        }
        
        const uiBotones3D = document.querySelector('#container-view-3d .grid.grid-cols-4');
        if(uiBotones3D) uiBotones3D.style.display = 'none';
        
        return;
    }

    if (badgeText) badgeText.innerText = `${visibles.length} Elementos`;
    
    if (vistaModo === 'grid') {
        if(gridBox) gridBox.classList.remove('hidden'); 
        if(box3D) box3D.classList.add('hidden');
        renderizarGridDOM(visibles);
    } else {
        if(gridBox) gridBox.classList.add('hidden'); 
        if(box3D) box3D.classList.remove('hidden');
        construirCoverflowDOMEstatico(visibles);
    }
}

function formatearFechaPS4(fechaStr) {
    if (!fechaStr || fechaStr === "0" || fechaStr.length < 5) return "NUNCA";
    try {
        let d = new Date(fechaStr);
        if (isNaN(d.getTime())) return "NUNCA";
        return d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    } catch(e) { return "NUNCA"; }
}

function renderizarGridDOM(visibles) {
    const gridDom = document.getElementById('dom-grid-target');
    if (!gridDom) return;
    gridDom.innerHTML = "";
    
    let cols = parseInt(localStorage.getItem('cfg_sebas_biblio_cols')) || 3;
    gridDom.className = `grid grid-cols-${cols} gap-4 w-full px-1 pb-24`;

    const searchInput = document.getElementById('engine-search');
    const query = searchInput ? searchInput.value.toLowerCase().trim() : "";

    if (visibles.length === 0) { 
        gridDom.innerHTML = `<div class="col-span-full py-12 text-center opacity-40"><p class="text-[9px] uppercase font-black tracking-widest text-gray-400">Sin elementos encontrados</p></div>`; 
        return; 
    }

    if (filtroActivo !== 'MENU' && filtroActivo !== 'SISTEMA' && filtroActivo !== 'OCULTOS' && query === "") {
        const btnBack = document.createElement('div');
        btnBack.className = "game-card tactile-fx relative flex flex-col justify-center items-center bg-white/5 border border-white/10 cursor-pointer shadow-lg";
        btnBack.onclick = () => seleccionarFiltro('MENU');
        btnBack.innerHTML = `<i class="fa-solid fa-arrow-left text-3xl text-gray-400 mb-2"></i><span class="text-[10px] font-black tracking-widest uppercase text-gray-400">Volver</span>`;
        gridDom.appendChild(btnBack);
    }
    
    visibles.forEach((jg) => {
        const divCard = document.createElement('div');
        divCard.className = "game-card tactile-fx relative flex flex-col justify-between animate-fade-in cursor-pointer overflow-hidden";
        
        if (jg.is_folder) {
            divCard.onclick = () => { entrarCarpeta(jg.id, jg.nombre); };
            
            let miniCoversHtml = '';
            if (jg.items && jg.items.length > 0) {
                const maxCovers = Math.min(jg.items.length, 4);
                let coversHtml = '';
                for (let i = 0; i < maxCovers; i++) {
                    coversHtml += `<div class="w-full h-full bg-cover bg-center rounded-md border border-white/5 shadow-inner pointer-events-none" style="background-image: url('${jg.items[i].img}'), url('${window.DEFAULT_COVER_IMG}')"></div>`;
                }
                miniCoversHtml = `
                    <div class="absolute inset-0 flex items-center justify-center p-2 z-10 pointer-events-none">
                        <div class="w-[95%] aspect-square grid ${maxCovers > 1 ? 'grid-cols-2 grid-rows-2 gap-1' : 'grid-cols-1 grid-rows-1'} bg-[#0a0f1a]/80 p-1.5 rounded-[1.2rem] border border-cyan-500/20 shadow-lg backdrop-blur-md">
                            ${coversHtml}
                        </div>
                    </div>
                `;
            } else {
                miniCoversHtml = `<div class="absolute inset-0 flex items-center justify-center z-10 pointer-events-none"><i class="fa-solid fa-folder-open text-white/10 text-6xl drop-shadow-md"></i></div>`;
            }

            divCard.innerHTML = `
                <div class="absolute inset-0 bg-gradient-to-b from-cyan-900/20 to-transparent z-0 pointer-events-none rounded-2xl"></div>
                <div class="game-img-wrap w-full relative flex items-center justify-center bg-[#050810] z-10 border border-white/5 shadow-inner pointer-events-none">
                    ${miniCoversHtml}
                </div>
                <div class="flex items-center justify-between w-full px-1 pt-2 pb-0.5 mt-auto pointer-events-none">
                    <p class="game-title truncate flex-1 text-cyan-400 drop-shadow-md m-0 text-left pointer-events-none">${jg.nombre}</p>
                    <span class="bg-black/60 text-white border border-white/10 text-[7px] font-black px-1.5 py-0.5 rounded-md shadow-md shrink-0 ml-1 leading-none self-end mb-[2px] pointer-events-none">${jg.items ? jg.items.length : 0} ITEMS</span>
                </div>
            `;
        } else {
            divCard.onclick = () => { abrirOpcionesJuegoDirecto(jg); };
            let badge = '';
            if (jg.is_system) { badge = `<span class="absolute top-2 left-2 bg-purple-600/90 text-white text-[7px] font-black px-1.5 py-0.5 rounded-full z-10 border border-purple-400/50 shadow-md pointer-events-none">SYS</span>`; }
            divCard.innerHTML = `
                ${badge}
                <div class="game-img-wrap w-full relative pointer-events-none">
                    <img src="${jg.img}" class="w-full h-full object-cover" onerror="this.src=window.DEFAULT_COVER_IMG">
                </div>
                <p class="game-title truncate w-full m-0 pt-2 pb-0.5 text-center pointer-events-none">${jg.nombre}</p>
            `;
        }
        gridDom.appendChild(divCard);
    });
}

// Vinculación segura de clics
window.seleccionarTarjeta3D = function(domIdx) {
    const visibles = obtenerVisiblesFiltrados();
    const searchInput = document.getElementById('engine-search');
    const query = searchInput ? searchInput.value.toLowerCase().trim() : "";
    const isInsideFolder = (filtroActivo !== 'MENU' && filtroActivo !== 'SISTEMA' && filtroActivo !== 'OCULTOS' && query === "");
    
    if (domIdx !== index3DActivo) { 
        index3DActivo = domIdx; 
        actualizarCoverflow3DStyles(); 
    } else { 
        let clickedJg;
        if (isInsideFolder && domIdx === 0) {
            clickedJg = { is_back_btn: true };
        } else {
            const dataIdx = isInsideFolder ? domIdx - 1 : domIdx;
            clickedJg = visibles[dataIdx];
        }
        
        if (clickedJg) {
            if (clickedJg.is_back_btn) {
                seleccionarFiltro('MENU');
            } else if (clickedJg.is_folder) {
                entrarCarpeta(clickedJg.id, clickedJg.nombre);
            } else {
                abrirOpcionesJuegoDirecto(clickedJg);
            }
        }
    }
};

function construirCoverflowDOMEstatico(visibles) {
    const target3D = document.getElementById('dom-3d-target');
    if (!target3D) return;
    
    const searchInput = document.getElementById('engine-search');
    const query = searchInput ? searchInput.value.toLowerCase().trim() : "";
    const isInsideFolder = (filtroActivo !== 'MENU' && filtroActivo !== 'SISTEMA' && filtroActivo !== 'OCULTOS' && query === "");
    
    const totalCards = isInsideFolder ? visibles.length + 1 : visibles.length;
    
    if (totalCards === 0) {
        target3D.innerHTML = `<div class="text-center mt-10"><i class="fa-solid fa-triangle-exclamation text-4xl text-amber-500 mb-3"></i><p class="text-[10px] text-gray-400 font-mono tracking-widest uppercase">SIN TÍTULOS EN ESTE FILTRO.</p></div>`;
        juegoSeleccionadoLocal = null;
        const uiBotones3D = document.querySelector('#container-view-3d .grid.grid-cols-4');
        if(uiBotones3D) uiBotones3D.style.display = 'none';
        return;
    }
    
    if (index3DActivo >= totalCards) index3DActivo = totalCards - 1;
    if (index3DActivo < 0) index3DActivo = 0;
    
    let html3D = "";
    
    // Función para calcular estilos iniciales y evitar el salto visual
    const getInitialStyle = (idx) => {
        let offset = idx - index3DActivo;
        if (offset < -Math.floor(totalCards / 2)) { offset += totalCards; } 
        else if (offset > Math.floor(totalCards / 2)) { offset -= totalCards; }
        const absOffset = Math.abs(offset);
        if (absOffset === 0) {
            return `transform: translateX(0px) translateZ(100px) rotateY(0deg) scale(1.15); opacity: 1; z-index: 100; pointer-events: auto;`;
        } else if (offset > 0) {
            return `transform: translateX(${145 + (offset - 1) * 35}px) translateZ(${-30 - (offset - 1) * 30}px) rotateY(-40deg) scale(0.85); opacity: ${absOffset > 2 ? '0' : '0.55'}; z-index: ${50 - absOffset}; pointer-events: ${absOffset === 1 ? 'auto' : 'none'};`;
        } else {
            return `transform: translateX(${-145 + (offset + 1) * 35}px) translateZ(${-30 - (absOffset - 1) * 30}px) rotateY(40deg) scale(0.85); opacity: ${absOffset > 2 ? '0' : '0.55'}; z-index: ${50 - absOffset}; pointer-events: ${absOffset === 1 ? 'auto' : 'none'};`;
        }
    };

    let loopIndex = 0;
    
    if (isInsideFolder) {
        const style = getInitialStyle(loopIndex);
        const actClass = loopIndex === index3DActivo ? 'active' : '';
        html3D += `
        <div class="ps4-box-case tactile-fx bg-[#0a0f1a] flex flex-col items-center justify-center border border-white/10 relative overflow-hidden ${actClass}" style="${style}" onclick="window.seleccionarTarjeta3D(${loopIndex})">
            <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent pointer-events-none"></div>
            <i class="fa-solid fa-arrow-left text-white/50 text-6xl drop-shadow-md mb-2 pointer-events-none"></i>
            <span class="text-white/70 text-[10px] font-black uppercase tracking-widest pointer-events-none">VOLVER AL MENÚ</span>
        </div>`;
        loopIndex++;
    }
    
    visibles.forEach((jg) => {
        const style = getInitialStyle(loopIndex);
        const actClass = loopIndex === index3DActivo ? 'active' : '';
        const domIdx = loopIndex;
        
        if (jg.is_folder) {
            let miniCoversHtml = '';
            if (jg.items && jg.items.length > 0) {
                const maxCovers = Math.min(jg.items.length, 4);
                let coversHtml = '';
                for (let i = 0; i < maxCovers; i++) {
                    coversHtml += `<img src="${jg.items[i].img}" class="w-full h-full object-cover rounded-md pointer-events-none" onerror="this.src=window.DEFAULT_COVER_IMG">`;
                }
                miniCoversHtml = `
                    <div class="w-[85%] aspect-square grid ${maxCovers > 1 ? 'grid-cols-2 grid-rows-2 gap-1.5' : 'grid-cols-1 grid-rows-1'} bg-black/60 p-2 rounded-[1.2rem] border border-white/20 shadow-lg backdrop-blur-md mb-2 pointer-events-none">
                        ${coversHtml}
                    </div>
                `;
            } else {
                miniCoversHtml = `<i class="fa-solid fa-folder-open text-white/20 text-6xl drop-shadow-md mb-4 pointer-events-none"></i>`;
            }
            html3D += `<div class="ps4-box-case tactile-fx bg-[#0a0f1a] flex flex-col items-center justify-center border border-cyan-500/30 relative overflow-hidden ${actClass}" style="${style}" onclick="window.seleccionarTarjeta3D(${domIdx})"><div class="absolute inset-0 bg-gradient-to-b from-cyan-900/20 to-transparent pointer-events-none"></div>${miniCoversHtml}<span class="bg-black/80 border border-white/20 text-white text-[9px] font-black px-2 py-0.5 rounded-md shadow-md z-10 pointer-events-none">${jg.items ? jg.items.length : 0} ITEMS</span></div>`;
        } else {
            html3D += `<div class="ps4-box-case tactile-fx ${actClass}" style="${style}" onclick="window.seleccionarTarjeta3D(${domIdx})"><div class="ps4-top-ribbon pointer-events-none"><i class="fa-brands fa-playstation text-white text-[10px]"></i><span class="ps4-ribbon-text">PS4</span></div><img src="${jg.img}" class="w-full h-full object-cover rounded-lg pointer-events-none" onerror="this.src=window.DEFAULT_COVER_IMG"></div>`;
        }
        loopIndex++;
    });
    
    target3D.innerHTML = html3D;
    configurarRastreadorSwipe();
    actualizarCoverflow3DStyles();
}

function actualizarCoverflow3DStyles() {
    const target3D = document.getElementById('dom-3d-target');
    const glowBg = document.getElementById('dom-glow-target');
    const uiBotones3D = document.querySelector('#container-view-3d .grid.grid-cols-4'); 
    
    if (!target3D) return;
    const visibles = obtenerVisiblesFiltrados();
    
    const searchInput = document.getElementById('engine-search');
    const query = searchInput ? searchInput.value.toLowerCase().trim() : "";
    const isInsideFolder = (filtroActivo !== 'MENU' && filtroActivo !== 'SISTEMA' && filtroActivo !== 'OCULTOS' && query === "");
    
    const tarjetas = target3D.querySelectorAll('.ps4-box-case');
    const totalCards = tarjetas.length;
    
    if (totalCards === 0) {
        if(uiBotones3D) uiBotones3D.style.display = 'none';
        return;
    }
    
    if (index3DActivo >= totalCards) index3DActivo = totalCards - 1;
    if (index3DActivo < 0) index3DActivo = 0;
    
    tarjetas.forEach((card, i) => {
        let offset = i - index3DActivo;
        if (offset < -Math.floor(totalCards / 2)) { offset += totalCards; } 
        else if (offset > Math.floor(totalCards / 2)) { offset -= totalCards; }
        const absOffset = Math.abs(offset);
        card.classList.remove('active');
        
        if (absOffset === 0) {
            card.classList.add('active');
            card.style.transform = `translateX(0px) translateZ(100px) rotateY(0deg) scale(1.15)`;
            card.style.opacity = '1';
            card.style.zIndex = '100';
            card.style.pointerEvents = 'auto';
            
            let currentJg;
            if (isInsideFolder && i === 0) {
                currentJg = { id: 'VOLVER AL MENÚ', nombre: 'REGRESAR', is_folder: true, is_back_btn: true };
            } else {
                const dataIdx = isInsideFolder ? i - 1 : i;
                currentJg = visibles[dataIdx];
            }
            
            juegoSeleccionadoLocal = currentJg; 
            
            const tTitle = document.getElementById('text-title-3d');
            const tCusa = document.getElementById('text-cusa-3d');
            
            if (tTitle) tTitle.innerText = currentJg ? currentJg.nombre : '';
            if (tCusa && currentJg) {
                if (currentJg.is_back_btn) {
                    tCusa.innerHTML = '';
                } else if (currentJg.is_folder) {
                    tCusa.innerHTML = `CARPETA`;
                } else {
                    let textHtml = currentJg.id;
                    if (currentJg.size) {
                        textHtml += ` | <span class="text-emerald-500">${currentJg.size}</span>`;
                    }
                    if (currentJg.last_played && currentJg.last_played !== '0') {
                        textHtml += `<span class="text-[8px] text-gray-500 font-medium tracking-widest mt-1 block">JUGADO: ${formatearFechaPS4(currentJg.last_played)}</span>`;
                    }
                    tCusa.innerHTML = textHtml;
                }
                
                // Exterminador de los puntitos residuales solucionado
                if (tCusa.parentElement) {
                    const els = tCusa.parentElement.querySelectorAll('span, div, p');
                    els.forEach(el => {
                        if (el.childNodes.length === 1 && el.firstChild.nodeType === 3 && el.textContent.trim() === '---') {
                            el.style.display = 'none';
                        }
                    });
                }
            }
            
            if (uiBotones3D) {
                if (!currentJg || currentJg.is_folder || currentJg.is_back_btn) {
                    uiBotones3D.style.display = 'none';
                } else {
                    uiBotones3D.style.display = 'grid';
                    actualizarConteoCapturasDock(currentJg.id, 'sheet-count-capturas-badge-3d');
                }
            }
            
            if (glowBg && currentJg) {
                glowBg.style.background = (currentJg.is_folder || currentJg.is_back_btn) 
                    ? `radial-gradient(circle, rgba(234, 179, 8, 0.5) 0%, rgba(202, 138, 4, 0.15) 45%, rgba(0,0,0,0) 70%)` 
                    : `radial-gradient(circle, rgba(0, 162, 255, 0.5) 0%, rgba(0, 114, 230, 0.15) 45%, rgba(0,0,0,0) 70%)`;
            }
            
        } else if (offset > 0) {
            card.style.transform = `translateX(${145 + (offset - 1) * 35}px) translateZ(${-30 - (offset - 1) * 30}px) rotateY(-40deg) scale(0.85)`;
            card.style.opacity = absOffset > 2 ? '0' : '0.55';
            card.style.zIndex = `${50 - absOffset}`;
            card.style.pointerEvents = absOffset === 1 ? 'auto' : 'none';
        } else {
            card.style.transform = `translateX(${-145 + (offset + 1) * 35}px) translateZ(${-30 - (absOffset - 1) * 30}px) rotateY(40deg) scale(0.85)`;
            card.style.opacity = absOffset > 2 ? '0' : '0.55';
            card.style.zIndex = `${50 - absOffset}`;
            card.style.pointerEvents = absOffset === 1 ? 'auto' : 'none';
        }
    });
}

function configurarRastreadorSwipe() {
    const viewPort = document.getElementById('swipe-touch-zone');
    if (!viewPort) return;
    
    if (viewPort.dataset.swipeBound === 'true') return;
    viewPort.dataset.swipeBound = 'true';
    
    viewPort.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; isDragging = true; }, { passive: true });
    viewPort.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        currentX = e.touches[0].clientX;
        const diffX = startX - currentX;
        if (Math.abs(diffX) > 50) {
            const target3D = document.getElementById('dom-3d-target');
            if(!target3D) return;
            const tarjetas = target3D.querySelectorAll('.ps4-box-case');
            const totalCards = tarjetas.length;
            if (totalCards === 0) { isDragging = false; return; }
            
            if (diffX > 0) index3DActivo = (index3DActivo + 1) % totalCards;
            else index3DActivo = (index3DActivo - 1 + totalCards) % totalCards;
            
            actualizarCoverflow3DStyles();
            isDragging = false; 
        }
    }, { passive: true });
    newViewPort.addEventListener('touchend', () => { isDragging = false; });
}

function alternarColumnasGrid() {
    if (typeof window.alternarColumnasBiblioAjustes === 'function') {
        window.alternarColumnasBiblioAjustes();
    } else {
        let cols = parseInt(localStorage.getItem('cfg_sebas_biblio_cols')) || 3;
        cols = cols === 3 ? 2 : 3;
        localStorage.setItem('cfg_sebas_biblio_cols', cols);
        if (typeof window.aplicarTemaBiblioteca === 'function') window.aplicarTemaBiblioteca();
    }
}

function conmutarModoVista() {
    vistaModo = (vistaModo === 'grid') ? '3d' : 'grid';
    const icono = document.getElementById('icono-vista');
    if (icono) icono.className = vistaModo === '3d' ? 'fa-solid fa-table-cells text-purple-400 text-xs' : 'fa-solid fa-cube text-cyan-400 text-xs';
    if (vistaModo === '3d' && window.sysNotification) window.sysNotification("MODO VISTA", "Portadas 3D Interactivas", "fa-cube");
    recompilarTodo();
}

function entrarCarpeta(id, nombre) {
    filtroActivo = id;
    index3DActivo = 0;
    actualizarInterfazFiltros();
    
    const searchInput = document.getElementById('engine-search');
    if (searchInput) searchInput.value = "";
    const btnSync = document.querySelector('.search-box button');
    if (btnSync) btnSync.style.display = 'none';
    
    recompilarTodo();
}

function actualizarInterfazFiltros() {
    const lbl = document.getElementById('label-filtro-actual');
    if (!lbl) return;
    
    if (filtroActivo === 'MENU') {
        lbl.innerText = 'MENÚ PRINCIPAL';
    } else if (filtroActivo === 'SISTEMA') {
        lbl.innerText = 'SISTEMA';
    } else if (filtroActivo === 'OCULTOS') {
        lbl.innerText = 'OCULTOS';
    } else {
        const carpeta = catalogoActual.carpetas.find(c => c.id === filtroActivo);
        lbl.innerText = carpeta ? carpeta.nombre : 'MENÚ PRINCIPAL';
    }
}

function abrirModalFiltro() {
    const container = document.getElementById('lista-filtros-custom');
    if (!container) return;
    container.innerHTML = "";
    
    const opcionesFijas = [
        { id: 'MENU', label: 'Menú Principal', icon: 'fa-tv', color: 'text-cyan-400', bg: 'bg-cyan-500/10', border: 'border-cyan-500/30' },
        { id: 'SISTEMA', label: 'Sistema', icon: 'fa-server', color: 'text-purple-400', bg: 'bg-purple-500/10', border: 'border-purple-500/30' },
        { id: 'OCULTOS', label: 'Ocultos', icon: 'fa-eye-slash', color: 'text-red-400', bg: 'bg-red-500/10', border: 'border-red-500/30' }
    ];

    opcionesFijas.forEach(opt => {
        const isActive = (opt.id === filtroActivo);
        const iconHtml = isActive ? `<i class="fa-solid fa-circle-check ${opt.color} text-lg"></i>` : `<i class="fa-regular fa-circle text-gray-600 text-lg"></i>`;
        const divOption = document.createElement('div');
        divOption.className = `tactile-fx flex items-center justify-between p-3.5 rounded-xl ${isActive ? `${opt.bg} border ${opt.border}` : 'bg-[#111827] border border-white/5'}`;
        divOption.onclick = () => { seleccionarFiltro(opt.id); };
        divOption.innerHTML = `<div class="flex items-center gap-3"><i class="fa-solid ${opt.icon} ${opt.color}"></i><span class="text-[12px] font-black uppercase tracking-widest ${isActive ? opt.color : 'text-gray-300'}">${opt.label}</span></div>${iconHtml}`;
        container.appendChild(divOption);
    });

    if (catalogoActual.carpetas && catalogoActual.carpetas.length > 0) {
        const sep = document.createElement('div');
        sep.className = "w-full h-px bg-white/10 my-2";
        container.appendChild(sep);

        catalogoActual.carpetas.forEach(cat => {
            const isActive = (cat.id === filtroActivo);
            const iconHtml = isActive ? `<i class="fa-solid fa-circle-check text-cyan-400 text-lg"></i>` : `<i class="fa-regular fa-circle text-gray-600 text-lg"></i>`;
            const divOption = document.createElement('div');
            divOption.className = `tactile-fx flex items-center justify-between p-3.5 rounded-xl ${isActive ? 'bg-cyan-500/10 border border-cyan-500/30' : 'bg-[#111827] border border-white/5'}`;
            divOption.onclick = () => { seleccionarFiltro(cat.id); };
            divOption.innerHTML = `<div class="flex items-center gap-3 overflow-hidden"><i class="fa-solid fa-folder text-cyan-500"></i><span class="text-[12px] font-black uppercase tracking-widest ${isActive ? 'text-cyan-400' : 'text-gray-300'} truncate">${cat.nombre}</span></div>${iconHtml}`;
            container.appendChild(divOption);
        });
    }

    const modal = document.getElementById('modal-filtro-categorias');
    if (modal) { 
        modal.classList.remove('hidden'); 
        setTimeout(() => { 
            modal.classList.add('opacity-100'); 
            document.getElementById('modal-filtro-content').classList.remove('translate-y-full'); 
        }, 10); 
    }
}

function cerrarModalFiltro(e) {
    if (e && e.target !== document.getElementById('modal-filtro-categorias')) return;
    document.getElementById('modal-filtro-content').classList.add('translate-y-full');
    const modal = document.getElementById('modal-filtro-categorias');
    if (modal) { modal.classList.remove('opacity-100'); setTimeout(() => modal.classList.add('hidden'), 300); }
}

function seleccionarFiltro(idFiltro) { 
    filtroActivo = idFiltro; 
    index3DActivo = 0; 
    
    const searchInput = document.getElementById('engine-search');
    if (searchInput) searchInput.value = "";
    const btnSync = document.querySelector('.search-box button');
    if (btnSync) btnSync.style.display = 'none';
    
    actualizarInterfazFiltros(); 
    cerrarModalFiltro(); 
    recompilarTodo(); 
}

function abrirModalOrden() {
    const container = document.getElementById('lista-orden-custom');
    if (!container) return;
    container.innerHTML = "";
    const opcionesOrden = [{ id: 'nombre_az', label: 'Nombre A-Z', icon: 'fa-arrow-down-a-z' }, { id: 'nombre_za', label: 'Nombre Z-A', icon: 'fa-arrow-up-z-a' }, { id: 'recientes', label: 'Title ID', icon: 'fa-fingerprint' }];
    opcionesOrden.forEach(opt => {
        const isActive = (opt.id === criterioOrdenGlobal);
        const iconSelect = isActive ? `<i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>` : `<i class="fa-regular fa-circle text-gray-600 text-lg"></i>`;
        const divOption = document.createElement('div');
        divOption.className = `tactile-fx flex items-center justify-between p-4 rounded-xl ${isActive ? 'bg-emerald-500/10 border border-emerald-500/30' : 'bg-[#111827] border border-white/5'}`;
        divOption.onclick = () => { seleccionarOrden(opt.id, opt.label, opt.icon); };
        divOption.innerHTML = `<div class="flex items-center gap-3"><i class="fa-solid ${opt.icon} ${isActive ? 'text-emerald-400' : 'text-gray-500'}"></i><span class="text-[12px] font-black uppercase tracking-widest ${isActive ? 'text-emerald-400' : 'text-gray-300'}">${opt.label}</span></div>${iconSelect}`;
        container.appendChild(divOption);
    });
    const modal = document.getElementById('modal-orden-custom');
    if (modal) { modal.classList.remove('hidden'); setTimeout(() => { modal.classList.add('opacity-100'); document.getElementById('modal-orden-content').classList.remove('translate-y-full'); }, 10); }
}

function cerrarModalOrden(e) {
    if (e && e.target !== document.getElementById('modal-orden-custom')) return;
    document.getElementById('modal-orden-content').classList.add('translate-y-full');
    const modal = document.getElementById('modal-orden-custom');
    if (modal) { modal.classList.remove('opacity-100'); setTimeout(() => modal.classList.add('hidden'), 300); }
}

function seleccionarOrden(id, label, iconClass) {
    criterioOrdenGlobal = id;
    document.getElementById('label-orden-actual').innerText = label;
    document.getElementById('icono-orden-actual').className = `fa-solid ${iconClass} text-[10px] shrink-0 opacity-80`;
    index3DActivo = 0;
    cerrarModalOrden();
    recompilarTodo();
}

function ejecutarFiltroGlobal() { recompilarTodo(); }

async function actualizarConteoCapturasDock(cusa, targetBadgeId = 'sheet-count-capturas-badge') {
    const ip = localStorage.getItem('sebas_ip_final_libre') || '192.168.1.28';
    const badge = document.getElementById(targetBadgeId);
    if (!badge) return;
    badge.classList.add('hidden'); 
    try {
        let fd = new FormData();
        fd.append('action', 'count_only');
        fd.append('host_ip', ip);
        fd.append('cusa_id', cusa);
        let res = await fetch('api/ps4_screenshots_api.php', { method: 'POST', body: fd });
        let data = await res.json();
        if(juegoSeleccionadoLocal && juegoSeleccionadoLocal.id === cusa && data && data.status === 'success' && data.count > 0) {
            badge.innerText = data.count;
            badge.classList.remove('hidden');
        }
    } catch(e) {}
}

async function ejecutarAccionRapidaJuego(accion) {
    if (!juegoSeleccionadoLocal) return;
    if (accion === 'editar_portada') {
        if (window.sysNotification) window.sysNotification("MODDING", `Abriendo editor para ${juegoSeleccionadoLocal.nombre}...`, "fa-wand-magic-sparkles");
        localStorage.setItem('modding_cusa_activo', juegoSeleccionadoLocal.id);
        cerrarBottomSheet(null);
        setTimeout(() => { 
            if (typeof window.abrirModuloNativo === 'function') { window.abrirModuloNativo('modding'); } 
            else if (typeof window.abrirModulo === 'function') { window.abrirModulo('modding'); }
        }, 300);
    } 
    else if (accion === 'saves') { 
        if (window.sysNotification) { window.sysNotification("PRÓXIMAMENTE", "Gestor de partidas.", "fa-clock"); } 
        cerrarBottomSheet(null);
    } 
    else if (accion === 'galeria') { cerrarBottomSheet(null); abrirGaleriaJuego(); } 
    else if (accion === 'dlcs') { cerrarBottomSheet(null); abrirModalDLC(); }
}

function abrirOpcionesDesde3D() { 
    if (!juegoSeleccionadoLocal) return;
    
    if (juegoSeleccionadoLocal.is_back_btn) {
        seleccionarFiltro('MENU');
    } else if (juegoSeleccionadoLocal.is_folder) {
        entrarCarpeta(juegoSeleccionadoLocal.id, juegoSeleccionadoLocal.nombre);
    } else {
        abrirOpcionesJuegoDirecto(juegoSeleccionadoLocal); 
    }
}

function abrirOpcionesJuegoDirecto(juegoObj) {
    juegoSeleccionadoLocal = juegoObj;
    if (!juegoSeleccionadoLocal) return;
    document.getElementById('panel-game-title').innerText = juegoSeleccionadoLocal.nombre;
    
    const cusaEl = document.getElementById('panel-game-cusa');
    if (cusaEl) {
        let lastPlayedHtml = '';
        if (juegoSeleccionadoLocal.last_played && juegoSeleccionadoLocal.last_played !== '0') {
            lastPlayedHtml = `<br><span class="text-[9px] text-gray-500 font-medium tracking-widest mt-1 inline-block">JUGADO: ${formatearFechaPS4(juegoSeleccionadoLocal.last_played)}</span>`;
        }
        let sizeHtml = '';
        if (juegoSeleccionadoLocal.size) {
            sizeHtml = ` | <span class="text-emerald-500">${juegoSeleccionadoLocal.size}</span>`;
        }
        cusaEl.innerHTML = `${juegoSeleccionadoLocal.id}${sizeHtml}${lastPlayedHtml}`;
        
        if (cusaEl.parentElement && cusaEl.parentElement.parentElement) {
            const allElements = cusaEl.parentElement.parentElement.querySelectorAll('*');
            allElements.forEach(el => {
                if (el.childNodes.length === 1 && el.firstChild.nodeType === 3 && el.textContent.trim() === '---') {
                    el.style.display = 'none';
                }
            });
        }
    }
    
    let etiquetaCategoria = filtroActivo;
    if (filtroActivo === 'MENU') etiquetaCategoria = 'Menú Principal';
    else if (filtroActivo !== 'SISTEMA' && filtroActivo !== 'OCULTOS') {
        const carpeta = catalogoActual.carpetas.find(c => c.id === filtroActivo);
        if (carpeta) etiquetaCategoria = carpeta.nombre;
    }
    const labelCat = document.getElementById('label-categoria-actual');
    if (labelCat) labelCat.innerText = etiquetaCategoria;
    
    const panelBg = document.getElementById('panel-bg-blur');
    if(panelBg) panelBg.style.backgroundImage = `url('${juegoSeleccionadoLocal.img}')`;
    const avatarArt = document.getElementById('panel-avatar-art');
    if (avatarArt) avatarArt.style.backgroundImage = `url('${juegoSeleccionadoLocal.img}'), url('${window.DEFAULT_COVER_IMG}')`;
    actualizarConteoCapturasDock(juegoSeleccionadoLocal.id, 'sheet-count-capturas-badge');
    const sheet = document.getElementById('sheet-detalles-juego');
    if (sheet) { sheet.classList.remove('hidden'); setTimeout(() => { sheet.classList.add('opacity-100'); const cardContent = document.getElementById('sheet-content-card'); if (cardContent) cardContent.classList.remove('translate-y-full'); }, 10); }
}

function cerrarBottomSheet(e) {
    if (e && e.target !== document.getElementById('sheet-detalles-juego')) return;
    const cardContent = document.getElementById('sheet-content-card');
    if (cardContent) cardContent.classList.add('translate-y-full');
    const sheet = document.getElementById('sheet-detalles-juego');
    if (sheet) { sheet.classList.remove('opacity-100'); setTimeout(() => sheet.classList.add('hidden'), 300); }
}

function alternarColumnasGaleria() {
    let cols = parseInt(localStorage.getItem('cfg_sebas_galeria_cols')) || 3;
    cols = cols === 3 ? 2 : 3;
    localStorage.setItem('cfg_sebas_galeria_cols', cols);
    if (galeriaCacheData && galeriaCacheData.length > 0) { renderizarGaleriaGrid({ images: galeriaCacheData }); } 
    else { const iconBtn = document.getElementById('icono-columnas-galeria'); if (iconBtn) { iconBtn.className = cols === 2 ? "fa-solid fa-list text-xs" : "fa-solid fa-border-all text-xs"; } }
}

function renderizarGaleriaGrid(data) {
    const grid = document.getElementById('galeria-grid');
    const cols = parseInt(localStorage.getItem('cfg_sebas_galeria_cols')) || 3;
    grid.className = `grid grid-cols-${cols} gap-4 w-full px-1`;
    
    const iconBtn = document.getElementById('icono-columnas-galeria');
    if (iconBtn) { iconBtn.className = cols === 2 ? "fa-solid fa-list text-xs" : "fa-solid fa-border-all text-xs"; }

    document.getElementById('galeria-count').innerText = `${data.images.length} Archivos`;
    let htmlGrid = "";
    data.images.forEach((img, idx) => {
        const isVid = img.type === 'video';
        const iconHTML = isVid ? `<div class="w-10 h-10 rounded-full bg-cyan-500/20 backdrop-blur-md flex items-center justify-center text-cyan-400 border border-cyan-500/30 shadow-[0_0_15px_rgba(34,211,238,0.5)]"><i class="fa-solid fa-play ml-1"></i></div>` : '';
        const badgeClass = isVid ? "bg-cyan-600/80 text-cyan-100 border-cyan-400/50" : "bg-emerald-600/80 text-emerald-100 border-emerald-400/50";
        htmlGrid += `
        <div onclick="abrirLightbox(${idx})" class="tactile-fx relative w-full aspect-video rounded-xl overflow-hidden bg-[#0a0f1a] border border-white/5 shadow-lg cursor-pointer group">
            <img src="${img.thumb_url}" loading="lazy" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex items-center justify-center">${iconHTML}</div>
            <span class="absolute bottom-2 right-2 text-[8px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded backdrop-blur-md border shadow-md ${badgeClass} z-10">${isVid ? 'MP4' : 'IMG'}</span>
        </div>`;
    });
    grid.innerHTML = htmlGrid;
    grid.classList.remove('hidden');
}

function abrirGaleriaGlobal() {
    isGlobalGallery = true;
    juegoSeleccionadoLocal = null; 
    document.getElementById('galeria-title').innerText = "MULTIMEDIA GLOBAL";
    document.getElementById('galeria-count').innerText = "Conectando...";
    document.getElementById('galeria-grid').innerHTML = '';
    document.getElementById('galeria-grid').classList.add('hidden');
    document.getElementById('galeria-loader').classList.remove('hidden');
    const modal = document.getElementById('modal-galeria-juego');
    if (modal) { modal.classList.remove('hidden'); setTimeout(() => { modal.classList.remove('translate-y-full'); modal.classList.add('opacity-100'); }, 10); }
    cargarFotosGaleriaGlobal(false);
}

async function cargarFotosGaleriaGlobal(force = false) {
    const ip = localStorage.getItem('sebas_ip_final_libre') || '192.168.1.28';
    try {
        let fd = new FormData();
        fd.append('action', 'get_all_caps');
        fd.append('host_ip', ip);
        fd.append('force', '0'); 
        let res = await fetch('api/ps4_screenshots_api.php', { method: 'POST', body: fd });
        let data = await res.json();
        document.getElementById('galeria-loader').classList.add('hidden');
        if (data && data.status === 'success' && data.images && data.images.length > 0) {
            galeriaCacheData = data.images;
            renderizarGaleriaGrid(data);
        } else if (!force) {
            document.getElementById('galeria-loader').classList.remove('hidden');
        }
        if (!force && data.status === 'success') {
            let fdSilent = new FormData();
            fdSilent.append('action', 'get_all_caps');
            fdSilent.append('host_ip', ip);
            fdSilent.append('force', '1');
            fetch('api/ps4_screenshots_api.php', { method: 'POST', body: fdSilent })
            .then(r => r.json())
            .then(newData => {
                if(newData.images && newData.images.length !== galeriaCacheData.length) {
                    galeriaCacheData = newData.images;
                    document.getElementById('galeria-loader').classList.add('hidden');
                    renderizarGaleriaGrid(newData);
                }
            }).catch(()=>{});
        }
    } catch(e) {
        document.getElementById('galeria-loader').classList.add('hidden');
        document.getElementById('galeria-count').innerText = "Error";
    }
}

function abrirGaleriaJuego() {
    if (!juegoSeleccionadoLocal) return;
    isGlobalGallery = false; 
    document.getElementById('galeria-title').innerText = juegoSeleccionadoLocal.nombre;
    document.getElementById('galeria-count').innerText = "Conectando...";
    document.getElementById('galeria-grid').innerHTML = '';
    document.getElementById('galeria-grid').classList.add('hidden');
    document.getElementById('galeria-loader').classList.remove('hidden');
    const modal = document.getElementById('modal-galeria-juego');
    if (modal) { modal.classList.remove('hidden'); setTimeout(() => { modal.classList.remove('translate-y-full'); modal.classList.add('opacity-100'); }, 10); }
    cargarFotosGaleria(false); 
}

function cerrarGaleriaJuego() {
    const modal = document.getElementById('modal-galeria-juego');
    if (modal) { modal.classList.remove('opacity-100'); modal.classList.add('translate-y-full'); setTimeout(() => { modal.classList.add('hidden'); }, 300); }
}

async function cargarFotosGaleria(force = false) {
    const ip = localStorage.getItem('sebas_ip_final_libre') || '192.168.1.28';
    try {
        let fd = new FormData();
        fd.append('action', 'get_caps');
        fd.append('host_ip', ip);
        fd.append('cusa_id', juegoSeleccionadoLocal.id);
        fd.append('force', '0'); 
        let res = await fetch('api/ps4_screenshots_api.php', { method: 'POST', body: fd });
        let data = await res.json();
        document.getElementById('galeria-loader').classList.add('hidden');
        if (data && data.status === 'success' && data.images && data.images.length > 0) {
            galeriaCacheData = data.images;
            renderizarGaleriaGrid(data);
        } else if (!force) {
            document.getElementById('galeria-loader').classList.remove('hidden');
        }
        if (!force && data.status === 'success') {
            let fdSilent = new FormData();
            fdSilent.append('action', 'get_caps');
            fdSilent.append('host_ip', ip);
            fdSilent.append('cusa_id', juegoSeleccionadoLocal.id);
            fdSilent.append('force', '1');
            fetch('api/ps4_screenshots_api.php', { method: 'POST', body: fdSilent })
            .then(r => r.json())
            .then(newData => {
                if(newData.images && newData.images.length !== galeriaCacheData.length) {
                    galeriaCacheData = newData.images;
                    document.getElementById('galeria-loader').classList.add('hidden');
                    renderizarGaleriaGrid(newData);
                }
            }).catch(()=>{});
        }
    } catch(e) {
        document.getElementById('galeria-loader').classList.add('hidden');
        document.getElementById('galeria-count').innerText = "Error";
    }
}

function forzarRecargaGaleria() {
    document.getElementById('galeria-grid').classList.add('hidden');
    document.getElementById('galeria-loader').classList.remove('hidden');
    if (isGlobalGallery) { cargarFotosGaleriaGlobal(true); } else { cargarFotosGaleria(true); }
}

function setupCustomVideoPlayer() {
    const video = document.getElementById('lightbox-video');
    const playBtn = document.getElementById('video-play-btn');
    const muteBtn = document.getElementById('video-mute-btn');
    const fullBtn = document.getElementById('video-fullscreen-btn');
    const progBar = document.getElementById('video-progress');
    const curTime = document.getElementById('video-current-time');
    const durTime = document.getElementById('video-duration');
    const controls = document.getElementById('video-custom-controls');
    if(!video || !playBtn) return;
    function formatTime(seconds) {
        if(isNaN(seconds)) return "00:00";
        const m = Math.floor(seconds / 60);
        const s = Math.floor(seconds % 60);
        return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    }
    video.addEventListener('loadedmetadata', () => {
        progBar.max = video.duration;
        durTime.innerText = formatTime(video.duration);
    });
    video.addEventListener('timeupdate', () => {
        if (!progBar.getAttribute('data-seeking')) { progBar.value = video.currentTime; }
        curTime.innerText = formatTime(video.currentTime);
        if(!isNaN(video.duration)) durTime.innerText = formatTime(video.duration);
    });
    progBar.addEventListener('input', () => {
        progBar.setAttribute('data-seeking', 'true');
        curTime.innerText = formatTime(progBar.value);
    });
    progBar.addEventListener('change', () => {
        video.currentTime = progBar.value;
        progBar.removeAttribute('data-seeking');
    });
    const togglePlay = () => {
        if (video.paused) { video.play(); playBtn.innerHTML = '<i class="fa-solid fa-pause text-xl"></i>'; }
        else { video.pause(); playBtn.innerHTML = '<i class="fa-solid fa-play text-xl"></i>'; }
    };
    playBtn.onclick = togglePlay;
    video.onclick = (e) => { e.stopPropagation(); togglePlay(); };
    muteBtn.onclick = () => {
        video.muted = !video.muted;
        muteBtn.innerHTML = video.muted ? '<i class="fa-solid fa-volume-xmark text-sm"></i>' : '<i class="fa-solid fa-volume-high text-sm"></i>';
    };
    fullBtn.onclick = () => {
        if (video.requestFullscreen) { video.requestFullscreen(); }
        else if (video.webkitRequestFullscreen) { video.webkitRequestFullscreen(); }
    };
}

function abrirLightbox(index) {
    const visor = document.getElementById('lightbox-visor');
    const modal = document.getElementById('lightbox-modal');
    const imgElement = document.getElementById('lightbox-img');
    const videoElement = document.getElementById('lightbox-video');
    const nameLabel = document.getElementById('lightbox-name');
    const controls = document.getElementById('video-custom-controls');
    
    if (visor && imgElement && nameLabel && videoElement) {
        indexLightboxActual = index;
        const media = galeriaCacheData[index];
        urlImagenActualLightbox = media.url;
        nombreImagenActualLightbox = media.name;
        nameLabel.innerText = nombreImagenActualLightbox;

        if (media.type === 'video') {
            imgElement.classList.add('hidden');
            videoElement.classList.add('hidden');
            controls.classList.add('hidden');
            videoElement.pause();
            videoElement.src = "";
            document.getElementById('video-play-btn').innerHTML = '<i class="fa-solid fa-pause text-xl"></i>';
            nameLabel.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Cargando Búfer...';
            
            visor.classList.remove('hidden');
            setTimeout(() => { 
                visor.classList.add('opacity-100'); 
                modal.classList.remove('scale-95');
                modal.classList.add('scale-100');
            }, 10);

            const ip = localStorage.getItem('sebas_ip_final_libre');
            fetch(`api/stream_image_api.php?ip=${ip}&path=${encodeURIComponent(media.remote_path)}&buffer=1`)
            .then(() => {
                setTimeout(() => {
                    nameLabel.innerText = nombreImagenActualLightbox;
                    videoElement.src = urlImagenActualLightbox;
                    videoElement.classList.remove('hidden');
                    controls.classList.remove('hidden', 'opacity-0');
                    videoElement.play().catch(e=>console.log(e));
                }, 2500);
            }).catch(() => {});
        } else {
            videoElement.classList.add('hidden');
            controls.classList.add('hidden');
            videoElement.pause();
            imgElement.classList.remove('hidden');
            imgElement.src = urlImagenActualLightbox;
            nameLabel.innerText = nombreImagenActualLightbox;

            visor.classList.remove('hidden');
            setTimeout(() => { 
                visor.classList.add('opacity-100'); 
                modal.classList.remove('scale-95');
                modal.classList.add('scale-100');
            }, 10);
        }
    }
}

function cerrarLightbox() {
    const visor = document.getElementById('lightbox-visor');
    const modal = document.getElementById('lightbox-modal');
    const imgElement = document.getElementById('lightbox-img');
    const videoElement = document.getElementById('lightbox-video');
    const controls = document.getElementById('video-custom-controls');
    
    if (visor) {
        visor.classList.remove('opacity-100');
        if (modal) {
            modal.classList.remove('scale-100');
            modal.classList.add('scale-95');
        }
        if (videoElement) { 
            videoElement.pause(); 
            videoElement.src = ""; 
        }
        if (controls) { controls.classList.add('hidden'); }
        
        setTimeout(() => { 
            visor.classList.add('hidden'); 
            if(imgElement) imgElement.src = ""; 
            indexLightboxActual = -1;
        }, 300);
    }
}

function borrarMediaLightbox() {
    if (indexLightboxActual === -1) return;
    const modal = document.getElementById('modal-confirmar-borrar-galeria');
    const content = document.getElementById('modal-content-borrar-galeria');
    if (modal && content) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.add('opacity-100');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }
}

function cerrarModalBorrarGaleria() {
    const modal = document.getElementById('modal-confirmar-borrar-galeria');
    const content = document.getElementById('modal-content-borrar-galeria');
    if (modal && content) {
        modal.classList.remove('opacity-100');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }
}

async function ejecutarBorradoMediaConfirmado() {
    if (indexLightboxActual === -1) { cerrarModalBorrarGaleria(); return; }
    const media = galeriaCacheData[indexLightboxActual];
    const ip = localStorage.getItem('sebas_ip_final_libre');
    if (!ip) { cerrarModalBorrarGaleria(); return; }

    try {
        let fd = new FormData();
        fd.append('action', 'delete_capture');
        fd.append('host_ip', ip);
        
        if (media.remote_path) {
            fd.append('remote_path', media.remote_path);
        } else {
            fd.append('local_url', media.url);
        }

        let res = await fetch('api/ps4_screenshots_api.php', { method: 'POST', body: fd });
        let data = await res.json();

        cerrarModalBorrarGaleria();

        if (data.status === 'success') {
            if (window.sysNotification) window.sysNotification("BORRADO", "Archivo eliminado.", "fa-trash-can");
            cerrarLightbox();
            forzarRecargaGaleria();
        } else {
            if (window.sysNotification) window.sysNotification("ERROR", "No se pudo borrar.", "fa-times");
        }
    } catch(e) {
        cerrarModalBorrarGaleria();
        if (window.sysNotification) window.sysNotification("ERROR", "Fallo de conexión.", "fa-wifi");
    }
}

async function compartirImagenLightbox() {
    if (!urlImagenActualLightbox) return;
    
    try {
        if (window.sysNotification) window.sysNotification("PREPARANDO", "Preparando archivo para enviar...", "fa-share-nodes");
        
        let fetchUrl = urlImagenActualLightbox;
        if (!fetchUrl.startsWith('http') && !fetchUrl.startsWith('blob')) {
            fetchUrl = window.location.origin + window.location.pathname.replace(/[^/]*$/, '') + urlImagenActualLightbox;
        }
        
        const response = await fetch(fetchUrl);
        if (!response.ok) throw new Error("Fallo en la respuesta del servidor");
        
        const blob = await response.blob();
        const media = galeriaCacheData[indexLightboxActual];
        
        let mimeType = blob.type;
        let finalName = nombreImagenActualLightbox.trim().replace(/[^a-zA-Z0-9.\-_]/g, '_');

        if (media.type === 'video') {
            mimeType = 'video/mp4';
            if (!finalName.toLowerCase().endsWith('.mp4')) finalName += '.mp4';
        } else {
            if (!mimeType || mimeType === 'text/html' || mimeType === 'text/plain') {
                mimeType = finalName.toLowerCase().endsWith('.png') ? 'image/png' : 'image/jpeg';
            }
            if (!finalName.toLowerCase().match(/\.(jpg|jpeg|png)$/)) finalName += '.jpg';
        }

        const file = new File([blob], finalName, { type: mimeType });
        
        if (navigator.canShare && navigator.canShare({ files: [file] })) {
            await navigator.share({ 
                title: finalName, 
                text: `Multimedia de ${juegoSeleccionadoLocal ? juegoSeleccionadoLocal.nombre : 'PS4'}`, 
                files: [file] 
            });
        } else {
            throw new Error("Navegador no soporta compartir este archivo");
        }
    } catch (err) {
        if (err.name === 'AbortError') return; 
        
        if (window.sysNotification) window.sysNotification("DESCARGANDO", "Guardando archivo localmente...", "fa-download");
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = urlImagenActualLightbox;
        a.download = nombreImagenActualLightbox.trim().replace(/[^a-zA-Z0-9.\-_]/g, '_');
        document.body.appendChild(a);
        a.click();
        setTimeout(() => { document.body.removeChild(a); }, 100);
    }
}

let contenidoAEliminarData = null; 

function abrirModalDLC() {
    if (!juegoSeleccionadoLocal) return;
    
    document.getElementById('dlc-game-title').innerText = juegoSeleccionadoLocal.nombre;
    document.getElementById('dlc-game-cusa').innerText = juegoSeleccionadoLocal.id;
    
    const panelBg = document.getElementById('dlc-bg-blur');
    if(panelBg) panelBg.style.backgroundImage = `url('${juegoSeleccionadoLocal.img}')`;

    document.getElementById('dlc-data-container').classList.add('hidden');
    document.getElementById('dlc-loader').classList.remove('hidden');

    const modal = document.getElementById('modal-dlc-update');
    if (modal) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.add('opacity-100');
            const content = document.getElementById('modal-dlc-content');
            if (content) {
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }
        }, 10);
    }

    escanearDLCsYUpdates(false);
}

function cerrarModalDLC() {
    const modal = document.getElementById('modal-dlc-update');
    const content = document.getElementById('modal-dlc-content');
    if (modal && content) {
        modal.classList.remove('opacity-100');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }
}

function forzarRecargaDLCs() {
    document.getElementById('dlc-data-container').classList.add('hidden');
    document.getElementById('dlc-loader').classList.remove('hidden');
    escanearDLCsYUpdates(true);
}

async function escanearDLCsYUpdates(force = false) {
    const ip = localStorage.getItem('sebas_ip_final_libre') || '192.168.1.28';
    try {
        let fd = new FormData();
        fd.append('action', 'scan');
        fd.append('host_ip', ip);
        fd.append('cusa_id', juegoSeleccionadoLocal.id);
        fd.append('force', force ? '1' : '0');
        
        let res = await fetch('api/dlc_update_biblioteca.php', { method: 'POST', body: fd });
        let data = await res.json();
        
        document.getElementById('dlc-loader').classList.add('hidden');
        document.getElementById('dlc-data-container').classList.remove('hidden');
        
        if (data && data.status === 'success') {
            const updateBox = document.getElementById('dlc-update-box');
            
            if (data.update.installed) {
                const iconLoc = data.update.location.includes('Ampliado') ? '<i class="fa-solid fa-server text-emerald-500"></i>' : '<i class="fa-solid fa-hdd text-gray-400"></i>';
                updateBox.innerHTML = `
                    <div class="flex items-center flex-1 gap-4 overflow-hidden">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20 shrink-0">
                            <i class="fa-solid fa-check text-emerald-400 text-lg"></i>
                        </div>
                        <div class="flex flex-col flex-1 truncate">
                            <span class="text-[12px] font-black uppercase tracking-widest text-emerald-400">Parche / Update</span>
                            <span class="text-[10px] text-gray-400 mt-0.5 flex items-center gap-1.5">${iconLoc} ${data.update.size_label}</span>
                        </div>
                    </div>
                    <button onclick="prepararEliminacionContenido('${data.update.path}', 'Update Parche')" class="tactile-fx w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500 border border-red-500/20 shrink-0">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                `;
            } else {
                updateBox.innerHTML = `
                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center border border-white/10 shrink-0">
                        <i class="fa-solid fa-minus text-gray-500 text-lg"></i>
                    </div>
                    <div class="flex flex-col flex-1">
                        <span class="text-[12px] font-black uppercase tracking-widest text-gray-400">Sin Update</span>
                        <span class="text-[10px] text-gray-500 mt-0.5">Versión Base (v1.00)</span>
                    </div>
                `;
            }

            const dlcBox = document.getElementById('dlc-list-box');
            const badge = document.getElementById('dlc-count-badge');
            badge.innerText = data.dlcs.length;
            
            if (data.dlcs.length > 0) {
                let dlcHtml = '';
                data.dlcs.forEach(dlc => {
                    const iconLoc = dlc.location.includes('Ampliado') ? '<i class="fa-solid fa-server text-blue-400"></i>' : '<i class="fa-solid fa-hdd text-gray-400"></i>';
                    dlcHtml += `
                    <div class="tactile-fx flex items-center gap-3 p-3 rounded-xl bg-[#111827] border border-white/5 hover:border-blue-500/30 transition-colors">
                        <div class="flex items-center flex-1 gap-3 overflow-hidden">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center border border-blue-500/20 shrink-0">
                                <i class="fa-solid fa-puzzle-piece text-blue-400 text-sm"></i>
                            </div>
                            <div class="flex flex-col flex-1 overflow-hidden">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-200 truncate">${dlc.id}</span>
                                <span class="text-[8px] text-gray-500 mt-0.5 flex items-center gap-1">${iconLoc} ${dlc.size_label}</span>
                            </div>
                        </div>
                        <button onclick="prepararEliminacionContenido('${dlc.path}', '${dlc.id}')" class="tactile-fx w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center text-red-500 border border-red-500/20 shrink-0">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>`;
                });
                dlcBox.innerHTML = dlcHtml;
            } else {
                dlcBox.innerHTML = `
                    <div class="p-4 rounded-xl border border-dashed border-white/10 text-center">
                        <span class="text-[10px] font-black tracking-widest text-gray-500 uppercase">Ningún DLC detectado</span>
                    </div>
                `;
            }

        } else {
            if (window.sysNotification) window.sysNotification("ERROR", "Fallo al leer la consola", "fa-triangle-exclamation");
        }
    } catch(e) {
        document.getElementById('dlc-loader').classList.add('hidden');
        if (window.sysNotification) window.sysNotification("ERROR", "No hay conexión con FTP", "fa-wifi");
    }
}

function prepararEliminacionContenido(path, nombreVisual) {
    contenidoAEliminarData = { path: path, nombre: nombreVisual };
    document.getElementById('nombre-contenido-eliminar').innerText = nombreVisual;
    
    const modal = document.getElementById('modal-confirmar-eliminar-contenido');
    const content = document.getElementById('modal-content-eliminar-contenido');
    
    if (modal && content) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.add('opacity-100');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }
}

function cerrarModalEliminarContenido() {
    const modal = document.getElementById('modal-confirmar-eliminar-contenido');
    const content = document.getElementById('modal-content-eliminar-contenido');
    if (modal && content) {
        modal.classList.remove('opacity-100');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        setTimeout(() => { 
            modal.classList.add('hidden'); 
            contenidoAEliminarData = null; 
            const iconBtn = document.getElementById('icono-btn-eliminar');
            if (iconBtn) iconBtn.className = "fa-solid fa-skull";
        }, 300);
    }
}

async function ejecutarEliminacionContenidoFTP() {
    if (!contenidoAEliminarData) return;
    
    const iconBtn = document.getElementById('icono-btn-eliminar');
    if (iconBtn) iconBtn.className = "fa-solid fa-spinner fa-spin";

    const ip = localStorage.getItem('sebas_ip_final_libre') || '192.168.1.28';
    
    try {
        let fd = new FormData();
        fd.append('action', 'delete_content');
        fd.append('host_ip', ip);
        fd.append('cusa_id', juegoSeleccionadoLocal.id);
        fd.append('target_path', contenidoAEliminarData.path);
        
        let res = await fetch('api/dlc_update_biblioteca.php', { method: 'POST', body: fd });
        let data = await res.json();
        
        cerrarModalEliminarContenido();
        
        if (data && data.status === 'success') {
            if (window.sysNotification) window.sysNotification("SISTEMA", `Contenido eliminado con éxito de la consola.`, "fa-trash-can");
            document.getElementById('dlc-data-container').classList.add('hidden');
            document.getElementById('dlc-loader').classList.remove('hidden');
            setTimeout(() => { escanearDLCsYUpdates(true); }, 500); 
        } else {
            if (window.sysNotification) window.sysNotification("ERROR", "No se pudo borrar por permisos de FTP.", "fa-circle-xmark");
        }
    } catch(e) {
        cerrarModalEliminarContenido();
        if (window.sysNotification) window.sysNotification("ERROR", "Se perdió la conexión.", "fa-wifi");
    }
}
// Developed By SeBaS
