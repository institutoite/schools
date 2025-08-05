document.addEventListener('DOMContentLoaded', function() {
    // Initialize elements
    const heatmapGrid = document.getElementById('heatmap-grid');
    const barChartCtx = document.getElementById('bar-chart').getContext('2d');
    const pieChartCtx = document.getElementById('pie-chart').getContext('2d');
    const filterBtn = document.getElementById('filter-btn');
    const deptoFilter = document.getElementById('departamento-filter');
    const provFilter = document.getElementById('provincia-filter');
    
    // Initialize map
    const map = L.map('bolivia-map').setView([-16.5, -64.5], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Process data and render visualizations
    function renderVisualizations() {
        const data = window.phpData.results;
        
        // Clear previous heatmap
        heatmapGrid.innerHTML = '';
        
        // Find max values for normalization
        const maxColegios = Math.max(...data.map(item => item.cantidad_colegios));
        const maxEstudiantes = Math.max(...data.map(item => item.total_estudiantes));
        const maxDensidad = Math.max(...data.map(item => item.densidad));
        
        // Generate heatmap items
        data.forEach(item => {
            // Calculate heat intensity (0-1)
            const intensity = item.densidad / maxDensidad;
            
            // Determine color based on intensity
            const hue = 120 - (intensity * 120); // Green (120) to Red (0)
            const color = `hsl(${hue}, 100%, 50%)`;
            
            // Create heatmap item
            const heatItem = document.createElement('div');
            heatItem.className = 'heatmap-item';
            heatItem.style.backgroundColor = color;
            heatItem.innerHTML = `
                <h4>${item.departamento || item.provincia || item.municipio}</h4>
                <p>${item.densidad.toFixed(2)}%</p>
                <small>${item.cantidad_colegios} colegios</small>
            `;
            
            heatmapGrid.appendChild(heatItem);
        });
        
        // Render bar chart
        new Chart(barChartCtx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.departamento || item.provincia || item.municipio),
                datasets: [{
                    label: 'Cantidad de Colegios',
                    data: data.map(item => item.cantidad_colegios),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Render pie chart
        new Chart(pieChartCtx, {
            type: 'pie',
            data: {
                labels: data.map(item => item.departamento || item.provincia || item.municipio),
                datasets: [{
                    data: data.map(item => item.total_estudiantes),
                    backgroundColor: [
                        'rgba(255, 150, 132, 0.7)',
                        'rgba(54, 55, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(199, 199, 199, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        // Add markers to map
        data.forEach(item => {
            // Simplified coordinates for Bolivia departments
            const coords = getApproximateCoordinates(item.departamento || 'Bolivia');
            
            L.marker([coords.lat, coords.lng]).addTo(map)
                .bindPopup(`
                    <b>${item.departamento || item.provincia || item.municipio}</b><br>
                    Colegios: ${item.cantidad_colegios}<br>
                    Estudiantes: ${item.total_estudiantes.toLocaleString()}<br>
                    Densidad: ${item.densidad.toFixed(2)}%
                `);
        });
    }
    
    // Helper function for approximate coordinates
    function getApproximateCoordinates(department) {
        const coords = {
            'La Paz': { lat: -16.5, lng: -68.15 },
            'Cochabamba': { lat: -17.3895, lng: -66.1568 },
            'Santa Cruz': { lat: -17.7833, lng: -63.1667 },
            'Oruro': { lat: -17.9833, lng: -67.15 },
            'Potosí': { lat: -19.5833, lng: -65.75 },
            'Tarija': { lat: -21.5355, lng: -64.7296 },
            'Chuquisaca': { lat: -19.0333, lng: -65.25 },
            'Beni': { lat: -14.8333, lng: -64.9167 },
            'Pando': { lat: -11.0333, lng: -68.7333 }
        };
        
        return coords[department] || { lat: -16.5, lng: -64.5 };
    }
    
    // Filter button event
    filterBtn.addEventListener('click', function() {
        const depto = deptoFilter.value;
        const prov = provFilter ? provFilter.value : '';
        
        let url = '{{ route("densidad.educativa") }}?';
        if (depto) url += `departamento=${encodeURIComponent(depto)}`;
        if (prov) url += `&provincia=${encodeURIComponent(prov)}`;
        
        window.location.href = url;
    });
    
    // Initial render
    renderVisualizations();
});