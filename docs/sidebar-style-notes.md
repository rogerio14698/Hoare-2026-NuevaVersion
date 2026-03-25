Objetivo:
Cómo cambiar el estilo del sidebar (navegador izquierdo) de AdminLTE en este proyecto.

Archivos relevantes:
- Partial del sidebar: resources/views/vendor/adminlte/partials/left-sidebar.blade.php
- Layout que lo incluye: resources/views/vendor/adminlte/page.blade.php
- Master template: resources/views/vendor/adminlte/master.blade.php (incluye eunomia.css)
- CSS global personalizado: public/css/eunomia.css y public/css/nuevoCSS/* (recomendado añadir reglas aquí)
- Configuración menú: config/adminlte.php (o donde se genere $adminlte->menu('sidebar'))

Cómo aplicar estilos (recomendado):
1) Añadir reglas CSS en tu hoja de estilos personalizada (por ejemplo public/css/nuevoCSS/sidebar.css o en public/css/eunomia.css).
2) Asegúrate de que la hoja se carga después de AdminLTE (master.blade.php ya carga eunomia.css al final).
3) Si el navegador caching lo impide, recarga con ?v=filemtime(...) (ya aplicado a eunomia.css) o limpia cache del navegador.

Selectores útiles y ejemplos (cópialos a tu CSS personalizado):

/* Fondo y color del sidebar */
.main-sidebar {
  background-color: #0b2a3a !important; /* color de fondo */
  color: #e6f0f2 !important;            /* color por defecto del texto */
}

/* Brand / logo */
.main-sidebar .brand-link {
  background-color: #072331 !important;
  color: #fff !important;
}

/* Items del menú */
.main-sidebar .nav-sidebar .nav-link {
  color: #c9d6df !important;
}
.main-sidebar .nav-sidebar .nav-link .nav-icon {
  color: inherit !important;
}

/* Item activo */
.main-sidebar .nav-sidebar .nav-link.active {
  background-color: #0073b7 !important;
  color: #fff !important;
}

/* Hover */
.main-sidebar .nav-sidebar .nav-link:hover {
  background-color: rgba(255,255,255,0.03) !important;
}

/* Submenú (treeview) */
.main-sidebar .nav-treeview {
  background: transparent !important;
}

/* Usuario (user-panel) */
.user-panel .info { color: #dfeff6 !important; }
.user-panel .image img { width: 2.25rem; height: 2.25rem; }

Control de ancho / colapso:
- AdminLTE controla colapso vía JS. Para forzar estilos cuando está colapsado:
  .sidebar-collapse .main-sidebar { width: 80px; }
  .sidebar-collapse .main-sidebar .brand-link .brand-text { display: none; }

Problemas comunes y soluciones:
- Tus reglas no aplican: aumenta especificidad o usa !important con moderación.
- El contenido interno fuerza la anchura: añade `min-width: 0;` a los hijos del grid (ya aplicado en dashboard CSS) y usa `overflow: hidden` o `table-layout: fixed` para tablas.
- Cambios no se ven por cache: asegúrate de limpiar `php artisan view:clear` (plantillas) y cache del navegador o usar querystring con `filemtime()`.

Uso de variables (si usas `nuevoCSS/colores.css`):
- Define variables en `colores.css` y referencia en el CSS del sidebar:
  :root { --sidebar-bg: #072331; --sidebar-text: #c9d6df; }
  .main-sidebar { background-color: var(--sidebar-bg) !important; color: var(--sidebar-text) !important; }

Edición rápida (pasos prácticos):
1. Abrir `public/css/eunomia.css` o `public/css/nuevoCSS/sidebar.css` y pegar los bloques de ejemplo.
2. Subir archivo si trabajas con build tools; de lo contrario asegúrate que el archivo está en `public/css` y se está incluyendo.
3. Limpiar caches: `php artisan view:clear && php artisan cache:clear` y recargar el navegador con Ctrl+F5.

Notas finales:
- Si quieres cambiar etiquetas/estructura del menú (HTML), edita `left-sidebar.blade.php` o los templates `adminlte::partials.sidebar.menu-item`.
- Para cambios permanentes y controlables por entorno, configura colores en `nuevoCSS/colores.css` y reutiliza variables.

Si quieres, aplico un ejemplo concreto (colores y tamaños) directamente en `public/css/nuevoCSS/sidebar.css` y lo incluyo en `eunomia.css` o te lo dejo solo en notas para que lo revises.
