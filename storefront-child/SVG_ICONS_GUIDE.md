# SVG Icon System - Guía de Uso

## Descripción General

Se ha consolidado un sistema centralizado de iconos SVG usando un **sprite** único. 

**Ventajas:**
- ✅ 1 HTTP request en lugar de 15+ (mejor rendimiento)
- ✅ Caching global del sprite
- ✅ Control global de estilos y colores
- ✅ Código PHP limpio y legible
- ✅ Compatible con shortcodes antiguos `[svg_*]`

---

## Uso en Plantillas PHP

### 1. **Función Helper Directa** (Recomendado)

```php
<?php echo trekkium_icon_acceso('icon-md', 'Acceso'); ?>
<?php echo trekkium_icon_actividades2('icon-lg', 'Actividades'); ?>
<?php echo trekkium_icon_blog('', 'Blog'); ?>
<?php echo trekkium_icon_dificultad('icon-sm', 'Dificultad'); ?>
<?php echo trekkium_icon_duracion('icon-md', 'Duración', '1'); // variant 1 o 2 ?>
<?php echo trekkium_icon_estrella('icon-lg', 'Valoración'); ?>
<?php echo trekkium_icon_fecha('', 'Fecha'); ?>
<?php echo trekkium_icon_guias('icon-md', 'Guías', '1'); // variant 1 o 2 ?>
<?php echo trekkium_icon_modalidad('', 'Modalidad'); ?>
<?php echo trekkium_icon_region('icon-sm', 'Región'); ?>
<?php echo trekkium_icon_user_avatar('', 'Mi Cuenta'); ?>
```

### 2. **Función Genérica**

```php
<?php echo trekkium_svg('icon-acceso', 'icon-md', 'Acceso'); ?>
<?php echo trekkium_svg('icon-blog1', ['icon-sm', 'my-class'], 'Blog'); ?>
```

### 3. **Shortcode** (Compatibilidad)

```php
<?php echo do_shortcode('[icon id="icon-acceso" class="icon-md" label="Acceso"]'); ?>
<?php echo do_shortcode('[svg_acceso]'); ?> <!-- Antiguo, sigue funcionando -->
```

---

## Clases CSS de Tamaño

| Clase | Tamaño |
|-------|--------|
| `icon-xs` | 16px (tiny) |
| `icon-sm` | 24px (small) |
| `icon-md` | 32px (medium, por defecto) |
| `icon-lg` | 48px (large) |
| `icon-xl` | 64px (extra large) |

**Ejemplo:**
```php
<?php echo trekkium_icon_blog('icon-lg', 'Blog Destacado'); ?>
```

---

## Clases CSS de Color

| Clase | Color |
|-------|-------|
| `icon-primary` | Color primario |
| `icon-secondary` | Color secundario |
| `icon-success` | Verde |
| `icon-danger` | Rojo |
| `icon-warning` | Amarillo/Naranja |
| `icon-info` | Azul |

**Ejemplo:**
```php
<?php echo trekkium_svg('icon-acceso', ['icon-md', 'icon-primary'], 'Acceso'); ?>
```

---

## Lista Completa de Iconos

| Función | ID | Descripción |
|---------|----|----|
| `trekkium_icon_acceso()` | `icon-acceso` | Puerta de acceso/login |
| `trekkium_icon_actividades1()` | `icon-actividades1` | Avión de papel |
| `trekkium_icon_actividades2()` | `icon-actividades2` | Flecha de intercambio |
| `trekkium_icon_actividades3()` | `icon-actividades3` | Círculo sonriente |
| `trekkium_icon_admin_dashboard()` | `icon-admin-dashboard` | Panel administrativo |
| `trekkium_icon_blog()` | `icon-blog1` | Pluma/Pen |
| `trekkium_icon_dificultad()` | `icon-dificultad1` | Cara sonriente |
| `trekkium_icon_duracion()` | `icon-duracion1` o `icon-duracion2` | Reloj con flecha / Reloj |
| `trekkium_icon_estrella()` | `icon-estrella1` | Estrella de valoración |
| `trekkium_icon_fecha()` | `icon-fecha1` | Calendario |
| `trekkium_icon_guias()` | `icon-guias1` o `icon-guias2` | Medalla/Premio |
| `trekkium_icon_modalidad()` | `icon-modalidad1` | Engranaje |
| `trekkium_icon_region()` | `icon-region1` | Globo terráqueo |
| `trekkium_icon_user_avatar()` | `icon-user-avatar` | Avatar de usuario |

---

## Atributos Adicionales

Puedes pasar atributos HTML personalizados:

```php
<?php 
$attrs = [
    'data-toggle' => 'tooltip',
    'data-placement' => 'top'
];
echo trekkium_svg('icon-blog1', 'icon-md', 'Blog', $attrs); 
?>
```

---

## Migración desde Sistema Anterior

### Antes:
```php
<?php echo do_shortcode('[svg_acceso]'); ?>
<?php echo do_shortcode('[svg_blog1]'); ?>
```

### Ahora:
```php
<?php echo trekkium_icon_acceso(); ?>
<?php echo trekkium_icon_blog(); ?>
```

> **Nota:** Los shortcodes antiguos siguen funcionando por compatibilidad, pero se recomienda usar las funciones helper.

---

## Archivo de Sprite

- **Ubicación:** `/assets/svg/icons.svg`
- **Tamaño:** ~8KB (una sola carga)
- **HTTP Requests:** Reducidos de 15+ a 1

## Helpers

- **Ubicación:** `/inc/svg/svg-helpers.php`
- **Cargado por:** `functions.php`
- **Funciones:** 15+ funciones helper + 1 shortcode genérico + compatibilidad

---

## Soporte & Mantenimiento

Para agregar un nuevo icono:

1. Edita `/assets/svg/icons.svg` y agrega un nuevo `<symbol>`
2. Crea una función helper en `/inc/svg/svg-helpers.php`
3. Usa en tus plantillas

**Ejemplo para un nuevo icono "mapa":**

```php
// En svg-helpers.php
function trekkium_icon_map($class = '', $aria_label = 'Mapa') {
    return trekkium_svg('icon-map', $class, $aria_label);
}

// En tus plantillas
<?php echo trekkium_icon_map('icon-lg', 'Mapa de excursiones'); ?>
```

---

## Performance

- **Antes:** 15+ requests SVG individuales
- **Después:** 1 request SVG + referencias internas vía `<use>`
- **Mejora esperada:** +30-40% en tiempo de carga

El sprite se inyecta automáticamente en el footer via hook `wp_footer`.
