# Diario de prácticas



## 22/09/2025
- Inicio de prácticas.
- Configuración de entorno: primero con XAMPP + PHP 8.2; detección de incompatibilidad con Laravel 5.6.
- Instalación de **XAMPP con PHP 7.2** y ajuste de PATH para usar la versión correcta en consola.
- Levantamiento inicial del proyecto (Laravel 5.6) y primeras pruebas en local.
- Comienzo de estabilización (ajustes de `.env`, base de datos, rutas y vistas).

---

## 23/09/2025
- Aplicación **estable en local**; comprobación de rutas clave y vistas principales.
- Arreglo de recursos estáticos (avatars por defecto, assets en `public/`).
- Limpieza de etiquetas `<label for=…>` desparejadas y pequeños ajustes de vistas.
- Identificación de **muchos avisos JS/Blade** en VS Code y en DevTools; planificación de limpieza.

---

## 24/09/2025
- Foco en errores JS en **/eunomia/projects/create** y algunas listas.
- Errores observados:
  - `Uncaught ReferenceError: $ is not defined`
  - `jQuery is not defined`
  - (ruido) *Permissions policy violation: unload…*
- Hipótesis: **orden/duplicación** de scripts (jQuery/Bootstrap/plugins) y `<script>` ejecutándose antes de que cargue jQuery.
- Acciones realizadas:
  - Centralizar carga de jQuery/Bootstrap en el **layout**.
  - Eliminar jQuery duplicado en plantillas heredadas.
  - Mover scripts de vistas a `@section('js')` y revisar con `grep` dónde hay `<script>` sueltos.
  - Añadir comprobaciones en consola (`typeof jQuery`, `typeof $`) y revisar **Network**.
- **Estado**: el problema **persiste** en `/eunomia/projects/create` (work-in-progress).
- Aprendizaje: repaso de **jQuery**, **AdminLTE**, **DataTables**, **TinyMCE** y **AJAX**; criterios para orden correcto de scripts.
- Preparación de documentación (`diario.md`, `incidencias.md`) y decisión de usar **Git local** para versionado.

---

## 25/09/2025
- **Formulario de proyectos**
  - Corrección de duplicación en creación de proyectos.
  - Arreglado manejo del token CSRF.
  - Mejora del flujo AJAX y prevención de envíos múltiples.
  - Commit: `Fix: corregidos errores en formulario de proyectos (CSRF, duplicación, AJAX y protección múltiple envío)`.

- **JavaScript general**
  - Modernización de código (var → const/let).
  - Corrección de errores de sintaxis en formularios.
  - Mejora en manejo de eventos y diálogos.
  - Commit: `refactor: modernización de JS, formularios y dependencias (faker, CSRF, avatares)`.

- **Manejo de avatares**
  - Implementación de imagen por defecto para usuarios.
  - Corrección de rutas de carga de imágenes.
  - Commit incluido en refactor anterior.

- **Dependencias**
  - Sustitución de `fzaninotto/faker` por `fakerphp/faker`.
  - Ajustes en `composer.json`.

---

## 26/09/2025
- Corrección de tipos en `HoraeProjectController.php` para mejorar la compatibilidad con el tipo checking.
- Mejora de accesibilidad en formularios de proyectos y tareas.
- Resolución de conflictos con IDs duplicados en elementos de formulario.
- Optimización de la tabla de fichajes:
  - Implementación de DataTables con child rows
  - Separación de la vista de fichajes en una vista parcial
  - Mejora del manejo de eventos para expandir/colapsar filas

---

## 29/09/2025
- Refactorización de formularios de usuarios, configuración y listados de módulos/roles.
- Mejora de atributos de accesibilidad: corrección de IDs y elementos `autocomplete`.
- Limpieza de recursos: adición de imagen de ayuda (`help.png`) y limpieza de archivos obsoletos.
- Optimización de vistas en `listado_modulos.blade.php` y en `form_edit_configuracion.blade.php`.

---

## 30/09/2025
### **Mañana (antes de migrar)**
- **Holiday Days:** Corregido PartyDayController y HolidayDayController → ahora devuelven JSON en lugar de HTML completo
- **Fechas:** Añadido try/catch para parseo de fechas con Carbon
- **Emails:** Comentado envío de emails problemático (errores en email_vacaciones.blade.php)
- **Cambio de contraseña:** Corregidos nombres de campos (mypassword, password, password_confirmation) y mejorados mensajes de validación
- **Preparación migración:** 
  - Backup de BD con mysqldump
  - Renombrado proyecto a Horae_Raquel_v5.6 para conservar versión estable
  - Creada rama upgrade/laravel-5.8

### **Tarde (durante la migración)**
- **composer.json:** Limpieza y actualización de dependencias
- **Paquetes:** Adaptación de conflictivos (musonza/chat, jeroennoten/adminlte)
- **Config:** Ajustes de config/app.php y .env
- **Verificación:** Comprobación de artisan, autoload y versiones instaladas
- **Resultado:** Sistema restaurado en Laravel 5.8.38

### **Final del día**
- **UI/UX:** Recuperación de colores en labels, counters y estados de tareas/proyectos
- **Validaciones:** Arreglados formularios de usuarios, proyectos y tareas
- **Commit aplicado:** `fix(ui): validaciones de formularios, respuestas AJAX y sistema de labels con colores`

---

## 01/10/2025
### **Corrección de errores menores**
- Ajustada la carga de `awesomeicons.yml` en `MenuAdminController` (`getIndex` y `getEdit`), usando `public_path` y validación de errores de YAML.
- Corregido el parseo de iconos en las vistas `builder.blade.php` y `edit.blade.php`, simplificando el bucle `@foreach` y evitando errores de índices.
- Solucionados avisos de etiquetas `<label>` sin `for`/`id` correspondientes en las vistas del menú de administración.
- Revisada configuración de logging y mail en `.env` y `config/mail.php` para evitar errores 500 por canal de log no definido.
- Confirmado funcionamiento correcto del menú de administración en Laravel 5.8.
- Excluidos de seguimiento en Git archivos generados automáticamente por IDE y `laravel-ide-helper` (`.phpstorm.meta.php`, `_ide_helper.php`, `_ide_helper_models.php`).

### **ToDoList - funcionalidad de edición**
- **Problema principal:** Edición de tareas en ToDoList no funcionaba - los cambios no se guardaban ni reflejaban en la interfaz.
- **Diagnóstico:** Investigación exhaustiva reveló múltiples problemas:
  - Campo ID de tarea no se enviaba (valor `null` en requests)
  - Error 422 en validación por tabla incorrecta (`todo_tasks` vs `todotasks`)
  - JavaScript no se ejecutaba por orden de carga de scripts
  - jQuery no disponible cuando se ejecutaba el código del ToDoList
- **Soluciones aplicadas:**
  - Corregida validación en `HoraeTodoTaskController`: `exists:todotasks,id` en lugar de `exists:todo_tasks,id`
  - Añadido ID específico al campo oculto: `['id' => 'edit_task_id']`
  - Reubicado script JavaScript del ToDoList desde `@push('scripts')` a `@section('js')` donde jQuery ya está disponible
  - Simplificado manejo de ID: asignación directa usando `taskId` del icono clickeado
- **Resultado:** ToDoList completamente funcional - edición, guardado y actualización visual en tiempo real.

### **Aprendizajes técnicos:**
- **Orden de carga de scripts:** Crucial en aplicaciones Laravel con AdminLTE - los scripts deben ejecutarse después de que jQuery esté disponible.
- **Validación de base de datos:** Los nombres de tabla en `exists:` deben coincidir exactamente con el esquema real.
- **AJAX en Laravel:** Importancia de enviar correctamente el ID y validar que todos los campos requeridos estén presentes.
- **Debugging JavaScript:** Uso de alerts temporales y logs del servidor para identificar puntos de fallo específicos.

---

## 02/10/2025
### **ToDoList**
- **Edición funcionando:** campo `id` oculto corregido, validación `exists:todotasks,id`, scripts movidos a `@section('js')` para asegurar carga de jQuery, y actualización del DOM sin recargar página.
- **Eliminados** `@push('scripts')` duplicados que disparaban listeners dobles.

### **Fichajes / Informe mensual**
- **Arreglado error 500:** por parámetros `mes/anio` y `array_inicio_fin` no inicializado.
- **Nuevo render:** una celda por hora (8–18) con colores planos:  
  - blanco (sin fichajes)  
  - verde `#008D4C` (pasado)  
  - cian `#00C0EF` (hora actual)  
- **CSS `public/css/informe_horas_trabajadas_mes.css`:** `table-layout: fixed`, paddings reducidos, fuera `<center>` y estilos inline; añadida `.text-center`.  
- **Ajustes:** 11 horas → 13 columnas totales; fila de totales alineada.  
- **Impresión:** estilos listos, vista apaisada opcional.

**Verificación rápida:**  
`Eunomía → Fichajes → Informe mes → Enviar` → tabla sin desbordes, 11 celdas de 8 a 18, columna **Total** alineada. (Opcional) pulsar **Imprimir** para comprobar layout.

---

## 03/10/2025
### **Fichajes - Depuración múltiples problemas**
- **Problemas identificados:** Tabla de fichajes mostraba horas 8-20 (debería ser 8-18), celdas vacías sin colores, rendimiento muy lento (~2 minutos carga), botones editar con errores, solo mostraba Lunes-Jueves.
- **Algoritmo optimizado:** Reemplazado algoritmo O(n³) ineficiente por lógica directa de tramos entrada-salida, eliminando 3300+ iteraciones innecesarias.
- **UI mejorada:** Centrado de contenido, columna "Día" reducida al 5%, distribución equilibrada de anchos de columnas.
- **Rendimiento:** Eliminado `setInterval` que recargaba tabla cada 60 segundos causando llamadas múltiples.
- **CSS corregido:** Incluido `informe_horas_trabajadas_mes.css` faltante, aumentada altura de barras de tiempo a 20px.
- **Edición fichajes:** Corregida URL hardcodeada usando helper `{{url()}}`.
- **Rango días:** Función `generateDateRange()` corregida para mostrar Lunes-Viernes completo.
- **Estado:** Tabla más rápida y visualmente mejorada, pendiente verificar visualización de colores en celdas.

---

## 06/10/2025
### **Fichajes - Continuación optimización sistema**
- **Algoritmo mejorado:** Refinado sistema de tramos entrada-salida para renderizado más preciso de colores en barras de tiempo.
- **Debugging intensivo:** Implementados logs detallados para rastrear procesamiento de fichajes por día y coloración de minutos.
- **Edición fichajes:** Modal de edición completamente funcional tras corrección de try/catch y manejo de errores.
- **UI optimizada:** Implementado sistema de rowspan para vistas mensuales, bordes consistentes en todas las tablas, y separación clara entre datos de días y fichajes detallados.
- **Colores CSS:** Clases `.is-vacio`, `.is-pasado`, `.is-ahora` aplicadas correctamente para diferenciación visual.
- **Estado:** Sistema de fichajes completamente estable y funcional, preparado para generación de PDF.

---

## 07/10/2025
### **PDF Informe Horas - Optimización layout completo**
- **Objetivo:** Hacer PDF "Informe horas" exactamente igual a visualización en pantalla.
- **Header perfecto:** Logo izquierda (30%), info empresa/trabajador derecha (70%), mes/año debajo del logo con tipografía 12px.
- **Línea separadora:** Implementada línea gris `2px solid #ccc` perfectamente posicionada entre header y tabla.
- **Espaciado fino:** Ajustado margin tabla a 4px para separación ideal de línea gris.
- **Cache busting:** Sistema doble `md5(microtime()) + time()` + JavaScript forzado para evitar múltiples impresiones.
- **CSS @media print:** Reescritura completa con `print-color-adjust: exact` y layout Bootstrap replicado píxel a píxel.

### **Círculos colores - Problema navegador**
- **Intentos múltiples:** Probadas 4 estrategias diferentes (FontAwesome, Unicode, bordes, fondos) para mostrar círculos verdes/rojos.
- **Problema persistente:** Navegador ignora colores en PDF a pesar de `-webkit-print-color-adjust: exact`.
- **Decisión tutor:** Problema dejado para final por indicaciones, priorizar siguiente funcionalidad.
- **Estado:** Layout PDF perfecto, círculos pendientes configuración navegador usuario final.

---

## 08/10/2025
### **Investigación y análisis siguiente funcionalidad**
- **Revisión sistema completo:** Análisis de funcionalidades pendientes tras completar PDF "Informe horas".
- **Identificación problema crítico:** Detectado bug en sistema check in/out donde editar fichajes no actualiza estado de botones.
- **Documentación issue:** Bug reportado donde modificar checkout desde modal no cambia botón de "Check Out" a "Check In".
- **Preparación debugging:** Configuración entorno para investigación técnica del problema AJAX.
- **Estado:** Problema identificado, preparado para resolución día siguiente.

---

## 09/10/2025
### **Sistema Check In/Out - Resolución completa**
- **Problema:** Modal edición no actualizaba botones, secuencias ilógicas permitidas, errores 405
- **Solución:** AJAX con recarga forzada, detección peticiones AJAX, validaciones robustas
- **Validaciones:** Prevención fichajes consecutivos mismo tipo, misma hora, mensajes error específicos
- **Archivos:** `FichajeController.php`, `modifica_hora_fichaje.blade.php`, `dashboard.blade.php`
- **Resultado:** Sistema check in/out completamente operativo con validaciones completas

### **Errores TodoTask y JavaScript**
- **Bug:** TodoTask fechas dd/mm/yyyy causaba errores SQL, "dialog is not defined" en modales
- **Solución:** Reescritura `HoraeTodoTaskController::store()` con parsing Carbon, comunicación cross-window
- **Archivos:** `HoraeTodoTaskController.php`, archivos blade modales usuarios
- **Resultado:** TodoTask y modales JavaScript operativos

---

## 10/10/2025
### **Fix errores "Trying to get property of non-object"**
- **Problema:** Null object access en vistas tras upgrade Laravel
- **Solución:** Eager loading + protección `optional()` en dashboard, projects, tasks, calendario
- **Archivos:** `HoraeProjectController`, `HoraeTaskController`, `HomeController`, vistas blade
- **Resultado:** Acceso seguro a relaciones, dashboard completamente funcional

### **Errores 500 clientes inexistentes**
- **Problema:** Error 500 al añadir proyecto (cliente ID 219) y calendario tareas
- **Solución:** `Customer::find()` + validación + vista error, eager loading calendarios
- **Archivos:** `HoraeProjectController.php`, `HoraeTaskCalendarController.php`, `customer_not_found.blade.php`, `calendar_tasks.blade.php`
- **Resultado:** Error handling gracioso, calendarios funcionales

### **Sistema vacaciones no guardaba**
- **Problema:** Fechas seleccionadas no se persistían, error 500, sin feedback
- **Solución:** Agregado `save()`, eliminado campo `name` inexistente, corregido CSRF, mejorado JS
- **Archivos:** `HolidayDayController.php`, `form_edit_holiday_days.blade.php`
- **Resultado:** Sistema vacaciones completamente funcional

---

## 14/10/2025
- **Errores del cliente 219 en tareas y proyectos**
  - Corrección de errores en formularios de tareas y proyectos para cliente 219.
  - Arreglado import de `JsValidator` → `JsValidatorFacade` en controladores.
  - Agregado eager loading de relaciones en `muestraFormularioProyecto()`.
  - Corregido filtro `role_id` en método `addProjects()` para consistencia.
  - Arreglada relación User-Userdata: `belongsTo` → `hasOne`.
- **Resultado:** Cliente 219 puede crear tareas y proyectos sin errores.
- **Commit:** `Arreglar errores del cliente 219: JsValidator, filtros de proyectos y eager loading en tareas`

---

## 15/10/2025
- **Funcionalidad básica del mailbox**
  - Configuración de cuenta Gmail de prueba (horaetest2025@gmail.com) para testing.
  - Arreglada compatibilidad con paquete webklex/laravel-imap versión antigua.
  - Agregadas funciones auxiliares para métodos IMAP no soportados (`isSeen`, `isFlagged`, etc.).
  - Corregido método `subeAdjuntos` para subida de archivos.
  - Configurado envío de correos por SMTP con Gmail.
  - Implementados contadores básicos de mensajes en carpetas.
  - Agregado marcado automático de mensajes como leídos al abrirlos.
- **Funcionalidades operativas:** Lectura de correos, envío de correos, subida de archivos, contadores básicos.
- **Pendiente para Laravel 12.x:** Contadores dinámicos, flags avanzados (requiere librerías actualizadas).
- **Commit:** `Arreglar funcionalidad básica del mailbox - envío correos, adjuntos y contadores`

- **Sistema de fichajes - Eliminación con errores de integridad**
  - Error `SQLSTATE[23000]` al eliminar fichajes (eliminaba usuarios por error)
  - Implementación del método `destroy` en `FichajeController` con permisos
  - Creación de método AJAX `getFichajesUsuario` para cargar fichajes por usuario
  - Corrección de rutas y vista con JavaScript apropiado para eliminación individual
  - Agregadas instrucciones claras y funcionalidad de edición con modal responsive
  - Validaciones completas de horarios y secuencia lógica entrada/salida

---

## 16/10/2025
- **Sistema de filtros y paginación en fichajes**
  - Implementación de filtros por período (mes actual, último mes, últimos 3 meses, todos)
  - Sistema de paginación AJAX con 20 registros por página
  - Debugging de eventos JavaScript y problemas de scope con función `cargarFichajes`
  - Corrección de errores de sintaxis JS (paréntesis faltantes)
  - Implementación de data attributes para mantener estado en paginación
  - Resolución de problemas con event delegation en elementos dinámicos

- **Compliance legal - Análisis de eliminación de usuarios**
  - Identificación de problema crítico: eliminación física vs baja lógica
  - Análisis de requisitos legales (conservación de fichajes 4+ años)
  - Modificación del método `destroy` en `HoraeUserController` para baja lógica
  - Cambio de eliminación física a marcado `baja=1` para preservar registros
  - Actualización de textos de interfaz ("Eliminar" → "Dar de Baja")

---

## 17/10/2025
- **Sistema de filtros avanzado para usuarios**
  - Implementación de filtros inteligentes (Solo Activos / Solo Inactivos / Todos)
  - Agregado de indicadores visuales para usuarios inactivos (fondo amarillo, etiquetas)
  - Sistema completo de compliance legal con acceso a historial completo
  - Mensajes informativos sobre conservación de registros para inspecciones

- **Funcionalidad de reactivación de usuarios**
  - Implementación de botón "Reactivar" para usuarios dados de baja
  - Creación de ruta y método `reactivar` en `HoraeUserController`
  - Confirmación elegante con BootstrapDialog y feedback visual
  - Validación de permisos (`crear-usuario`) y manejo de errores completo
  - Sistema permite recontrataciones, corrección de errores y trabajadores temporales

- **Mejoras de UX/UI**
  - Estilos CSS para diferenciación visual de estados de usuario
  - Botones de filtro con iconos descriptivos y colores apropiados
  - Help text explicativo sobre compliance legal
  - Recarga automática tras operaciones exitosas
  - Interface profesional lista para producción

---

## 20/10/2025
- Investigación y depuración de por qué los módulos personalizados no aparecían en el menú lateral.
- Revisión de rutas, controladores y vistas para acceso por slug.
- Análisis de la tabla `menu_admin` y comprobación de campos clave (`modulo_id`, `url`, `visible`).
- Pruebas de permisos y roles para módulos personalizados.
- Debug en provider para comprobar lógica de construcción del menú.
- **Optimización de carga de tareas cerradas:** refactorización del método en el controlador para usar `with()` y `paginate(50)`, mejorando el rendimiento de la vista de tareas históricas.
- Mejora de la accesibilidad en formularios de edición de tareas (corrección de `label for` y `id`).
- Documentación de pasos y pruebas realizadas.

---

## 21/10/2025
- Solución definitiva: ajuste de `modulo_id` en `menu_admin` y correspondencia con permisos.
- Creación y asignación correcta del permiso `mostrar` para el módulo personalizado.
- Limpieza de caché y validación de acceso y visualización en menú lateral.
- Verificación de acceso correcto por slug y visualización de la vista de detalle del módulo.
- Documentación final y commit de los cambios.

---

## 22/10/2025 
- Modernización de la vista **Control de accesos** para replicar la UX de **Fichajes**:
  - Implementadas filas expandibles (td.details-control + template) y carga del historial por AJAX desde `AccessControlController::getHistorialUsuario` (filtros por período y paginación).
  - Solucionado conflicto con DataTables Responsive — desactivado el detalle automático (`responsive: { details: false }`) para evitar duplicidad de iconos.
  - Movidos estilos a `@section('css')` y depuración realizada; tras pruebas locales el comportamiento quedó estable y equivalente al de fichajes.
  
---

## 23/10/2025
- Migración técnica a Laravel 6 (branch `upgrade/laravel-6`):
  - Actualizado `composer.json` para requerir `laravel/framework ^6.0` y ejecutado `composer update` con resolución de dependencias.
  - Paquetes clave actualizados (p.ej. `jeroennoten/laravel-adminlte` → v3, `laravelcollective/html` → v6, `monolog` → v2).
  - Problemas detectados al arrancar: incompatibilidades en package-discovery por formato Composer v2 (`installed.json`), renombres de providers y discrepancias de firma en algunas aserciones de PHPUnit.
  - Solución temporal: parches locales para normalizar `installed.json` en `PackageManifest`, ajuste del provider de AdminLTE en `config/app.php` y adaptación puntual en `Illuminate\Foundation\Testing\Assert` para permitir ejecutar pruebas.
  - Estado: migración funcional en entorno local; pendiente limpiar los parches en `vendor/` o pinnear versiones en `composer.json` y ejecutar la suite completa de tests.

---

## 24/10/2025
- Tests y factories:
  - Añadidas factories (`FichajeFactory`, `AccessControlFactory`) y tests Feature básicos: `AuthRoutesTest`, `FichajesAjaxTest`, `AccessControlAjaxTest` (tests escritos usando factories y fakes/mocks cuando fue necesario).
  - Correcciones aplicadas: reemplazo de helpers obsoletos (`str_random()` → `\Illuminate\Support\Str::random()`), y ajuste de rutas de prueba para incluir el prefijo `eunomia`.
  - Ejecución y fallo detectado: `FichajesAjaxTest` devolvía el partial con el mensaje "No hay fichajes para el período seleccionado" porque las factories generaban fechas fuera del periodo `mes_actual` por defecto.
  - Solución aplicada en el test: crear 3 fichajes con `fecha` = now (Carbon::now()) para el usuario de prueba; el test corregido pasó en ejecución local aislada.
  - Estado: tests Feature añadidos y verificados individualmente; siguiente paso ejecutar la batería completa `tests/Feature` y resolver cualquier fallo remanente.

---

## 27/10/2025
- **Suite de tests completa para Laravel 6:**
  - Ampliación de tests Feature a 31 tests en total (48 assertions).
  - Configuración de SQLite en memoria (`phpunit.xml`) para independencia de MySQL en tests.
  - Creación de tests adicionales: `RouteAccessTest` (7 tests), `MiddlewareTest` (2 tests).

---

## 28/10/2025
- **Suite de tests (continuación):**
  - Creación de tests: `ApplicationConfigTest` (7 tests), `PermissionsTest` (3 tests), `ModelsExistTest` (7 tests).
  - Tests simplificados usando mocks de autenticación sin dependencias de base de datos real.
  - Factories creadas (`FichajeFactory`, `AccessControlFactory`) disponibles para uso futuro.

---

## 29/10/2025
- **Finalización y documentación de tests:**
  - Verificación completa de la suite de tests: 31 tests ejecutándose correctamente.
  - Actualización de documentación de proyecto con entradas pendientes de días anteriores.
  - **Resultado:** Sistema de testing robusto establecido, cobertura básica completa en 2.84 segundos.

---

## 30/10/2025
- **Detección de helpers obsoletos post-migración:**
  - Identificados 7 usos de funciones helper eliminadas en Laravel 6 mediante búsqueda sistemática.
  - Análisis de impacto: helpers `starts_with`, `str_limit` y `str_singular` presentes en vistas de proyectos y filemanager.
  - Planificación de corrección mediante uso de clase `Str` de Illuminate.

---

## 31/10/2025
- **Corrección completa de helpers obsoletos:**
  - Reemplazo sistemático de helpers por métodos estáticos de `\Illuminate\Support\Str` en 6 archivos.
  - Corrección aplicada en vistas de proyectos y módulo filemanager.
  - Limpieza de cachés (vistas, configuración, rutas) para aplicar cambios.
  - Verificación final: aplicación completamente funcional en Laravel 6 sin errores.

---

## 03/11/2025
- **Correcciones funcionales en filtros de usuarios, clientes y proyectos:**
  - Eliminados los filtros role_id restrictivos para mostrar todos los trabajadores activos en los selectores de tareas y proyectos.
  - Revisión de addProjects() y muestraFormularioProyecto() para coherencia de datos en relaciones User, Project y Task.
  - Mejora de consistencia general en listados y formularios relacionados.
  - **Resultado:** Listados y selectores de usuarios, clientes y proyectos accesibles a todos los roles autorizados.

---

## 04/11/2025
- **Visibilidad de tareas por rol de usuario:**
  - mplementada lógica de visibilidad condicional: los trabajadores solo ven sus tareas asignadas, los administradores ven todas.
  - Revisión del middleware de permisos y consultas Task::with() para aplicar el filtro user_id según rol.
  - **Resultado:** Mayor seguridad y control de acceso en el módulo de tareas.

---

## 05/11/2025
- **Validaciones y orden de carga JS:**
  - Corrección de validaciones AJAX en creación de clientes: comprobación de unicidad de código y email.
  - Reorganización del orden de carga de jQuery y dependencias en vistas para evitar errores $.validator no definido.
  - Refactor de formularios con JsValidator para garantizar comportamiento consistente en todos los módulos.
  - **Resultado:** Validaciones funcionales, sin conflictos de scripts ni errores de dependencias.

---

## 06/11/2025
- **Migración técnica a Laravel 7.x y ajustes de seguridad:**
  - Eliminado paquete obsoleto musonza/chat y su provider/facade.
  - Arreglo del Handler.php y actualización de config/imap.php según estructura de Laravel 7.
  - Instalado laravel/ui para restablecer Auth::routes() y componentes de autenticación.
  - Limpieza de dependencias y configuración de providers actualizados.
  - **Resultado:** Proyecto estable en Laravel 7.30.7 con autenticación funcional y estructura limpia.

---

## 07/11/2025
- **Modernización completa de interfaz y migración a AdminLTE 3.14.3:**
  - Actualizadas vistas de clientes, usuarios, tareas, proyectos, fichajes, festivos y vacaciones a AdminLTE 3.
  - Sustitución de modales por Bootstrap 5, incorporación de Select2 Bootstrap 4 y DataTables responsive.
  - Añadido padding y bordes uniformes al calendario del dashboard y corrección de CSS personalizado.
  - Reparado bug array_push en LayoutHelper.
  - **Resultado:** Aplicación visualmente coherente, responsive, moderna y totalmente operativa tras la migración.

  ---

  ## 10/11/2025
- **Modernización de vistas de permisos y módulos:**
  - Actualización del diseño de las pantallas de permisos, módulos y sus listados.
  - Unificación de estilos visuales y estandarización de iconografía y botones.
  - Validaciones traducidas completamente al español.
  - Implementación de modal de confirmación de borrado con Bootstrap 4.
  - **Resultado:** Secciones de permisos y módulos alineadas con el estilo de Proyectos, más intuitivas y consistentes.

---

## 11/11/2025
- **Unificación de vistas de días festivos:**
  - Refactor y estandarización de la vista de edición de días festivos.
  - Corrección y homogeneización de redirecciones tras crear/editar/eliminar.
  - Ajuste de formularios y layout siguiendo la línea común del resto de módulos.
  - **Resultado:** Gestión de festivos unificada, coherente y con navegación corregida.

---

## 12/11/2025
- **Modernización transversal de clientes, proyectos, tareas, usuarios, días festivos y menús:**
  - Actualizados listados, formularios y acciones a la nueva línea visual.
  - Unificación del uso de cards, espaciados, tipografías y botones en todos los módulos principales.
  - Ajustes de usabilidad y estandarización de mensajes de acción.
  - **Resultado:** Experiencia fluida en todas las áreas clave del sistema.

---

## 13/11/2025
- **Unificación de vistas de roles, permisos y accesos:**
  - Modernización y adaptación visual de las vistas de roles: alta, edición, listado y matriz.
  - Rediseño de vistas de control de accesos para igualarlas con Proyectos.
  - Corrección de integración con DataTables y scripts JS asociados.
  - Eliminación de migas de pan antiguas y modernización del encabezado en cambio de contraseña.
  - **Resultado:** Sistema de roles/accesos mucho más claro, ordenado y con interacción más cómoda.

---

## 14/11/2025
- **Migración completa a Laravel 8:**
  - Actualización de dependencias del framework y librerías satélite.
  - Eliminación total de laravelcollective/html y sustitución de helpers.
  - Corrección de rutas de assets locales (DataTables, Select2, AdminLTE).
  - Ajustes menores tras la migración para evitar incompatibilidades.
  - Inicio de modernización de vistas de Usuarios y Clientes para igualarlas al módulo de Proyectos.
  - **Resultado:** Proyecto actualizado a Laravel 8, más ligero, moderno y sin dependencias obsoletas.

---

## 17/11/2025
- **Restauración de columna de acciones y limpieza de depuración:**
  - Recuperada la columna de acciones al final de la tabla de tareas para mantener coherencia con el resto del sistema.
  - Eliminados mensajes de debug que aparecían en vistas y podían interferir en la experiencia del usuario.
  - Ajustados estilos y alineación de botones con Bootstrap 4.
  - **Resultado:** Listado de tareas limpio, uniforme y sin elementos residuales de depuración.

---

## 18/11/2025
- **Unificación de vistas y paginación en tablas y festivos:**
  - Homogeneizados estilos de tablas, paginación y layout en módulos que aún usaban plantillas antiguas.
  - Adaptadas las vistas de festivos al estándar visual del resto de secciones.
  - Verificadas las plantillas compartidas y corregidas inconsistencias menores.
  - Resultado: Tablas y secciones de festivos totalmente integradas con el nuevo diseño.

---

## 19/11/2025
- **Corrección de rutas de idioma de DataTables y limpieza de assets:**
  - Revisados y corregidos los paths de traducción de DataTables tras la migración a Laravel 8.
  - Eliminados archivos CSS inexistentes que provocaban errores 404 en consola y en servidor.
  - Limpieza de assets duplicados o no utilizados.
  - **Resultado:** Carga correcta de DataTables sin advertencias ni recursos faltantes.

---

## 20/11/2025
- **Actualización de modales a Bootstrap 5:**
  - Migrados atributos antiguos (data-dismiss) a los nuevos (data-bs-dismiss).
  - Revisado el funcionamiento de modales en formularios, listados y confirmaciones.
  - Ajustados estilos de impresión en informes de horas.
  - **Resultado:** Modales plenamente compatibles con Bootstrap 5 y estilos optimizados.

---

## 21/11/2025
- **Estabilización de la migración a Laravel 8:**
  - Revisión de helpers retirados y sustitución por alternativas nativas.
  - Ajuste de rutas de recursos, imports y assets heredados.
  - Pruebas generales de vistas críticas tras varios cambios visuales y estructurales.
  - **Resultado:** Base del proyecto más estable, limpia y sin errores derivados de la migración.

---

## 24/11/2025
- **Preparación de modals y vistas de tareas para modernización:**
  - Revisión de componentes que aún dependían de Bootstrap 4.
  - Identificación de incompatibilidades entre atributos antiguos y BS5.
  - Ajuste de estructura HTML para permitir migración limpia.
  - **Resultado:** Vistas preparadas para su actualización completa a Bootstrap 5.

---

## 25/11/2025
- **Modernización completa de modals del to-do list:**
  - Actualización de todos los modals a Bootstrap 5, revisando atributos y eventos.
  - Corrección del botón de cancelar y mejora de cierre de ventana.
  - Limpieza de estilos duplicados y alineación visual.
  - **Resultado:** Modals del to-do list totalmente funcionales y coherentes con el resto de la interfaz.

---

## 26/11/2025
- **Corrección del error “Attempt to read property id on string”:**
  - Revisión del formulario de comentarios de tareas.
  - Detección del origen del error por datos recibidos en formato incorrecto.
  - Ajuste del controlador y del modelo para asegurar valores tipados.
  - **Resultado:** Formulario estable, sin errores y capturando correctamente los datos.

---

## 27/11/2025
- **Modernización de la vista de edición de tareas con Bootstrap 5:**
  - Eliminación de breadcrumbs antiguos y restructuración del encabezado.
  - Actualización de input-groups, Select2 con tema BS5 y colores del módulo.
  - Revisión de compatibilidad en formularios y scripts.
  - **Resultado:** Pantalla de edición moderna, clara y completamente alineada con la nueva línea visual.

---

## 28/11/2025
- **Despliegue a producción de la aplicación:**
  - Comprobación de migraciones, rutas, assets compilados y cachés.
  - Revisión final de logs y pruebas rápidas del flujo principal.
  - Ajustes menores tras el deploy para asegurar estabilidad.
  - **Resultado:** Nueva versión del sistema desplegada correctamente y funcionando en producción.