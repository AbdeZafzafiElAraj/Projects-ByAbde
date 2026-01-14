from consultar_filemaker import autenticar, obtener_registros
from generar_content import generar_archivo_content, validar_registro
from tts_wrapper import generar_audio_google as generar_audio
from generar_sections import buscar_y_procesar_vtts as generar_sections
from generar_vtt import generar_vtt_per_totes_les_carpetes as generar_vtt
from generar_html import generar_index_html_para_todos
from pathlib import Path
import os

# Rutas base
TPL_PATH = "./tpl_bbdd.txt"
OUTPUT_PATH = "./output"
HTML_TEMPLATE = "plantilla_html.html"
SIGLAS_OBJETIVO = {"HIS"}
def main():
    print("────────────────────────────────────────────")
    print("🚀 Iniciando pipeline de generación de contenido")
    print("────────────────────────────────────────────")

    print("[🔐] Autenticando con FileMaker...")
    token = autenticar()
    registros = obtener_registros(token, lote=250, max_registros=float("inf"))

    if not registros:
        print("[❌] No se obtuvieron registros desde FileMaker. Proceso abortado.")
        return

    print(f"[📥] {len(registros)} registro(s) recibido(s).")

    print("[📝] Generando archivos content.js para asignaturas objetivo...")
    total_generados = 0
    for registro in registros:
        siglas = (registro.get("SIGLAS", "ALTRES") or "ALTRES").strip().upper()
        if siglas not in SIGLAS_OBJETIVO:
            continue
        if not validar_registro(registro):
            continue


        serial = str(registro.get("Serial", "sense_serial")).strip()
        carpeta_salida = os.path.join(OUTPUT_PATH, siglas, serial)

        print(f"[📄] Generant content.js per al registre {siglas}/{serial}...")
        exito = generar_archivo_content(registro, TPL_PATH, carpeta_salida)

        if not exito:
            print(f"[❌] Error al generar content.js per al registre {serial}")
            continue

        total_generados += 1

    if total_generados == 0:
        print("[❌] No se generó ningún archivo content.js. Revisa los registros.")
        return

    print(f"[✅] Content.js generados per a {total_generados} registre(s).")

    print("[🔊] Generando audios con SSML solo para carpetas modificadas...")
    generar_audio(OUTPUT_PATH)

    print("[💬] Generando subtítulos vtt.js...")
    generar_vtt(Path(OUTPUT_PATH))

    print("[📍] Generando sections.js desde marcas SSML...")
    generar_sections(OUTPUT_PATH)

    print("[🌐] Generando index.html...")
    generar_index_html_para_todos(OUTPUT_PATH, HTML_TEMPLATE)

    print("────────────────────────────────────────────")
    print("[✅] Proceso finalizado con éxito.")
    print("────────────────────────────────────────────")

if __name__ == "__main__":
    main()