import json

def validar_json(path_archivo):
    try:
        with open(path_archivo, "r", encoding="utf-8") as archivo:
            json.load(archivo)
        print("✅ El archivo JSON es válido.")
    except json.JSONDecodeError as e:
        print("❌ Error de sintaxis en el JSON:")
        print(f"  → Detalle: {e.msg}")
        print(f"  → Línea: {e.lineno}")
        print(f"  → Columna: {e.colno}")
        print(f"  → Texto: {e.doc.splitlines()[e.lineno - 1]}")
    except Exception as ex:
        print("⚠️ Otro error al abrir o leer el archivo:")
        print(ex)

# Ejemplo de uso
ruta = "colegios_data_completo.json"  # Reemplaza por la ruta real de tu archivo
validar_json(ruta)
