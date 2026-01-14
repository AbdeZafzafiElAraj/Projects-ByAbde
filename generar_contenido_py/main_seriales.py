from consultar_filemaker import autenticar, obtener_registros
from generar_content import generar_archivo_content, validar_registro
from tts_wrapper import generar_audio_google as generar_audio
from generar_sections import buscar_y_procesar_vtts as generar_sections
from generar_vtt import generar_vtt_per_totes_les_carpetes as generar_vtt
from generar_html import generar_index_html_para_todos
from pathlib import Path
import os
import re

# Configuración
TPL_PATH = "./tpl_bbdd.txt"
OUTPUT_PATH = "./output"
HTML_TEMPLATE = "plantilla_html.html"

# IDs y Serials objetivo
SIGLAS_OBJETIVO =  {"HIS", "HISAR", "FUNAR", "FIL", "FIS"}


def es_valido_con_img(registro):
    """
    Valida si el registro tiene mínimo las opciones A y B, y puede tener imagen en la pregunta.
    """
    campos = ["Question_CAT", "OpcionA_CAT", "OpcionB_CAT"]
    if not all(registro.get(campo, "").strip() for campo in campos):
        return False

    pregunta = registro.get("Question_CAT", "")
    if not re.search(r"<img[^>]+src=", pregunta):
        return True  # si no hay imagen, usar validador normal

    # si hay imagen, asegúrate que las opciones siguen siendo válidas
    return bool(registro.get("OpcionA_CAT")) and bool(registro.get("OpcionB_CAT"))


def main():
    print("\n────────────────────────────────────────────")
    print("🚀 Iniciando pipeline para registros CAT y FUNAR")
    print("────────────────────────────────────────────")

    print("[🔐] Autenticando con FileMaker...")
    token = autenticar()
    registros = obtener_registros(token, lote=500, max_registros=7000)

    if not registros:
        print("[❌] No se obtuvieron registros desde FileMaker.")
        return

    print(f"[📥] {len(registros)} registro(s) recibido(s).")

    seleccionados = 0
    for registro in registros:
        id_reg = str(registro.get("ID", "")).strip()
        serial = str(registro.get("Serial", "")).strip()
        sigla = registro.get("SIGLAS", "").strip().upper()

        if sigla not in SIGLAS_OBJETIVO:
            continue
        if id_reg not in IDS_OBJETIVO and serial not in SERIALS_OBJETIVO:
            continue

        print(f"[🔎] Evaluando → ID: {id_reg}, Serial: {serial}, SIGLAS: {sigla}")

        if not es_valido_con_img(registro):
            print(f"[⚠️] Registro no válido (revisar imagen/pregunta): ID {id_reg}, Serial {serial}")
            continue

        carpeta_salida = os.path.join(OUTPUT_PATH, sigla, serial)
        print(f"[📄] Generando content.js para ID {id_reg}, Serial {serial} ({sigla})")
        exito = generar_archivo_content(registro, TPL_PATH, carpeta_salida)
        if exito:
            seleccionados += 1
        else:
            print(f"[❌] Error generando content.js para {serial}")

    if seleccionados == 0:
        print("[⚠️] No se generó ningún contenido válido. Fin del proceso.")
        return

    print("[🔊] Generando audios...")
    generar_audio(OUTPUT_PATH)

    print("[💬] Generando subtítulos...")
    generar_vtt(Path(OUTPUT_PATH))

    print("[📍] Generando sections.js...")
    generar_sections(OUTPUT_PATH)

    print("[🌐] Generando index.html...")
    generar_index_html_para_todos(OUTPUT_PATH, HTML_TEMPLATE)

    print("\n────────────────────────────────────────────")
    print(f"[✅] Proceso finalizado para {seleccionados} registro(s).")
    print("────────────────────────────────────────────")


if __name__ == "__main__":
    main()
