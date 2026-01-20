import re
import json
from pathlib import Path
from typing import List, Tuple, Dict

# Constants de plantilla
VTT_TEMPLATE = """// vtt.js - Define variable with subtitle data
const subtitlesData = `WEBVTT

{cues}
`;
"""

VTT_BLOCK = """{note}
{start} --> {end}
{text}
"""

log_lines = []

# --- Utilitats ---

def format_timestamp(seconds: float) -> str:
    h = int(seconds // 3600)
    m = int((seconds % 3600) // 60)
    s = seconds % 60
    return f"{h:02}:{m:02}:{s:06.3f}".replace('.', ',')

def clean_text(text: str) -> str:
    """Neteja marques SSML i codis HTML que no calen al VTT."""
    substitutions = [
        (r'<mark name="[^"]+"\s*/>', ''),           # Elimina marques <mark>
        (r'<break[^>]*?>', '', re.IGNORECASE),     # Elimina <break>
        (r'</?speak\s*>', '', re.IGNORECASE),      # Elimina <speak>
        (r'/speak>', '', re.IGNORECASE),
        (r'&quot;', '"'),
        (r'\s*(break time="\d+ms"\s*/?>?)\s*', '', re.IGNORECASE),
    ]
    for pattern, repl, *flags in substitutions:
        text = re.sub(pattern, repl, text, *flags)
    return text.replace(',.', '.').replace('> ', '').strip()

def parse_ssml(ssml: str) -> List[Tuple[str, str, str]]:
    """Extreu tuples (scene, sub, text) de l’SSML."""
    items = []
    current_scene, current_sub, buffer = None, None, ""

    pattern = r'<mark name="(scene_\w+|sub_\d+)"\s*/>|([^<]+)'
    for match in re.finditer(pattern, ssml):
        mark, text = match.groups()
        if mark:
            if mark.startswith("scene_"):
                if current_scene and current_sub and buffer:
                    items.append((current_scene, current_sub, buffer.strip()))
                current_scene, current_sub, buffer = mark, None, ""
            elif mark.startswith("sub_"):
                if current_sub and buffer:
                    items.append((current_scene, current_sub, buffer.strip()))
                current_sub, buffer = mark, ""
        elif text and current_scene and current_sub:
            buffer += " " + text.strip()

    if current_scene and current_sub and buffer:
        items.append((current_scene, current_sub, buffer.strip()))

    return items

def scene_prefix(scene: str) -> str:
    """Retorna el prefix textual per a cada escena."""
    mapping = {
        "scene_question": "",
        "scene_optiona": "",
        "scene_optionb": "",
        "scene_optionc": "",
        "scene_optiond": "",
        "scene_correct": "",
        "scene_explanation": "",
    }
    return mapping.get(scene.lower(), "")

def validar_escenes_completes(parsed: List[Tuple[str, str, str]], carpeta: Path):
    """Afegeix al log les escenes amb texts buits."""
    from collections import defaultdict
    escena_to_subs = defaultdict(list)
    for scene, sub, text in parsed:
        escena_to_subs[scene].append((sub, text))
    for scene in sorted(escena_to_subs.keys()):
        if not any(clean_text(t) for _, t in escena_to_subs[scene]):
            log_lines.append(f"❌ Falta text/sub_X per a {scene} a {carpeta.relative_to(carpeta.parents[1])}")

# --- Generació VTT ---

def generate_vtt(ssml_path: Path, marks_path: Path, output_path: Path, base_dir: Path):
    try:
        ssml_text = ssml_path.read_text(encoding='utf-8')
        ssml_match = re.search(r'const ssml\s*=\s*`([\s\S]+?)`;', ssml_text)
        if not ssml_match:
            raise ValueError("Bloque SSML no trobat.")
        ssml = ssml_match.group(1)

        parsed = parse_ssml(ssml)
        validar_escenes_completes(parsed, ssml_path.parent)

        marks = json.loads(marks_path.read_text(encoding='utf-8')).get('marks', [])
        mark_dict: Dict[str, Dict] = {m['value']: m for m in marks if m['value'].startswith('sub_')}

        subs_definits = {sub for _, sub, _ in parsed}
        for sub in subs_definits - mark_dict.keys():
            log_lines.append(f"⚠️ sub {sub} present a SSML però NO al marks.json")

        cues, seen_scenes = [], set()

        for i, (scene, sub, text) in enumerate(parsed):
            mark = mark_dict.get(sub)
            if not mark:
                continue

            start = mark['start']
            end = (
                mark_dict.get(parsed[i+1][1], {}).get('start', start + 2.5)
                if i + 1 < len(parsed) else start + 2.5
            )

            cleaned = clean_text(text)
            if not cleaned:
                log_lines.append(f"⚠️ TEXT ignorat per {scene} - {sub}: només break o buit")
                continue

            prefix = scene_prefix(scene) if scene.lower() not in seen_scenes else ""
            seen_scenes.add(scene.lower())

            cues.append(VTT_BLOCK.format(
                note=f"NOTE {scene}",
                start=format_timestamp(start),
                end=format_timestamp(end),
                text=prefix + cleaned
            ))

        output_path.write_text(VTT_TEMPLATE.format(cues='\n'.join(cues)), encoding='utf-8')
        log_lines.append(f"✅ {output_path.relative_to(base_dir)}")

    except Exception as e:
        log_lines.append(f"❌ Error en {ssml_path.parent.relative_to(base_dir)}: {str(e)}")

# --- Execució per lots ---

def generar_vtt_per_totes_les_carpetes(base_dir: Path):
    for folder in base_dir.rglob("*"):
        if not folder.is_dir():
            continue
        ssml_path = folder / "content.js"
        marks_path = folder / "marks.json"
        output_path = folder / "vtt.js"
        if ssml_path.exists() and marks_path.exists():
            generate_vtt(ssml_path, marks_path, output_path, base_dir)
        else:
            log_lines.append(f"⏭️ Saltant {folder.relative_to(base_dir)} (faltan fitxers)")

# --- Entrada principal ---

if __name__ == "__main__":
    root = Path("output/")
    generar_vtt_per_totes_les_carpetes(root)
    Path("log.txt").write_text('\n'.join(log_lines), encoding='utf-8')
    print("📄 log.txt creat.")
