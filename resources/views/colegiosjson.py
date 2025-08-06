import requests
from bs4 import BeautifulSoup
import re
import json
from urllib.parse import urljoin
import os
import time
import random

def extract_coordinates_ultra_robust(text):
    """Extrae coordenadas usando múltiples patrones"""
    # Patrones comunes de coordenadas
    patterns = [
        # Formato Y: -14.701032894984 X: -67.2129316329958
        r'Y:\s*(-?\d+\.\d+)[,\s]*X:\s*(-?\d+\.\d+)',
        # Formato X: -67.2129316329958 Y: -14.701032894984
        r'X:\s*(-?\d+\.\d+)[,\s]*Y:\s*(-?\d+\.\d+)',
        # Formato Latitud: -14.701032894984 Longitud: -67.2129316329958
        r'Latitud:\s*(-?\d+\.\d+)[,\s]*Longitud:\s*(-?\d+\.\d+)',
        # Formato -14.701032894984, -67.2129316329958
        r'(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)',
        # Formato -14.701032894984 -67.2129316329958
        r'(-?\d+\.\d+)\s+(-?\d+\.\d+)'
    ]
    
    for pattern in patterns:
        match = re.search(pattern, text, re.IGNORECASE)
        if match:
            try:
                # Determinar qué grupo es latitud y cuál es longitud
                if 'X:' in pattern or 'Longitud:' in pattern:
                    lon = float(match.group(1))
                    lat = float(match.group(2))
                else:
                    lat = float(match.group(1))
                    lon = float(match.group(2))
                
                return {
                    'latitud': lat,
                    'longitud': lon,
                    'texto': f"Y: {lat} X: {lon}"
                }
            except ValueError:
                continue
    
    # Si no se encontró con patrones, buscar números decimales con signo
    numbers = re.findall(r'-?\d+\.\d+', text)
    if len(numbers) >= 2:
        try:
            return {
                'latitud': float(numbers[0]),
                'longitud': float(numbers[1]),
                'texto': f"Y: {numbers[0]} X: {numbers[1]}"
            }
        except ValueError:
            pass
    
    return {'latitud': None, 'longitud': None, 'texto': text}
    #return {'latitud': None, 'longitud': None, 'texto': text}


        
def get_coordinates_from_html(soup):
    """
    Busca coordenadas buscando específicamente los patrones X: e Y: en el HTML,
    examinando múltiples ubicaciones posibles.
    """
    # 1. Primero buscamos directamente los textos X: e Y: en todo el documento
    texts_to_check = []
    
    # Buscar en elementos <dd> que suelen contener datos
    dd_elements = soup.find_all('dd')
    texts_to_check.extend([dd.get_text(strip=True) for dd in dd_elements])
    
    # Buscar en elementos con clases comunes que podrían contener coordenadas
    common_classes = ['coordenadas', 'geo-data', 'location-data', 'map-data', 'info-box-text']
    for class_name in common_classes:
        elements = soup.find_all(class_=class_name)
        texts_to_check.extend([el.get_text(strip=True) for el in elements])
    
    # Buscar en elementos <strong> que podrían etiquetar las coordenadas
    strong_elements = soup.find_all('strong')
    for strong in strong_elements:
        parent_text = strong.parent.get_text(strip=True)
        if 'X:' in parent_text or 'Y:' in parent_text:
            texts_to_check.append(parent_text)
    
    # 2. Buscar en cualquier texto que contenga X: o Y:
    all_texts = soup.find_all(string=lambda text: 'X:' in str(text) or 'Y:' in str(text))
    texts_to_check.extend([text.strip() for text in all_texts if text.strip()])
    
    # 3. Procesar todos los textos candidatos
    for text in texts_to_check:
        # Buscar el patrón Y: <número> X: <número>
        pattern1 = r'Y:\s*(-?\d+\.\d+)\s*X:\s*(-?\d+\.\d+)'
        # Buscar el patrón X: <número> Y: <número>
        pattern2 = r'X:\s*(-?\d+\.\d+)\s*Y:\s*(-?\d+\.\d+)'
        
        for pattern in [pattern1, pattern2]:
            match = re.search(pattern, text)
            if match:
                try:
                    lat = float(match.group(1))
                    lon = float(match.group(2))
                    return {
                        'latitud': lat,
                        'longitud': lon,
                        'texto': f"Y: {lat} X: {lon}"
                    }
                except ValueError:
                    continue
    
    # 4. Último intento: buscar cualquier número decimal cerca de X: o Y:
    for text in texts_to_check:
        # Buscar Y: seguido de número
        y_match = re.search(r'Y:\s*(-?\d+\.\d+)', text)
        # Buscar X: seguido de número
        x_match = re.search(r'X:\s*(-?\d+\.\d+)', text)
        
        if y_match and x_match:
            try:
                lat = float(y_match.group(1))
                lon = float(x_match.group(1))
                return {
                    'latitud': lat,
                    'longitud': lon,
                    'texto': f"Y: {lat} X: {lon}"
                }
            except ValueError:
                continue
    
    # 5. Si todo falla, buscar dos números decimales juntos
    for text in texts_to_check:
        numbers = re.findall(r'-?\d+\.\d+', text)
        if len(numbers) >= 2:
            try:
                return {
                    'latitud': float(numbers[0]),
                    'longitud': float(numbers[1]),
                    'texto': f"Y: {numbers[0]} X: {numbers[1]}"
                }
            except ValueError:
                continue
    
    # Si no se encuentra nada
    
    return {'latitud': None, 'longitud': None, 'texto': None}
def extract_table_data(soup, title):
    """Extrae datos de una tabla estadística específica"""
    title_tag = soup.find(lambda tag: tag.name in ['h3', 'h4'] and title in str(tag.text))
    if not title_tag:
        return {}
    
    table = title_tag.find_next('table')
    if not table:
        return {}
    
    # Extraer encabezados (años)
    headers = [th.get_text(strip=True) for th in table.find_all('th')]
    years = [h for h in headers if h.replace('.', '').isdigit()]
    
    # Organizar datos
    stats = {}
    for tr in table.find_all('tr')[1:]:  # Saltar fila de encabezado
        cells = tr.find_all('td')
        if cells:
            # Limpiar nombre de categoría (eliminar números y espacios)
            category = re.sub(r'[\d\s]+', '', cells[0].get_text(strip=True))
            stats[category] = {
                years[i]: cell.get_text(strip=True) 
                for i, cell in enumerate(cells[1:]) 
                if i < len(years)
            }
    
    return stats

def extract_statistics(soup):
    """Extrae todas las estadísticas de las tablas"""
    stats = {}
    sections = [
        ('Matrícula escolar', 'matricula'),
        ('Estudiantes promovidos', 'promovidos'),
        ('Estudiantes reprobados', 'reprobados'),
        ('Estudiantes retirados por abandono', 'abandono')
    ]
    
    for section_title, section_key in sections:
        stats[section_key] = extract_table_data(soup, section_title)
    
    return stats


def extract_infrastructure_data(soup):
    """Extrae datos de infraestructura de la estructura HTML específica"""
    def get_value(label):
        # Buscar todos los elementos <b> que contengan exactamente el label
        b_tags = soup.find_all('b', string=lambda t: t and str(t).strip() == label.strip())
        
        for b_tag in b_tags:
            # El valor está en el next_sibling (texto después del </b>)
            value = b_tag.next_sibling
            if value:
                value = str(value).strip()
                # Limpiar valores como "--" o vacíos
                return value if value and value != '--' else None
        
        return None

    # Extraer datos de servicios (de los cuadros info-box)
    def get_service_data():
        services = {}
        service_labels = {
            'agua': 'Servicio de agua',
            'electricidad': 'Servicio de energía eléctrica',
            'banos': 'Baterías de baño',
            'internet': 'Internet'
        }
        
        for key, label in service_labels.items():
            span = soup.find('span', class_='info-box-number', string=lambda t: label in str(t))
            if span:
                value = span.find_next('strong').get_text(strip=True)
                services[key] = value if value != '--' else None
            else:
                services[key] = None
                
        return services

    return {
        'servicios': get_service_data(),
        'ambientes': {
            'aulas': get_value('Nº de Aulas:'),
            'laboratorios': get_value('Nº de Laboratorios:'),
            'bibliotecas': get_value('Nº de Bibliotecas:'),
            'computacion': get_value('Nº de Salas de Computación:'),
            'canchas': get_value('Nº de Canchas:'),
            'gimnasios': get_value('Nº de Gimnasios:'),
            'coliseos': get_value('Nº de Coliseos:'),
            'piscinas': get_value('Nº de Piscinas:'),
            'secretaria': get_value('Secretaría:'),
            'reuniones': get_value('Sala de reuniones'),
            'talleres': get_value('Nº de Talleres:')
        }
    }
    
    # Resto del código igual...
def get_info_box_data(soup, label):
    """Extrae datos de los cuadros de información"""
    span = soup.find('span', class_='info-box-number', string=lambda t: label in str(t))
    if span:
        value = span.find_next('strong').get_text(strip=True)
        return value if value != '--' else None
    return None

def extract_school_data(url):
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    }
    
    try:
        response = requests.get(url, headers=headers, timeout=15)
        response.raise_for_status()
        
        soup = BeautifulSoup(response.text, 'html.parser')
        #print("sopa",soup)
        # Extraer coordenadas
        #coordenadas = get_coordinates_from_html(soup)
        coordenadas = get_coordinates_from_html(soup)
        print(coordenadas)
        
        #debug_coordinates_extraction(soup, url)
        # Mostrar advertencia si no se encontraron coordenadas
        #if coordenadas['latitud'] is None and coordenadas['texto']:
        print("Advertencia: No se pudieron extraer coordenadas de: ",coordenadas)
        
        # Función auxiliar para extraer otros datos
        def get_dl_data(label):
            dt = soup.find('dt', class_='mayuscula', string=lambda t: label in str(t))
            if dt:
                dd = dt.find_next_sibling('dd')
                return dd.get_text(strip=True) if dd else None
            return None

        data = {
            'general': {
                'nombre': soup.find('strong', string=lambda t: 'UNIDAD EDUCATIVA:' in t).parent.get_text(strip=True).split(':')[-1].strip(),
                'codigo_rue': soup.find('strong', string=lambda t: 'CÓDIGO RUE:' in t).parent.get_text(strip=True).split(':')[-1].strip(),
                'director': get_dl_data('Director(a):'),
                'direccion': get_dl_data('Dirección:'),
                'telefonos': get_dl_data('Teléfono(s):'),
                'dependencia': get_dl_data('Dependencia:'),
                'niveles': get_dl_data('Nivel(es):'),
                'turnos': get_dl_data('Turno(s):'),
                'humanistico': extract_humanistico_data(soup),
                # 'tecnio': get_dl_data('Turno(s):')
            },
            'ubicacion': {
                'departamento': get_dl_data('Departamento:'),
                'provincia': get_dl_data('Provincia:'),
                'municipio': get_dl_data('Municipio:'),
                'distrito': get_dl_data('Distrito Educativo:'),
                'area': get_dl_data('Área Geográfica:'),
                'coordenadas': coordenadas
            },
            'estadisticas': extract_statistics(soup),
            'infraestructura': extract_infrastructure_data(soup),
            'url': url  # Guardar la URL de origen
        }
        
        return data
    
    except Exception as e:
        print(f"Error al procesar {url}: {str(e)}")
        return None
def extract_humanistico_data(soup):
    print("\n=== INICIANDO EXTRACCIÓN DE HUMANÍSTICO ===")
    
    # 1. Primero buscamos todos los posibles contenedores
    box_headers = soup.find_all('div', class_='box-header with-border')
    print(f"\nPaso 1: Encontrados {len(box_headers)} divs con clase 'box-header with-border'")
    
    for i, header in enumerate(box_headers, 1):
        print(f"\nPaso 2.{i}: Analizando div #{i}")
        #print("Contenido del div:", header.prettify()[:200] + "...")  # Mostramos solo el inicio
        
        # 2. Buscamos el h3 con el texto
        h3 = header.find('h3', class_='box-title')
        if h3:
            h3_text = h3.get_text(strip=True)
            print(f"Paso 3.{i}: Texto del h3 encontrado: '{h3_text}'")
            
            if 'bachillerato técnico humanístico:' in h3_text.lower():
                print("Paso 4.{i}: ¡Encontrado el h3 correcto!")
                
                # 3. Extraemos el valor
                parts = h3_text.split(':')
                if len(parts) > 1:
                    value = parts[-1].replace('&nbsp;', '').strip()
                    print(f"Paso 5.{i}: Valor crudo extraído: '{value}'")
                    
                    # Limpieza final
                    clean_value = value if value and value != '--' else None
                    print(f"Paso 6.{i}: Valor final: {clean_value}")
                    return clean_value
                else:
                    print(f"Paso 5.{i}: No se pudo dividir el texto por ':'")
            else:
                print(f"Paso 4.{i}: Este h3 no contiene el texto buscado")
        else:
            print(f"Paso 3.{i}: No se encontró h3 dentro de este div")
    
    print("\n=== FIN DE BÚSQUEDA - NO SE ENCONTRÓ EL DATO ===")
    return None

def process_range(start_code, end_code, output_file='colegios_data.json'):
    base_url = "https://seie.minedu.gob.bo/reportes/mapas_unidades_educativas/ficha/ver/"
    colegios_data = []
    
    for code in range(start_code, end_code + 1):
        url = f"{base_url}{code}"
        print(f"\nProcesando código RUE: {code}")
        
        data = extract_school_data(url)
        if data and data['general']['nombre']:
            colegios_data.append(data)
            print(f"✓ Datos encontrados: {data['general']['nombre']}")
            
            # Mostrar coordenadas extraídas
            coords = data['ubicacion']['coordenadas']
            if coords['latitud']:
                print(f"   Coordenadas: Lat {coords['latitud']}, Long {coords['longitud']}")
            else:
                print("   No se encontraron coordenadas")
            
            # Guardar progreso parcial cada 5 registros
            if len(colegios_data) % 5 == 0:
                with open(output_file, 'w', encoding='utf-8') as f:
                    json.dump(colegios_data, f, ensure_ascii=False, indent=2)
        else:
            print(f"✗ No se encontraron datos para código {code}")
    
    # Guardar resultados finales
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(colegios_data, f, ensure_ascii=False, indent=2)
    
    print(f"\nProceso completado. Datos guardados en {output_file}")
    print(f"Total de colegios procesados: {len(colegios_data)}")
    return colegios_data

# %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% m a i n %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
def guardar_json_incremental(nuevos_datos, output_file):
    # Leer lo anterior si ya existe
    if os.path.exists(output_file):
        with open(output_file, 'r', encoding='utf-8') as f:
            try:
                datos_existentes = json.load(f)
            except json.JSONDecodeError:
                datos_existentes = []
    else:
        datos_existentes = []

    # Agregar los nuevos datos
    datos_existentes.extend(nuevos_datos)

    # Guardar todo junto
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(datos_existentes, f, indent=4, ensure_ascii=False)

    print(f"✅ Se agregaron {len(nuevos_datos)} datos al archivo {output_file} (total: {len(datos_existentes)}).")

if __name__ == "__main__":
    print("=== EXTRACTOR DE DATOS DE COLEGIOS ===")

    output_file = 'colegios_data_completo.json'  # Un solo archivo

    intervalos = [
(71720033,71720033),
(71720060,71720060),
(71720011,71720011),
(71720042,71720042),
(71720030,71720030),
(71720044,71720044),
(71720028,71720028),
(71720038,71720038),
(71710056,71710056),
(71710023,71710023),
(71710048,71710048),
(71710057,71710057),
(71690046,71690046),
(71690065,71690065),
(71690043,71690043),
(71690063,71690063),
(71690012,71690012),
(71690022,71690022),
(71690030,71690030),
(71690021,71690021),
(71690002,71690002),
(71690008,71690008),
(71690017,71690017),
(71690072,71690072),
(71690059,71690059),
(81680098,81680098),
(81680080,81680080),
(81680097,81680097),
(81680087,81680087),
(81680084,81680084),
(81720003,81720003),
(81720097,81720097),
(81720090,81720090),
(81720080,81720080),
(81720091,81720091),
(81720098,81720098),
(81720086,81720086),
(81720083,81720083),
(81690008,81690008),
(81690017,81690017),
(81690013,81690013),
(81690073,81690073),
(81690006,81690006),
(81690085,81690085),
(81690082,81690082),
(81730213,81730213),
(81730234,81730234),
(81730182,81730182),
(81730180,81730180),
(81730221,81730221),
(81730061,81730061),
(81730218,81730218),
(81730309,81730309),
(81730310,81730310),
(81730124,81730124),
(81730080,81730080),
(81730183,81730183),
(81730215,81730215),
(81730227,81730227),
(81730291,81730291),
(81730126,81730126),
(81730137,81730137),
(81730038,81730038),
(81730226,81730226),
(81730119,81730119),
(81730145,81730145),
(81730228,81730228),
(81730259,81730259),
(81730185,81730185),
(81730212,81730212),
(81730007,81730007),
(81730186,81730186),
(81730150,81730150),
(81730179,81730179),
(81730307,81730307),
(81730229,81730229),
(81730033,81730033),
(81730220,81730220),
(81730096,81730096),
(81730026,81730026),
(81730255,81730255),
(81730092,81730092),
(81730099,81730099),
(81730306,81730306),
(81730021,81730021),
(81730261,81730261),
(81730196,81730196),
(81730193,81730193),
(81730190,81730190),
(81730214,81730214),
(81730311,81730311),
(81730195,81730195),
(81730266,81730266),
(81730091,81730091),
(81730232,81730232),
(81730239,81730239),
(81730198,81730198),
(81730267,81730267),
(81730264,81730264),
(81730225,81730225),
(81730197,81730197),
(81730222,81730222),
(81730245,81730245),
(81730236,81730236),
(81730233,81730233),
(81730237,81730237),
(81730192,81730192),
(81730200,81730200),
(81730238,81730238),
(81730187,81730187),
(81730246,81730246),
(81730201,81730201),
(81730235,81730235),
(81730194,81730194),
(81730199,81730199),
(81730299,81730299),
(81700025,81700025),
(81700021,81700021),
(81700041,81700041),
(81700053,81700053),
(81700048,81700048),
(81700046,81700046),
(61710081,61710081),
(61710098,61710098),
(61710024,61710024),
(61710067,61710067),
(61710060,61710060),
(61710057,61710057),
(61710070,61710070),
(61710073,61710073),
(61710054,61710054),
(81710133,81710133),
(81710027,81710027),
(81710105,81710105),
(81710132,81710132),
(81710015,81710015),
(81710064,81710064),
(81710134,81710134),
(81710089,81710089),
(81710072,81710072),
(81710131,81710131),
(81710075,81710075),
(81710078,81710078),
(81710121,81710121),
(81710077,81710077),
(71700044,71700044),
(71700041,71700041),


    ]


    for idx, (start_code, end_code) in enumerate(intervalos, start=1):
        print(f"\n[{idx}] Procesando códigos desde {start_code} hasta {end_code}...")

        if start_code > end_code:
            print(f"⚠️  Error: Código inicial {start_code} > final {end_code}, se omite.")
            continue

        nuevos_datos = process_range(start_code, end_code)
        guardar_json_incremental(nuevos_datos, output_file)

        """ espera = random.uniform(2, 6)
            print(f"⏳ Esperando {espera:.2f} segundos antes de continuar...")
            time.sleep(espera)
        """
    print("\n🎉 Todos los intervalos han sido procesados y agregados al JSON.")

