# GUÍA DE PRUEBA: Agente Universal y Autogestión 🚀

He transformado el sistema para que sea **100% autogestionable**. Ya no necesitas editar archivos `.ini` a mano. Aquí tienes cómo probarlo con tu PC y la Notebook.

## 🏁 Paso 1: Preparar el PC Servidor
1. Asegúrate de que XAMPP esté corriendo.
2. Entra al Dashboard de ServiRec en tu PC.
3. Verás un nuevo banner azul que dice: `AYUDA_DESPLIEGUE_LAN`. 
4. **Copia la IP** que aparece ahí (ej: `10.51.9.152`).

## 💻 Paso 2: Probar en el PC Local (Simulación)
1. Ve a la carpeta del proyecto: `c:\xampp\htdocs\servirec\scripts\agent\`.
2. **Borra** el archivo `config.ini` si existe.
3. Ejecuta el script: `python cyber_lock.py`.
4. **¡MAGIA!** Se abrirá una ventana de configuración:
   - Pega la IP del servidor.
   - Presiona "CONECTAR Y BUSCAR SIMULADORES".
   - Verás una lista desplegable con tus simuladores. **Elige uno** (ej: Simulador 01).
   - Presiona "GUARDAR Y BLOQUEAR".
5. El equipo se bloqueará automáticamente vinculado a ese ID.

## 📓 Paso 3: Probar en la Notebook (Red Real)
1. Copia el archivo `cyber_lock.py` (y la carpeta `dist` si ya generaste el exe) a la Notebook.
2. Asegúrate de que la Notebook esté en la **misma red Wi-Fi** que el PC Servidor.
3. Abre la app en la Notebook.
4. Al no tener configuración, te pedirá la IP. Pon la IP que viste en el Dashboard del Paso 1.
5. Selecciona un simulador diferente (ej: Simulador 02).
6. ¡Listo! La Notebook ahora es un terminal de ServiRec autogestionado.

---

### 💡 Tips Avanzados:
- **Para salir del bloqueo durante pruebas**: Presiona `CTRL + Q`.
- **Escalabilidad**: Puedes copiar el mismo archivo a 50 PCs, y en cada uno solo eliges qué número de simulador es.
- **Cambio de IP**: Si tu router cambia la IP del servidor algún día, solo borras el `config.ini` de los clientes y pones la nueva IP por la interfaz.

¡Pruébalo y dime qué tal te parece este nuevo flujo de trabajo! 🎮✨
