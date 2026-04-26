#!/bin/bash

# Este script automatiza la reparación de MySQL y phpMyAdmin
# Detiene servicios, crea carpetas faltantes, resetea permisos y usuarios.

echo ">>> 1. Deteniendo cualquier proceso de MySQL activo..."
sudo systemctl stop mysql 2>/dev/null
sudo killall -9 mysqld mysqld_safe 2>/dev/null

echo ">>> 2. Asegurando directorios críticos (socket lock)..."
sudo mkdir -p /var/run/mysqld
sudo chown mysql:mysql /var/run/mysqld
sudo chmod 755 /var/run/mysqld

echo ">>> 3. Iniciando MySQL en modo seguro (Sin contraseña)..."
# Iniciamos en background
sudo mysqld_safe --skip-grant-tables --skip-networking &
SAFE_PID=$!

# Esperamos 10 segundos para asegurarnos de que arranque
echo ">>> Esperando 10 segundos para que arranque el motor..."
sleep 10

echo ">>> 4. Ejecutando reparaciones SQL..."
# Inyectamos el SQL directamente
sudo mysql -u root <<EOF
FLUSH PRIVILEGES;

-- 1. Asegurar usuario root
UPDATE mysql.user SET Grant_priv='Y', Super_priv='Y' WHERE User='root';
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_root_password';

-- 2. Asegurar usuario phpmyadmin
DROP USER IF EXISTS 'phpmyadmin'@'localhost';
CREATE USER 'phpmyadmin'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_phpmyadmin_password';
GRANT ALL PRIVILEGES ON *.* TO 'phpmyadmin'@'localhost' WITH GRANT OPTION;

-- 3. Base de datos interna de phpmyadmin
CREATE DATABASE IF NOT EXISTS phpmyadmin;
GRANT ALL PRIVILEGES ON phpmyadmin.* TO 'phpmyadmin'@'localhost';

FLUSH PRIVILEGES;
EOF

echo ">>> 5. Reiniciando servicio en modo normal..."
# Matamos el proceso safe
sudo kill -9 $SAFE_PID 2>/dev/null
sudo killall -9 mysqld mysqld_safe 2>/dev/null
sleep 2

# Iniciamos el servicio normal
sudo systemctl start mysql

echo "--------------------------------------------------------"
echo "✅ PROCESO COMPLETADO."
echo "Prueba entrar ahora a: http://localhost/phpmyadmin"
echo "Usuario: root"
echo "Contraseña: root"
echo "--------------------------------------------------------"
