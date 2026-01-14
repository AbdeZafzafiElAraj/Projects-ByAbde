import os
from pathlib import Path

def verificar_archivo_vtt(vtt_path):
    try:
        with open(vtt_path, 'r', encoding='utf-8') as f:
            contenido = f.read()
            return 'const subtitlesData =' in contenido and 'WEBVTT' in contenido
    except Exception as e:
        print(f"[ERROR] Error verificando {vtt_path}: {e}")
        return False

def verificar_archivo_js(js_path):
    try:
        with open(js_path, 'r', encoding='utf-8') as f:
            contenido = f.read()
            return len(contenido.strip()) > 0
    except Exception as e:
        print(f"[ERROR] Error verificando {js_path}: {e}")
        return False

def verificar_archivo_audio(carpeta):
    """Acepta audio.wav o audio.mp3"""
    for ext in ['audio.wav', 'audio.mp3']:
        audio_path = carpeta / ext
        if audio_path.exists():
            try:
                return os.path.getsize(audio_path) > 0
            except Exception as e:
                print(f"[ERROR] Error verificando {audio_path}: {e}")
                return False
    print(f"[WARNING] No se encontró archivo de audio en {carpeta}")
    return False

def generar_index_html_para_todos(output_dir, plantilla_html):
    if not os.path.exists(plantilla_html):
        print(f"[ERROR] Plantilla HTML no encontrada: {plantilla_html}")
        return

    with open(plantilla_html, "r", encoding="utf-8") as f:
        html_base = f.read()

    output_path = Path(output_dir)
    archivos_requeridos = {
        "content.js": verificar_archivo_js,
        "vtt.js": verificar_archivo_vtt,
        "sections.js": verificar_archivo_js
    }

    for content_path in output_path.rglob("content.js"):
        carpeta = content_path.parent
        archivos_validos = True

        # Verificamos primero los JS y VTT
        for archivo, verificador in archivos_requeridos.items():
            ruta_archivo = carpeta / archivo
            if not ruta_archivo.exists():
                print(f"[WARNING] Falta {archivo} en {carpeta}")
                archivos_validos = False
                break
            if not verificador(ruta_archivo):
                print(f"[WARNING] {archivo} inválido en {carpeta}")
                archivos_validos = False
                break

        # Verificar audio separado (puede ser .mp3 o .wav)
        if archivos_validos and not verificar_archivo_audio(carpeta):
            archivos_validos = False

        if not archivos_validos:
            continue

        ruta_index = carpeta / "index.html"
        if ruta_index.exists():
            print(f"[SKIP] Ya existe {ruta_index}, se omite")
            continue

        try:
            with open(ruta_index, "w", encoding="utf-8") as out:
                out.write(html_base)
            print(f"[OK] Generado {ruta_index}")
        except Exception as e:
            print(f"[ERROR] Error generando index.html en {carpeta}: {e}")

if __name__ == "__main__":
    output_dir = "./output"
    plantilla = "plantilla_html.html"
    generar_index_html_para_todos(output_dir, plantilla)
