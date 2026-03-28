import os
import sys
import shutil
import tkinter as tk
from tkinter import filedialog, messagebox
from PIL import Image, ImageTk

APP_TITLE = "A4R Installer"


def resource_path(*parts):
    base = getattr(sys, '_MEIPASS', os.path.dirname(os.path.abspath(__file__)))
    return os.path.join(base, *parts)


class InstallerApp:
    def __init__(self, root):
        self.root = root
        self.root.title(APP_TITLE)
        self.root.geometry('640x420')
        self.root.configure(bg='#0a0a0a')

        self.logo_img = None
        self.dest_var = tk.StringVar(value='C:/A4R_Agente')

        self.render_ui()

    def render_ui(self):
        frame = tk.Frame(self.root, bg='#0a0a0a')
        frame.pack(expand=True, fill='both')

        logo_frame = tk.Frame(frame, bg='#0a0a0a')
        logo_frame.pack(pady=(24, 8))
        self.render_logo(logo_frame, 240)

        tk.Label(frame, text='INSTALADOR A4R', fg='white', bg='#0a0a0a', font=('Segoe UI', 16, 'bold')).pack(pady=(4, 6))
        tk.Label(frame, text='Seleccione la carpeta y presione Instalar', fg='#888', bg='#0a0a0a', font=('Segoe UI', 10)).pack(pady=(0, 12))

        entry = tk.Entry(frame, textvariable=self.dest_var, font=('Segoe UI', 11), width=40, justify='center')
        entry.pack(pady=6)

        tk.Button(frame, text='ELEGIR CARPETA', command=self.pick_folder, bg='#111', fg='white', relief='flat', width=18).pack(pady=6)
        tk.Button(frame, text='INSTALAR', command=self.install, bg='#ff0000', fg='white', font=('Segoe UI', 11, 'bold'), relief='flat', width=18).pack(pady=8)

        self.status = tk.Label(frame, text='Listo para instalar', fg='#00ff9d', bg='#0a0a0a', font=('Segoe UI', 10, 'bold'))
        self.status.pack(pady=(8, 0))

    def render_logo(self, parent, width):
        try:
            logo_path = resource_path('assets', 'logoa4r.png')
            img = Image.open(logo_path)
            wpercent = width / float(img.size[0])
            hsize = int(float(img.size[1]) * wpercent)
            img = img.resize((width, hsize), Image.LANCZOS)
            self.logo_img = ImageTk.PhotoImage(img)
            tk.Label(parent, image=self.logo_img, bg='#0a0a0a').pack()
        except Exception:
            tk.Label(parent, text='A4R', fg='white', bg='#0a0a0a', font=('Segoe UI', 36, 'bold')).pack()

    def pick_folder(self):
        folder = filedialog.askdirectory()
        if folder:
            self.dest_var.set(folder)

    def install(self):
        dest = self.dest_var.get().strip()
        if not dest:
            messagebox.showerror('Error', 'Seleccione una carpeta')
            return
        try:
            os.makedirs(dest, exist_ok=True)
            src_exe = resource_path('payload', 'Agente_A4R.exe')
            src_ini = resource_path('payload', 'config.ini')
            shutil.copy2(src_exe, os.path.join(dest, 'Agente_A4R.exe'))
            shutil.copy2(src_ini, os.path.join(dest, 'config.ini'))
            self.status.config(text='Instalacion completa', fg='#00ff9d')
            messagebox.showinfo('A4R', 'Instalacion completa')
        except Exception as e:
            self.status.config(text='Error al instalar', fg='#ff6b6b')
            messagebox.showerror('Error', str(e))


if __name__ == '__main__':
    root = tk.Tk()
    app = InstallerApp(root)
    root.mainloop()
