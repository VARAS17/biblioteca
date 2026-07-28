<<<<<<< HEAD
# web_encuestas
=======
<p align="center">
    <a href="https://laravel.com" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
    </a>
</p>

# Proyecto Laravel con Livewire

Este proyecto está desarrollado en **Laravel** y utiliza **Livewire** para la construcción de interfaces dinámicas.

---

## 🚀 Requisitos previos

Asegúrate de tener instalados:

- [PHP ^8.4.20](https://www.php.net/downloads)
- [Composer](https://getcomposer.org/)
- [Node.js y NPM](https://nodejs.org/)
- [MySQL ](https://www.mysql.com/) 

---

## ⚙️ Instalación

**1. Clona el repositorio:**
   ```bash
   git clone https://github.com/tu-usuario/tu-proyecto.git
   cd tu-proyecto
```

2. Instala las dependencias de PHP:

   ```bash
   composer install
   ```

3. Instala las dependencias de Node:

   ```bash
   npm install
   ```

4. Copia el archivo de entorno y configura tu base de datos:

   ```bash
   cp .env.example .env
   ```

   Edita el archivo `.env` con tus credenciales (DB, mail, etc.).

5. Genera la clave de la aplicación:

   ```bash
   php artisan key:generate
   ```

6. Ejecuta las migraciones:

   ```bash
   php artisan migrate
   ```
7. Ejecuta los seeders
   ```bash
   php artisan db:seed
   ```
8. habilitar el storage
   ```bash
   php artisan storage:link
   ```
---

## ▶️ Ejecución del proyecto

En dos terminales diferentes:

* Compilar los assets:

  ```bash
  npm run dev
  ```

* Levantar el servidor de Laravel:

  ```bash
  php artisan serve
  ```

Luego abre en tu navegador:

👉 [http://localhost:8000](http://localhost:8000)

---

## 📚 Tecnologías usadas

* **Laravel** – Framework backend
* **Livewire** – Interfaces dinámicas sin JavaScript complejo
* **TailwindCSS / Vite** – Estilos y compilación de assets

---

## 📄 Licencia

Este proyecto está bajo la licencia [MIT](https://opensource.org/licenses/MIT).

```
>>>>>>> varas
