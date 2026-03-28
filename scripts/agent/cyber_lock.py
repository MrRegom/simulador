import tkinter as tk
from tkinter import messagebox
import time
import requests
import configparser
import os
import sys
import socket
from urllib.parse import urlparse
import qrcode
from PIL import ImageTk
from PIL import Image
import threading
import base64
import io

APP_TITLE = "A4R - SEGURIDAD ACTIVA"
DEFAULT_INTERVAL = 5
SERVER_LOCAL = "http://localhost/servirec"
SERVER_PUBLIC = "https://www.a4rsimracing.cl/a4r"

EMBEDDED_LOGO_B64 = (
    "iVBORw0KGgoAAAANSUhEUgAAAlgAAAGQCAMAAABF6+6qAAABN2lDQ1BBZG9iZSBS"
    "R0IgKDE5OTgpAAAokZWPv0rDUBSHvxtFxaFWCOLgcCdRUGzVwYxJW4ogWKtDkq1"
    "JQ5ViEm6uf/oQjm4dXNx9AidHwUHxCXwDxamDQ4QMBYvf9J3fORzOAaNi152GUYb"
    "zWKt205Gu58vZF2aYAoBOmKV2q3UAECdxxBjf7wiA10277jTG+38yH6ZKAyNguxt"
    "lIYgK0L/SqQYxBMygn2oQD4CpTto1EE9AqZf7G1AKcv8ASsr1fBBfgNlzPR+MOcA"
    "Mcl8BTB1da4Bakg7UWe9Uy6plWdLuJkEkjweZjs4zuR+HiUoT1dFRF8jvA2AxH2w"
    "3HblWtay99X/+PRHX82Vun0cIQCw9F1lBeKEuf1UYO5PrYsdwGQ7vYXpUZLs3cLc"
    "BC7dFtlqF8hY8Dn8AwMZP/fNTP8gAAAMAUExURcbGxqampvObm3R0dPzp6bS0tPmj"
    "o/zl5YaGhv76+v3s7PvY2A0NDZycnOg/PycnJz4+PvnMzKqqqoiIiJmZmUJCQpqa"
    "mgAAADw8PPWpqYSEhPjFxRkZGXd3dxUVFbKysvrR0faysqysrLe3t0ZGRuxhYaGho"
    "Xp6epKSkvq2tve9vWBgYIyMjO91dUhISKKioo6OjulJSXJych4eHn19feQdHfvc3"
    "Pzz825ubmZmZpCQkOUpKe5vb+MVFaioqLCwsFRUVPGNjTQ0NOUhIYqKijAwMGtra"
    "+xlZfWkpPnJyfShoepMTOYtLepSUggICP729lJSUoCAgBISEvGJiVhYWGRkZOMREf"
    "WtreYxMetVVfrU1EBAQOUkJO1oaOhBQfe5ue95eQUFBUxMTFxcXE5OTvKRkTIyMu"
    "5xcf74+GJiYvOdnURERDY2Nv/8/JSUlDk5OetZWfN0dF5eXlZWVvvh4YKCgmxsbC"
    "IiIiwsLPCFhexdXSQkJPKVlfa2tigoKOIMDPjCwioqKvCBgVpaWhwcHPB+fi4uLi"
    "AgIOQaGg8PD/zi4jo6OuIPD/mWluc0NP28vOlFRf709P3w8Oc6Ou1ra+taWgMDA5"
    "6enmhoaMPDw1FRUUtLS3BwcPT09OXl5Xh4ePz8/Pj4+Ofn59XV1ff39/39/c/Pz/v"
    "7+6SkpPr6+uvr6729vfHx8fPz876+vvn5+fb29uzs7O3t7X9/f+bm5vX19fDw8O7"
    "u7tfX18LCwt3d3cnJyb+/v9TU1NjY2K+vr/Ly8paWlt/f38TExM7OzkpKSn5+fsz"
    "MzOnp6d7e3uPj49HR0bu7u83Nze/v7+Dg4Nra2peXl8XFxXl5eejo6NPT08vLy+Tk"
    "5Ly8vNzc3NDQ0OHh4bi4uLm5ucDAwHFxcdvb266urtnZ2erq6srKysHBwaWlpdbW"
    "1sjIyLq6uuLi4tLS0lBQUOsdHWlpaZ+fn+INDfiZmeg7O/BQUP7z8/fAwOMXF+9HR"
    "/l+fvddXfSfn/34+P3Ly/BMTPNtbf///0/1ViUAAAEAdFJOU////////////////"
    "////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////wBT9wcl"
    "AAAACXBIWXMAAAsTAAALEwEAmpwYAAAFtmlUWHRYTUw6Y29tLmFkb2JlLnhtcAAA"
    "AAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRc"
    "emtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4"
    "bXB0az0iQWRvYmUgWE1QIENvcmUgNi4wLWMwMDIgNzkuMTY0NDYwLCAyMDIwLzA1"
    "LzEyLTE2OjA0OjE3ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6"
    "Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRl"
    "c2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9i"
    "ZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29t"
    "L3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94"
    "YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9u"
    "cy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIgeG1sbnM6"
    "ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIiB4bWxuczpwaG90"
    "b3Nob3A9Imh0dHA6Ly9ucy5hZG9iZS5jb20vcGhvdG9zaG9wLzEuMC8iIHhtcDpD"
    "cmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIDI3LjQgKFdpbmRvd3MpIiB4bXA6"
    "Q3JlYXRlRGF0ZT0iMjAyNi0wMy0wN1QxNDo0MTo1My0wMzowMCIgeG1wOk1vZGlm"
    "eURhdGU9IjIwMjYtMDMtMDdUMTQ6NDYtMDM6MDAiIHhtcDpNZXRhZGF0YURhdGU9"
    "IjIwMjYtMDMtMDdUMTQ6NDYtMDM6MDAiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5p"
    "aWQ6ZmY0OGU2M2QtMGE3ZC1hNDQyLWIwZGMtNDRmMGJjZGQyOWM4IiB4bXBNTTpE"
    "b2N1bWVudElEPSJ4bXAuZGlkOjhFRkI4NTRCMTBCQzExRjE4MUNGRjlEQUQyM0I3"
    "NDIyIiB4bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ9InhtcC5kaWQ6OEVGQjg1NEIx"
    "MEJDMTFGMTgxQ0ZGOURBRDIzQjc0MjIiIGRjOmZvcm1hdD0iaW1hZ2UvcG5nIiBw"
    "aG90b3Nob3A6Q29sb3JNb2RlPSIyIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVm"
    "Omluc3RhbmNlSUQ9InhtcC5paWQ6OEVGQjg1NDgxMEJDMTFGMTgxQ0ZGOURBRDIz"
    "Qjc0MjIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6OEVGQjg1NDkxMEJDMTFG"
    "MTgxQ0ZGOURBRDIzQjc0MjIiLz4gPHhtcE1NOkhpc3Rvcnk+IDxyZGY6U2VxPiA8"
    "cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9Inht"
    "cC5paWQ6ZmY0OGU2M2QtMGE3ZC1hNDQyLWIwZGMtNDRmMGJjZGQyOWM4IiBzdEV2"
    "dDp3aGVuPSIyMDI2LTAzLTA3VDE0OjQ2LTAzOjAwIiBzdEV2dDpzb2Z0d2FyZUFn"
    "ZW50PSJBZG9iZSBQaG90b3Nob3AgMjEuMiAoV2luZG93cykiIHN0RXZ0OmNoYW5n"
    "ZWQ9Ii8iLz4gPC9yZGY6U2VxPiA8L3htcE1NOkhpc3Rvcnk+IDwvcmRmOkRlc2Ny"
    "aXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJy"
    "Ij8+ZhaEjgAAJsNJREFUeJztnXu0FdWd57+X5/XCDQgKCthGBIIPwqNVyBCkG0VR"
    "J3pjIosciLZ0r3S7usOMnc64OqtNk5lZcdJOljOs9KSXq9sEEk4Tbe2LjvhAMUhI"
    "RAjCGN5CB0FRiHBNAEHw0n/UqVO7qnZV7V/t/asqnN/nHy51du3a55zv2c/fo+UMB"
    "ME9PcpugPDxRIQlsCDCElgQYQksiLAEFkRYAgsiLIEFEZbAgghLYEGEJbAgwhJYEG"
    "EJLIiwBBZEWAILIiyBBRGWwIIIS2BBhCWwIMISWBBhCSyIsAQWRFgCCyIsgQURlsC"
    "CCEtgQYQlsCDCElgQYQksiLAEFkRYAgsiLIEFEZbAgghLYEGEJbAgwhJYEGEJLIiw"
    "BBZEWAILIiyBBRGWwIIIS2BBhCWwIMISWBBhCSyIsAQWRFgCCyIsgQURlsCCCEtgQ"
    "YQlsCDCElgQYQksiLAEFkRYAgsiLIEFEZbAgghLYEGEJbAgwhJYEGEJLIiwBBZEWA"
    "ILIiyBBRGWwIIIS2BBhCWwIMISWBBhCSyIsAQWRFgCCyIsgQURlsCCCEtgQYQlsCD"
    "CElgQYQksiLAEFkRYAgsiLIEFEZbAgghLYEGEJbAgwhJYEGEJLIiwBBZEWAILIiyB"
    "BRGWwIIIS2BBhCWwIMISWBBhCSyIsAQWRFgCCyIsgQURlsCCCEtgQYQlsCDCElgQY"
    "QksiLAEFkRYAgsiLIEFEZbAgghLYOHfATGRacXzhwrqAAAAAElFTkSuQmCC"
)


def get_config_path():
    if getattr(sys, 'frozen', False):
        application_path = os.path.dirname(sys.executable)
    else:
        application_path = os.path.dirname(os.path.abspath(__file__))
    return os.path.join(application_path, 'config.ini')

def get_asset_path(rel_path):
    if getattr(sys, 'frozen', False):
        base = getattr(sys, '_MEIPASS', os.path.dirname(sys.executable))
    else:
        base = os.path.dirname(os.path.abspath(__file__))
    return os.path.join(base, 'assets', rel_path)


def read_config_section():
    path = get_config_path()
    if not os.path.exists(path):
        return None
    config = configparser.ConfigParser()
    try:
        config.read(path)
        if 'CONFIG' in config:
            return config['CONFIG']
        if 'DEFAULT' in config:
            return config['DEFAULT']
        return None
    except Exception:
        return None


def normalize_server_url(value):
    if not value:
        return None
    val = value.strip()
    if not val:
        return None
    if not val.startswith('http://') and not val.startswith('https://'):
        val = 'http://' + val
    return val.rstrip('/')


def derive_server_url_from_api(api_url):
    if not api_url:
        return None
    try:
        parsed = urlparse(api_url.strip())
        path = parsed.path or ''
        if path.endswith('/api/simulador/estado'):
            path = path[:-len('/api/simulador/estado')]
        elif path.endswith('/public/index.php'):
            path = path[:-len('/public/index.php')]
        elif path.endswith('/index.php'):
            path = path[:-len('/index.php')]
        base = f"{parsed.scheme}://{parsed.netloc}{path}"
        return base.rstrip('/')
    except Exception:
        return None


def derive_server_url_from_legacy(server_ip, api_path):
    if not server_ip:
        return None
    base_path = ''
    if api_path:
        path = api_path.split('?', 1)[0]
        if path.endswith('/public/index.php'):
            base_path = path[:-len('/public/index.php')]
        elif path.endswith('/index.php'):
            base_path = path[:-len('/index.php')]
        else:
            base_path = os.path.dirname(path)
    return f"http://{server_ip}{base_path}".rstrip('/')


def load_normalized_config():
    section = read_config_section()
    if not section:
        return None

    server_url = section.get('server_url')
    if not server_url:
        api_url = section.get('api_url') or section.get('API_URL')
        server_url = derive_server_url_from_api(api_url)

    if not server_url:
        server_ip = section.get('server_ip') or section.get('SERVER_IP')
        api_path = section.get('api_path') or section.get('API_PATH')
        server_url = derive_server_url_from_legacy(server_ip, api_path)

    server_url = normalize_server_url(server_url) if server_url else None

    simulador_id = section.get('simulador_id') or section.get('id_simulador') or section.get('ID_SIMULADOR')
    check_interval = section.get('check_interval') or section.get('CHECK_INTERVAL') or str(DEFAULT_INTERVAL)

    if simulador_id:
        simulador_id = str(simulador_id).strip()

    try:
        check_interval = int(check_interval)
    except Exception:
        check_interval = DEFAULT_INTERVAL

    if not server_url or not simulador_id:
        return None

    return {
        'server_url': server_url,
        'simulador_id': simulador_id,
        'check_interval': check_interval
    }


def save_config(server_url, simulador_id, check_interval=DEFAULT_INTERVAL):
    path = get_config_path()
    config = configparser.ConfigParser()
    config['CONFIG'] = {
        'server_url': server_url,
        'simulador_id': str(simulador_id),
        'check_interval': str(check_interval)
    }
    with open(path, 'w') as configfile:
        config.write(configfile)


class CyberLockApp:
    def __init__(self, root):
        self.root = root
        self.root.title(APP_TITLE)
        self.root.configure(bg='black')

        self.server_url = None
        self.simulador_id = None
        self.check_interval = DEFAULT_INTERVAL
        self.pair_token = None
        self.pair_url = None
        self.mode = 'setup'
        self.qr_image = None
        self.logo_img = None

        self.root.bind('<Control-q>', lambda e: self.graceful_exit())

        self.play_startup_sound()

        cfg = load_normalized_config()
        if cfg:
            self.server_url = cfg['server_url']
            self.simulador_id = cfg['simulador_id']
            self.check_interval = cfg['check_interval']
            save_config(self.server_url, self.simulador_id, self.check_interval)
            self.start_lock_mode()
        else:
            self.start_setup_mode()

    def clear_root(self):
        for child in self.root.winfo_children():
            child.destroy()

    def start_setup_mode(self):
        self.mode = 'setup'
        self.clear_root()
        self.root.attributes('-fullscreen', False)
        self.root.attributes('-topmost', False)
        self.root.geometry('900x620')

        frame = tk.Frame(self.root, bg='#0a0a0a')
        frame.pack(expand=True, fill='both')

        bg = tk.Canvas(frame, bg='#0a0a0a', highlightthickness=0)
        bg.place(relx=0, rely=0, relwidth=1, relheight=1)
        self.draw_vertical_gradient(bg, '#050505', '#111111')

        logo_frame = tk.Frame(frame, bg='#0a0a0a')
        logo_frame.pack(pady=(22, 6))
        self.render_logo(logo_frame, 280)

        title = tk.Label(frame, text='CONFIGURAR AGENTE', fg='white', bg='#0a0a0a', font=('Segoe UI', 18, 'bold'))
        title.pack(pady=(4, 6))

        desc = tk.Label(frame, text='Selecciona el entorno y genera el QR para vincular este PC.', fg='#888', bg='#0a0a0a', font=('Segoe UI', 11))
        desc.pack(pady=(0, 12))

        self.server_var = tk.StringVar(value=self.server_url or SERVER_LOCAL)
        entry = tk.Entry(frame, textvariable=self.server_var, font=('Segoe UI', 12), width=45, state='readonly', readonlybackground='#000', fg='white', justify='center')
        entry.pack(pady=6)

        btns = tk.Frame(frame, bg='#0a0a0a')
        btns.pack(pady=6)
        tk.Button(btns, text='LOCAL (LAN)', command=lambda: self.server_var.set(SERVER_LOCAL),
                  bg='#111', fg='white', font=('Segoe UI', 10, 'bold'),
                  relief='flat', width=16, activebackground='#ff0000', activeforeground='#fff').grid(row=0, column=0, padx=6)
        tk.Button(btns, text='SERVIDOR (WEB)', command=lambda: self.server_var.set(SERVER_PUBLIC),
                  bg='#111', fg='white', font=('Segoe UI', 10, 'bold'),
                  relief='flat', width=16, activebackground='#ff0000', activeforeground='#fff').grid(row=0, column=1, padx=6)

        btn = tk.Button(frame, text='GENERAR QR', command=self.generate_pairing, bg='#ff0000', fg='white', font=('Segoe UI', 12, 'bold'), relief='flat')
        btn.pack(pady=10)

        self.qr_label = tk.Label(frame, bg='white', width=260, height=260)
        self.qr_label.pack(pady=12)

        self.url_label = tk.Label(frame, text='QR no generado', fg='#666', bg='#0a0a0a', font=('Consolas', 10))
        self.url_label.pack(pady=(5, 8))

        self.status_label = tk.Label(frame, text='Esperando configuracion...', fg='#00ff9d', bg='#0a0a0a', font=('Segoe UI', 11, 'bold'))
        self.status_label.pack(pady=(4, 10))

        hint = tk.Label(frame, text='Si no hay internet, conecte el celular a la misma Wi-Fi del servidor.', fg='#555', bg='#0a0a0a', font=('Segoe UI', 9))
        hint.pack(pady=(0, 10))

    def generate_pairing(self):
        server_url = normalize_server_url(self.server_var.get())
        if not server_url:
            messagebox.showerror('ERROR', 'Ingrese una URL valida del servidor.')
            return

        self.server_url = server_url
        self.status_label.config(text='Conectando al servidor...', fg='#00ff9d')
        self.root.update_idletasks()

        try:
            payload = {'client_name': socket.gethostname()}
            r = requests.post(f"{server_url}/api/pair/new", json=payload, timeout=6)
            data = r.json()
        except Exception:
            self.status_label.config(text='No se pudo conectar al servidor.', fg='#ff6b6b')
            return

        if not data.get('success'):
            self.status_label.config(text='Error al generar QR.', fg='#ff6b6b')
            return

        self.pair_token = data.get('token')
        self.pair_url = data.get('pair_url')

        if not self.pair_url:
            self.status_label.config(text='QR invalido. Intente nuevamente.', fg='#ff6b6b')
            return

        self.render_qr(self.pair_url)
        self.url_label.config(text=self.pair_url)
        self.status_label.config(text='Escanee el QR y asigne el simulador.', fg='#00ff9d')

        self.root.after(1500, self.poll_pair_status)

    def render_qr(self, data):
        qr = qrcode.QRCode(border=1, box_size=6)
        qr.add_data(data)
        qr.make(fit=True)
        img = qr.make_image(fill_color='black', back_color='white')
        self.qr_image = ImageTk.PhotoImage(img)
        self.qr_label.config(image=self.qr_image)

    def poll_pair_status(self):
        if not self.server_url or not self.pair_token:
            return
        try:
            r = requests.get(f"{self.server_url}/api/pair/status?token={self.pair_token}", timeout=4)
            data = r.json()
        except Exception:
            self.root.after(2000, self.poll_pair_status)
            return

        if data.get('expired'):
            self.status_label.config(text='QR expirado. Genere uno nuevo.', fg='#ff6b6b')
            return

        if data.get('assigned') and data.get('equipo_id'):
            self.simulador_id = str(data.get('equipo_id'))
            save_config(self.server_url, self.simulador_id, self.check_interval)
            self.start_lock_mode()
            return

        self.root.after(2000, self.poll_pair_status)

    def start_lock_mode(self):
        self.mode = 'lock'
        self.clear_root()
        self.root.attributes('-fullscreen', True)
        self.root.attributes('-topmost', True)
        self.root.protocol("WM_DELETE_WINDOW", lambda: None)

        full_frame = tk.Frame(self.root, bg='black')
        full_frame.pack(expand=True, fill='both')

        logo_frame = tk.Frame(full_frame, bg='black')
        logo_frame.place(relx=0.5, rely=0.35, anchor='center')
        self.render_logo(logo_frame, 420, bg='black')

        self.lbl_msg = tk.Label(full_frame, text='INICIANDO SEGURIDAD...', font=('Segoe UI', 30, 'bold'), fg='white', bg='black')
        self.lbl_msg.place(relx=0.5, rely=0.65, anchor='center')

        self.lbl_sub = tk.Label(full_frame, text='POR FAVOR, CANCELA TU TIEMPO EN EL MESON', font=('Segoe UI', 16), fg='#444', bg='black')
        self.lbl_sub.place(relx=0.5, rely=0.75, anchor='center')

        info = f"ESTACION ID: {self.simulador_id} | SERVIDOR: {self.server_url}"
        tk.Label(full_frame, text=info, fg='#222', bg='black', font=('Arial', 10)).pack(side='bottom', pady=20)

        self.root.after(1000, self.check_status)

    def render_logo(self, parent, width, bg='#0a0a0a'):
        img = None
        try:
            logo_path = get_asset_path('logoa4r.png')
            if os.path.exists(logo_path):
                img = Image.open(logo_path)
        except Exception:
            img = None

        if img is None:
            try:
                data = base64.b64decode(EMBEDDED_LOGO_B64)
                img = Image.open(io.BytesIO(data))
            except Exception:
                img = None

        if img is None:
            # Fallback textual logo
            f_logo = ('Arial Black', 72, 'italic')
            cv = tk.Canvas(parent, width=520, height=140, bg=bg, highlightthickness=0)
            cv.pack()
            cv.create_text(150, 70, text='A', fill='#E30613', font=f_logo)
            cv.create_text(260, 70, text='4', fill='white', font=f_logo)
            cv.create_text(370, 70, text='R', fill='#E30613', font=f_logo)
            return

        wpercent = (width / float(img.size[0]))
        hsize = int((float(img.size[1]) * float(wpercent)))
        img = img.resize((width, hsize), Image.LANCZOS)
        self.logo_img = ImageTk.PhotoImage(img)
        lbl = tk.Label(parent, image=self.logo_img, bg=bg)
        lbl.pack()

    def draw_vertical_gradient(self, canvas, color1, color2):
        try:
            width = canvas.winfo_screenwidth()
            height = canvas.winfo_screenheight()
            r1, g1, b1 = self.hex_to_rgb(color1)
            r2, g2, b2 = self.hex_to_rgb(color2)
            for i in range(height):
                r = int(r1 + (r2 - r1) * i / height)
                g = int(g1 + (g2 - g1) * i / height)
                b = int(b1 + (b2 - b1) * i / height)
                canvas.create_line(0, i, width, i, fill=self.rgb_to_hex(r, g, b))
        except Exception:
            pass

    def hex_to_rgb(self, hex_color):
        hex_color = hex_color.lstrip('#')
        return tuple(int(hex_color[i:i+2], 16) for i in (0, 2, 4))

    def rgb_to_hex(self, r, g, b):
        return f"#{r:02x}{g:02x}{b:02x}"

    def play_startup_sound(self):
        def _play():
            try:
                import winsound
                winsound.Beep(880, 120)
                winsound.Beep(1320, 120)
                winsound.Beep(990, 140)
            except Exception:
                pass
        threading.Thread(target=_play, daemon=True).start()

    def check_status(self):
        try:
            url = f"{self.server_url}/api/simulador/estado?id={self.simulador_id}"
            r = requests.get(url, timeout=4)
            data = r.json()

            if data.get('bloqueado', True):
                self.lock_screen(data.get('mensaje', 'ESTACION BLOQUEADA'))
            else:
                self.unlock_screen()
        except Exception:
            self.lbl_msg.config(text='RECONECTANDO AL SERVIDOR...', fg='#E30613')
            self.root.deiconify()

        intervalo = int(self.check_interval) * 1000
        self.root.after(intervalo, self.check_status)

    def lock_screen(self, msg):
        self.lbl_msg.config(text=str(msg).upper(), fg='white')
        self.root.deiconify()
        self.root.attributes('-topmost', True)

    def unlock_screen(self):
        self.root.withdraw()

    def send_offline(self):
        if self.mode != 'lock' or not self.server_url or not self.simulador_id:
            return
        try:
            requests.post(f"{self.server_url}/api/simulador/offline", json={'id': self.simulador_id}, timeout=2)
        except Exception:
            pass

    def graceful_exit(self):
        self.send_offline()
        self.root.destroy()


if __name__ == '__main__':
    root = tk.Tk()
    app = CyberLockApp(root)
    root.mainloop()
