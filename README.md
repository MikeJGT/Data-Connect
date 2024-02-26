# Data Connect
Este proyecto sirve una api basada en symfony 6.4, PHP 8.2. y Api Platform.

## Installation
Para usar este proyecto solo debe ejecutar el siguiente comando.
```bash
composer install
```

## Usage
He incorporado los archivos .env y .env.local para que no tenga que configurar de nuevo la ruta de las dos bases de datos sqlite y el CORS de la API. 

Para usar la api ejecute el siguiente comando.
```bash
symfony serve
```
Encontrarás la api en localhost:8000/api

Pudes ejecutar los test con:
```bash
php bin/phpUnit
```
