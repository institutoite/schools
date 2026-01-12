// rankings.js - lógica de autocompletado y AJAX para rankings


function setupAutocomplete() {
    let input = document.getElementById('q');
    let list = document.getElementById('autocomplete-list');
    let schoolIdInput = document.getElementById('school_id');
    const anioSelect = document.getElementById('anio');
    const nivelSelect = document.getElementById('nivel');
    const form = input ? input.form : null;
    function submitFormAjax() {
        if (!form) return;
        const formData = new FormData(form);
        // Mantener valores actuales de colegio seleccionado
        if (input && input.value) formData.set('q', input.value);
        if (schoolIdInput && schoolIdInput.value) formData.set('school_id', schoolIdInput.value);
        fetch(window.location.pathname + '?' + new URLSearchParams(formData), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            // Actualizar card y tabla
            const parser = new DOMParser();
            // Card
            const contextHtml = parser.parseFromString(data.contextHtml, 'text/html').body.firstChild;
            const oldCard = document.querySelector('.card.p-4.mb-4');
            if (contextHtml) {
                if (oldCard && oldCard !== contextHtml) {
                    oldCard.replaceWith(contextHtml);
                } else if (!oldCard) {
                    const table = document.querySelector('.table-responsive, .card.overflow-x-auto');
                    if (table) table.parentNode.insertBefore(contextHtml, table);
                }
            }
            // Tabla
            const tableHtml = parser.parseFromString(data.tableHtml, 'text/html').body.firstChild;
            const oldTable = document.querySelector('.table-responsive, .card.overflow-x-auto');
            if (tableHtml) {
                if (oldTable && oldTable !== tableHtml) {
                    oldTable.replaceWith(tableHtml);
                }
            }
        });
    }
        if (anioSelect && !anioSelect._ajaxBound) {
            anioSelect.addEventListener('change', submitFormAjax);
            anioSelect._ajaxBound = true;
        }
        if (nivelSelect && !nivelSelect._ajaxBound) {
            nivelSelect.addEventListener('change', submitFormAjax);
            nivelSelect._ajaxBound = true;
        }
    let timeout = null;
    if (!input || !list || !schoolIdInput) return;
    const endpoint = input.dataset.endpoint || '/api/schools/search';

    // Limpiar campos si no hay búsqueda activa
    if (!input.value) {
        schoolIdInput.value = '';
    }

    // Evitar doble binding
    if (input._autocompleteBound) return;
    input._autocompleteBound = true;

    input.addEventListener('input', function() {
        //console.log('[autocomplete] evento input disparado, valor:', this.value);
        clearTimeout(timeout);
        const val = this.value;
        schoolIdInput.value = '';
        if (val.length < 2) { list.innerHTML = ''; return; }
        timeout = setTimeout(() => {
            fetch(endpoint + '?q=' + encodeURIComponent(val))
                .then(r => {
                    if (!r.ok) {
                        alert('Error en la respuesta del servidor: ' + r.status);
                        return [];
                    }
                    return r.json();
                })
                .then(data => {
                    // console.log('[autocomplete] respuesta fetch:', data);
                    list.innerHTML = '';
                    if (!Array.isArray(data) || data.length === 0) {
                        // No mostrar alert si no hay resultados
                        return;
                    }
                    data.forEach(s => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.innerHTML = `
                            <div class="nombre-colegio">${s.nombre}</div>
                            <div class="info-colegio">
                                <span>${s.codigo_rue ? 'RUE: ' + s.codigo_rue + ' · ' : ''}${s.turno ? 'Turno: ' + s.turno + ' · ' : ''}${s.nivel ? 'Nivel: ' + s.nivel + ' · ' : ''}${s.dependencia ? s.dependencia + ' · ' : ''}</span>
                                <span>${s.ubicacion ? [s.ubicacion.departamento, s.ubicacion.provincia, s.ubicacion.municipio, s.ubicacion.distrito].filter(Boolean).join(' · ') : ''}</span>
                            </div>
                        `;
                        div.onclick = () => {
                            input.value = s.nombre;
                            schoolIdInput.value = s.id;
                            list.innerHTML = '';
                            // AJAX: actualizar card y tabla sin recargar
                            const form = input.form;
                            if (form) {
                                const formData = new FormData(form);
                                formData.set('school_id', s.id);
                                formData.set('q', s.nombre);
                                fetch(window.location.pathname + '?' + new URLSearchParams(formData), {
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                })
                                .then(r => r.json())
                                .then(data => {
                                    // Actualizar card y tabla
                                    const parser = new DOMParser();
                                    // Card
                                    const contextHtml = parser.parseFromString(data.contextHtml, 'text/html').body.firstChild;
                                    const oldCard = document.querySelector('.card.p-4.mb-4');
                                    if (contextHtml) {
                                        if (oldCard && oldCard !== contextHtml) {
                                            oldCard.replaceWith(contextHtml);
                                        } else if (!oldCard) {
                                            // Si no existe, insertar antes de la tabla
                                            const table = document.querySelector('.table-responsive, .card.overflow-x-auto');
                                            if (table) table.parentNode.insertBefore(contextHtml, table);
                                        }
                                    }
                                    // Tabla
                                    const tableHtml = parser.parseFromString(data.tableHtml, 'text/html').body.firstChild;
                                    const oldTable = document.querySelector('.table-responsive, .card.overflow-x-auto');
                                    if (tableHtml) {
                                        if (oldTable && oldTable !== tableHtml) {
                                            oldTable.replaceWith(tableHtml);
                                        }
                                    }
                                });
                            }
                        };
                        list.appendChild(div);
                    });
                })
                .catch(err => {
                    alert('Error en el fetch/autocompletado: ' + err);
                    console.error('[autocomplete] error:', err);
                });
        }, 200);
    });
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !list.contains(e.target)) {
            list.innerHTML = '';
        }
    });
}

// Lanzar setupAutocomplete al cargar y cuando cambie el DOM
document.addEventListener('DOMContentLoaded', setupAutocomplete);

// Observer para re-enlazar si el input aparece tras AJAX
const observer = new MutationObserver(() => {
    setupAutocomplete();
});
observer.observe(document.body, { childList: true, subtree: true });
