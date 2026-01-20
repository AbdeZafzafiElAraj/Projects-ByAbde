import pandas as pd
import html
from pathlib import Path
from bs4 import BeautifulSoup

import warnings
from bs4 import MarkupResemblesLocatorWarning
warnings.filterwarnings("ignore", category=MarkupResemblesLocatorWarning)

# 🔧 Configuració
EXCEL_PATH = "registre_letsebau.xlsx"
TEMPLATE_PATH = "tabla_html_catalan.html"
OUTPUT_PATH = "index_final.html"

def netejar(text):
    if pd.notna(text):
        text = str(text)
        soup = BeautifulSoup(text, 'html.parser')
        for img in soup.find_all('img'):
            img.decompose()
        return html.escape(soup.get_text()).strip()
    else:
        return "—"

def construir_fila(row):
    serial_raw = str(row.get("Serial", "")).strip()
    serial_sense_zero = str(int(serial_raw)) if serial_raw.isdigit() else serial_raw
    serial_mostrar = serial_raw.zfill(4)

    nom = netejar(row.get("ASIGNATURA_CAT", ""))
    descripcio = netejar(row.get("Question_CAT", ""))
    categoria = netejar(row.get("TEMA_CAT", ""))
    audio = f"https://letsebau.es/contenido/{serial_sense_zero}/audio.mp3"
    animacio = f"https://letsebau.es/contenido/{serial_sense_zero}/index.html"

    return f"""
      <tr>
        <td>{serial_mostrar}</td>
        <td>{nom}</td>
        <td class="truncable" title="{descripcio}">{descripcio}</td>
        <td>{categoria}</td>
        <td><a href="{audio}" class="audio-link">🎵 Àudio</a></td>
        <td><a href="{animacio}" class="animation-link">🎬 Animació</a></td>
      </tr>
    """

def generar_index_html():
    print("📥 Carregant dades de l'Excel...")
    df = pd.read_excel(EXCEL_PATH)

    print(f"🧾 Processant {len(df)} registres...")
    files_html = "\n".join(construir_fila(row) for _, row in df.iterrows())

    print("🧩 Carregant plantilla HTML...")
    with open(TEMPLATE_PATH, "r", encoding="utf-8") as f:
        plantilla = f.read()

    print("🎨 Injectant contingut i ajustant enllaços...")
    plantilla = plantilla.replace(
        "</style>",
        ".truncable { max-width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }\n</style>"
    )
    plantilla = plantilla.replace("https://example.com/", "https://letsebau.es/contenido/")

    # Inserta contingut a <tbody> sense usar re.sub per evitar errors amb escapes
    start_tag = "<tbody>"
    end_tag = "</tbody>"
    start = plantilla.find(start_tag)
    end = plantilla.find(end_tag, start)

    if start == -1 or end == -1:
        raise ValueError("No s'ha trobat el bloc <tbody>...</tbody> a la plantilla HTML.")

    html_resultat = (
        plantilla[:start + len(start_tag)] +
        "\n" + files_html + "\n" +
        plantilla[end:]
    )

    print(f"💾 Guardant el fitxer final a {OUTPUT_PATH}...")
    with open(OUTPUT_PATH, "w", encoding="utf-8") as f:
        f.write(html_resultat)

    print("✅ HTML generat correctament.")

if __name__ == "__main__":
    generar_index_html()



# 🔧 Corrección automática de enlaces para audio y animación
from bs4 import BeautifulSoup

with open(OUTPUT_PATH, "r", encoding="utf-8") as f:
    html_text = f.read()
    soup = BeautifulSoup(html_text, "html.parser")

for row in soup.find_all("tr")[1:]:  # omitir encabezado
    cols = row.find_all("td")
    if len(cols) >= 5:
        serie = str(int(cols[0].text.strip()))
        audio_link = cols[4].find("a")
        if audio_link:
            audio_link["href"] = f"https://letsebau.es/contenido/{serie}/audio.mp3"
        animation_link = cols[5].find("a")
        if animation_link:
            animation_link["href"] = f"https://letsebau.es/contenido/{serie}/index.html"

with open(OUTPUT_PATH, "w", encoding="utf-8") as f:
    f.write(str(soup))
print("✅ Enllaços actualitzats correctament en el fitxer HTML final.")
