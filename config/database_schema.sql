-- Esquema de Base de Datos - Sistema de Repuestos de Vehículos
-- Crear base de datos
CREATE DATABASE IF NOT EXISTS repuestos_vehiculos 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE repuestos_vehiculos;

-- Tabla de usuarios (RF1-RF3)
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'empleado') NOT NULL DEFAULT 'empleado',
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de categorías de repuestos (RF6)
CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    activa BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de repuestos (RF4-RF7)
CREATE TABLE repuestos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    categoria_id INT NOT NULL,
    precio_compra DECIMAL(10,2) NOT NULL,
    precio_venta DECIMAL(10,2) NOT NULL,
    stock_actual INT DEFAULT 0,
    stock_minimo INT DEFAULT 5,
    stock_maximo INT DEFAULT 100,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- Tabla de proveedores (RF11)
CREATE TABLE proveedores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    contacto VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de movimientos de inventario (RF8-RF10)
CREATE TABLE movimientos_inventario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    repuesto_id INT NOT NULL,
    tipo ENUM('entrada', 'salida', 'ajuste') NOT NULL,
    cantidad INT NOT NULL,
    motivo VARCHAR(200),
    proveedor_id INT NULL,
    usuario_id INT NOT NULL,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    observaciones TEXT,
    FOREIGN KEY (repuesto_id) REFERENCES repuestos(id),
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de ventas (RF13-RF15)
CREATE TABLE ventas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_venta VARCHAR(20) UNIQUE NOT NULL,
    cliente_nombre VARCHAR(100),
    cliente_documento VARCHAR(20),
    cliente_telefono VARCHAR(20),
    subtotal DECIMAL(10,2) NOT NULL,
    descuento DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'pendiente',
    usuario_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de detalles de venta
CREATE TABLE venta_detalles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    venta_id INT NOT NULL,
    repuesto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (repuesto_id) REFERENCES repuestos(id)
);

-- Tabla de compras (relacionada con proveedores)
CREATE TABLE compras (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_compra VARCHAR(20) UNIQUE NOT NULL,
    proveedor_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    fecha_compra DATE NOT NULL,
    usuario_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de detalles de compra
CREATE TABLE compra_detalles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    compra_id INT NOT NULL,
    repuesto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (compra_id) REFERENCES compras(id) ON DELETE CASCADE,
    FOREIGN KEY (repuesto_id) REFERENCES repuestos(id)
);

-- Insertar datos iniciales

-- Usuario administrador por defecto
INSERT INTO usuarios (nombre, email, password, rol) VALUES 
('Administrador', 'admin@repuestos.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador');
-- Password: password

-- Categorías iniciales
INSERT INTO categorias (nombre, descripcion) VALUES 
('Frenos', 'Sistema de frenado del vehículo'),
('Motor', 'Componentes del motor'),
('Suspensión', 'Sistema de suspensión'),
('Transmisión', 'Sistema de transmisión'),
('Eléctrico', 'Sistema eléctrico del vehículo'),
('Carrocería', 'Partes de la carrocería'),
('Filtros', 'Filtros de aire, aceite, combustible'),
('Lubricantes', 'Aceites y lubricantes');

-- Índices para mejorar rendimiento
CREATE INDEX idx_repuestos_codigo ON repuestos(codigo);
CREATE INDEX idx_repuestos_categoria ON repuestos(categoria_id);
CREATE INDEX idx_repuestos_activo ON repuestos(activo);
CREATE INDEX idx_movimientos_fecha ON movimientos_inventario(fecha_movimiento);
CREATE INDEX idx_movimientos_repuesto ON movimientos_inventario(repuesto_id);
CREATE INDEX idx_ventas_fecha ON ventas(created_at);
CREATE INDEX idx_ventas_usuario ON ventas(usuario_id);
CREATE INDEX idx_usuarios_email ON usuarios(email);
CREATE INDEX idx_usuarios_activo ON usuarios(activo);
