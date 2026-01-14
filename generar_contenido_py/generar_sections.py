import re
import json
from pathlib import Path
from typing import Dict, List, Tuple

MIN_DURATION = 1.5  # durada mínima per secció
SAFETY_MARGIN = 0.25  # separació mínima entre seccions

def parse_ssml_marks(ssml: str) -> List[Tuple[str, str]]:
    result = []
    current_scene = None
    for match in re.finditer(r'<mark name="(scene_\w+|sub_\d+)"\s*/>', ssml):
        mark = match.group(1)
        if mark.startswith("scene_"):
            current_scene = mark
        elif mark.startswith("sub_") and current_scene:
            result.append((current_scene, mark))
    return result

def clean_explanation(explanation: str) -> str:
    # Eliminar la línea de la respuesta correcta
    explanation = re.sub(r'La resposta correcta és la [A-D]\)\s*.*?(?:\n|$)', '', explanation)
    
    # Limpiar líneas vacías y espacios extra
    lines = [line.strip() for line in explanation.split('\n') if line.strip()]
    return '\n'.join(lines)

def generate_sections(content_path: Path, marks_path: Path, output_path: Path):
    # Leer el contenido del archivo
    content_text = content_path.read_text(encoding="utf-8")
    
    # Extraer el SSML
    ssml_match = re.search(
        r'const ssml\s*=\s*`([\s\S]+?)`;',
        content_text
    )
    if not ssml_match:
        raise ValueError("No s'ha trobat cap bloc ssml dins de content.js")
    
    ssml = ssml_match.group(1)
    ssml = ssml.replace('</speak>', '').replace('/speak>', '')

    # Limpiar la explicación en el SSML
    explanation_match = re.search(r'<mark name="scene_explanation"/>([\s\S]+?)(?:<break|$)', ssml)
    if explanation_match:
        explanation = explanation_match.group(1)
        cleaned_explanation = clean_explanation(explanation)
        ssml = ssml.replace(explanation, cleaned_explanation)

    scene_to_subs = {}
    for scene, sub in parse_ssml_marks(ssml):
        scene_to_subs.setdefault(scene.replace("scene_", ""), []).append(sub)

    marks_data = json.loads(marks_path.read_text(encoding="utf-8"))
    sub_to_time = {m["value"]: m["start"] for m in marks_data["marks"] if m["type"] == "MARK"}

    scene_order = ["question", "optionA", "optionB", "optionC", "optionD", "correct", "explanation"]
    sections = []

    for scene in scene_order:
        subs = scene_to_subs.get(scene)
        if not subs:
            continue
        start = sub_to_time.get(subs[0])
        end = sub_to_time.get(subs[-1])
        if start is not None and end is not None:
            duration = max(MIN_DURATION, end - start + SAFETY_MARGIN)
            sections.append({
                "id": scene,
                "startTime": round(start, 3),
                "endTime": round(start + duration, 3)
            })

    # Corregir encavalcaments ajustant endTime segons el següent startTime
    for i in range(len(sections) - 1):
        curr = sections[i]
        next_start = sections[i + 1]["startTime"]
        if curr["endTime"] > next_start:
            curr["endTime"] = round(next_start - SAFETY_MARGIN, 3)

    with output_path.open("w", encoding="utf-8") as f:
        f.write("const sectionTimings = [\n")
        for sec in sections:
            f.write(f"  {{ id: '{sec['id']}', startTime: {sec['startTime']}, endTime: {sec['endTime']} }},\n")
        f.write("];\n")

log_lines = []
def buscar_y_procesar_vtts(base_dir: Path):
    base_dir = Path(base_dir)  # <-- converteix string a Path
    for folder in base_dir.rglob("*"):
        if not folder.is_dir():
            continue
        content_path = folder / "content.js"
        marks_path = folder / "marks.json"
        output_path = folder / "sections.js"
        if content_path.exists() and marks_path.exists():
            try:
                generate_sections(content_path, marks_path, output_path)
                log_lines.append(f"✅ {output_path.relative_to(base_dir)}")
            except Exception as e:
                log_lines.append(f"❌ Error en {folder.relative_to(base_dir)}: {str(e)}")
        else:
            log_lines.append(f"⏭️ Saltant {folder.relative_to(base_dir)} (faltan fitxers)")

if __name__ == "__main__":
    buscar_y_procesar_vtts(Path("output/"))

Path("log.txt").write_text("\n".join(log_lines), encoding="utf-8")
print("📄 log.txt creat.")