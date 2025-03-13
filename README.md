<<<<<<< HEAD
📌 README.md para GitHub

# 📋 Sistema de Asistencia - La Metalera

### 🏢 Aplicación web para la gestión de asistencia de trabajadores

![Sistema de Asistencia](https://via.placeholder.com/1200x400?text=Banner+del+Sistema+de+Asistencia)

---

## 🚀 **Descripción del Proyecto**
El **Sistema de Asistencia** de **La Metalera** es una aplicación web diseñada para gestionar la asistencia de los trabajadores de una empresa de reciclaje. Permite registrar ingresos y salidas, generar reportes en **PDF**, calcular pagos y administrar trabajadores de manera eficiente.

La aplicación cuenta con **dos tipos de usuarios**:
- **Administrador**: Puede gestionar trabajadores, revisar reportes y administrar asistencias.
- **Trabajador**: Puede marcar su ingreso y salida, ver su historial de asistencias y consultar sus pagos acumulados.

---

## 🛠 **Tecnologías Utilizadas**
- **Backend**: PHP 8, MySQL, PDO
- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript (jQuery)
- **Generación de Reportes**: Dompdf
- **Servidor Local**: XAMPP
- **Control de Versiones**: Git & GitHub

---

## ⚙️ **Instalación y Configuración**
### 📥 **1. Clonar el repositorio**
```bash
git clone https://github.com/tu-usuario/sistema-asistencia.git
cd sistema-asistencia
🔧 2. Configurar la Base de Datos
Importa el archivo database.sql en MySQL para crear las tablas necesarias.
Configura la conexión en el archivo config/database.php:

$host = 'localhost';
$dbname = 'sistema_asistencia';
$username = 'root';
$password = '';
▶️ 3. Iniciar el Servidor Local
Si usas XAMPP, mueve el proyecto a htdocs y accede a:

http://localhost/sistema-asistencia/public/index.php
📌 Características Principales
✅ Login seguro con autenticación por rol (Administrador/Trabajador).
✅ Registro de asistencia con un solo clic (Ingreso y salida).
✅ Historial de asistencias con detalles de fecha, horas trabajadas y pago acumulado.
✅ Generación de reportes en PDF con información detallada de asistencia.
✅ Filtros avanzados por fecha, empleado y días de la semana.
✅ Panel de administración para gestionar trabajadores y asistencias.
✅ Interfaz responsiva y moderna con Bootstrap 5.

📄 Estructura del Proyecto php

sistema-asistencia/
│── app/
│   ├── controllers/         # Controladores de la aplicación (Auth, Usuario, Reportes)
│   ├── models/              # Modelos para la base de datos (Usuarios, Asistencias)
│   ├── views/               # Vistas del sistema (Administrador y Trabajador)
│   ├── libs/                # Librerías externas (Dompdf)
│   ├── config/              # Configuración del sistema (Base de datos)
│── public/
│   ├── css/                 # Archivos de estilos personalizados
│   ├── img/                 # Logotipos y recursos gráficos
│   ├── js/                  # Scripts de la aplicación
│   ├── index.php            # Punto de entrada principal
│── database.sql              # Script para la base de datos
│── README.md                 # Documentación del proyecto
📝 Contribuciones
¡Las contribuciones son bienvenidas! Si deseas mejorar este proyecto:

Fork este repositorio.
Crea una rama con tu mejora:

git checkout -b feature/nueva-funcionalidad

Realiza cambios y haz un commit:
git commit -m "Añadida nueva funcionalidad"

Sube tus cambios:
git push origin feature/nueva-funcionalidad
Abre un Pull Request en GitHub.

📞 Contacto
👤 Desarrollador: Mauricio Correa
📧 Email: maucorreadev@gmail.com 


Si encuentras algún error o tienes sugerencias, ¡no dudes en abrir un Issue en el repositorio! 🚀

© 2025 La Metalera - Todos los derechos reservados.

=======

