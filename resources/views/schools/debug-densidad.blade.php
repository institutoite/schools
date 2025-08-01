<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Densidad - Colegios de Bolivia</title>
    <link rel="stylesheet" href="{{ asset('css/vistas/schools/densidad.css') }}">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1><i class="fas fa-chart-area"></i> Debug Dashboard de Densidad</h1>
            <p>Página de debug para diagnosticar el problema del mapa de calor</p>
        </div>

        <!-- Debug Info -->
        <div class="chart-container">
            <h3><i class="fas fa-bug"></i> Información de Debug</h3>
            <div id="debug-info">
                <p>Cargando información de debug...</p>
            </div>
        </div>

        <!-- Heatmap Section -->
        <div class="heatmap-container">
            <h3><i class="fas fa-fire"></i> Mapa de Calor de Debug</h3>
            <div class="heatmap-grid" id="heatmap-grid">
                <p>Esperando datos...</p>
            </div>
        </div>
    </div>

    <script>
        // Datos de prueba estáticos
        const debugData = {
            departments: [
                { name: 'La Paz', schools: 1250, students: 450000, density: 85 },
                { name: 'Santa Cruz', schools: 980, students: 380000, density: 72 },
                { name: 'Cochabamba', schools: 750, students: 280000, density: 65 },
                { name: 'Potosí', schools: 420, students: 150000, density: 45 },
                { name: 'Oruro', schools: 380, students: 120000, density: 40 }
            ],
            totalSchools: 3780,
            totalStudents: 1380000,
            averageDensity: 61.4
        };

        function updateDebugInfo() {
            const debugContainer = document.getElementById('debug-info');
            debugContainer.innerHTML = `
                <h4>Datos de Debug:</h4>
                <ul>
                    <li><strong>Departamentos:</strong> ${debugData.departments.length}</li>
                    <li><strong>Total Colegios:</strong> ${debugData.totalSchools.toLocaleString()}</li>
                    <li><strong>Total Estudiantes:</strong> ${debugData.totalStudents.toLocaleString()}</li>
                    <li><strong>Densidad Promedio:</strong> ${debugData.averageDensity}</li>
                </ul>
                <h4>Departamentos:</h4>
                <ul>
                    ${debugData.departments.map(dept => 
                        `<li>${dept.name}: ${dept.schools} colegios, densidad ${dept.density}</li>`
                    ).join('')}
                </ul>
            `;
        }

        function renderDebugHeatmap() {
            console.log('=== RENDER DEBUG HEATMAP START ===');
            
            const heatmapContainer = document.getElementById('heatmap-grid');
            console.log('Heatmap container:', heatmapContainer);
            
            if (!heatmapContainer) {
                console.error('Heatmap container not found');
                return;
            }
            
            console.log('Debug data:', debugData);
            console.log('Departments:', debugData.departments);
            
            if (!debugData.departments || debugData.departments.length === 0) {
                console.error('No departments data available');
                heatmapContainer.innerHTML = '<div class="error-message">No hay datos de departamentos disponibles</div>';
                return;
            }

            const maxDensity = Math.max(...debugData.departments.map(d => d.density));
            console.log('Max density:', maxDensity);
            
            console.log('Generating heatmap HTML...');
            
            const heatmapHTML = debugData.departments.map(dept => {
                const intensity = maxDensity > 0 ? (dept.density / maxDensity) * 100 : 0;
                const percentage = debugData.totalSchools > 0 ? ((dept.schools / debugData.totalSchools) * 100).toFixed(1) : '0.0';
                
                console.log(`Generating item for ${dept.name}: intensity=${intensity}, percentage=${percentage}`);
                
                return `
                    <div class="heatmap-item" 
                         style="background: linear-gradient(135deg, 
                                 hsl(${200 - intensity * 2}, 70%, ${50 + intensity * 0.3}%), 
                                 hsl(${220 - intensity * 2}, 60%, ${40 + intensity * 0.3}%))"
                         data-department="${dept.name}">
                        <h4>${dept.name}</h4>
                        <div class="value">${dept.schools.toLocaleString()}</div>
                        <div class="percentage">${percentage}% del total</div>
                        <div class="density">Densidad: ${dept.density}</div>
                    </div>
                `;
            }).join('');
            
            console.log('Generated HTML length:', heatmapHTML.length);
            console.log('Generated HTML preview:', heatmapHTML.substring(0, 200) + '...');
            console.log('Setting innerHTML...');
            
            heatmapContainer.innerHTML = heatmapHTML;
            
            console.log('HTML set, adding click events...');
            
            // Add click events to heatmap items
            const heatmapItems = heatmapContainer.querySelectorAll('.heatmap-item');
            console.log('Found heatmap items:', heatmapItems.length);
            
            heatmapItems.forEach(item => {
                item.addEventListener('click', () => {
                    const department = item.dataset.department;
                    console.log('Clicked on department:', department);
                    alert(`Hiciste clic en: ${department}`);
                });
            });
            
            console.log('=== RENDER DEBUG HEATMAP COMPLETE ===');
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Debug page loaded, initializing...');
            updateDebugInfo();
            renderDebugHeatmap();
        });
    </script>
</body>
</html> 