# Plan de migración Laravel 5.6 → 12.x

Este documento recoge el plan preliminar para migrar la aplicación interna de **Laravel 5.6** a **Laravel 12.x**, garantizando compatibilidad progresiva y minimizando riesgos.

---

## 0. Preparación
- Revisión del entorno local (PHP, Composer, XAMPP).  
- Confirmar compatibilidad mínima de dependencias.  
- Configurar repositorio con ramas según estrategia definida.  
- Establecer checklist de pruebas básicas. 

## Estrategia de ramas
- **main** → Producción estable.
- **upgrade/series-6** → Migraciones menores hasta Laravel 6.x.  
  - `step/5.7` → upgrade 5.6 → 5.7 → COMPLETADO
  - `step/5.8` → upgrade 5.7 → 5.8 → COMPLETADO  
  - `step/6.x` → upgrade 5.8 → 6.x → SIGUIENTE PASO  
- **upgrade/series-8** → Migraciones intermedias hasta Laravel 8.x.  
  - `step/7.x` → upgrade 6.x → 7.x  
  - `step/8.x` → upgrade 7.x → 8.x  
- **upgrade/series-12** → Migraciones finales hasta Laravel 12.x.  
  - `step/9.x` → upgrade 8.x → 9.x  
  - `step/10.x` → upgrade 9.x → 10.x  
  - `step/12.x` → upgrade 10.x → 12.x  

- En **series-6/series-8**: seguir con `laravelcollective/html`.
- En **series-12**: retirar `laravelcollective/html` y migrar:
  - Formularios `Form::` → Blade + requests/Blade components.
  - Si se quiere un builder: evaluar `spatie/laravel-html` (compatible con versiones modernas).

## Flujo de trabajo
1. Crear rama de serie (ej. `upgrade/series-6`).  
2. Dentro, trabajar cada paso en sub-rama (`step/5.7`, `step/5.8`, etc.).  
3. Validar aplicación tras cada paso: `composer update`, revisar rutas críticas, ejecutar tests.  
4. Merge de cada step a la serie y creación de **tags**:  
   - `v5.7-ready`, `v5.8-ready`, `v6-ready` …  
5. Cuando la serie esté estable, merge de la serie a `main`.  
6. Repetir con la siguiente serie.  

## Notas
- **No saltarse pasos**: cada versión debe quedar estable antes de continuar.  
- **Requisitos de PHP**: ajustar versión en cada salto si es necesario.  
- **Documentación**: registrar en `docs/incidencias.md` cualquier cambio manual o fix requerido en cada paso.    

- **Entornos PHP:**  
| Laravel | PHP mínimo | Notas |
|---------|------------|-------|
| 5.6     | 7.1–7.2    | Legacy |
| 6.x LTS | 7.2–7.3    | Primer salto estable |
| 8.x     | 7.4        | Namespaces, factories |
| 12.x    | 8.2–8.3    | Vite, Jetstream/Breeze |  

- **Tests básicos:** configurar PHPUnit (pruebas de humo para login, dashboard y CRUD).  

- **Inventario real:** helpers `str_*`, `array_*`, `Auth::routes()`, `factory()` legacy, assets con Laravel Mix.

---

## 1. Inventario de dependencias

- **AdminLTE 2 (Bootstrap 3 + jQuery)** → mantener como legacy; crear nuevo layout con **Bootstrap 5**.  
- **jQuery** → dependencia fuerte en legacy; evitar en vistas nuevas.  
- **Bootstrap 3.3.7** → migración progresiva a Bootstrap 5.  
- **DataTables** → actualizar a v2 (sin jQuery) en vistas nuevas.  
- **TinyMCE** → mantener, evaluar alternativas ligeras.  
- **laravel-jsvalidation** → verificar compatibilidad; sustituir si es necesario.  

---

## 2. Riesgos detectados

- Cambios en middlewares y kernel.  
- Autenticación: `Auth::routes()` → revisar alternativas (Laravel UI / Breeze / Jetstream).  
- Cambios en rutas (namespaces, firmas de controladores).  
- Ruptura por migración masiva del frontend (Bootstrap/jQuery).  

### Lecciones aprendidas (5.6 → 5.8)
- Configuración de logging y mail modernizada (`logging.php`, `MAIL_DRIVER → MAIL_MAILER`).
- Migración de `awesomeicons.yml` → parse con Symfony Yaml + ajuste en vistas.
- Normalización de `Auth::user()->compruebaSeguridad()` con `use Illuminate\Support\Facades\Auth`.
- Fix de labels en formularios para cumplir accesibilidad.


---

## 3. Estrategia de migración por fases

### Fase A — 5.6 → 6.x
- **Objetivo:** aplicación funcionando igual, sin tocar el frontend.  
- Ajustes: helpers (`Str::`, `Arr::`), Carbon 2, middlewares, mail.  
- Composer actualizado a `"laravel/framework": "^6.20"`.  

### Fase B — 6.x → 8.x
- **Objetivo:** modernización del core.  
- Cambios clave: factories modernas, namespaces en rutas, auth scaffolding.  
- PHP mínimo 7.4.  

### Fase C — 8.x → 12.x
- **Objetivo:** framework moderno con soporte oficial.  
- Cambios clave:  
  - Migración de Mix → Vite.  
  - Flysystem v3.  
  - Auth con Breeze/Jetstream.  
  - Frontend final en Bootstrap 5 + sin jQuery.  

---

## 4. Estrategia de frontend

- Mantener dos layouts en paralelo:  
  - `layouts/app.blade.php` → legacy (AdminLTE 2, Bootstrap 3, jQuery).  
  - `layouts/app-bs5.blade.php` → nuevo (Bootstrap 5, Vite, sin jQuery).  

- Migración por módulos:  
  1. Usuarios  
  2. Proyectos  
  3. Tareas  
  4. Configuración / Admin  

---

## 5. Checklists de control

### Antes de cada salto
- [ ] `composer outdated` revisado.  
- [ ] Tests básicos en verde.  
- [ ] Cache limpiada (`php artisan config:clear`, etc.).  
- [ ] Helpers revisados.  

### Al subir a L8
- [ ] Factories migradas.  
- [ ] Namespaces en rutas revisados.  
- [ ] Auth actualizado.  

### Al subir a L9–12
- [ ] Migración a Vite completada.  
- [ ] Storage revisado (Flysystem v3).  
- [ ] PHP actualizado a 8.2+.  

### Post-migración a Laravel 12
- [ ] Todas las vistas migradas a Bootstrap 5.  
- [ ] Eliminada dependencia de jQuery.  
- [ ] Vite configurado y funcionando en producción.  
- [ ] Tests de regresión completados.  
- [ ] Documentación final entregada. 

---

## 6. Próximos pasos inmediatos

1. Crear rama `upgrade/series-6`.  
2. Subir core a Laravel 6 sin modificar frontend.  
3. Crear layout `app-bs5.blade.php` con Bootstrap 5.  
4. Servir vista de prueba `/health` con el layout moderno.  
5. Documentar incidencias y soluciones en `docs/incidencias.md`.  


 

