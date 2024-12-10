# Sistema de Reservaciones de Transporte

Sistema web desarrollado con Laravel para la gestión de reservaciones de transporte terrestre.

## Características

- Gestión de usuarios (admin y clientes)
- Reservaciones de asientos
- Gestión de vehículos y conductores
- Gestión de rutas y horarios
- Procesamiento de pagos con PayPal
- Panel administrativo
- Reportes y estadísticas

## Requisitos

- PHP >= 8.1
- Composer
- Node.js y NPM
- MySQL/SQLite
- Cuenta de PayPal para procesamiento de pagos

## Instalación

1. Clonar el repositorio
```bash
git clone [url-del-repositorio]
cd transport-reservations
```

2. Instalar dependencias de PHP
```bash
composer install
```

3. Instalar dependencias de JavaScript
```bash
npm install
```

4. Configurar el entorno
```bash
cp .env.example .env
php artisan key:generate
```

5. Configurar la base de datos en el archivo .env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=transport_reservations
DB_USERNAME=root
DB_PASSWORD=
```

6. Ejecutar las migraciones
```bash
php artisan migrate
```

7. Compilar assets
```bash
npm run dev
```

## Configuración de PayPal

1. Crear una cuenta de desarrollador en PayPal
2. Obtener las credenciales de API (Sandbox)
3. Configurar las variables de entorno en .env:
```
PAYPAL_MODE=sandbox
PAYPAL_SANDBOX_CLIENT_ID=your_client_id
PAYPAL_SANDBOX_CLIENT_SECRET=your_client_secret
```

## Uso

1. Iniciar el servidor de desarrollo
```bash
php artisan serve
```

2. Acceder a la aplicación en `http://localhost:8000`

## Pruebas

Ejecutar las pruebas con PHPUnit:
```bash
php artisan test
```

## Contribución

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para más detalles.

## Contacto

Eden Mendez - [https://github.com/Edenml264](https://github.com/Edenml264) - info@edenmendez.com

Link del Proyecto: [https://github.com/Edenml264/transport-reservations](https://github.com/Edenml264/transport-reservations)
