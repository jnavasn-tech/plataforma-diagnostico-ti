# Plataforma Web de Diagnóstico y Capacitación Técnica en Informática

Prototipo funcional (fase de construcción inicial) del proyecto de tesis
*"Desarrollo e implementación de una plataforma web basada en Inteligencia
Artificial y de acceso gratuito, para el diagnóstico de habilidades y la
capacitación técnica en informática..."* — Josué Fernando Navas Nájera,
Universidad Mariano Gálvez de Guatemala.

## Stack utilizado en esta fase

- **Backend:** PHP 8.1+ (sin framework, estructura MVC simplificada)
- **Base de datos:** MySQL 8 (acceso vía PDO)
- **Frontend:** HTML5 + CSS3 (sin dependencias externas)
- **IA (pendiente de conectar):** OpenAI y/o DeepSeek — placeholder en `src/includes/ia_helper.php`

> El marco teórico de la tesis contempla también Python y Docker para
> etapas posteriores (ver Capítulo I, Objetivo específico d). Este scaffold
> arranca en PHP puro por ser el paso más rápido para tener el flujo
> completo funcionando; Python/Docker se incorporan cuando se conecte la
> API de IA real.

## Estructura del proyecto

```
plataforma-web/
├── public/                 # Document root (apuntar aquí el servidor web)
│   ├── index.php            # Landing page
│   ├── registro.php         # Alta de usuario
│   ├── login.php / logout.php
│   ├── dashboard.php        # Progreso del usuario por módulo
│   ├── diagnostico.php      # Cuestionario de diagnóstico
│   ├── capacitacion.php     # Catálogo de módulos
│   └── assets/css/style.css
├── src/
│   ├── config/               # Conexión a BD, variables de entorno
│   ├── includes/             # header/footer/auth/ia_helper
│   └── models/                # Usuario, Diagnostico, Modulo (acceso a datos)
├── database/schema.sql       # Esquema completo + datos semilla
├── .env.example
└── README.md
```

## Cómo levantarlo en local

### Opción A — XAMPP / WAMP (recomendado en Windows)

1. Copiar la carpeta `plataforma-web` dentro de `htdocs` (XAMPP) o `www` (WAMP).
2. Abrir phpMyAdmin y ejecutar el contenido de `database/schema.sql` (crea la
   base `plataforma_capacitacion` con tablas y datos de prueba).
3. Copiar `.env.example` a `.env` dentro de `plataforma-web/` y ajustar
   `DB_USER` / `DB_PASS` según tu instalación (por defecto XAMPP usa
   usuario `root` sin contraseña).
4. Visitar `http://localhost/plataforma-web/public/` en el navegador.

### Opción B — Servidor embebido de PHP (rápido para pruebas)

```bash
cd plataforma-web
cp .env.example .env   # editar credenciales de MySQL
php -S localhost:8000 -t public
```

Luego abrir `http://localhost:8000/`.

## Flujo funcional ya implementado

1. Un visitante se registra (`registro.php`) → queda autenticado.
2. Responde el diagnóstico (`diagnostico.php`), un cuestionario de opción
   múltiple sobre ofimática y ciberseguridad básica.
3. Al enviarlo, el sistema calcula el puntaje y genera una ruta de
   aprendizaje (por ahora con una regla simple basada en las respuestas
   falladas — ver `ia_helper.php`) asignando módulos de `capacitacion.php`.
4. El usuario ve su progreso en `dashboard.php`.

## Próximos pasos técnicos (para retroalimentar el Capítulo IV de la tesis)

- Sustituir la regla base de `generar_ruta_aprendizaje()` por una llamada
  real a la API de OpenAI/DeepSeek (prompt con resultados del diagnóstico).
- Agregar registro de progreso módulo a módulo (marcar como completado).
- Contenerizar con Docker (según el objetivo específico d de la tesis).
- Sumar pruebas de usabilidad con el grupo piloto (instrumento ya definido
  en el Marco Metodológico, sección 5.4).
- Endurecer seguridad: límite de intentos de login, CSRF token en formularios,
  validación de fuerza de contraseña.

## Notas

- No se pudo ejecutar `php -l` en este entorno de desarrollo por no contar
  con el intérprete de PHP instalado; se verificó balance de llaves/paréntesis
  en todos los archivos como chequeo básico. Se recomienda correr `php -l`
  o abrir el proyecto en XAMPP antes de considerarlo verificado en tu máquina.
