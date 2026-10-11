# Laumin — sistema escolar

Sistema PHP para gestión escolar: **asistencia** de estudiantes y **inventario** de bienes, con login, recuperación de clave y registro.

## Estructura

```
Laumin/
├── index.php            # entrada / panel
├── login/ · registro.php · recuperar_clave/
├── inventario/          # módulo de inventario (admin, formularios, reportes, respaldo/restore)
├── images/ · styless.css
└── asistencia_lau.sql   # esquema de base de datos
```

El módulo de inventario exporta reportes a PDF/Excel (plantillas `ultimate-export` incluidas).

## Puesta en marcha

1. Importar `asistencia_lau.sql` en MySQL/MariaDB.
2. Configurar la conexión en el módulo de login.
3. Servir la carpeta con Apache/PHP.
