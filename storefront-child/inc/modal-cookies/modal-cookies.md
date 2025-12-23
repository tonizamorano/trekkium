# Modal de Cookies para Trekkium

Este modal se muestra cuando los usuarios visitan la web de Trekkium por primera vez y les permite gestionar sus preferencias de cookies.

## Características

- Modal completamente centrado vertical y horizontalmente
- Diseño responsive (600px de ancho en escritorio, 90% en móvil)
- Dividido en dos secciones: contenido desplazable y pie fijo
- Opciones para:
  - Aceptar todas las cookies
  - Rechazar todas las cookies
  - Configurar preferencias personalizadas
- Integración con AJAX para guardar preferencias sin recargar la página
- Cookies persistentes por 1 año

## Estructura del Modal

### Parte superior (contenido desplazable)
1. Título: "Tu Privacidad es importante para Trekkium"
2. Descripción explicativa
3. Enlace a la Política de Cookies
4. Separador visual
5. Sección de Cookies Técnicas (siempre activas)
6. Sección de Cookies Analíticas con opción de aceptar/rechazar
7. Botón "Confirmar las preferencias"

### Parte inferior (fija)
1. Botón "Rechazar todas"
2. Botón "Aceptar todas"

## Instalación y Uso

1. Copiar la carpeta `inc/modal` en tu tema hijo de Trekkium
2. Asegurarse de que el archivo `modal-cookies.php` esté incluido en el `functions.php` del tema hijo:

```php
// En el functions.php de tu tema hijo
require_once get_stylesheet_directory() . '/inc/modal/modal-cookies.php';