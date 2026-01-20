import requests
import json
import urllib3
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

host = "s3.jupiter.cat"
database = "EB25"
layout = "EB25"
usuario = "@abde"
password = "7810LPW"

def autenticar():
    url = f"https://{host}/fmi/data/v1/databases/{database}/sessions"
    headers = {
        "Content-Type": "application/json"
    }
    r = requests.post(url, auth=(usuario, password), headers=headers, verify=False)
    r.raise_for_status()
    token = r.json()["response"]["token"]
    return token


def obtener_registros(token, lote=250, max_registros=float("inf")):
    headers = {
        "Authorization": f"Bearer {token}",
        "Content-Type": "application/json"
    }
    todos = []
    offset = 0

    while offset < max_registros:
        print(f"[🔄] Descargando registros {offset} - {offset + lote}...")

        if offset == 0:
            url = f"https://{host}/fmi/data/v1/databases/{database}/layouts/{layout}/records?_limit={lote}"
        else:
            url = f"https://{host}/fmi/data/v1/databases/{database}/layouts/{layout}/records?_limit={lote}&_offset={offset}"


        r = requests.get(url, headers=headers, verify=False, timeout=180)

        if r.status_code == 401:
            raise Exception("🔒 Token expirado o inválido.")
        if r.status_code == 400:
            error_details = r.json().get("messages", [])
            print(f"❌ Error 400: {error_details}")
            break

        r.raise_for_status()
        data = r.json().get("response", {}).get("data", [])

        if not data:
            print("[ℹ️] No hay más registros disponibles.")
            break

        todos.extend([reg["fieldData"] for reg in data])

        if len(data) < lote:
            break

        offset += lote

    return todos
