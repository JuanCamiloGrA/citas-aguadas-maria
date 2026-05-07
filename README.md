# Sistema De Agendamiento de Citas Hospital San José

App demo en PHP puro para agendar citas desde una vista pública y administrarlas desde un panel protegido.

## Qué incluye

- Página pública para seleccionar fecha, horario y registrar una cita.
- Calendario hecho con HTML, CSS y JavaScript puro.
- API simple en PHP para consultar disponibilidad y reservar.
- Panel administrativo con login por sesiones PHP.
- Registro de nuevos administradores.
- Cancelación de citas para liberar horarios.
- Base de datos MySQL con tablas `admins` y `appointments`.

## Requisitos

- UwAmp abierto y con Apache/MySQL iniciados.
- Proyecto ubicado en `C:\UwAmp\www\my-app`.
- Navegador web.

## Instalación desde cero

1. Abre UwAmp y confirma que Apache y MySQL estén corriendo.
2. Abre en el navegador:

   `http://localhost/my-app/install.php`

3. El instalador muestra datos por defecto:

   - Usuario: `admin`
   - Contraseña: `admin123`

4. Haz clic en **Instalar app demo**.
5. Entra a la app pública:

   `http://localhost/my-app/`

6. Entra al panel administrativo:

   `http://localhost/my-app/admin/`

## Configuración de base de datos

La conexión está en:

`config/database.php`

Valores por defecto para UwAmp:

- Host: `localhost`
- Base de datos: `hospital_sanjose_demo`
- Usuario: `root`
- Contraseña: `root`

Si tu instalación usa otra contraseña, cambia `DB_PASS`.

## Uso de la demo

Vista pública:

1. Selecciona una fecha en el calendario.
2. Elige un horario disponible.
3. Ingresa nombre, teléfono y correo opcional.
4. Confirma la cita.

Panel administrativo:

1. Inicia sesión con el admin creado en `install.php`.
2. Revisa las citas registradas.
3. Cancela citas cuando sea necesario.
4. Crea otros administradores desde **Nuevo admin**.

## Nota

Esta app es solo una demo. No implementa seguridad avanzada, JWT, roles complejos ni controles production ready.

