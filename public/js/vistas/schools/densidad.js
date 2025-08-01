// Dashboard Density JavaScript
class DensityDashboard {
    constructor() {
        this.charts = {};
        this.map = null;
        this.data = null;
        this.filters = {
            departamento: '',
            dependencia: '',
            area: ''
        };
        
        this.init();
    }

    async init() {
        console.log('Initializing dashboard...');
        await this.loadData();
        this.setupEventListeners();
        this.renderStats();
        this.renderHeatmap();
        this.renderCharts();
        this.renderMap();
        this.setupFilters();
        console.log('Dashboard initialization complete');
    }

    async loadData() {
        try {
            console.log('Loading data...');
            // Simulate loading data - replace with actual API call
            this.data = await this.fetchDensityData();
            console.log('Data loaded successfully:', this.data);
        } catch (error) {
            console.error('Error loading data:', error);
            this.showError('Error al cargar los datos');
        }
    }

    async fetchDensityData() {
        console.log('=== FETCH DENSITY DATA START ===');
        console.log('window.phpData:', window.phpData);
        console.log('window.phpData.results:', window.phpData?.results);
        console.log('window.phpData.results length:', window.phpData?.results?.length);
        
        // Use PHP data passed from the server
        if (window.phpData && window.phpData.results) {
            console.log('PHP data available, processing results...');
            console.log('Results count:', window.phpData.results.length);
            console.log('First result item:', window.phpData.results[0]);
            
            let fieldName = 'departamento';
            if (window.phpData.departamento && window.phpData.provincia) {
                fieldName = 'municipio';
            } else if (window.phpData.departamento) {
                fieldName = 'provincia';
            }
            
            console.log('Using field name:', fieldName);
            console.log('Sample result item:', window.phpData.results[0]);
            
            const departments = window.phpData.results.map(item => {
                console.log('Processing item:', item);
                const dept = {
                    name: item[fieldName],
                    schools: parseInt(item.cantidad_colegios),
                    students: parseInt(item.total_estudiantes),
                    density: parseFloat(item.densidad) || 0
                };
                console.log(`Processed ${dept.name}:`, dept);
                return dept;
            });

            console.log('All processed departments:', departments);

            const totalSchools = departments.reduce((sum, dept) => sum + dept.schools, 0);
            const totalStudents = departments.reduce((sum, dept) => sum + dept.students, 0);
            const averageDensity = departments.reduce((sum, dept) => sum + dept.density, 0) / departments.length;

            const result = {
                departments,
                totalSchools,
                totalStudents,
                averageDensity: averageDensity || 0
            };
            
            console.log('Final result:', result);
            console.log('=== FETCH DENSITY DATA COMPLETE (PHP) ===');
            return result;
        }
        
        console.log('No PHP data available, using fallback');

        // Fallback to sample data if PHP data is not available
        console.log('Using fallback sample data');
        const fallbackData = {
            departments: [
                { name: 'La Paz', schools: 1250, students: 450000, density: 85 },
                { name: 'Santa Cruz', schools: 980, students: 380000, density: 72 },
                { name: 'Cochabamba', schools: 750, students: 280000, density: 65 },
                { name: 'Potosí', schools: 420, students: 150000, density: 45 },
                { name: 'Oruro', schools: 380, students: 120000, density: 40 },
                { name: 'Chuquisaca', schools: 320, students: 95000, density: 35 },
                { name: 'Tarija', schools: 280, students: 85000, density: 30 },
                { name: 'Beni', schools: 220, students: 65000, density: 25 },
                { name: 'Pando', schools: 150, students: 45000, density: 20 }
            ],
            totalSchools: 4750,
            totalStudents: 1650000,
            averageDensity: 46.8
        };
        console.log('Fallback data:', fallbackData);
        console.log('=== FETCH DENSITY DATA COMPLETE (FALLBACK) ===');
        return fallbackData;
    }

    setupEventListeners() {
        // Filter controls
        document.getElementById('departamento-filter')?.addEventListener('change', (e) => {
            this.filters.departamento = e.target.value;
            this.submitFilters();
        });

        document.getElementById('provincia-filter')?.addEventListener('change', (e) => {
            this.filters.provincia = e.target.value;
            this.submitFilters();
        });

        document.getElementById('dependencia-filter')?.addEventListener('change', (e) => {
            this.filters.dependencia = e.target.value;
            this.updateDashboard();
        });

        document.getElementById('area-filter')?.addEventListener('change', (e) => {
            this.filters.area = e.target.value;
            this.updateDashboard();
        });

        // Reset filters
        document.getElementById('reset-filters')?.addEventListener('click', () => {
            this.resetFilters();
        });

        // Export functionality
        document.getElementById('export-data')?.addEventListener('click', () => {
            this.exportData();
        });
    }

    submitFilters() {
        const params = new URLSearchParams();
        if (this.filters.departamento) {
            params.append('departamento', this.filters.departamento);
        }
        if (this.filters.provincia) {
            params.append('provincia', this.filters.provincia);
        }
        
        const url = `/densidad-educativa?${params.toString()}`;
        window.location.href = url;
    }

    renderStats() {
        const statsContainer = document.getElementById('stats-grid');
        if (!statsContainer || !this.data) return;

        let currentLevel = 'Departamentos';
        let currentCount = this.data.departments.length;
        
        if (window.phpData.departamento && window.phpData.provincia) {
            currentLevel = 'Municipios';
            currentCount = this.data.departments.length;
        } else if (window.phpData.departamento) {
            currentLevel = 'Provincias';
            currentCount = window.phpData.cantidad_provincias || this.data.departments.length;
        }

        const stats = [
            {
                title: 'Total de Colegios',
                value: this.data.totalSchools.toLocaleString(),
                icon: 'fas fa-school',
                change: '+5.2%',
                changeType: 'positive'
            },
            {
                title: 'Total de Estudiantes',
                value: this.data.totalStudents.toLocaleString(),
                icon: 'fas fa-users',
                change: '+3.8%',
                changeType: 'positive'
            },
            {
                title: 'Densidad Promedio',
                value: window.phpData.densidad_promedio.toFixed(1),
                icon: 'fas fa-chart-line',
                change: '+2.1%',
                changeType: 'positive'
            },
            {
                title: currentLevel,
                value: currentCount,
                icon: 'fas fa-map',
                change: 'Estable',
                changeType: 'neutral'
            }
        ];

        statsContainer.innerHTML = stats.map(stat => `
            <div class="stat-card fade-in">
                <h3><i class="${stat.icon}"></i> ${stat.title}</h3>
                <div class="stat-value">${stat.value}</div>
                <div class="stat-change ${stat.changeType}">
                    <i class="fas fa-${stat.changeType === 'positive' ? 'arrow-up' : stat.changeType === 'negative' ? 'arrow-down' : 'minus'}"></i>
                    ${stat.change}
                </div>
            </div>
        `).join('');
    }

    renderHeatmap() {
        console.log('=== RENDER HEATMAP START ===');
        
        const heatmapContainer = document.getElementById('heatmap-grid');
        console.log('Heatmap container:', heatmapContainer);
        
        if (!heatmapContainer) {
            console.error('Heatmap container not found');
            return;
        }
        
        console.log('Data available:', this.data);
        
        if (!this.data) {
            console.error('No data available for heatmap');
            heatmapContainer.innerHTML = '<div class="error-message">No hay datos disponibles para mostrar el mapa de calor</div>';
            return;
        }
        
        console.log('Departments data:', this.data.departments);
        
        if (!this.data.departments || this.data.departments.length === 0) {
            console.error('No departments data available');
            heatmapContainer.innerHTML = '<div class="error-message">No hay datos de departamentos disponibles</div>';
            return;
        }

        const maxDensity = Math.max(...this.data.departments.map(d => d.density));
        console.log('Max density:', maxDensity);
        
        let currentLevel = 'Departamentos';
        if (window.phpData && window.phpData.departamento && window.phpData.provincia) {
            currentLevel = 'Municipios';
        } else if (window.phpData && window.phpData.departamento) {
            currentLevel = 'Provincias';
        }
        
        console.log('Current level:', currentLevel);
        console.log('Generating heatmap HTML...');
        
        const heatmapHTML = this.data.departments.map(dept => {
            const intensity = maxDensity > 0 ? (dept.density / maxDensity) * 100 : 0;
            const percentage = this.data.totalSchools > 0 ? ((dept.schools / this.data.totalSchools) * 100).toFixed(1) : '0.0';
            
            console.log(`Generating item for ${dept.name}: intensity=${intensity}, percentage=${percentage}`);
            
            // Use a simpler background color approach
            const hue = 200 - (intensity * 2);
            const saturation = 70;
            const lightness = 50 + (intensity * 0.3);
            
            return `
                <div class="heatmap-item" 
                     style="background-color: hsl(${hue}, ${saturation}%, ${lightness}%); border: 3px solid white;"
                     data-department="${dept.name}">
                    <h4>${dept.name}</h4>
                    <div class="value">${dept.schools.toLocaleString()}</div>
                    <div class="percentage">${percentage}% del total</div>
                    <div class="density">Densidad: ${dept.density}</div>
                </div>
            `;
        }).join('');
        
        console.log('Generated HTML length:', heatmapHTML.length);
        console.log('Setting innerHTML...');
        
        heatmapContainer.innerHTML = heatmapHTML;
        
        console.log('HTML set, adding click events...');
        
        // Add click events to heatmap items
        const heatmapItems = heatmapContainer.querySelectorAll('.heatmap-item');
        console.log('Found heatmap items:', heatmapItems.length);
        
        heatmapItems.forEach(item => {
            item.addEventListener('click', () => {
                const department = item.dataset.department;
                this.highlightDepartment(department);
            });
        });
        
        console.log('=== RENDER HEATMAP COMPLETE ===');
    }

    renderCharts() {
        this.renderBarChart();
        this.renderPieChart();
    }

    renderBarChart() {
        const ctx = document.getElementById('bar-chart');
        if (!ctx || !this.data) return;

        const chartData = {
            labels: this.data.departments.map(d => d.name),
            datasets: [{
                label: 'Cantidad de Colegios',
                data: this.data.departments.map(d => d.schools),
                backgroundColor: 'rgba(38, 186, 165, 0.8)',
                borderColor: 'rgba(38, 186, 165, 1)',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        };

        this.charts.bar = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(38, 186, 165, 0.5)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            color: '#375f7a'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#375f7a',
                            maxRotation: 45
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }

    renderPieChart() {
        const ctx = document.getElementById('pie-chart');
        if (!ctx || !this.data) return;

        const colors = [
            '#26baa5', '#375f7a', '#10b981', '#f59e0b', '#ef4444',
            '#8b5cf6', '#06b6d4', '#84cc16', '#f97316'
        ];

        const chartData = {
            labels: this.data.departments.map(d => d.name),
            datasets: [{
                data: this.data.departments.map(d => d.schools),
                backgroundColor: colors,
                borderColor: 'white',
                borderWidth: 2,
                hoverOffset: 4
            }]
        };

        this.charts.pie = new Chart(ctx, {
            type: 'doughnut',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            color: '#375f7a'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(38, 186, 165, 0.5)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }

    renderMap() {
        const mapContainer = document.getElementById('bolivia-map');
        if (!mapContainer || !this.data) return;

        // Initialize Leaflet map
        this.map = L.map('bolivia-map').setView([-16.5, -64.5], 6);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(this.map);

        // Bolivia department and province coordinates and data
        const locationData = {
            // Departments
            'La Paz': { lat: -16.5, lng: -68.15, schools: 1250, students: 5 },
            'Santa Cruz': { lat: -17.8, lng: -63.18, schools: 980, students: 380000 },
            'Cochabamba': { lat: -17.4, lng: -66.16, schools: 750, students: 280000 },
            'Potosí': { lat: -19.6, lng: -65.75, schools: 420, students: 150000 },
            'Oruro': { lat: -17.97, lng: -67.12, schools: 380, students: 120000 },
            'Chuquisaca': { lat: -19.05, lng: -65.26, schools: 320, students: 95000 },
            'Tarija': { lat: -21.53, lng: -64.73, schools: 280, students: 85000 },
            'Beni': { lat: -14.83, lng: -64.9, schools: 220, students: 65000 },
            'Pando': { lat: -11.03, lng: -68.77, schools: 150, students: 45000 },
            
            // La Paz Provinces (sample coordinates)
            'Murillo': { lat: -16.5, lng: -68.15, schools: 200, students: 75000 },
            'Omasuyos': { lat: -16.2, lng: -68.7, schools: 150, students: 55000 },
            'Los Andes': { lat: -16.8, lng: -68.3, schools: 120, students: 45000 },
            'Ingavi': { lat: -16.6, lng: -68.8, schools: 100, students: 38000 },
            'Pacajes': { lat: -17.2, lng: -68.5, schools: 80, students: 30000 },
            
            // Santa Cruz Provinces (sample coordinates)
            'Andrés Ibáñez': { lat: -17.8, lng: -63.18, schools: 180, students: 68000 },
            'Warnes': { lat: -17.5, lng: -63.2, schools: 120, students: 45000 },
            'Sara': { lat: -17.2, lng: -63.5, schools: 90, students: 34000 },
            'Cordillera': { lat: -18.5, lng: -63.8, schools: 70, students: 26000 },
            'Vallegrande': { lat: -18.5, lng: -64.1, schools: 60, students: 22000 },
            
                         // Cochabamba Provinces (sample coordinates)
             'Cercado': { lat: -17.4, lng: -66.16, schools: 160, students: 60000 },
             'Quillacollo': { lat: -17.4, lng: -66.3, schools: 140, students: 52000 },
             'Chapare': { lat: -16.8, lng: -65.8, schools: 100, students: 38000 },
             'Tapacarí': { lat: -17.8, lng: -66.5, schools: 80, students: 30000 },
             'Arani': { lat: -17.6, lng: -65.8, schools: 70, students: 26000 },
             
             // Sample Municipalities (for when province is selected)
             'Cochabamba': { lat: -17.4, lng: -66.16, schools: 120, students: 45000 },
             'Sacaba': { lat: -17.4, lng: -66.0, schools: 80, students: 30000 },
             'Quillacollo': { lat: -17.4, lng: -66.3, schools: 90, students: 35000 },
             'Tiquipaya': { lat: -17.3, lng: -66.2, schools: 60, students: 22000 },
             'Colcapirhua': { lat: -17.4, lng: -66.25, schools: 40, students: 15000 }
         };

        // Add markers for each location (department, province, or municipality)
        Object.entries(locationData).forEach(([name, data]) => {
            // Only show relevant locations based on current filter
            let shouldShow = false;
            
            if (window.phpData.departamento && window.phpData.provincia) {
                // Municipality view - show only if it's in our data
                shouldShow = this.data.departments.some(d => d.name === name);
            } else if (window.phpData.departamento) {
                // Province view - show only if it's in our data
                shouldShow = this.data.departments.some(d => d.name === name);
            } else {
                // Department view - show only if it's in our data
                shouldShow = this.data.departments.some(d => d.name === name);
            }
            
            if (!shouldShow) return;
            
            const radius = Math.sqrt(data.schools) * 2;
            const color = this.getColorByDensity(data.schools);
            
            const circle = L.circleMarker([data.lat, data.lng], {
                radius: radius,
                fillColor: color,
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(this.map);

            // Create popup content
            const popupContent = `
                <div class="department-popup">
                    <h4>${name}</h4>
                    <div class="stats">
                        <div class="stat">
                            <strong>Colegios:</strong> ${data.schools.toLocaleString()}
                        </div>
                        <div class="stat">
                            <strong>Estudiantes:</strong> ${data.students.toLocaleString()}
                        </div>
                    </div>
                </div>
            `;

            circle.bindPopup(popupContent);

            // Add click event
            circle.on('click', () => {
                this.highlightDepartment(name);
            });
        });

        // Add legend
        const legend = L.control({ position: 'bottomright' });
        legend.onAdd = () => {
            const div = L.DomUtil.create('div', 'info legend');
            div.style.backgroundColor = 'white';
            div.style.padding = '10px';
            div.style.borderRadius = '5px';
            div.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
            
            const grades = [0, 300, 600, 900, 1200];
            const colors = ['#fee5d9', '#fcae91', '#fb6a4a', '#de2d26', '#a50f15'];
            
            div.innerHTML = '<h4>Densidad de Colegios</h4>';
            
            for (let i = 0; i < grades.length; i++) {
                div.innerHTML +=
                    '<i style="background:' + colors[i] + '; width: 18px; height: 18px; float: left; margin-right: 8px; opacity: 0.7"></i> ' +
                    grades[i] + (grades[i + 1] ? '&ndash;' + grades[i + 1] + '<br>' : '+');
            }
            
            return div;
        };
        legend.addTo(this.map);
    }

    getColorByDensity(schools) {
        if (schools > 1000) return '#a50f15';
        if (schools > 700) return '#de2d26';
        if (schools > 400) return '#fb6a4a';
        if (schools > 200) return '#fcae91';
        return '#fee5d9';
    }

    highlightDepartment(locationName) {
        // Highlight in heatmap
        document.querySelectorAll('.heatmap-item').forEach(item => {
            item.style.transform = item.dataset.department === locationName ? 'scale(1.1)' : 'scale(1)';
            item.style.boxShadow = item.dataset.department === locationName ? '0 8px 25px rgba(0,0,0,0.3)' : 'none';
        });

        // Highlight in charts
        if (this.charts.bar) {
            const index = this.data.departments.findIndex(d => d.name === locationName);
            if (index !== -1) {
                this.charts.bar.setActiveElements([{
                    datasetIndex: 0,
                    index: index
                }]);
                this.charts.bar.update();
            }
        }

        if (this.charts.pie) {
            const index = this.data.departments.findIndex(d => d.name === locationName);
            if (index !== -1) {
                this.charts.pie.setActiveElements([{
                    datasetIndex: 0,
                    index: index
                }]);
                this.charts.pie.update();
            }
        }

        // Center map on location
        const locationData = this.data.departments.find(d => d.name === locationName);
        if (locationData && this.map) {
            // You would need to add coordinates to your data
            // this.map.setView([locationData.lat, locationData.lng], 8);
        }
    }

    setupFilters() {
        // Initialize filters from PHP data
        this.filters = {
            departamento: window.phpData.departamento || '',
            provincia: window.phpData.provincia || '',
            dependencia: '',
            area: ''
        };

        // Set current filter values
        if (document.getElementById('departamento-filter')) {
            document.getElementById('departamento-filter').value = this.filters.departamento;
        }
        if (document.getElementById('provincia-filter')) {
            document.getElementById('provincia-filter').value = this.filters.provincia;
        }
    }

    updateDashboard() {
        // Filter data based on current filters
        let filteredData = this.data.departments;

        if (this.filters.departamento) {
            filteredData = filteredData.filter(d => d.name === this.filters.departamento);
        }

        // Update charts with filtered data
        this.updateCharts(filteredData);
        this.updateMap(filteredData);
    }

    updateCharts(filteredData) {
        // Update bar chart
        if (this.charts.bar) {
            this.charts.bar.data.labels = filteredData.map(d => d.name);
            this.charts.bar.data.datasets[0].data = filteredData.map(d => d.schools);
            this.charts.bar.update();
        }

        // Update pie chart
        if (this.charts.pie) {
            this.charts.pie.data.labels = filteredData.map(d => d.name);
            this.charts.pie.data.datasets[0].data = filteredData.map(d => d.schools);
            this.charts.pie.update();
        }
    }

    updateMap(filteredData) {
        // Clear existing markers
        this.map.eachLayer((layer) => {
            if (layer instanceof L.CircleMarker) {
                this.map.removeLayer(layer);
            }
        });

        // Add filtered markers
        // Implementation would be similar to renderMap but with filtered data
    }

    resetFilters() {
        this.filters = {
            departamento: '',
            provincia: '',
            dependencia: '',
            area: ''
        };

        // Reset filter controls
        document.getElementById('departamento-filter').value = '';
        if (document.getElementById('provincia-filter')) {
            document.getElementById('provincia-filter').value = '';
        }
        document.getElementById('dependencia-filter').value = '';
        document.getElementById('area-filter').value = '';

        // Redirect to base URL
        window.location.href = '/densidad-educativa';
    }

    exportData() {
        const dataStr = JSON.stringify(this.data, null, 2);
        const dataBlob = new Blob([dataStr], { type: 'application/json' });
        const url = URL.createObjectURL(dataBlob);
        
        const link = document.createElement('a');
        link.href = url;
        link.download = 'densidad-colegios.json';
        link.click();
        
        URL.revokeObjectURL(url);
    }

    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #ef4444;
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
        `;
        errorDiv.textContent = message;
        
        document.body.appendChild(errorDiv);
        
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, creating dashboard...');
    try {
        new DensityDashboard();
    } catch (error) {
        console.error('Error creating dashboard:', error);
    }
});

// Utility functions
function formatNumber(num) {
    return new Intl.NumberFormat('es-BO').format(num);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Export for use in other modules
window.DensityDashboard = DensityDashboard; 