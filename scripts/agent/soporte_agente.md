# Guía de Soporte: Conexión CyberLock Agent A4R

Si el agente muestra el mensaje **"No se detecta el servidor A4R en esa IP"**, sigue estos pasos técnicos en orden.

## 1. Archivos Necesarios
*   **Solo necesitas `CyberLock.exe`**. 
*   **NO** necesitas un archivo `.ino` (eso es para Arduino).
*   El archivo **`config.ini`** se crea solo cuando la conexión es exitosa por primera vez. Si lo tienes y quieres "empezar de cero", puedes borrarlo.

## 2. Corrección de Servidor (¡IMPORTANTE!)
He detectado que faltaba una "puerta" (ruta) en el servidor para que el agente pudiera ver la lista de simuladores.
> [!IMPORTANT]
> **Acabo de corregir el archivo `public/index.php`**. Asegúrate de que el servidor XAMPP tenga los cambios que apliqué.

## 3. Verificación de Red
Para que el agente encuentre al servidor:
1.  **Misma Red**: Ambos equipos (PC Servidor y Notebook/Simulador) deben estar en el mismo Wi-Fi o cable.
2.  **IP Correcta**: 
    *   En el PC Servidor, abre una terminal y escribe `ipconfig`.
    *   Usa la **Dirección IPv4** (ejemplo: `192.168.1.15`).
3.  **Firewall de Windows**: 
    *   El PC que tiene XAMPP (el servidor) suele bloquear conexiones entrantes.
    *   Prueba desactivando el Firewall temporalmente o añade una regla para permitir el puerto 80.

## 4. Prueba de Navegador
En el Notebook (donde falla el agente), abre Chrome y pega esto:
`http://TU_IP_SERVIDOR/servirec/public/index.php?url=api/simulador/listar_equipos`

*   **Si carga un texto con letras (JSON)**: El servidor está accesible y el agente debería funcionar.
*   **Si NO carga nada o dice "No se puede acceder"**: Es el Firewall o la IP está mal escrita.

## 5. El Backdoor de Emergencia
Si por alguna razón el agente se bloquea y no puedes salir:
*   Usa la combinación **`Ctrl + Q`** en el teclado para cerrar el programa inmediatamente.
