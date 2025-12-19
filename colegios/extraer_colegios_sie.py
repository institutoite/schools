import requests
from bs4 import BeautifulSoup
import pandas as pd

# URL de la página con la lista de colegios
target_url = "https://reportes.sie.gob.bo/reporteestadistico/directorio/institucioneducativa/busqueda/regular"

# NOTA: Si la tabla es dinámica (JavaScript), este script no funcionará. En ese caso, se debe usar Selenium.

# Realizar la petición HTTP
response = requests.get(target_url)
response.raise_for_status()

# Parsear el HTML
soup = BeautifulSoup(response.text, 'html.parser')

# Buscar la tabla (ajustar el selector según la estructura real)
table = soup.find('table')
if not table:
    raise Exception('No se encontró la tabla en la página.')

# Extraer encabezados
headers = [th.get_text(strip=True) for th in table.find_all('th')]

# Extraer filas de datos
data = []
for row in table.find_all('tr')[1:]:
    cols = [td.get_text(strip=True) for td in row.find_all('td')]
    if cols:
        data.append(cols)

# Crear DataFrame y guardar a Excel
df = pd.DataFrame(data, columns=headers)
df.to_excel('colegios_sie.xlsx', index=False)

print('Datos exportados a colegios_sie.xlsx')
