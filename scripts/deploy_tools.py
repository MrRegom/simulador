import os
import shutil

# Configuración
BASE_DIR = 'public\\downloads'
EXE_NAME = 'Agente_A4R.exe'
NUM_SIMULADORES = 6
SERVER_IP = '192.168.1.100' # IP EJEMPLO

def create_deploy_folders():
    if not os.path.exists(os.path.join(BASE_DIR, EXE_NAME)):
        print(f"Error: {EXE_NAME} no encontrado en {BASE_DIR}")
        return

    deploy_root = os.path.join(BASE_DIR, 'deploy_pack')
    if os.path.exists(deploy_root):
        shutil.rmtree(deploy_root)
    os.makedirs(deploy_root)

    print(f"Generando paquetes para {NUM_SIMULADORES} simuladores en {deploy_root}...")

    for i in range(1, NUM_SIMULADORES + 1):
        folder_name = f"Simulador_{i:02d}"
        target_dir = os.path.join(deploy_root, folder_name)
        os.makedirs(target_dir)
        
        # 1. Copiar EXE
        src_exe = os.path.join(BASE_DIR, EXE_NAME)
        dst_exe = os.path.join(target_dir, EXE_NAME)
        shutil.copy2(src_exe, dst_exe)
        
        # 2. Crear config.ini específico
        config_content = f"""[CONFIG]
server_url=http://{SERVER_IP}/servirec
simulador_id={i}
check_interval=5
"""
        with open(os.path.join(target_dir, 'config.ini'), 'w') as f:
            f.write(config_content)
            
        print(f"  [+] Creado {folder_name} (ID={i})")

    print("\n¡Listo! Copia cada carpeta al PC correspondiente.")

if __name__ == "__main__":
    create_deploy_folders()
