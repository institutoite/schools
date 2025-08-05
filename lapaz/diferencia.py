import json
from pathlib import Path

def diferencia_json_completa(archivoA, archivoB, archivo_salida):
    """
    Obtiene los registros completos que están en A pero no en B,
    basado en codigo_rue dentro del objeto 'general'
    
    Args:
        archivoA (str): Ruta del primer archivo JSON (A)
        archivoB (str): Ruta del segundo archivo JSON (B)
        archivo_salida (str): Ruta del archivo de salida con la diferencia A-B
    """
    try:
        # Cargar archivos
        with open(archivoA, 'r', encoding='utf-8') as fa:
            datosA = json.load(fa)
        
        with open(archivoB, 'r', encoding='utf-8') as fb:
            datosB = json.load(fb)
        
        # Crear conjunto de códigos_rue presentes en B (buscando en general.codigo_rue)
        codigos_en_B = set()
        for registro in datosB:
            if isinstance(registro, dict) and 'general' in registro:
                codigo = registro['general'].get('codigo_rue')
                if codigo:
                    codigos_en_B.add(str(codigo))
        
        # Filtrar A: registros cuyo codigo_rue no está en B
        diferencia = []
        for registro in datosA:
            if isinstance(registro, dict) and 'general' in registro:
                codigo = registro['general'].get('codigo_rue')
                if codigo and str(codigo) not in codigos_en_B:
                    diferencia.append(registro)
        
        # Guardar resultado
        Path(archivo_salida).parent.mkdir(parents=True, exist_ok=True)
        with open(archivo_salida, 'w', encoding='utf-8') as f_out:
            json.dump(diferencia, f_out, ensure_ascii=False, indent=4)
        
        # Generar reporte detallado
        reporte = {
            'total_registros_A': len(datosA),
            'total_registros_B': len(datosB),
            'registros_en_A_no_en_B': len(diferencia),
            'porcentaje_diferencia': f"{(len(diferencia)/len(datosA)*100):.2f}%",
            'archivo_resultado': str(Path(archivo_salida).absolute())
        }
        
        print("\nReporte de diferencia A-B:")
        print(json.dumps(reporte, indent=4))
        
        return diferencia
        
    except Exception as e:
        print(f"Error: {str(e)}")
        return None

# Ejemplo de uso
diferencia_json_completa('lapaz/posibles.json', 'lapaz/basedatos.json', 'lapaz/diferencia_AB.json')