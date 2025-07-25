import json

# Cargar archivo JSON
with open("colegios_data.json", "r", encoding="utf-8") as f:
    data = json.load(f)

# Eliminar duplicados por 'codigo_rue'
unicos = {}
for colegio in data:
    codigo = colegio["general"]["codigo_rue"]
    unicos[codigo] = colegio  # Si ya existe, lo sobrescribe (último queda)

# Guardar archivo limpio
with open("colegios_limpios.json", "w", encoding="utf-8") as f:
    json.dump(list(unicos.values()), f, indent=4, ensure_ascii=False)

print("✅ JSON depurado con éxito.")
