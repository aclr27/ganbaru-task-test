CREATE DATABASE IF NOT EXISTS ganbaru_tasks
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE ganbaru_tasks;

-- Tabla de tareas
CREATE TABLE IF NOT EXISTS tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de categorías
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Relación N:M (tareas <-> categorías)
CREATE TABLE IF NOT EXISTS task_categories (
  task_id INT NOT NULL,
  category_id INT NOT NULL,
  PRIMARY KEY (task_id, category_id),
  CONSTRAINT fk_tc_task
    FOREIGN KEY (task_id) REFERENCES tasks(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_tc_category
    FOREIGN KEY (category_id) REFERENCES categories(id)
    ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Categorías fijas
INSERT IGNORE INTO categories (name) VALUES ('PHP'), ('Javascript'), ('CSS');
