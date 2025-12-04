📚 Sistema de Biblioteca Virtual (Laravel 12 + Livewire)

Un sistema de gestión bibliotecaria moderno, rápido y reactivo. Permite a los bibliotecarios gestionar el inventario y préstamos, y a los estudiantes reservar libros desde un catálogo digital.

🚀 Requisitos Previos

Para ejecutar este proyecto necesitas tener instalado el siguiente software. Recomendamos encarecidamente usar Laragon en Windows para evitar problemas con versiones antiguas de PHP.

Laragon: (Full edition recomendada). Asegúrate de que incluya PHP 8.2 o superior.

Composer: Gestor de paquetes de PHP (generalmente viene con Laragon, pero verifica que esté actualizado).

Node.js: Versión LTS (18 o superior) para compilar los estilos.

Git: Para clonar el repositorio.

🛠️ Guía de Instalación Paso a Paso

Sigue estos pasos para tener el sistema corriendo en tu máquina local.

1. Clonar el Proyecto

Abre tu terminal (o la terminal de Laragon Cmder) y ejecuta:

git clone [https://github.com/tu-usuario/biblioteca-virtual.git](https://github.com/tu-usuario/biblioteca-virtual.git)
cd biblioteca-virtual


2. Instalar Dependencias PHP

Descarga todas las librerías del framework y Spatie:

composer install


3. Instalar Dependencias Frontend

Descarga las librerías de diseño (Tailwind, Alpine.js):

npm install


4. Configurar el Entorno (.env)

Duplica el archivo de ejemplo para crear tu configuración:

cp .env.example .env


Genera la clave de encriptación de la aplicación:

php artisan key:generate


5. Configurar la Base de Datos

Abre Laragon y haz clic en Iniciar Todo.

Haz clic en el botón Base de Datos (abrirá HeidiSQL o phpMyAdmin).

Crea una nueva base de datos llamada: biblioteca_virtual (cotejamiento utf8mb4_general_ci).

(Opcional) Si tu usuario de MySQL no es root sin contraseña, edita el archivo .env con tus credenciales.

6. Migrar y Sembrar Datos (Seeders)

Este comando creará las tablas, los roles (Bibliotecario/Estudiante) y los usuarios de prueba:

php artisan migrate --seed


7. Enlace Simbólico de Imágenes

Para que las portadas de los libros sean visibles:

php artisan storage:link


▶️ Ejecutar el Proyecto

Necesitarás dos terminales abiertas simultáneamente:

Terminal 1 (Compilación de estilos en tiempo real):

npm run dev


Terminal 2 (Servidor Laravel):

php artisan serve


Ahora abre tu navegador y entra a: http://127.0.0.1:8000

🔑 Usuarios de Prueba

El sistema viene pre-cargado con estos usuarios para que puedas probar todos los roles.

👨‍💼 Rol: Bibliotecario (Administrador)

Tiene acceso total: Gestión de libros, préstamos, devoluciones y cobro de multas.

Credencial

Valor

Email

admin@biblioteca.com

Contraseña

password

🎓 Rol: Estudiante

Puede ver el catálogo, reservar libros y ver su historial de préstamos.

Credencial

Valor

Email

student@biblioteca.com

Contraseña

password

📂 Estructura de Módulos Clave

Si necesitas editar el código, aquí están los archivos principales:

📚 Libros: app/Livewire/Admin/BookManager.php

🔄 Préstamos: app/Livewire/Admin/LoanManager.php

💰 Multas: app/Livewire/Admin/PenaltyManager.php

🔎 Catálogo: app/Livewire/Student/BookCatalog.php

👤 Mis Libros: app/Livewire/Student/MyLoans.php

🐛 Solución de Problemas Comunes

Error "Vite manifest not found": Olvidaste ejecutar npm run dev o npm run build.

Error de base de datos: Verifica que Laragon (MySQL) esté iniciado y que el nombre en el .env coincida.

Imágenes no cargan: Asegúrate de haber ejecutado php artisan storage:link.

Hecho con ❤️ para la gestión educativa.