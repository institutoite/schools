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
"""(72220001,72220095),
(82220056,82220145),
(82220158,82220212),
(62160002,62160002),
(72160001,72160004),
(82160001,82160095),
(72210026,72210026),
(82210001,82210057),
(82220083,82220083),
(82220219,82220226),
(82210052,82210052),
(82220001,82220101),
(82220103,82220203),
(82220204,82220227),
(52210001,52210046),
(72180001,72180071),
(82180003,82180054),
(82220190,82220190),
(72210001,72210101),
(72210102,72210162),
(82190001,82190101),
(82190106,82190184),
(82200074,82200074),
(72230001,72230036),
(62170001,62170001),
(82170001,82170021),
(82170993,82171008),
(72170001,72170020),
(72200002,72200008),
(82200001,82200087),
(82220082,82220169),
(82220208,82220208),
(90240001,90240001),
(62210001,62210031),
(72230010,72230010),
(82160014,82160014),
(82220017,82220017),
(82230001,82230101),
(82230102,82230153),
(80470001,80470045),
(60420030,60420036),
(80420002,80420065),
(80400016,80400016),
(70400001,70400067),
(40450001,40450033),
(70440001,70440049),
(70390001,70390022),
(50460001,50460031),
(60420001,60420079),
(60400024,60400024),
(60390001,60390027),
(70390019,70390019),
(60460001,60460029),
(70460009,70460009),
(90490001,90490001),
(80440002,80440093),
(80450001,80450060),
(60480001,60480062),
(70460001,70460033),
(70420001,70420101),
(70420102,70420128),
(60480024,60480024),
(80480002,80480101),
(80480103,80480203),
(80480204,80480304),
(80480305,80480369),
(60450001,60450025),
(80430001,80430081),
(70470001,70470055),
(70450001,70450023),
(40450006,40450027),
(50450001,50450002),
(60400001,60400029),
(80400001,80400018),
(60420002,60420080),
(80410001,80410057),
(80390001,80390042),
(70430001,70430027),
(80430015,80430060),
(70480001,70480042),
(80460001,80460029),
(80970001,80970099),
(50950005,50950005),
(70950001,70950075),
(70940010,70940010),
(80830059,80830059),
(80940001,80940035),
(60950001,60950028),
(70930023,70930069),
(80930001,80930085),
(80960001,80960101),
(80960108,80960121),
(70930058,70930058),
(80840001,80840058),
(60920001,60920003),
(80920001,80920055),
(50870001,50870073),
(80910001,80910035),
(80980001,80980099),
(80980102,80980202),
(80980208,80980308),
(80980310,80980410),
(80980412,80980512),
(80980513,80980602),
(80980644,80980690),
(80980002,80980101),
(80980106,80980206),
(80980207,80980302),
(80980309,80980402),
(80980411,80980510),
(80980514,80980598),
(80980626,80980697),
(40900001,40900035),
(90990001,90990001),
(70890001,70890048),
(30870001,30870013),
(70870001,70870100),
(80860001,80860097),
(80860102,80860174),
(70960001,70960101),
(70960102,70960150),
(80830071,80830071),
(80900035,80900035),
(60970001,60970040),
(70870071,70870071),
(70970001,70970021),
(60870001,60870057),
(70940015,70940020),
(60970019,60970019),
(70870002,70870102),
(70870103,70870103),
(40870001,40870101),
(40870102,40870125),
(70870039,70870087),
(60850010,60850010),
(80850001,80850068),
(80940030,80940030),
(80900001,80900101),
(80900102,80900157),
(80980557,80980557),
(80890001,80890101),
(80890102,80890185),
(50950001,50950043),
(60850001,60850035),
(70920001,70920020),
(60890024,60890024),
(80830001,80830095),
(80830103,80830104),
(50900037,50900037),
(70900001,70900088),
(70930001,70930101),
(80880001,80880101),
(80880102,80880179),
(80950001,80950059),
(60900001,60900084),
(80830002,80830102),
(70910001,70910013),
(60910001,60910013),
(80870002,80870088),
(70940001,70940039),
(60860001,60860006),
(70860001,70860072),
(70940018,70940018),
(80860053,80860072),
(40850001,40850012),
(50850001,50850003),
(70850001,70850019),
(50870009,50870043),
(60890001,60890101),
(60890102,60890202),
(60890203,60890231),
(50900001,50900066),
(70900004,70900040),
(70680028,70680028),
(80670001,80670093),
(60680014,60680024),
(60690001,60690023),
(70690001,70690039),
(60610001,60610044),
(50620006,50620007),
(60620001,60620101),
(60620102,60620104),
(40650001,40650046),
(70630010,70630010),
(80650012,80650012),
(30640001,30640004),
(60640001,60640047),
(60710001,60710068),
(50610001,50610040),
(10710001,10710050),
(80710016,80710016),
(20710002,20710018),
(70710001,70710081),
(80710027,80710027),
(50630014,50630014),
(70680068,70680069),
(80540002,80540102),
(80540103,80540202),
(80540204,80540280),
(70580001,70580003),
(80580001,80580063),
(40710001,40710034),
(70550003,70550003),
(80550022,80550022),
(80630001,80630052),
(80690001,80690063),
(20610001,20610002),
(30610001,30610028),
(60610012,60610012),
(50640001,50640093),
(50710001,50710023),
(40680001,40680029),
(80570001,80570044),
(40630041,40630042),
(70600001,70600044),
(30710001,30710002),
(80710001,80710060),
(60630005,60630005),
(80600001,80600063),
(60560001,60560001),
(70560007,70560030),
(80560001,80560054),
(50660001,50660019),
(40730148,40730248),
(40730249,40730348),
(40730353,40730448),
(40730454,40730553),
(40730565,40730648),
(40730671,40730743),
(80620084,80620084),
(40730001,40730101),
(40730102,40730147),
(40730255,40730355),
(40730356,40730455),
(40730458,40730552),
(40730559,40730623),
(40730669,40730745),
(70620065,70620075),
(80730695,80730701),
(80730815,80730815),
(40730032,40730086),
(40730254,40730282),
(40730367,40730462),
(40730473,40730558),
(40730574,40730674),
(40730682,40730744),
(50730032,50730032),
(70620076,70620076),
(80730820,80730820),
(30680006,30680006),
(70680001,70680100),
(70680110,70680120),
(70660001,70660023),
(80720063,80720124),
(80720190,80720224),
(40640001,40640042),
(80640001,80640101),
(80640102,80640104),
(70630001,70630071),
(80630037,80630037),
(70590001,70590016),
(80590019,80590065),
(30660002,30660002),
(80660057,80660157),
(80660158,80660200),
(40630001,40630101),
(40630102,40630123),
(80540122,80540220),
(70620001,70620086),
(40730020,40730021),
(80730001,80730101),
(80730102,80730151),
(80730251,80730348),
(80730362,80730462),
(80730463,80730560),"""
(80730564,80730661),
(80730671,80730767),
(80730778,80730835),
(80730879,80730953),
(80730083,80730173),
(80730186,80730230),
(80730306,80730406),
(80730407,80730507),
(80730512,80730608),
(80730617,80730715),
(80730719,80730819),
(80730822,80730900),
(80730929,80730955),
(40730205,40730210),
(80730152,80730252),
(80730253,80730304),
(80730509,80730609),
(80730610,80730702),
(80730718,80730803),
(80730821,80730893),
(80730922,80730957),
(80650001,80650054),
(20680001,20680005),
(70680008,70680105),
(60700001,60700033),
(70700001,70700075),
(80700033,80700115),
(50630001,50630087),
(70560001,70560033),
(80560010,80560044),
(40610001,40610078),
(60610010,60610010),
(70670001,70670031),
(80670034,80670034),
(50620024,50620031),
(60620041,60620100),
(80620001,80620083),
(80700001,80700101),
(80700102,80700134),
(60700002,60700075),
(50620001,50620023),
(70640001,70640029),
(60730029,60730034),
(70730001,70730068),
(60730001,60730051),
(70730044,70730056),
(50730001,50730055),
(60730032,60730032),
(80720001,80720093),
(80720114,80720214),
(80720215,80720226),
(70720001,70720056),
(40660001,40660001),
(80660049,80660149),
(80660150,80660204),
(70590007,70590030),
(80590001,80590018),
(70550001,70550009),
(80550001,80550025),
(40650017,40650017),
(60730020,60730020),
(70650001,70650054),
(80650004,80650004),
(30610018,30610018),
(60610018,60610018),
(80610001,80610085),
(40680015,40680026),
(60680026,60680026),
(70680064,70680064),
(70720042,70720042),
(80680001,80680089),
(50680001,50680001),
(60680001,60680046),
(10680001,10680004),
(70680045,70680109),
(20660001,20660001),
(60660001,60660056),
(30680001,30680025),
(60570001,60570007),
(70570001,70570020),
(70610001,70610043),
(40730080,40730081),
(80660001,80660048),
(80660124,80660224),
(80660225,80660244),
(40650002,40650018),
(50650001,50650001),
(60650001,60650038),
(70650045,70650045),
(80650039,80650042),
(60630001,60630025),
(80630025,80630025),
(81120001,81120028),
(61230001,61230001),
(71230001,71230068),
(81230019,81230019),
(81200001,81200024),
(71180011,71180011),
(71220001,71220002),
(81100001,81100033),
(81220001,81220096),
(71210001,71210001),
(81210001,81210043),
(61230002,61230020),
(81130001,81130013),
(81190001,81190009),
(81170001,81170032),
(81080001,81080025),
(71170001,71170013),
(51230001,51230001),
(81230040,81230140),
(81230141,81230241),
(81230242,81230333),
(81230350,81230399),
(51230002,51230010),
(81230001,81230061),
(81230181,81230240),
(61180002,61180003),
(71180001,71180018),
(81180001,81180012),
(81180002,81180020),
(61150001,61150001),
(81090001,81090005),
(81150001,81150022),
(71160001,71160002),
(81150005,81150005),
(81160001,81160053),
(81100003,81100035),
(81140001,81140020),
(81110001,81110032),
(71200001,71200022),
(82460001,82460033),
(62470001,62470079),
(82470023,82470023),
(82490005,82490012),
(62460004,62460072),
(92490001,92490003),
(52480016,52480047),
(72480001,72480048),
(82450002,82450002),
(82480001,82480074),
(62460024,62460025),
(72460001,72460040),
(82460007,82460021),
(62480001,62480024),
(82470031,82470031),
(62470069,62470069),
(72450012,72450030),
(82450008,82450024),
(62440013,62440032),
(72440002,72440022),
(82440001,82440009),
(52490015,52490015),
(62470024,62470034),
(62480008,62480008),
(72470002,72470007),
(82450004,82450004),
(82470001,82470073),
(82490013,82490014),
(71360001,71360029),
(81360010,81360010),
(81360001,81360034),
(71410001,71410038),
(81460001,81460101),
(81460103,81460125),
(71380004,71380050),
(71420001,71420049),
(71460001,71460041),
(71470001,71470052),
(81380044,81380143),
(81380150,81380169),
(71400001,71400007),
(81330001,81330002),
(81400001,81400074),
(51450036,51450040),
(81450001,81450101),
(81450102,81450130),
(81430001,81430101),
(81430102,81430149),
(61470001,61470060),
(71470041,71470042),
(71350001,71350003),
(81350001,81350043),
(51450001,51450058),
(71450071,71450073),
(81450051,81450103),
(61450001,61450096),
(61450103,61450121),
(61370001,61370022),
(51480001,51480007),
(61480023,61480057),
(71480001,71480032),
(71480103,71480125),
(81480001,81480101),
(81480102,81480202),
(81480203,81480260),
(71380001,71380043),
(81380001,81380098),
(81380103,81380167),
(51450004,51450008),
(71450001,71450083),
(81420001,81420086),
(61390001,61390002),
(71390001,71390001),
(81390001,81390028),
(81440001,81440101),
(81440102,81440141),
(61460001,61460060),
(61480006,61480007),
(71480011,71480102),
(71480113,71480146),
(71370001,71370075),
(81370022,81370082),
(71440001,71440066),
(81410001,81410101),
(81410102,81410167),
(81470001,81470089),
(51480005,51480006),
(81370001,81370097),
(81340001,81340091),
(71430001,71430057),
(81430107,81430107),
(61480001,61480058),
(81480211,81480211),
(61980001,61980044),
(21920001,21920014),
(81950001,81950035),
(81950103,81950118),
(41980057,41980057),
(61920001,61920059),
(31920001,31920066),
(71920001,71920087),
(81860001,81860058),
(81880001,81880101),
(81880103,81880106),
(71980001,71980081),
(31880001,31880010),
(61880001,61880100),
(61880102,61880179),
(51920001,51920024),
(61840001,61840052),
(41980005,41980104),
(41980106,41980115),
(81840001,81840044),
(41920001,41920051),
(51980001,51980101),
(51980102,51980136),
(81920001,81920027),
(61900001,61900034),
(41890001,41890008),
(51890001,51890002),
(61890002,61890102),
(61890103,61890134),
(81890001,81890100),
(81890102,81890150),
(61910001,61910023),
(71970001,71970006),
(81970026,81970098),
(71940001,71940036),
(71900001,71900022),
(81980045,81980144),
(81980162,81980189),
(81980317,81980379),
(81980518,81980616),
(81980619,81980718),
(81980721,81980779),
(81980955,81981043),
(81981089,81981188),
(81981200,81981298),
(81981301,81981401),
(81981402,81981485),
(81981505,81981602),
(81981633,81981732),
(81981737,81981779),
(61930001,61930002),
(81930001,81930073),
(51910001,51910017),
(41910001,41910023),
(71850001,71850024),
(61850001,61850003),
(81850001,81850058),
(51900001,51900010),
(61940001,61940065),
(81870047,81870047),
(81940005,81940021),
(71890001,71890041),
(71860001,71860018),
(81900001,81900038),
(81901001,81901009),
(41880001,41880002),
(81880038,81880078),
(71950001,71950077),
(71960022,71960022),
(81960001,81960101),
(81960102,81960171),
(61880185,61880191),
(71880001,71880039),
(81940001,81940056),
(51950001,51950002),
(71950016,71950064),
(31880002,31880007),
(51880001,51880035),
(61840020,61840020),
(61880005,61880104),
(61880106,61880184),
(61890114,61890114),
(81870001,81870058),
(71960001,71960043),
(61960001,61960019),
(80480368,80480368),
(81901000,81901000),
(81980002,81980102),
(81980103,81980203),
(81980204,81980220),
(81980353,81980437),
(81980987,81981086),
(81981088,81981186),
(81981194,81981282),
(81981302,81981400),
(81981403,81981494),
(81981511,81981605),
(81981635,81981733),
(81981748,81981774),
(81980019,81980029),
(81980210,81980310),
(81980311,81980352),
(81980665,81980761),
(81980766,81980866),
(81980868,81980968),
(81980969,81981068),
(81981071,81981169),
(81981172,81981271),
(81981279,81981379),
(81981380,81981477),
(81981487,81981564),
(81981599,81981691),
(81981722,81981777),
(81980069,81980163),
(81980170,81980270),
(81980272,81980288),
(81980440,81980540),
(81980545,81980574),
(81980772,81980772),
(81980988,81981082),
(81981109,81981207),
(81981211,81981304),
(81981314,81981414),
(81981416,81981513),
(81981518,81981603),
(81981630,81981721),
(81981745,81981745),
(71930001,71930094),
(71910002,71910028),
(71840002,71840025),
(81910002,81910102),
(81910103,81910111),
(81970001,81970101),
(81970102,81970164),
(81980405,81980405),
(61950001,61950087),
(81950038,81950102),
(71720001,71720060),
(81720018,81720018),
(71710001,71710058),
(71690001,71690072),
(81680001,81680101),
(81730133,81730133),
(81720001,81720098),
(81690001,81690090),
(81730001,81730101),
(81730102,81730201),
(81730212,81730311),
(81700001,81700054),
(61710001,61710099),
(61710021,61710093),
(81710001,81710101),
(81710102,81710134),
(71700001,71700044),


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

