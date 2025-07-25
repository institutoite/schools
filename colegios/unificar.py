import os
import pandas as pd

# Carpeta principal (donde están las 9 carpetas)
carpeta_principal = os.getcwd()

# Lista para guardar todos los DataFrames
todos_los_datos = []

# Recorrer todas las carpetas dentro del directorio principal
for carpeta in os.listdir(carpeta_principal):
    ruta_carpeta = os.path.join(carpeta_principal, carpeta)
    
    if os.path.isdir(ruta_carpeta):
        # Recorrer cada archivo dentro de la carpeta
        for archivo in os.listdir(ruta_carpeta):
            if archivo.endswith(".csv"):  # Cambia a .xlsx o .txt si es otro tipo
                ruta_archivo = os.path.join(ruta_carpeta, archivo)
                try:
                    df = pd.read_csv(ruta_archivo)  # Usa read_excel para .xlsx
                    todos_los_datos.append(df)
                except Exception as e:
                    print(f"Error leyendo {ruta_archivo}: {e}")

# Unir todos los DataFrames en uno solo
df_unificado = pd.concat(todos_los_datos, ignore_index=True)

# Guardar el archivo final en la raíz
archivo_salida = os.path.join(carpeta_principal, "todos_los_colegios.csv")
df_unificado.to_csv(archivo_salida, index=False, encoding='utf-8-sig')

print(f"✅ Archivo generado: {archivo_salida}")
