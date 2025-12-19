from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC
import pandas as pd
import time
import os

# Configuración de Selenium para Chrome (sin headless para ver el proceso)
chrome_options = Options()
# chrome_options.add_argument('--headless')  # Comenta para ver el navegador
chrome_options.add_argument('--no-sandbox')
chrome_options.add_argument('--disable-dev-shm-usage')

MAX_RETRIES = 3
RETRY_WAIT = 10  # segundos entre reintentos
LONG_WAIT = 10   # espera corta tras hacer clic en buscar (10 segundos por departamento)

print("[1/9] Iniciando navegador...")
driver = webdriver.Chrome(options=chrome_options)
url = "https://reportes.sie.gob.bo/reporteestadistico/directorio/institucioneducativa/busqueda/regular"
driver.get(url)
wait = WebDriverWait(driver, 180)  # Aumentar tiempo de espera a 180 segundos

# [NUEVO] Intentar cerrar modal si aparece, priorizando 'continuar'
try:
    print("[2/9] Buscando modal inicial...")
    continuar_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(translate(., 'CONTINUAR', 'continuar'), 'continuar')]")))
    print(f"[2/9] Modal detectado. Haciendo clic en el botón: {continuar_btn.text}")
    continuar_btn.click()
    time.sleep(1)
except Exception:
    print("[2/9] No se detectó modal inicial con botón 'continuar' o ya fue cerrado.")

# Lista fija de departamentos (value, nombre)
departamentos = [
    ("3", "Cochabamba"),
    """("5", "Potosi"),
    ("5", "Potosi"),
    ("6", "Tarija"),
    ("7", "Santa Cruz"),
    ("8", "Beni"),
    ("9", "Pando")"""
]

# Esperar el select de departamento por id correcto
print("[3/9] Esperando select de departamento...")
dep_select = wait.until(EC.presence_of_element_located((By.ID, "form_departamento")))
print(f"Departamentos fijos: {[d[1] for d in departamentos]}")

for dep_value, dep_name in departamentos:
    print(f"\n[4/9] Procesando departamento: {dep_name}")
    # Siempre volver a buscar el select antes de seleccionar
    dep_select = driver.find_element(By.ID, "form_departamento")
    Select(dep_select).select_by_value(dep_value)
    time.sleep(1)
    for attempt in range(1, MAX_RETRIES+1):
        print(f"[5/9] Intento {attempt}: esperando botón 'Buscar'...")
        buscar_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(.,'Buscar')]")))
        print("[6/9] Haciendo clic en 'Buscar' para cargar la lista...")
        buscar_btn.click()
        print(f"[7/9] Esperando {LONG_WAIT} segundos para carga de datos...")
        time.sleep(LONG_WAIT)
        # Verificar si aparece mensaje de error
        error_msgs = driver.find_elements(By.XPATH, "//*[contains(text(),'Dificultades al enviar información')]")
        if error_msgs:
            print(f"[!] Error detectado en intento {attempt} para {dep_name}. Reintentando en {RETRY_WAIT} segundos...")
            time.sleep(RETRY_WAIT)
            driver.refresh()
            # Re-seleccionar departamento tras refresh
            dep_select = wait.until(EC.presence_of_element_located((By.ID, "form_departamento")))
            Select(dep_select).select_by_value(dep_value)
            time.sleep(1)
            continue
        # Verificar si la tabla está presente
        try:
            table = driver.find_element(By.TAG_NAME, "table")
            print("[8/9] Tabla encontrada, extrayendo datos...")
            break
        except:
            print(f"[!] Tabla no encontrada en intento {attempt} para {dep_name}. Reintentando en {RETRY_WAIT} segundos...")
            time.sleep(RETRY_WAIT)
            driver.refresh()
            dep_select = wait.until(EC.presence_of_element_located((By.ID, "form_departamento")))
            Select(dep_select).select_by_value(dep_value)
            time.sleep(1)
    else:
        print(f"[X] No se pudo obtener la tabla para {dep_name} tras varios intentos.")
        continue
    # Extraer encabezados
    headers = [th.text.strip() for th in table.find_elements(By.TAG_NAME, "th")]
    # Extraer filas
    data = []
    for row in table.find_elements(By.TAG_NAME, "tr")[1:]:
        cols = [td.text.strip() for td in row.find_elements(By.TAG_NAME, "td")]
        if cols:
            data.append(cols)
    fname = f"colegios_sie_{dep_name.replace(' ','_')}.xlsx"
    print(f"[9/9] Exportando {len(data)} filas a {fname} ...")
    df = pd.DataFrame(data, columns=headers)
    df.to_excel(fname, index=False)
print("\n¡Extracción por departamento finalizada!")
driver.quit()
