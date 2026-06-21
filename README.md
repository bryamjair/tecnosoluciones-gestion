# TecnoSoluciones-Gestor - Sistema de Gestión de Proyectos

[![PHP Version](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Production-brightgreen)]()

Sistema integral de gestion de proyectos, clientes y tareas con auditoria, reportes y control de roles. Desarrollado en PHP MVC sin frameworks.

## Descripcion

**TecnoSoluciones-Gestor** es un sistema de gestion empresarial desarrollado en PHP puro (sin frameworks) que permite administrar:

- **Clientes**: CRUD completo con validaciones de datos
- **Proyectos**: Gestion de proyectos con estados y seguimiento
- **Tareas**: Asignacion, prioridades y fechas limite
- **Reportes**: Generacion de reportes en PDF, Excel e impresion
- **Seguridad**: Autenticacion robusta, CSRF, proteccion XSS/SQL Injection
- **Auditoria**: Registro detallado de todas las actividades del sistema
- **Usuarios**: Gestion de roles (Usuario, Admin, Super Admin)

## Caracteristicas Principales

### Gestion de Proyectos
- CRUD completo de proyectos
- Asignacion a clientes
- Estados: Pendiente, En Progreso, Completado
- Filtros avanzados por estado y busqueda

### Gestion de Tareas
- Asignacion de tareas a usuarios
- Prioridades: Baja, Media, Alta
- Fechas limite con visualizacion de vencimiento
- Seguimiento de estado

### Reportes Profesionales
- Exportacion a PDF con html2pdf.js
- Exportacion a CSV (Excel)
- Impresion directa
- Diseño responsive para todos los reportes

### Seguridad
- Proteccion CSRF
- Sanitizacion de inputs
- Password hashing con password_hash()
- Proteccion contra XSS y SQL Injection
- Session management seguro

### Auditoria
- Registro de todas las acciones
- IP, navegador y timestamp
- Filtros por usuario, fecha y accion
- Acceso solo para roles admin

## Tecnologias Utilizadas

| Tecnologia | Descripcion |
|------------|-------------|
| PHP 7.4+ | Backend puro, sin frameworks |
| MySQL | Base de datos relacional |
| PDO | Conexion segura a base de datos |
| Chart.js | Graficos interactivos en el dashboard |
| html2pdf.js | Generacion de PDFs en el cliente |
| CSS3 | Diseño responsive sin frameworks externos |
| JavaScript Vanilla | Interactividad sin dependencias pesadas |

## Instalacion

### Requisitos Previos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)

### Pasos de Instalacion

1. Clonar el repositorio
```bash
git clone https://github.com/bryamjair/tecnosoluciones-gestion.git
cd tecnosoluciones-gestion
