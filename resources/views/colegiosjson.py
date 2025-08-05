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
        (20680003,20680003),
(70680008,70680008),
(80720204,80720204),
(80720206,80720206),
(80720146,80720146),
(80720093,80720093),
(80720202,80720202),
(80720074,80720074),
(80720203,80720203),
(80720213,80720213),
(80720119,80720119),
(80720205,80720205),
(80720077,80720077),
(80720070,80720070),
(80720217,80720217),
(80720226,80720226),
(80720225,80720225),
(80720201,80720201),
(80720219,80720219),
(80720200,80720200),
(80720211,80720211),
(80720199,80720199),
(50730054,50730054),
(50730036,50730036),
(50730031,50730031),
(50730053,50730053),
(50730055,50730055),
(50730041,50730041),
(50730038,50730038),
(70720012,70720012),
(70720050,70720050),
(70720054,70720054),
(70720055,70720055),
(80670087,80670087),
(80670042,80670042),
(80670051,80670051),
(80670007,80670007),
(80670065,80670065),
(80670092,80670092),
(80670086,80670086),
(80670064,80670064),
(80670040,80670040),
(80670062,80670062),
(80670093,80670093),
(80670079,80670079),
(60690011,60690011),
(60690015,60690015),
(60690013,60690013),
(60690020,60690020),
(60610011,60610011),
(60620053,60620053),
(60620094,60620094),
(60620057,60620057),
(60620049,60620049),
(60620093,60620093),
(60620052,60620052),
(60620104,60620104),
(60620098,60620098),
(60620095,60620095),
(60620103,60620103),
(40650046,40650046),
(60640010,60640010),
(60640038,60640038),
(60640034,60640034),
(30640003,30640003),
(60640046,60640046),
(30640004,30640004),
(60640045,60640045),
(60710066,60710066),
(60710016,60710016),
(60710023,60710023),
(60710006,60710006),
(60710049,60710049),
(60710053,60710053),
(60710025,60710025),
(60710062,60710062),
(60710068,60710068),
(50610018,50610018),
(50610040,50610040),
(50610037,50610037),
(50610039,50610039),
(70710030,70710030),
(70710055,70710055),
(70710052,70710052),
(70710021,70710021),
(70710081,70710081),
(20710017,20710017),
(70710080,70710080),
(20710018,20710018),
(80540094,80540094),
(80540276,80540276),
(80540280,80540280),
(80540071,80540071),
(80540210,80540210),
(80540076,80540076),
(80540058,80540058),
(80540167,80540167),
(80540117,80540117),
(80540059,80540059),
(80540179,80540179),
(80540176,80540176),
(80540183,80540183),
(80540237,80540237),
(80540063,80540063),
(80540047,80540047),
(80540172,80540172),
(80540113,80540113),
(80540178,80540178),
(80540108,80540108),
(80540235,80540235),
(80540240,80540240),
(80540278,80540278),
(80540236,80540236),
(80540171,80540171),
(80540145,80540145),
(80540279,80540279),
(80540230,80540230),
(80540138,80540138),
(80540252,80540252),
(80540055,80540055),
(80540211,80540211),
(80540219,80540219),
(80540144,80540144),
(80540242,80540242),
(80540164,80540164),
(80540004,80540004),
(80540277,80540277),
(80540039,80540039),
(80540218,80540218),
(80540261,80540261),
(80540170,80540170),
(80540274,80540274),
(80540233,80540233),
(80540275,80540275),
(80580055,80580055),
(80580023,80580023),
(80580004,80580004),
(80580057,80580057),
(80580063,80580063),
(70580001,70580001),
(40710027,40710027),
(40710005,40710005),
(40710028,40710028),
(40710033,40710033),
(80630052,80630052),
(80630040,80630040),
(80630042,80630042),
(80630048,80630048),
(80630051,80630051),
(80630044,80630044),
(80690063,80690063),
(80690062,80690062),
(80690021,80690021),
(80690024,80690024),
(80690031,80690031),
(80690041,80690041),
(80690044,80690044),
(80690050,80690050),
(80690052,80690052),
(80690055,80690055),
(80690057,80690057),
(80690058,80690058),
(30610014,30610014),
(30610026,30610026),
(30610028,30610028),
(30610027,30610027),
(50640057,50640057),
(50640058,50640058),
(50640015,50640015),
(50640076,50640076),
(50640072,50640072),
(50640073,50640073),
(50640077,50640077),
(50640063,50640063),
(50640046,50640046),
(50640026,50640026),
(50640020,50640020),
(50640005,50640005),
(50640022,50640022),
(50640092,50640092),
(50640038,50640038),
(50640008,50640008),
(50640056,50640056),
(50640093,50640093),
(50640091,50640091),
(50640031,50640031),
(50640075,50640075),
(50640086,50640086),
(50640078,50640078),
(50640079,50640079),
(50640088,50640088),
(50710023,50710023),
(40680003,40680003),
(80570043,80570043),
(80570018,80570018),
(80570044,80570044),
(80570042,80570042),
(80710053,80710053),
(80710055,80710055),
(30710001,30710001),
(80710059,80710059),
(80600047,80600047),
(80600020,80600020),
(80600059,80600059),
(80600053,80600053),
(50660003,50660003),
(50660017,50660017),
(50660018,50660018),
(50660019,50660019),
(40730384,40730384),
(40730381,40730381),
(40730340,40730340),
(40730274,40730274),
(40730411,40730411),
(40730349,40730349),
(40730455,40730455),
(40730379,40730379),
(40730012,40730012),
(40730304,40730304),
(40730332,40730332),
(40730041,40730041),
(40730383,40730383),
(40730022,40730022),
(40730409,40730409),
(40730364,40730364),
(40730289,40730289),
(40730297,40730297),
(40730382,40730382),
(40730087,40730087),
(40730270,40730270),
(40730352,40730352),
(40730343,40730343),
(40730564,40730564),
(40730052,40730052),
(40730721,40730721),
(40730361,40730361),
(40730387,40730387),
(40730371,40730371),
(40730386,40730386),
(40730351,40730351),
(40730287,40730287),
(40730331,40730331),
(40730360,40730360),
(40730561,40730561),
(40730355,40730355),
(40730486,40730486),
(80730701,80730701),
(40730330,40730330),
(40730350,40730350),
(40730359,40730359),
(40730281,40730281),
(40730494,40730494),
(40730055,40730055),
(40730356,40730356),
(40730145,40730145),
(40730531,40730531),
(40730566,40730566),
(40730324,40730324),
(40730523,40730523),
(40730745,40730745),
(40730541,40730541),
(40730323,40730323),
(40730328,40730328),
(40730604,40730604),
(40730322,40730322),
(40730603,40730603),
(40730716,40730716),
(40730508,40730508),
(80730695,80730695),
(40730327,40730327),
(40730256,40730256),
(80730815,80730815),
(40730326,40730326),
(40730475,40730475),
(40730596,40730596),
(40730532,40730532),
(40730515,40730515),
(40730329,40730329),
(40730737,40730737),
(40730728,40730728),
(80730820,80730820),
(40730730,40730730),
(40730744,40730744),
(40730740,40730740),
(40730739,40730739),
(40730738,40730738),
(40730705,40730705),
(40730425,40730425),
(40730431,40730431),
(40730682,40730682),
(40730703,40730703),
(40730704,40730704),
(40730406,40730406),
(40730288,40730288),
(40730306,40730306),
(40730279,40730279),
(40730414,40730414),
(40730278,40730278),
(40730743,40730743),
(40730741,40730741),
(40730735,40730735),
(80620084,80620084),
(40730733,40730733),
(40730277,40730277),
(40730727,40730727),
(40730291,40730291),
(40730377,40730377),
(40730407,40730407),
(40730219,40730219),
(40730731,40730731),
(40730300,40730300),
(40730378,40730378),
(40730480,40730480),
(40730299,40730299),
(40730347,40730347),
(40730742,40730742),
(40730405,40730405),
(40730333,40730333),
(40730310,40730310),
(40730345,40730345),
(40730302,40730302),
(40730734,40730734),
(40730736,40730736),
(40730416,40730416),
(40730722,40730722),
(40730725,40730725),
(40730311,40730311),
(40730269,40730269),
(40730264,40730264),
(40730348,40730348),
(40730315,40730315),
(40730321,40730321),
(40730540,40730540),
(40730711,40730711),
(40730433,40730433),
(40730524,40730524),
(40730648,40730648),
(40730493,40730493),
(40730565,40730565),
(40730726,40730726),
(40730334,40730334),
(40730412,40730412),
(40730549,40730549),
(40730602,40730602),
(40730318,40730318),
(40730317,40730317),
(40730320,40730320),
(40730506,40730506),
(70680113,70680113),
(70680046,70680046),
(70680032,70680032),
(70680120,70680120),
(70680020,70680020),
(70680119,70680119),
(70680110,70680110),
(80720094,80720094),
(80720216,80720216),
(80720212,80720212),
(80720220,80720220),
(40640022,40640022),
(40640011,40640011),
(40640008,40640008),
(40640037,40640037),
(40640029,40640029),
(40640041,40640041),
(80640008,80640008),
(80640014,80640014),
(80640092,80640092),
(80640012,80640012),
(80640100,80640100),
(80640050,80640050),
(80640028,80640028),
(80640051,80640051),
(80640097,80640097),
(80640022,80640022),
(80640027,80640027),
(80640096,80640096),
(80640077,80640077),
(70630033,70630033),
(70630048,70630048),
(70630041,70630041),
(70630071,70630071),
(70630059,70630059),
(80590065,80590065),
(80590046,80590046),
(80590043,80590043),
(70590008,70590008),
(80590028,80590028),
(80590047,80590047),
(80590036,80590036),
(80590042,80590042),
(80590062,80590062),
(80590045,80590045),
(80590064,80590064),
(80660109,80660109),
(80660089,80660089),
(80660090,80660090),
(30660002,30660002),
(80660200,80660200),
(80660194,80660194),
(80660196,80660196),
(80660192,80660192),
(40630091,40630091),
(40630123,40630123),
(40630122,40630122),
(40630118,40630118),
(40630119,40630119),
(40630059,40630059),
(40630117,40630117),
(40630121,40630121),
(40630116,40630116),
(40630120,40630120),
(40630112,40630112),
(70620035,70620035),
(70620074,70620074),
(70620020,70620020),
(70620039,70620039),
(70620077,70620077),
(70620061,70620061),
(70620042,70620042),
(70620086,70620086),
(70620080,70620080),
(70620073,70620073),
(70620083,70620083),
(80730767,80730767),
(80730061,80730061),
(80730748,80730748),
(80730953,80730953),
(80730001,80730001),
(80730746,80730746),
(80730348,80730348),
(80730493,80730493),
(80730951,80730951),
(80730766,80730766),
(80730016,80730016),
(80730018,80730018),
(80730486,80730486),
(80730059,80730059),
(80730141,80730141),
(80730034,80730034),
(80730717,80730717),
(80730750,80730750),
(80730115,80730115),
(80730052,80730052),
(80730143,80730143),
(80730714,80730714),
(80730564,80730564),
(80730063,80730063),
(80730793,80730793),
(80730782,80730782),
(80730778,80730778),
(80730506,80730506),
(80730716,80730716),
(80730058,80730058),
(80730111,80730111),
(80730039,80730039),
(80730637,80730637),
(80730705,80730705),
(80730470,80730470),
(80730128,80730128),
(80730727,80730727),
(80730511,80730511),
(80730950,80730950),
(80730943,80730943),
(80730721,80730721),
(80730054,80730054),
(80730130,80730130),
(80730149,80730149),
(80730952,80730952),
(80730011,80730011),
(80730566,80730566),
(80730755,80730755),
(80730597,80730597),
(80730571,80730571),
(80730818,80730818),
(80730634,80730634),
(80730635,80730635),
(80730732,80730732),
(80730648,80730648),
(80730649,80730649),
(80730729,80730729),
(80730745,80730745),
(80730817,80730817),
(80730733,80730733),
(80730661,80730661),
(80730604,80730604),
(80730693,80730693),
(80730598,80730598),
(80730588,80730588),
(80730684,80730684),
(80730599,80730599),
(80730728,80730728),
(80730640,80730640),
(80730638,80730638),
(80730671,80730671),
(80730507,80730507),
(80730942,80730942),
(80730949,80730949),
(80730831,80730831),
(80730955,80730955),
(80730434,80730434),
(80730310,80730310),
(80730703,80730703),
(80730351,80730351),
(80730742,80730742),
(80730361,80730361),
(80730542,80730542),
(80730096,80730096),
(80730941,80730941),
(80730711,80730711),
(80730409,80730409),
(80730545,80730545),
(80730938,80730938),
(80730722,80730722),
(80730740,80730740),
(80730384,80730384),
(80730760,80730760),
(80730523,80730523),
(80730754,80730754),
(80730665,80730665),
(80730329,80730329),
(80730937,80730937),
(80730355,80730355),
(80730411,80730411),
(80730806,80730806),
(80730457,80730457),
(80730495,80730495),
(80730444,80730444),
(80730737,80730737),
(80730440,80730440),
(80730168,80730168),
(80730667,80730667),
(80730366,80730366),
(80730437,80730437),
(80730898,80730898),
(80730540,80730540),
(80730934,80730934),
(80730426,80730426),
(80730747,80730747),
(80730401,80730401),
(80730494,80730494),
(80730645,80730645),
(80730512,80730512),
(80730568,80730568),
(80730809,80730809),
(80730707,80730707),
(80730357,80730357),
(80730381,80730381),
(80730940,80730940),
(80730475,80730475),
(80730388,80730388),
(80730710,80730710),
(80730629,80730629),
(80730558,80730558),
(80730939,80730939),
(80730577,80730577),
(80730706,80730706),
(80730808,80730808),
(80730479,80730479),
(80730422,80730422),
(80730773,80730773),
(80730352,80730352),
(80730799,80730799),
(80730374,80730374),
(80730417,80730417),
(80730514,80730514),
(80730720,80730720),
(80730807,80730807),
(80730504,80730504),
(80730662,80730662),
(80730954,80730954),
(80730709,80730709),
(80730499,80730499),
(80730582,80730582),
(80730734,80730734),
(80730836,80730836),
(80730628,80730628),
(80730796,80730796),
(80730658,80730658),
(80730639,80730639),
(80730586,80730586),
(80730690,80730690),
(80730600,80730600),
(80730669,80730669),
(80730632,80730632),
(80730696,80730696),
(80730619,80730619),
(80730827,80730827),
(80730768,80730768),
(80730601,80730601),
(80730584,80730584),
(80730653,80730653),
(80730743,80730743),
(80730642,80730642),
(80730723,80730723),
(80730663,80730663),
(80730670,80730670),
(80730761,80730761),
(80730666,80730666),
(80730414,80730414),
(80730794,80730794),
(80730816,80730816),
(80730644,80730644),
(80730719,80730719),
(80730929,80730929),
(80730641,80730641),
(80730608,80730608),
(80730631,80730631),
(80730726,80730726),
(80730627,80730627),
(80730617,80730617),
(80730741,80730741),
(80730784,80730784),
(80730945,80730945),
(80730276,80730276),
(80730236,80730236),
(80730242,80730242),
(80730946,80730946),
(80730947,80730947),
(80730204,80730204),
(80730195,80730195),
(80730255,80730255),
(80730763,80730763),
(80730196,80730196),
(80730235,80730235),
(80730789,80730789),
(80730215,80730215),
(80730530,80730530),
(80730557,80730557),
(80730212,80730212),
(80730538,80730538),
(80730272,80730272),
(80730225,80730225),
(80730241,80730241),
(80730190,80730190),
(80730757,80730757),
(80730181,80730181),
(80730565,80730565),
(80730529,80730529),
(80730570,80730570),
(80730700,80730700),
(80730221,80730221),
(80730956,80730956),
(80730269,80730269),
(80730569,80730569),
(80730301,80730301),
(80730155,80730155),
(80730957,80730957),
(80730651,80730651),
(80730668,80730668),
(80730616,80730616),
(80730591,80730591),
(80730613,80730613),
(80730821,80730821),
(80730659,80730659),
(80730781,80730781),
(80730893,80730893),
(80730607,80730607),
(80730657,80730657),
(80730660,80730660),
(80730197,80730197),
(80730615,80730615),
(80730611,80730611),
(80730655,80730655),
(80730596,80730596),
(80730718,80730718),
(80730605,80730605),
(80730609,80730609),
(80730633,80730633),
(80730654,80730654),
(80730610,80730610),
(80650027,80650027),
(80650034,80650034),
(80650043,80650043),
(80650028,80650028),
(80650048,80650048),
(80650053,80650053),
(80650054,80650054),
(60730013,60730013),
(60730045,60730045),
(60730030,60730030),
(60730047,60730047),
(60730051,60730051),
(70700070,70700070),
(70700034,70700034),
(70700071,70700071),
(70700030,70700030),
(80700037,80700037),
(70700015,70700015),
(70700075,70700075),
(70700073,70700073),
(70700067,70700067),
(50630052,50630052),
(50630053,50630053),
(50630087,50630087),
(50630016,50630016),
(50630007,50630007),
(50630051,50630051),
(50630054,50630054),
(50630040,50630040),
(50630079,50630079),
(50630058,50630058),
(50630065,50630065),
(50630034,50630034),
(50630048,50630048),
(50630049,50630049),
(50630066,50630066),
(70560003,70560003),
(70560019,70560019),
(70560033,70560033),
(40610078,40610078),
(40610053,40610053),
(40610061,40610061),
(40610056,40610056),
(40610055,40610055),
(40610063,40610063),
(40610073,40610073),
(70670031,70670031),
(80670034,80670034),
(70670030,70670030),
(70670029,70670029),
(80620004,80620004),
(80620024,80620024),
(80620069,80620069),
(80620083,80620083),
(80620071,80620071),
(80620048,80620048),
(60620059,60620059),
(80620025,80620025),
(80620065,80620065),
(80620070,80620070),
(80620077,80620077),
(80620067,80620067),
(80620068,80620068),
(60620100,60620100),
(80620079,80620079),
(80620075,80620075),
(80700075,80700075),
(80700134,80700134),
(80700112,80700112),
(80700056,80700056),
(80700048,80700048),
(80700014,80700014),
(80700121,80700121),
(80700132,80700132),
(80700122,80700122),
(60700030,60700030),
(60700058,60700058),
(60700068,60700068),
(60700075,60700075),
(60700074,60700074),
(50620017,50620017),
(50620004,50620004),
(70640020,70640020),
(70640021,70640021),
(70640023,70640023),
(70640027,70640027),
(70640029,70640029),
(40660001,40660001),
(80560018,80560018),
(80560033,80560033),
(80560037,80560037),
(80560026,80560026),
(80560054,80560054),
(70570019,70570019),
(70570020,70570020),
(70590030,70590030),
(70590027,70590027),
(70590028,70590028),
(10710029,10710029),
(10710034,10710034),
(10710026,10710026),
(10710003,10710003),
(80710016,80710016),
(70550009,70550009),
(70650029,70650029),
(70650054,70650054),
(80610071,80610071),
(80610084,80610084),
(80610064,80610064),
(80610085,80610085),
(80610072,80610072),
(80610041,80610041),
(80610063,80610063),
(80610017,80610017),
(80610074,80610074),
(80610034,80610034),
(80610062,80610062),
(80610061,80610061),
(80610023,80610023),
(80610016,80610016),
(80610081,80610081),
(80610083,80610083),
(80610082,80610082),
(80680057,80680057),
(80680065,80680065),
(80680018,80680018),
(80680026,80680026),
(40680026,40680026),
(80680023,80680023),
(80680022,80680022),
(80680089,80680089),
(80680064,80680064),
(70680064,70680064),
(40680021,40680021),
(80680078,80680078),
(80680019,80680019),
(80680077,80680077),
(80680080,80680080),
(80680088,80680088),
(80680082,80680082),
(60680005,60680005),
(60680039,60680039),
(60680042,60680042),
(50680001,50680001),
(60680040,60680040),
(60680046,60680046),
(70680090,70680090),
(70680108,70680108),
(70680092,70680092),
(70680059,70680059),
(70680062,70680062),
(70680107,70680107),
(70680061,70680061),
(70680071,70680071),
(60660004,60660004),
(20660001,20660001),
(60660052,60660052),
(60660054,60660054),
(60660053,60660053),
(60660056,60660056),
(30680019,30680019),
(30680016,30680016),
(30680017,30680017),
(30680025,30680025),
(70610008,70610008),
(70610014,70610014),
(70610002,70610002),
(80660190,80660190),
(80660243,80660243),
(80660124,80660124),
(80660176,80660176),
(80660046,80660046),
(80660244,80660244),
(80660012,80660012),
(80660164,80660164),
(80660242,80660242),
(80660210,80660210),
(80660197,80660197),
(80660195,80660195),
(80660237,80660237),
(80660219,80660219),
(80660224,80660224),
(80660198,80660198),
(80660225,80660225),
(80660218,80660218),
(80660191,80660191),
(60650038,60650038),
(60650036,60650036),
(60630014,60630014),
(60630025,60630025),
(60630024,60630024),
(60630020,60630020),
(70730041,70730041),
(70730058,70730058),
(70730063,70730063),
(70730067,70730067),


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

