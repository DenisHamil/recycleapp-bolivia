<div align="center">
  <img src="https://laravel.com/img/logotype.min.svg" alt="Laravel" width="300">
  <h1>♻️ RecycleApp Bolivia</h1>
</div>

<div align="center">

![Build Status](https://img.shields.io/badge/build-passing-brightgreen)  
![PHP](https://img.shields.io/badge/PHP-%3E=8.1-blue)  
![Laravel](https://img.shields.io/badge/Laravel-10.x-red)  
![License](https://img.shields.io/badge/license-MIT-lightgrey)

</div>

---

## 📌 About RecycleApp

**RecycleApp Bolivia** es una plataforma desarrollada en **Laravel 10** que conecta **donadores** y **recolectores** de materiales reciclables.  
Permite gestionar donaciones, historial de recolecciones, recompensas y notificaciones, fomentando la economía circular y el reciclaje responsable.  

Entre sus características principales:  
- ✅ Gestión de usuarios: donadores (familias/organizaciones) y recolectores.  
- ✅ Sistema de recompensas y puntos canjeables.  
- ✅ Notificaciones en tiempo real.  
- ✅ Historial detallado de donaciones y recolecciones.  
- ✅ Integración con mapas (*Leaflet*) para geolocalización.  
- ✅ Dashboard diferenciado para cada rol (donador, recolector, administrador).  

---

## ⚡ Requirements

Para ejecutar el proyecto necesitas:  

- PHP >= 8.1  
- Composer  
- Node.js >= 16 y NPM  
- MySQL >= 5.7  
- Extensiones PHP: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`  

---

## 🚀 Installation

1️⃣ **Clonar el repositorio**  
```bash
git clone https://github.com/DenisHamil/recycleapp-bolivia.git
cd recycleapp-bolivia
```

2️⃣ **Instalar dependencias**
```bash
composer install
npm install && npm run build
```

3️⃣ **Configurar variables de entorno**
```bash
cp .env.example .env
```
Editar el archivo `.env` y colocar tus credenciales:

```env
APP_NAME=RecycleApp
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://tusitio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_db
DB_USERNAME=usuario_db
DB_PASSWORD=contraseña_db

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=recycleapp.bo@gmail.com
MAIL_PASSWORD= ← (aquí colocar la contraseña de la empresa)
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=recycleapp.bo@gmail.com
MAIL_FROM_NAME="RecycleApp Bolivia"
```

Generar la clave de aplicación:
```bash
php artisan key:generate
```

4️⃣ **Migraciones y Seeders**
```bash
php artisan migrate --seed
```

---

## 👤 Crear Administrador

Existen dos formas de crear el primer administrador:

### 🔹 Opción 1: Usando Artisan Tinker (recomendado en Hostinger)
En la consola del servidor ejecuta:

```bash
php artisan tinker
```

Y luego pega esto:

```php
$user = new \App\Models\User();
$user->id = \Illuminate\Support\Str::uuid();
$user->first_name = 'Admin';
$user->last_name = 'Principal';
$user->email = 'admin@recycleapp.com';
$user->password = bcrypt('admin123');
$user->role = 'admin';
$user->status = 'active';
$user->save();
```

👉 Ahora puedes iniciar sesión con:
```
Email: admin@recycleapp.com
Contraseña: admin123
```

### 🔹 Opción 2: Crear un Seeder (si prefieres)
Crea un archivo `database/seeders/AdminSeeder.php` con este contenido:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@recycleapp.com'],
            [
                'id' => (string) Str::uuid(),
                'first_name' => 'Admin',
                'last_name' => 'Principal',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );
    }
}
```

Y lo ejecutas con:
```bash
php artisan db:seed --class=AdminSeeder
```

---

## 📦 Deployment on Hostinger

1. **Conectar tu cuenta Hostinger con el repositorio.**

2. **Ejecutar en el servidor:**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install && npm run build
   ```

3. **⚙️ Configuración de Base de Datos en Hostinger**

   3.1. Crear base de datos en el panel de Hostinger (MySQL).  
   
   3.2. Configurar las credenciales en el archivo `.env`:  
   ```env
   DB_CONNECTION=mysql
   DB_HOST=mysql.hostinger.com
   DB_PORT=3306
   DB_DATABASE=recycleapp
   DB_USERNAME=recycle_user
   DB_PASSWORD=contraseña_segura
   ```

   3.3. Ejecutar las migraciones y seeders en el servidor:
   ```bash
   php artisan migrate --seed
   ```

   3.4. (Opcional) Crear un administrador con **Tinker** si no usaste el seeder:
   ```bash
   php artisan tinker
   ```
   
   Y luego ejecutar:
   ```php
   $user = new \App\Models\User();
   $user->id = \Illuminate\Support\Str::uuid();
   $user->first_name = 'Admin';
   $user->last_name = 'Principal';
   $user->email = 'admin@recycleapp.com';
   $user->password = bcrypt('admin123');
   $user->role = 'admin';
   $user->status = 'active';
   $user->save();
   ```

4. **Crear symlink para storage y optimizar cachés:**
   ```bash
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   ```

5. **Configurar el dominio en Hostinger para apuntar a la carpeta `public/`.**

---

## 🛠 Useful Commands

```bash
# Migrar desde cero y cargar seeders
php artisan migrate:fresh --seed

# Limpiar cachés
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Ingresar al Tinker
php artisan tinker
```

---

## 📚 Technologies

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

</div>

- **Laravel 10**
- **MySQL**
- **Bootstrap 5**
- **Leaflet Maps**
- **Blade Templates**

---

## 📄 License

Este proyecto está licenciado bajo MIT.

<div align="center">
  <strong>Desarrollado con ❤️ en Bolivia 🇧🇴</strong>
</div>
