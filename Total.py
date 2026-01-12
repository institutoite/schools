import json
import os
import re

# Lista de archivos JSON a corregir
archivos = [
    "colegios_beni.json",
     "colegios_chuquisaca.json",
     "colegios_cochabamba.json",

    "colegios_lapaz.json",
     "colegios_oruro.json",
     "colegios_pando.json",

    "colegios_potosi.json",
     "colegios_santacruz.json",
     "colegios_tarija.json",
    ]

def corregir_total(diccionario):
    for seccion in ["matricula", "promovidos", "reprobados", "abandono"]:
        if seccion in diccionario.get("estadisticas", {}):
            datos = diccionario["estadisticas"][seccion]
            # Buscar claves tipo "Total" con puntos extra
            for clave in list(datos.keys()):
                if re.match(r"^Total\.{1,}$", clave):
                    datos["Total"] = datos.pop(clave)
    return diccionario

def limpiar_valores(diccionario):
    # Recorre cada sección relevante
    for seccion in ["matricula", "promovidos", "reprobados", "abandono"]:
        if seccion in diccionario.get("estadisticas", {}):
            datos = diccionario["estadisticas"][seccion]
            for clave in ["Total", "Mujer", "Hombre"]:
                if clave in datos:
                    for anio, valor in datos[clave].items():
                        # Elimina el punto si es un número mayor a 1000 (ej: "1.585" -> "1585")
                        if isinstance(valor, str) and valor.count('.') == 1 and len(valor) >= 5:
                            nuevo_valor = valor.replace('.', '')
                            datos[clave][anio] = nuevo_valor
    return diccionario

for archivo in archivos:
    ruta = os.path.join(os.getcwd(), archivo)
    with open(ruta, "r", encoding="utf-8") as f:
        datos = json.load(f)

    # Si el archivo es una lista de colegios
    if isinstance(datos, list):
        datos = [limpiar_valores(corregir_total(colegio)) for colegio in datos]
    # Si es un solo colegio
    elif isinstance(datos, dict):
        datos = limpiar_valores(corregir_total(datos))

    with open(ruta, "w", encoding="utf-8") as f:
        json.dump(datos, f, ensure_ascii=False, indent=2)

print("Corrección terminada.")