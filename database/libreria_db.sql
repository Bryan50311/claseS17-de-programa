-- =====================================================================
-- SCRIPT SQL PARA phpMyAdmin (XAMPP MySQL)
-- Proyecto: Sistema de Inventario de Libros
-- =====================================================================

-- 1. Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS `libreria_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `libreria_db`;

-- 2. Eliminar tablas previas si existen (para evitar conflictos al reinstalar)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `books`;
DROP TABLE IF EXISTS `categories`;
SET FOREIGN_KEY_CHECKS = 1;

-- 3. Crear tabla de Categorías (categories)
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Crear tabla de Libros (books)
CREATE TABLE `books` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `autor` varchar(255) NOT NULL,
  `editorial` varchar(255) NOT NULL,
  `edicion` varchar(255) NOT NULL,
  `fecha_lanzamiento` date NOT NULL,
  `imagen_portada` varchar(255) DEFAULT NULL,
  `categoria_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `books_categoria_id_foreign` (`categoria_id`),
  CONSTRAINT `books_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Insertar categorías previas de manera automática
INSERT INTO `categories` (`id`, `nombre`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Novela / Ficción', 'Obras literarias narrativas en prosa que relatan hechos ficticios o basados en la realidad.', NOW(), NOW()),
(2, 'Fantasía', 'Historias llenas de elementos mágicos, mundos imaginarios y criaturas míticas.', NOW(), NOW()),
(3, 'Ciencia Ficción', 'Narraciones basadas en futuros posibles, avances científicos y viajes espaciales.', NOW(), NOW()),
(4, 'Historia', 'Libros que analizan e investigan acontecimientos históricos del pasado de la humanidad.', NOW(), NOW()),
(5, 'Tecnología & Programación', 'Guías, manuales y teorías sobre el desarrollo de software, informática y nuevas tecnologías.', NOW(), NOW()),
(6, 'Desarrollo Personal', 'Libros de autoayuda, hábitos, crecimiento individual y superación personal.', NOW(), NOW()),
(7, 'Poesía', 'Obras escritas en verso que expresan emociones, belleza y reflexiones artísticas.', NOW(), NOW());
