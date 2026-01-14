import os
import sys
import json
import base64
import requests
from google.oauth2 import service_account
import re
from google.auth.transport.requests import Request

# Configuración
CREDENTIALS_PATH = os.path.abspath("mrwolf.json")
SCOPES = ["https://www.googleapis.com/auth/cloud-platform"]

def load_credentials():
    if not os.path.exists(CREDENTIALS_PATH):
        raise Exception(f"Archivo de credenciales no encontrado: {CREDENTIALS_PATH}")
    return service_account.Credentials.from_service_account_file(
        CREDENTIALS_PATH, scopes=SCOPES)

def extract_ssml(folder):
    content_path = os.path.join(folder, "content.js")
    with open(content_path, "r", encoding="utf-8") as f:
        content = f.read()
    match = re.search(r"const ssml = `([\s\S]*?)`;", content)
    if not match:
        raise ValueError("Formato SSML no encontrado")
    ssml = match.group(1).strip()
    if not (ssml.startswith("<speak>") and ssml.endswith("</speak>")):
        raise ValueError("SSML debe comenzar con <speak> y terminar con </speak>")
    return ssml

def synthesize_speech(credentials, ssml, output_dir):
    url = "https://texttospeech.googleapis.com/v1beta1/text:synthesize"
    if not credentials.valid:
        credentials.refresh(Request())
    headers = {
        "Authorization": f"Bearer {credentials.token}",
        "Content-Type": "application/json"
    }

    # Determinar si es asignatura científica de forma robusta
    path_parts = output_dir.replace("\\", "/").split("/")
    es_cientifica = any(sigla in path_parts for sigla in ["MAT", "QUI"])   
    
    payload = {
            "input": {"ssml": ssml},
            "voice": {
                "languageCode": "ca-ES",
                "name": "ca-ES-Wavenet-B" if es_cientifica else "ca-ES-Standard-A",
                "ssmlGender": "NEUTRAL"
            },
            "audioConfig": {
                "audioEncoding": "MP3",
                "speakingRate": 0.92 if es_cientifica else 1.0,  # Más lento para fórmulas
                "pitch": -3.0 if es_cientifica else 0.0,  # Más grave para mayor claridad
                "volumeGainDb": 2.0 if es_cientifica else 0.0,  # Más volumen para fórmulas
                "effectsProfileId": ["headphone-class-device"] if es_cientifica else []
            },
            "enableTimePointing": ["SSML_MARK"]
        }

    response = requests.post(url, headers=headers, json=payload, timeout=30)
    if response.status_code == 401:
        credentials.refresh(Request())
        headers["Authorization"] = f"Bearer {credentials.token}"
        response = requests.post(url, headers=headers, json=payload, timeout=30)
    response.raise_for_status()
    return response.json()

def save_results(data, output_dir):
    audio_path = os.path.join(output_dir, "audio.mp3")
    with open(audio_path, "wb") as f:
        f.write(base64.b64decode(data["audioContent"]))
    if "timepoints" in data:
        marks_path = os.path.join(output_dir, "marks.json")
        marks_data = {
            "marks": [
                {
                    "type": "MARK",
                    "value": tp["markName"],
                    "start": tp["timeSeconds"],
                    "end": tp["timeSeconds"]  # no tenim final exacte, però posem igual
                }
                for tp in data["timepoints"]
                if tp.get("markName", "").startswith("sub_")
            ]
        }
        with open(marks_path, "w", encoding="utf-8") as f:
            json.dump(marks_data, f, indent=2)
    return audio_path

def main():
    if len(sys.argv) < 2:
        print("Uso: python tts_js.py [ruta_relativa]")
        print("Ejemplo: python tts_js.py ECO/2222")
        return 1

    folder = os.path.join("output", sys.argv[1])
    try:
        print(f"\nℹ Procesando: {folder}")
        print("1. Autenticando...")
        creds = load_credentials()
        print(f"✓ Autenticado como: {creds.service_account_email}")
        print("2. Extrayendo SSML...")
        ssml = extract_ssml(folder)
        print(f"✓ SSML extraído ({len(ssml)} caracteres)")
        print("3. Sintetizando audio completo...")
        result = synthesize_speech(creds, ssml, folder)
        print("4. Guardando resultados...")
        audio_file = save_results(result, folder)
        print(f"✓ Audio generado: {audio_file}")
        if "timepoints" in result:
            print(f"✓ Marcas temporales guardadas ({len(result['timepoints'])} puntos)")
        print("\n✅ Proceso completado")
        return 0
    except Exception as e:
        print(f"\n❌ Error: {str(e)}")
        return 1
    except KeyboardInterrupt:
        print("\n⏹ Proceso cancelado")
        return 1

if __name__ == "__main__":
    sys.exit(main())