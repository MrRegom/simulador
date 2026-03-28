<?php
namespace App\Config;

class Config {
    // Configuración de la Base de Datos
    const DB_HOST = 'localhost';
    
    public static function getDBName() {
        $isLocal = !isset($_SERVER['HTTP_HOST']) || strpos($_SERVER['HTTP_HOST'], 'localhost') !== false;
        return $isLocal ? 'servirec' : 'arsimrac_simuladores';
    }

    public static function getDBUser() {
        $isLocal = !isset($_SERVER['HTTP_HOST']) || strpos($_SERVER['HTTP_HOST'], 'localhost') !== false;
        return $isLocal ? 'root' : 'arsimrac_usersimuladores';
    }

    public static function getDBPass() {
        $isLocal = !isset($_SERVER['HTTP_HOST']) || strpos($_SERVER['HTTP_HOST'], 'localhost') !== false;
        return $isLocal ? '' : 'Servirec_2025..';
    }

    // Configuración de la Aplicación
    public static function getAppUrl() {
        $isLocal = !isset($_SERVER['HTTP_HOST']) || strpos($_SERVER['HTTP_HOST'], 'localhost') !== false;
        return $isLocal ? 'http://localhost/servirec' : 'https://www.a4rsimracing.cl/a4r';
    }

    const APP_NAME = 'ServiRec A4R';
    const VERSION = '1.2.0';

    // Configuración de Sesiones
    const SESSION_NAME = 'servirec_session';
}
