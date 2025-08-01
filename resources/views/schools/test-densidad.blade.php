<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Densidad - Colegios de Bolivia</title>
    <link rel="stylesheet" href="{{ asset('css/vistas/schools/densidad.css') }}">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1><i class="fas fa-chart-area"></i> Test Dashboard de Densidad</h1>
            <p>Página de prueba para verificar el mapa de calor</p>
        </div>

        <!-- Heatmap Section -->
        <div class="heatmap-container">
            <h3><i class="fas fa-fire"></i> Mapa de Calor de Prueba</h3>
            <div class="heatmap-grid" id="heatmap-grid">
                <!-- Heatmap items will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <script>
        // Datos de prueba
        const testData = {
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

        function renderTestHeatmap() {
            const heatmapContainer = document.getElementById('heatmap-grid');
            if (!heatmapContainer) {
                console.error('Heatmap container not found');
                return;
            }

            console.log('Rendering test heatmap with data:', testData);
            
            const maxDensity = Math.max(...testData.departments.map(d => d.density));
            console.log('Max density:', maxDensity);
            
            heatmapContainer.innerHTML = testData.departments.map(dept => {
                const intensity = maxDensity > 0 ? (dept.density / maxDensity) * 100 : 0;
                const percentage = testData.totalSchools > 0 ? ((dept.schools / testData.totalSchools) * 100).toFixed(1) : '0.0';
                
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

            console.log('Heatmap HTML generated');
        }

        // Render when page loads
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Page loaded, rendering test heatmap...');
            renderTestHeatmap();
        });
    </script>
</body>
</html> 