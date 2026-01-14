import os
from pathlib import Path

def limpiar_output(siglas_filtrar={"ECO", "HISAR", "LAT", "HIS", "GEO", "FUNAR", "FIL", "CAT", "BIO", "MAT", "FIS", "QUI"}):
    output_dir = Path("output")
    if not output_dir.exists():
        print("❌ No existe el directorio 'output'")
        return

    print(f"🧹 Limpiando archivos para asignaturas: {', '.join(siglas_filtrar)}\n")

    archivos_a_eliminar = [
        "content.js",
        "audio.mp3",
        "audio.json",
        "marks.json",
        "vtt.js",
        "sections.js",
        "index.html",
        "correccions.json"
    ]

    total_eliminados = 0
    carpetas_afectadas = 0

    for carpeta in output_dir.rglob("*"):
        if carpeta.is_dir():
            partes_ruta = {p.upper() for p in carpeta.parts}
            if not partes_ruta & siglas_filtrar:
                continue

            archivos_encontrados = False
            for archivo in archivos_a_eliminar:
                ruta_archivo = carpeta / archivo
                if ruta_archivo.exists():
                    ruta_archivo.unlink()
                    total_eliminados += 1
                    archivos_encontrados = True
                    print(f"✅ Eliminado: {ruta_archivo}")
            
            if archivos_encontrados:
                carpetas_afectadas += 1

    print(f"\n✨ Total de archivos eliminados: {total_eliminados}")
    print(f"📂 Carpetas afectadas: {carpetas_afectadas}")

if __name__ == "__main__":
    limpiar_output()
