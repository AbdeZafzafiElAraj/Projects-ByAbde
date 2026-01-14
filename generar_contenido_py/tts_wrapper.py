import os
import subprocess
import sys
from pathlib import Path

def verificar_entorno():
    if not os.path.exists("tts_js.py"):
        print("[ERROR] No es troba tts_js.py")
        return False
    if not os.path.exists("mrwolf.json"):
        print("[ERROR] No es troben les credencials de Google Cloud (mrwolf.json)")
        return False
    return True

def ja_esta_generat(carpeta_abs_path):
    audio_path = Path(carpeta_abs_path) / "audio.mp3"
    marks_path = Path(carpeta_abs_path) / "marks.json"
    return audio_path.exists() and marks_path.exists()

def generar_audio_google(output_dir="./output"):
    if not verificar_entorno():
        return

    if not os.path.exists(output_dir):
        print(f"[ERROR] El directori de sortida {output_dir} no existeix.")
        return

    carpetes_procesar = []
    for root, _, files in os.walk(output_dir):
        if "content.js" in files:
            if not ja_esta_generat(root):
                rel_path = os.path.relpath(root, output_dir).replace("\\", "/")
                carpetes_procesar.append(rel_path)
            else:
                print(f"[⏭️] Ja existeixen audio.mp3 i marks.json a {root}, s’omet.")

    if not carpetes_procesar:
        print("[✅] Tots els audios ja estan generats. No cal fer res.")
        return

    print(f"[INFO] Es processaran {len(carpetes_procesar)} carpeta(s)...")

    for carpeta in carpetes_procesar:
        print(f"\n[PROCESANDO] {carpeta}")
        try:
            my_env = os.environ.copy()
            my_env["PYTHONIOENCODING"] = "utf-8"
            result = subprocess.run(
                [sys.executable, "tts_js.py", carpeta],
                check=True,
                capture_output=True,
                text=True,
                encoding='utf-8',
                env=my_env
            )
            if result.stdout:
                print(result.stdout)
            if result.stderr:
                print(result.stderr)
        except subprocess.CalledProcessError as e:
            print(f"[✗] Error processant {carpeta}:")
            if e.stdout:
                print(f"   [STDOUT] {e.stdout}")
            if e.stderr:
                print(f"   [STDERR] {e.stderr}")
        except Exception as e:
            print(f"[✗] Error inesperat processant {carpeta}: {e}")

if __name__ == "__main__":
    generar_audio_google()
