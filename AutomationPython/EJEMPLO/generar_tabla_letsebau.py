import requests
import json
import pandas as pd
from concurrent.futures import ThreadPoolExecutor, as_completed
from urllib3.exceptions import InsecureRequestWarning
import urllib3

# ⚠️ Desactivar warnings per verify=False
urllib3.disable_warnings(category=InsecureRequestWarning)

# 🔧 CONFIGURACIÓ
host = "s3.jupiter.cat"
database = "EB25"
layout = "EB25"
usuari = "@abde"
contrasenya = "7810LPW"
limit = 7000

BASE_URL = "https://letsebau.es/contenido"
CAMP_SERIAL_REAL = "Serial"

# 📝 Camps que volem exportar
camps_a_extreure = [
    "Serial",
    "SIGLAS",
    "ASIGNATURA_CAT",
    "TEMA_CAT",
    "Question_CAT",
    "OpcionA_CAT",
    "OpcionB_CAT",
    "OpcionC_CAT",
    "OpcionD_CAT",
    "Answer",
    "Answer_text_CAT",
    "EXPLICACION_CAT"
]

def autenticar():
    url = f"https://{host}/fmi/data/v1/databases/{database}/sessions"
    headers = {"Content-Type": "application/json"}
    r = requests.post(url, auth=(usuari, contrasenya), headers=headers, verify=False)
    r.raise_for_status()
    token = r.json()["response"]["token"]
    return token

def obtenir_registres(token):
    headers = {
        "Authorization": f"Bearer {token}",
        "Content-Type": "application/json"
    }
    url = f"https://{host}/fmi/data/v1/databases/{database}/layouts/{layout}/records?_limit={limit}"
    r = requests.get(url, headers=headers, verify=False)
    r.raise_for_status()
    data = r.json()["response"]["data"]
    return [r["fieldData"] for r in data]

def link_existeix(url):
    try:
        r = requests.head(url, timeout=5)
        return r.status_code == 200
    except:
        return False

def comprovar_registre(r):
    serial = str(r.get(CAMP_SERIAL_REAL, "")).strip()
    if not serial.isdigit():
        return None  # saltat

    audio_url = f"{BASE_URL}/{serial}/audio.mp3"
    index_url = f"{BASE_URL}/{serial}/index.html"

    if link_existeix(audio_url) and link_existeix(index_url):
        dades = {k: r.get(k, "") for k in camps_a_extreure}
        dades["audio_url"] = audio_url
        dades["index_url"] = index_url
        return dades
    else:
        return None

def filtrar_registres_parallel(registres, max_threads=20):
    resultats = []
    total = len(registres)
    valids = 0
    saltats = 0

    print(f"🧵 Iniciant comprovació amb {max_threads} fils...")
    with ThreadPoolExecutor(max_workers=max_threads) as executor:
        futures = [executor.submit(comprovar_registre, r) for r in registres]

        for i, future in enumerate(as_completed(futures), 1):
            resultat = future.result()
            if resultat:
                resultats.append(resultat)
                valids += 1
            else:
                saltats += 1

            if i % 100 == 0 or i == total:
                print(f"→ {i}/{total} processats... {valids} vàlids, {saltats} saltats")

    return resultats, saltats

def exportar_a_excel(dades, nom_fitxer="registre_letsebau.xlsx"):
    df = pd.DataFrame(dades)
    df.to_excel(nom_fitxer, index=False)
    print(f"✅ Excel guardat amb {len(dades)} registres: {nom_fitxer}")

def main():
    print("🔐 Connectant amb FileMaker...")
    try:
        token = autenticar()
        print("✅ Autenticació correcta.")
    except Exception as e:
        print(f"❌ Error autenticant: {e}")
        return

    try:
        print("📥 Obtenint registres...")
        registres = obtenir_registres(token)
        print(f"🔍 Obtinguts {len(registres)} registres.")
        print("\n📦 Exemple de registre:")
        print(json.dumps(registres[0], indent=2))

        print("\n⚡ Verificant existència d'àudio i animació...")
        valids, saltats = filtrar_registres_parallel(registres)

        exportar_a_excel(valids)

        print(f"\n📌 {len(valids)} registres vàlids guardats.")
        print(f"⚠️  {saltats} registres saltats.")
    except Exception as e:
        print(f"❌ Error durant el procés: {e}")

if __name__ == "__main__":
    main()
