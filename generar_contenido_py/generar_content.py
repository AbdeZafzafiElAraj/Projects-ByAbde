import re
import html
import unicodedata
import logging
import os
from typing import List, Dict

from dotenv import load_dotenv
import openai

# Carregar variables del .env (inclosa OPENAI_API_KEY)
load_dotenv()
openai.api_key = os.getenv("OPENAI_API_KEY")

# Configurar el logger
logging.basicConfig(level=logging.INFO, format="%(asctime)s - %(levelname)s - %(message)s")
logger = logging.getLogger(__name__)

# ✨ Correccions addicionals
CORRECCIONES_CATALANES = {
    "lopció": "l'opció",
    "lempresa": "l'empresa",
    "lactivitat": "l'activitat",
    "linforme": "l'informe",
    "lestudiant": "l'estudiant",
    "lassignatura": "l'assignatura",
    "lobjectiu": "l'objectiu",
    "dexistències": "d'existències",
    "dexplotació": "d'explotació",
    "dany": "d'any",
    "dempresa": "d'empresa",
    "l'immovil": "l'immobilitzat",
    "l'immovil.": "l'immobilitzat.",
    "immovil": "immobilitzat"
}

SIMBOLOS_MATEMATICOS = {
    r'\$': '',  # Eliminar delimitadores de fórmulas
    r'\^': ' elevat a ',
    r'\^\{([^}]+)\}': ' elevat a \1',
    r'_': ' sub ',
    r'_\{(.+?)\}': ' sub \1',
    r'\\frac\{(.+?)\}\{(.+?)\}': '\1 partit per \2',
    r'\\sqrt\{(.+?)\}': 'arrel quadrada de \1',
    r'\\sqrt\[(.+?)\]\{(.+?)\}': 'arrel \1 de \2',
    r'\\pi': 'pi',
    r'\\alpha': 'alfa',
    r'\\beta': 'beta',
    r'\\gamma': 'gamma',
    r'\\cdot': ' per ',
    r'\\times': ' per ',
    r'\\div': ' dividit per ',
    r'\\pm': ' més o menys ',
    r'\\approx': ' aproximadament igual a ',
    r'\\neq': ' diferent de ',
    r'\\leq': ' menor o igual que ',
    r'\\geq': ' major o igual que ',
    r'\\infty': ' infinit ',
    r'\\sum': ' sumatori ',
    r'\\int': ' integral ',
    r'\\lim': ' límit ',
    r'\\log': ' logaritme ',
    r'\\ln': ' logaritme neperià ',
    r'\\sin': ' sinus ',
    r'\\cos': ' cosinus ',
    r'\\tan': ' tangent ',
    r'\\theta': ' theta ',
    r'\\Delta': ' delta ',
    r'\\Omega': ' omega ',
    r'\\rightarrow': ' tendeix a ',
    r'\\leftarrow': ' prové de ',
    r'\\Rightarrow': ' per tant ',
    r'\\Leftrightarrow': ' si i només si ',
    
}

FORMULAS_QUIMICAS = {
    r'H_2O': 'aigua',
    r'CO_2': 'diòxid de carboni',
    r'CH_4': 'metà',
    r'NaCl': 'clorur de sodi',
    r'H_2SO_4': 'àcid sulfúric',
    r'NaOH': 'hidròxid de sodi',
    r'CaCO_3': 'carbonat de calci',
    r'NH_3': 'amoniac',
    r'HCl': 'àcid clorhídric',
    r'O_2': 'oxigen',
    r'N_2': 'nitrogen',
    r'H_2': 'hidrogen',
    r'C_6H_{12}O_6': 'glucosa',
    r'Fe_2O_3': 'òxid de ferro tres',
    r'Al_2O_3': 'òxid d\'alumini',
    r'Mg(OH)_2': 'hidròxid de magnesi',
    r'CH_3COOH': 'àcid acètic',
    r'C_2H_5OH': 'etanol',
}

UNIDADES_CIENTIFICAS = {
    r'(\d+)\s*m³': r'\1 metres cúbics',
    r'(\d+)\s*cm³': r'\1 centímetres cúbics',
    r'(\d+)\s*km/h': r'\1 quilòmetres per hora',
    r'(\d+)\s*m/s': r'\1 metres per segon',
    r'(\d+)\s*g/mol': r'\1 grams per mol',
    r'(\d+)\s*kg/m³': r'\1 quilograms per metre cúbic',
    r'(\d+)\s*N/m²': r'\1 newtons per metre quadrat',
    r'(\d+)\s*J/(kg·K)': r'\1 joules per quilogram kelvin',
    r'(\d+)\s*×\s*10\^\{?(\d+)\}?': r'\1 per 10 elevat a \2',
    r'(\d+)\s*°C': r'\1 graus centígrads',
    r'(\d+)\s*K': r'\1 kelvins',
    r'(\d+)\s*eV': r'\1 electrons volt',
    r'(\d+)\s*Å': r'\1 àngstroms',
    r'(\d+)\s*Pa': r'\1 pascals',
    r'(\d+)\s*atm': r'\1 atmosferes',
    r'(\d+)\s*V': r'\1 volts',
    r'(\d+)\s*A': r'\1 amperes',
    r'(\d+)\s*Ω': r'\1 ohms',
    r'(\d+)\s*W': r'\1 watts',
    r'(\d+)\s*Hz': r'\1 hertzs',
}


def procesar_formulas_completo(text):
    if not text:
        return ""

    # Substituir símbols específics
    for simbol, substitut in SIMBOLOS_MATEMATICOS.items():
        text = text.replace(simbol, substitut)

    # Substituir \mathrm{X} → X
    text = re.sub(r'\\mathrm\{([^}]+)\}', r'\1', text)

    # Substituir formats de potències comuns tipus 10^{-4}
    text = re.sub(r'10\s*\^\s*\{?-?(\d+)\}?', r'10 elevat a menys \1', text)
    text = re.sub(r'\^\s*\{?-?(\d+)\}?', r'elevat a menys \1', text)

    # Eliminar qualsevol resta de { ... }
    text = re.sub(r'\{([^{}]*)\}', r'\1', text)
    text = text.replace('{', '').replace('}', '')

    # Eliminar qualsevol etiqueta HTML que hagi quedat
    text = re.sub(r'<[^>]+>', '', text)

    # Normalitzar unicode (per exemple, subíndexs en caràcters rars)
    text = unicodedata.normalize("NFKD", text)

    return text.strip()


def netejar_textos_cientifics(diccionari, asig):
    if asig not in {"MAT", "FIS", "QUI"}:
        return diccionari

    claus_a_processar = [
        "question", "optionA", "optionB", "optionC", "optionD", "explanation"
    ]

    for clau in claus_a_processar:
        contingut = diccionari.get(clau, "")
        diccionari[clau] = procesar_formulas_completo(contingut)

    return diccionari



def procesar_numeros_y_operaciones(texto: str) -> str:
    """
    Convierte expresiones numéricas y operaciones básicas a texto legible.
    """
    # Convertir fracciones simples (1/2 → "1 sobre 2")
    texto = re.sub(r'(\d+)/(\d+)', r'\1 sobre \2', texto)
    
    # Convertir decimales (3.14 → "3 coma 14")
    texto = re.sub(r'(\d+)\.(\d+)', r'\1 coma \2', texto)
    
    # Convertir porcentajes (50% → "50 per cent")
    texto = re.sub(r'(\d+)%', r'\1 per cent', texto)
    
    # Convertir operaciones matemáticas simples
    texto = re.sub(r'(\d+)\s*\+\s*(\d+)', r'\1 més \2', texto)
    texto = re.sub(r'(\d+)\s*\-\s*(\d+)', r'\1 menys \2', texto)
    texto = re.sub(r'(\d+)\s*\*\s*(\d+)', r'\1 per \2', texto)
    texto = re.sub(r'(\d+)\s*/\s*(\d+)', r'\1 dividit per \2', texto)
    texto = re.sub(r'(\d+)\s*=\s*(\d+)', r'\1 igual a \2', texto)
    
    # Convertir números negativos (-5 → "menys 5")
    texto = re.sub(r'\-(\d+)', r'menys \1', texto)
    
    return texto

def limpiar_texto_con_formulas(texto: str) -> str:
    """
    Versión mejorada de limpiar_texto que maneja específicamente contenido científico.
    """
    if not texto:
        return ""
    
    # Proteger términos entre comillas
    protected_terms = re.findall(r'"[^"]+"', texto)
    for i, term in enumerate(protected_terms):
        texto = texto.replace(term, f"__PROTECTED_{i}__")
    
    # Procesar fórmulas y contenido científico
    texto = procesar_formulas_completo(texto)
    
    # Aplicar limpieza normal
    texto = html.unescape(texto)
    reemplazos = {
        "¶": "", "“": '"', "”": '"', "´": "'", "": " ",
        "‘": "'", "’": "'", "–": "-", "—": "-", "…": "..."
    }
    for original, nuevo in reemplazos.items():
        texto = texto.replace(original, nuevo)
    
    texto = unicodedata.normalize("NFC", texto)
    
    # Restaurar términos protegidos
    for i, term in enumerate(protected_terms):
        texto = texto.replace(f"__PROTECTED_{i}__", term)
    
    texto = re.sub(r'<[^>]+>', '', texto)
    return texto.strip()




def validar_formulas(texto: str) -> bool:
    """
    Valida que todas las fórmulas hayan sido convertidas correctamente.
    """
    # Comprobar si quedan símbolos matemáticos sin convertir
    simbolos_pendientes = re.findall(r'\\[a-zA-Z]+|\$|\^|_|\\[{}]', texto)
    if simbolos_pendientes:
        logger.warning(f"Símbolos matemáticos no convertidos: {set(simbolos_pendientes)}")
        return False
    
    # Comprobar fórmulas químicas sin convertir
    formulas_quimicas_pendientes = re.findall(r'[A-Z][a-z]?\d*[A-Z][a-z]?\d*', texto)
    if formulas_quimicas_pendientes:
        logger.warning(f"Posibles fórmulas químicas no convertidas: {set(formulas_quimicas_pendientes)}")
        return False
    
    return True



def generar_explicacion_automatica(registro: Dict) -> str:
    try:
        pregunta = limpiar_texto(registro.get("Question_CAT", ""))
        opcions = {
            lletra: limpiar_texto(registro.get(f"Opcion{lletra}_CAT", ""))
            for lletra in "ABCD"
        }
        resposta = registro.get("Answer", "").strip().upper()

        if not pregunta or resposta not in opcions:
            logger.warning("No es pot generar explicació: falta pregunta o resposta.")
            return ""

        prompt = (
            f"La següent pregunta té quatre opcions. Explica per què l'opció {resposta} és la correcta.\n\n"
            f"Pregunta: {pregunta}\n"
            f"Opció A: {opcions['A']}\n"
            f"Opció B: {opcions['B']}\n"
            f"Opció C: {opcions['C']}\n"
            f"Opció D: {opcions['D']}\n\n"
            f"Explicació en català:"
        )

        from openai import OpenAI

        client = OpenAI(api_key=os.getenv("OPENAI_API_KEY"))

        resposta_api = client.chat.completions.create(
            model="gpt-3.5-turbo",
            messages=[{"role": "user", "content": prompt}],
            temperature=0.6,
            max_tokens=350
        )
        return resposta_api.choices[0].message.content.strip()

    except Exception as e:
        logger.error(f"❌ Error amb ChatGPT: {e}")
        return ""


def validar_registro(registro: Dict) -> bool:
    """
    Valida que un registre tingui les dades mínimes necessàries.
    """
    SIGLAS_PERMITIDAS = {"HIS"}
    #SIGLAS_PERMITIDAS = {"ECO", "HISAR", "LAT", "HIS", "GEO", "FUNAR", "FIL", "CAT", "BIO", "MAT", "FIS", "QUI"}
    sigles = registro.get("SIGLAS", "").strip().upper()

    if sigles and sigles not in SIGLAS_PERMITIDAS:
        print(f"[⏭️] Assignatura exclosa: {sigles} — registre ignorat.")
        return False

    if not registro.get("ASIGNATURA_CAT"):
        registro["ASIGNATURA_CAT"] = "Sense assignatura"

    errors = []
    if not registro.get("Question_CAT"):
        errors.append("⚠️ Falta QUESTION_CAT")
    if not registro.get("OpcionA_CAT"):
        errors.append("⚠️ Falta OPCIONA_CAT")
    if not registro.get("OpcionB_CAT"):
        errors.append("⚠️ Falta OPCIONB_CAT")
    if not registro.get("EXPLICACION_CAT"):
        logger.info(f"🧠 Generant explicació automàtica pel registre {registro.get('Serial') or '[sense serial]'}...")
        explicacio = generar_explicacion_automatica(registro)
        if explicacio:
            registro["EXPLICACION_CAT"] = explicacio
            logger.info("✅ Explicació generada correctament.")
        else:
            errors.append("⚠️ Falta EXPLICACION_CAT (no s'ha pogut generar amb ChatGPT)")

    if errors:
        print(f"[⚠️ VALIDACIÓ] Errors en el registre {registro.get('Serial') or '[sense serial]'}:")
        for e in errors:
            print(f"   - {e}")
        return False

    return True


def limpiar_texto(texto: str) -> str:
    """
    Neteja bàsica de text: elimina HTML, normalitza espais i caràcters especials.
    Aquesta funció NO processa fórmules científiques. Per a això, utilitzar limpiar_texto_con_formulas.
    """
    if not texto:
        return ""
    
    texto = html.unescape(texto)  # ← elimina &quot;, &amp;, etc.

    # Primero, protegemos los términos entre comillas
    protected_terms = re.findall(r'"[^"]+"', texto)
    for i, term in enumerate(protected_terms):
        texto = texto.replace(term, f"__PROTECTED_{i}__")

    reemplazos = {
        "¶": "", "“": '"', "”": '"', "´": "'", "": " ",
        "‘": "'", "’": "'", "–": "-", "—": "-", "…": "..."
    }
    for original, nuevo in reemplazos.items():
        texto = texto.replace(original, nuevo)

    texto = unicodedata.normalize("NFC", texto)

    apostrofes = [
        (re.compile(r"\bl\s*['´‘’]\s*(\w)"), r"l'\1"),
        (re.compile(r"\bd\s*['´‘’]\s*(\w)"), r"d'\1"),
        (re.compile(r"\bn\s*['´‘’]\s*(\w)"), r"n'\1"),
        (re.compile(r"\bs\s*['´‘’]\s*(\w)"), r"s'\1"),
        (re.compile(r"\bm\s*['´‘’]\s*(\w)"), r"m'\1")
    ]
    for patron, reemplazo in apostrofes:
        texto = patron.sub(reemplazo, texto)

    for incorrecta, correcta in CORRECCIONES_CATALANES.items():
        texto = re.sub(rf"\b{re.escape(incorrecta)}\b", correcta, texto, flags=re.IGNORECASE)

    texto = re.sub(r"\s+([.,;:!?])", r"\1", texto)
    texto = re.sub(r"([.,;:!?])(?!\s|$)", r"\1 ", texto)
    texto = re.sub(r"\s+", " ", texto).strip()

    # Restauramos los términos protegidos
    for i, term in enumerate(protected_terms):
        texto = texto.replace(f"__PROTECTED_{i}__", term)

    return texto


def dividir_en_frases(texto: str, max_palabras: int = 14) -> List[str]:
    if not texto:
        return []

    texto = limpiar_texto(texto)
    frases_brutes = re.split(r'(?<=[.!?])\s+', texto)
    frases_resultat = []

    for frase in frases_brutes:
        subfrases = re.split(r'(?<=[,;:])\s+', frase)
        for sub in subfrases:
            paraules = sub.split()
            if not paraules:
                continue
            if len(paraules) <= max_palabras:
                frases_resultat.append(sub.strip())
            else:
                for i in range(0, len(paraules), max_palabras):
                    fragment = ' '.join(paraules[i:i + max_palabras])
                    frases_resultat.append(fragment.strip())

    # Combinar fragments massa curts
    agrupades = []
    i = 0
    while i < len(frases_resultat):
        actual = frases_resultat[i]
        if len(actual.split()) <= 3 and i + 1 < len(frases_resultat):
            combinada = f"{actual} {frases_resultat[i + 1]}"
            agrupades.append(combinada.strip())
            i += 2
        else:
            agrupades.append(actual)
            i += 1

    # Filtrar fragments buits o només puntuació
    final = [
        f for f in agrupades
        if f and not f.strip() in {".", "...", ",", ";", ":"}
        and len(f.strip()) > 1
        and not re.match(r"^[.,;:!?]+$", f.strip())
    ]

    # Eliminar duplicats mantingut ordre
    vist = set()
    return [f for f in final if not (f in vist or vist.add(f))]



def eliminar_imgs(text: str) -> str:
    return re.sub(r'<img[^>]+>', '', text, flags=re.IGNORECASE)




def limpiar_texto_para_ssml(texto: str) -> str:
    """
    Limpia texto para SSML eliminando todo HTML y normalizando el contenido.
    Versión más agresiva que limpiar_texto() específica para SSML.
    """
    if not texto:
        return ""
    
    # Eliminar todo HTML primero
    texto = re.sub(r'<[^>]+>', '', texto)
    
    # Normalizar espacios y caracteres especiales
    texto = html.unescape(texto)
    texto = unicodedata.normalize("NFC", texto)
    
    # Reemplazar caracteres problemáticos
    reemplazos = {
        "¶": "", "“": '"', "”": '"', "´": "'", "": " ",
        "‘": "'", "’": "'", "–": "-", "—": "-", "…": "..."
    }
    for original, nuevo in reemplazos.items():
        texto = texto.replace(original, nuevo)
    
    # Corregir apostrofes catalanes
    apostrofes = [
        (re.compile(r"\bl\s*['´‘’]\s*(\w)"), r"l'\1"),
        (re.compile(r"\bd\s*['´‘’]\s*(\w)"), r"d'\1"),
        (re.compile(r"\bn\s*['´‘’]\s*(\w)"), r"n'\1"),
        (re.compile(r"\bs\s*['´‘’]\s*(\w)"), r"s'\1"),
        (re.compile(r"\bm\s*['´‘’]\s*(\w)"), r"m'\1")
    ]
    for patron, reemplazo in apostrofes:
        texto = patron.sub(reemplazo, texto)
    
    # Aplicar correcciones catalanas
    for incorrecta, correcta in CORRECCIONES_CATALANES.items():
        texto = re.sub(rf"\b{re.escape(incorrecta)}\b", correcta, texto, flags=re.IGNORECASE)
    
    # Normalizar espacios y puntuación
    texto = re.sub(r"\s+([.,;:!?])", r"\1", texto)
    texto = re.sub(r"([.,;:!?])(?!\s|$)", r"\1 ", texto)
    texto = re.sub(r"\s+", " ", texto).strip()
    
    return texto

def generar_ssml(registro: Dict) -> str:
    """
    Genera una cadena SSML a partir d'un registre de dades.
    Versión mejorada con manejo robusto de contenido HTML y mejoras automáticas.
    """
    siglas = registro.get("SIGLAS", "").upper()
    es_cientifica = siglas in {"MAT", "FIS", "QUI"}
    
    # Función de limpieza adecuada
    func_neteja = limpiar_texto_con_formulas if es_cientifica else limpiar_texto_para_ssml

    # Procesar el enunciado
    enunciat_original = registro.get("Question_CAT", "Sense enunciat")
    tema = registro.get("TEMA_CAT", "")
    
    # Extraer y mejorar información de imágenes
    enunciat = eliminar_imgs(enunciat_original)  # ← nova línia afegida
    try:
        if "<img" in enunciat_original:
            img_match = re.search(r'src=["\']([^"\']+)["\']', enunciat_original)
            siglas = registro.get("SIGLAS", "").strip().upper()

            if siglas in {"HIS", "HISAR", "FUNAR"}:
                parts = re.split(r'</img>|<img[^>]+>', enunciat_original, flags=re.IGNORECASE)
                text_despres = " ".join(p.strip() for p in parts[1:] if p.strip())
                enunciat = f"Aquesta pregunta mostra una imatge visual. {text_despres.strip()}"
            elif img_match:
                img_path = img_match.group(1)
                img_name = os.path.basename(img_path).split('.')[0].replace('_', ' ')
                enunciat = eliminar_imgs(enunciat_original)
            else:
                logger.warning("⚠️ Imatge detectada però no es pot extreure el nom. Usant enunciat netejat.")
    except Exception as e:
        logger.error(f"❌ Error processant imatge dins del camp Question_CAT: {e}")


    # Aplicar limpieza y mejoras
    enunciat = func_neteja(enunciat)
    
    # Fallback si el texto queda vacío
    if not enunciat.strip():
        enunciat = f"Pregunta sobre {tema if tema else 'art visual'}."
    elif len(enunciat.split()) < 3:  # Texto muy corto
        enunciat = f"Pregunta sobre {tema if tema else 'art'}. {enunciat}"

    # Procesar opciones
    opcions_ssml = {
        k: func_neteja(re.sub(r'<[^>]+>', '', v))
        for k, v in {
            "A": registro.get("OpcionA_CAT", ""),
            "B": registro.get("OpcionB_CAT", ""),
            "C": registro.get("OpcionC_CAT", ""),
            "D": registro.get("OpcionD_CAT", "")
        }.items()
    }
    
    # Procesar y enriquecer explicación
    explicacio = func_neteja(registro.get("EXPLICACION_CAT", ""))
    
    opcion_correcta = registro.get("Answer", "").upper()

    # Filtrar frases redundantes en la explicación
    frases_explicacio = [
        f for f in dividir_en_frases(explicacio)
        if not re.match(r"^(La resposta correcta|Per tant|l'única opció)", f, re.IGNORECASE)
    ]
    
    # Construir SSML con mejor formato
    ssml_parts = ['<speak>']
    sub_index = 1

    def marca_sub():
        nonlocal sub_index
        marca = f'<mark name="sub_{sub_index}"/>'
        sub_index += 1
        return marca

    def afegir_seccio(scene: str, prefix: str, text: str):
        frases = dividir_en_frases(text)
        if not frases:
            return
        
        primera_frase = f"{prefix} {frases[0]}" if prefix else frases[0]
        ssml_parts.append(f'  <mark name="{scene}"/>{marca_sub()} {primera_frase}')
        
        for frase in frases[1:]:
            ssml_parts.append(f'  {marca_sub()}{frase}')
            
        ssml_parts.append('  <break time="700ms"/>')

    # Añadir secciones al SSML con mejor flujo
    afegir_seccio("scene_question", "", enunciat)
    
    for lletra in "ABCD":
        afegir_seccio(f"scene_option{lletra}", f"Opció {lletra}:", opcions_ssml[lletra])
    
    afegir_seccio("scene_correct", "", f"La resposta correcta és l'opció {opcion_correcta}.")
    
    if frases_explicacio:
        ssml_parts.append(f'  <mark name="scene_explanation"/>{marca_sub()} {frases_explicacio[0]}')
        for frase in frases_explicacio[1:]:
            ssml_parts.append(f'  {marca_sub()}{frase}')
        ssml_parts.append('  <break time="700ms"/>')
    
    ssml_parts.append('</speak>')
    ssml = '\n'.join(ssml_parts)
    
    return ssml if validar_ssml(ssml) else f'<speak><mark name="scene_error"/>Contingut invàlid per al registre {registro.get("Serial", "")}.</speak>'

def escapar_per_javascript(text: str) -> str:
    """
    Escapa caràcters que poden trencar interpolació en content.js (`${}`, backslashes, etc.)
    """
    text = text.replace("\\", "\\\\")  # escape backslash
    text = text.replace("`", "\\`")    # escape backticks
    text = text.replace("${", "\\${")  # evita interpolació
    return text

def generar_archivo_content(registro: Dict, plantilla_path: str, output_dir: str) -> bool:
    """Genera un archivo content.js a partir de un registro y una plantilla."""
    try:
        # Leer la plantilla
        with open(plantilla_path, 'r', encoding='utf-8') as f:
            plantilla = f.read()
        
        # Procesar datos
        asignatura = limpiar_texto(registro.get("ASIGNATURA_CAT", ""))
        tema = limpiar_texto(registro.get("TEMA_CAT", ""))
        enunciado = limpiar_texto(registro.get("Question_CAT", ""))
        opciones = {
            "A": limpiar_texto(registro.get("OpcionA_CAT", "")),
            "B": limpiar_texto(registro.get("OpcionB_CAT", "")),
            "C": limpiar_texto(registro.get("OpcionC_CAT", "")),
            "D": limpiar_texto(registro.get("OpcionD_CAT", ""))
        }
        opcion_correcta = registro.get("Answer", "")
        explicacion = limpiar_texto(registro.get("EXPLICACION_CAT", ""))
        
        # Generar SSML
        ssml = generar_ssml(registro)
        
        # Generar párrafos de explicación para HTML
        frases_explicacion = dividir_en_frases(explicacion, max_palabras=25)
        explicacion_html = "\n".join(
            f'      <p class="question">{frase}</p>' 
            for frase in frases_explicacion
        )
        
        # Reemplazar variables en la plantilla
        replacements = {
            "{{ASIGNATURA}}": escapar_per_javascript(asignatura),
            "{{TEMA}}": escapar_per_javascript(tema),
            "{{SERIAL}}": registro.get("Serial", ""),
            "{{SIGLAS}}": registro.get("SIGLAS", ""),
            "{{ENUNCIADO}}": escapar_per_javascript(enunciado),
            "{{OPCIONA}}": escapar_per_javascript(opciones["A"]),
            "{{OPCIONB}}": escapar_per_javascript(opciones["B"]),
            "{{OPCIONC}}": escapar_per_javascript(opciones["C"]),
            "{{OPCIOND}}": escapar_per_javascript(opciones["D"]),
            "{{OPCION_CORRECTA}}": opcion_correcta,
            "{{TEXTO_OPCION_CORRECTA}}": escapar_per_javascript(opciones.get(opcion_correcta, "")),
            "{{EXPLICACION}}": escapar_per_javascript(explicacion),
            "{{EXPLICACION_PARRAFOS}}": escapar_per_javascript(explicacion_html),
            "{{SSML}}": ssml  # NO escapem l’SSML
        }
        
        for placeholder, value in replacements.items():
            plantilla = plantilla.replace(placeholder, str(value) if value is not None else "")
        
        # Crear directorio si no existe
        os.makedirs(output_dir, exist_ok=True)
        
        # Escribir archivo
        ruta_content = os.path.join(output_dir, 'content.js')
        with open(ruta_content, 'w', encoding='utf-8') as f:
            f.write(plantilla)
        
        logger.info(f"Archivo generado: {ruta_content}")
        return True
        
    except Exception as e:
        logger.error(f"Error al generar archivo: {e}")
        return False
        
def validar_ssml(ssml: str) -> bool:
    """
    Valida que el contingut SSML tingui estructura segura abans de fer la síntesi.
    Comprova si hi ha tags no tancats, marques malformades, o caràcters prohibits.
    """
    try:
        if not ssml.startswith("<speak>") or not ssml.endswith("</speak>"):
            logger.error("❌ SSML no comença/acaba amb <speak>...</speak>")
            return False

        # Marques <mark name="..."/> ben formades
        for match in re.findall(r'<mark name="([^"]*)"\s*/>', ssml):
            if not match.strip():
                logger.error("❌ S'ha trobat una marca <mark name=\"\"/> sense nom.")
                return False

        # Caràcters no admesos (com {, }, $) — opcional
        if re.search(r'[{|}$]', ssml):
            logger.warning("⚠️ El SSML conté caràcters especials que poden trencar la síntesi.")

        # Comprovar que no hi ha etiquetes HTML (p, h1, etc)
        if re.search(r'<\/?(p|h\d|div|span|img|math)[^>]*>', ssml, re.IGNORECASE):
            logger.error("❌ El SSML conté HTML no permès.")
            return False

        if re.search(r'<mark name="sub_\d+"\s*/>\s*[.,;:!?]*\s*<', ssml):
            logger.error("❌ SSML conté marques <sub_X/> amb només puntuació o contingut invàlid.")
            return False

        return True
    except Exception as e:
        logger.error(f"❌ Error validant SSML: {e}")
        return False

if __name__ == "__main__":
    # Aquest script no fa execució directa — s'utilitza com a mòdul
    logger.info("Aquest script està preparat per ser importat i utilitzat des de main.py.")