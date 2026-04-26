# Documentación de API Motbic

Esta documentación detalla los endpoints disponibles, los headers requeridos y ejemplos de respuesta.

**Base URL:** `https://motbic.ahkintech.com/api`

---

## 1. Autenticación

### Login
**Path:** `POST /auth/login/`
**Headers:**
- `Content-Type: application/json`

**Body:**
```json
{
    "username": "admin",
    "password": "admin123"
}
```

**Respuesta (200 OK):**
```json
{
    "refresh": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "access": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

### Refresh Token
**Path:** `POST /auth/refresh/`
**Headers:**
- `Content-Type: application/json`

**Body:**
```json
{
    "refresh": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Respuesta (200 OK):**
```json
{
    "access": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```
---

## 2. Modelos

### Listar Modelos
**Path:** `GET /modelos/`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Respuesta (200 OK):**
```json
{
    "count": 1,
    "results": [
        {
            "id": "550e8400-e29b-41d4-a716-446655440000",
            "nombre": "MT-07",
            "marca": "Yamaha",
            "anio": "2024",
            "tipo_motor": "gasolina",
            "cilindrada": "689cc",
            "precio": "180000.00",
            "colores": ["azul", "negro"],
            "imagen": "https://example.com/mt-07.jpg",
            "created_at": "2025-11-24T20:00:00Z",
            "updated_at": "2025-11-24T20:00:00Z"
        }
    ]
}
```

### Buscar Modelos
**Path:** `GET /modelos/search/?query=yamaha`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Respuesta (200 OK):**
```json
{
    "count": 1,
    "results": [
        {
            "id": "550e8400-e29b-41d4-a716-446655440000",
            "nombre": "MT-07",
            "marca": "Yamaha",
            "anio": "2024",
            "tipo_motor": "gasolina",
            "cilindrada": "689cc",
            "precio": "180000.00",
            "colores": ["azul", "negro"],
            "imagen": "https://example.com/mt-07.jpg",
            "created_at": "2025-11-24T20:00:00Z",
            "updated_at": "2025-11-24T20:00:00Z"
        }
    ]
}
```

### Crear Modelo
**Path:** `POST /modelos/`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Body:**
```json
{
    "nombre": "Ninja 400",
    "marca": "Kawasaki",
    "anio": "2024",
    "tipo_motor": "gasolina",
    "cilindrada": "399cc",
    "precio": "150000.00",
    "colores": ["verde", "negro"],
    "imagen": "https://example.com/ninja.jpg" // Opcional
}
```

**Respuesta (200 OK):**
```json
{
    "id": "uuid-generado",
    "created_at": "2025-11-24T20:00:00Z",
    "updated_at": "2025-11-24T20:00:00Z",
    "nombre": "Ninja 400",
    "marca": "Kawasaki",
    "anio": "2024",
    "tipo_motor": "gasolina",
    "cilindrada": "399cc",
    "precio": "150000.00",
    "colores": ["verde", "negro"],
    "imagen": "https://example.com/ninja.jpg" // Opcional
}
```

### Editar Modelo
**Path:** `PATCH /modelos/<id>/`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Body:**
```json
{
    "nombre": "Ninja 400 SE",
    "marca": "Kawasaki",
    "anio": "2025",
    "tipo_motor": "gasolina",
    "cilindrada": "399cc",
    "precio": "155000.00",
    "colores": ["verde", "negro", "blanco"],
    "imagen": "https://example.com/ninja-se.jpg"
}
```

**Respuesta (200 OK):**
```json
{
    "message": "Modelo actualizado exitosamente"
}
```

### Eliminar Modelo
**Path:** `DELETE /modelos/<id>/`
**Headers:**
- `Authorization: Bearer <access_token>`

**Respuesta (200 OK):**
```json
{
    "message": "Modelo eliminado exitosamente"
}
```
---

## 3. Inventario

### Conteo de inventario
**Path:** `GET /inventario/conteo/`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Respuesta (200 OK):**
```json
{
    "total": 15,
    "disponibles": 10,
    "vendidas": 5,
    "defectuosas": 0
}
```

### Listar Inventario
**Path:** `GET /inventario/`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Respuesta (200 OK):**
```json
{
    "count": 1,
    "results": [
        {
            "id": "uuid-moto-1",
            "modelo_id": "uuid-modelo-1",
            "color": "azul",
            "serie": "SERIE12345",
            "motor": "MOTOR12345",
            "vin": "VIN12345678901234",
            "estado": "disponible",
            "created_at": "2025-11-24T20:00:00Z",
            "updated_at": "2025-11-24T20:00:00Z"
        }
    ]
}
```

### Crear Item de Inventario
**Path:** `POST /inventario/`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Body:**
```json
{
    "modelo_id": "uuid-modelo-1",
    "color": "rojo",
    "serie": "SERIE67890",
    "motor": "MOTOR67890",
    "vin": "VIN09876543210987",
    "estado": "disponible" // Opcional, default: disponible
}
```

**Respuesta (200 OK):**
```json
{
    "id": "uuid-generado",
    "modelo_id": "uuid-modelo-1",
    "color": "rojo",
    "serie": "SERIE67890",
    "motor": "MOTOR67890",
    "vin": "VIN09876543210987",
    "estado": "disponible",
    "created_at": "2025-11-25T12:00:00Z",
    "updated_at": "2025-11-25T12:00:00Z"
}
```

---

## 4. Clientes

### Obtener todos los clientes
**Path:** `GET /clientes/`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Respuesta (200 OK):**
```json
{
    "results": [
        {
            "id": "019afc22-8ad7-7363-ae18-21a05fa9c1aa",
            "nombres": "Pedro",
            "apellidos": "Lopez",
            "telefono": "9829876543",
            "email": "pedro@example.com",
            "rfc": "DATA900101HDF",
            "calle": "Calle 12 #47",
            "colonia": "Dominguez",
            "ciudad": "CDMX",
            "estado": "CDMX",
            "codigo_postal": "06600",
            "ultima_compra": null,
            "estado_servicios": null,
            "created_at": "2025-12-08T04:05:13.000000Z",
            "updated_at": "2025-12-08T04:05:13.000000Z"
        }
    ],
    "count": 1,
    "current_page": 1,
    "total_pages": 1
}
```
### Obtener un cliente
**Path:** `GET /clientes/{id}`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Respuesta (200 OK):**
```json
{
    "results": {
        "id": "019afc3a-53a8-720e-9897-4b2a110d9eeb",
        "nombres": "María",
        "apellidos": "González",
        "telefono": "5543219987",
        "email": "maria.gonzalez@example.com",
        "rfc": "GONM850312ABC",
        "calle": "Av. Reforma 120",
        "colonia": "Juárez",
        "ciudad": "CDMX",
        "estado": "CDMX",
        "codigo_postal": "06600",
        "ultima_compra": null,
        "estado_servicios": null,
        "created_at": "2025-12-08T04:31:12.000000Z",
        "updated_at": "2025-12-08T04:31:12.000000Z"
    }
}
```

### Crear Cliente
**Path:** `POST /clientes/`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Body:**
```json
{
    "nombres": "Maria",
    "apellidos": "Gomez",
    "telefono": "5559876543",
    "email": "maria@example.com",
    "rfc": "GOMA900101HDF",
    "calle": "Calle 10 #45",
    "colonia": "Juarez",
    "ciudad": "CDMX",
    "estado": "CDMX",
    "codigo_postal": "06600"
}
```

**Respuesta (200 OK):**
```json
{
    "id": "uuid-generado",
    "nombres": "Maria",
    "apellidos": "Gomez",
    "telefono": "5559876543",
    "email": "maria@example.com",
    "rfc": "GOMA900101HDF",
    "calle": "Calle 10 #45",
    "colonia": "Juarez",
    "ciudad": "CDMX",
    "estado": "CDMX",
    "codigo_postal": "06600",
    "ultima_compra": null,
    "estado_servicios": "al-dia",
    "created_at": "2025-11-25T12:00:00Z",
    "updated_at": "2025-11-25T12:00:00Z"
}
```

### Editar Cliente
**Path:** `PATCH /clientes/<id>/`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Body:**
```json
{
    "nombres": "Maria",
    "apellidos": "Gomez de Lopez",
    "telefono": "5551112233",
    "email": "maria.lopez@example.com",
    "rfc": "GOMA900101HDF",
    "calle": "Calle 10 #45",
    "colonia": "Juarez",
    "ciudad": "CDMX",
    "estado": "CDMX",
    "codigo_postal": "06600",
    "estado_servicios": "al-dia"
}
```

**Respuesta (200 OK):**
```json
{
    "message": "Cliente actualizado exitosamente"
}
```

### Eliminar Cliente
**Path:** `DELETE /clientes/<id>/`
**Headers:**
- `Authorization: Bearer <access_token>`

**Respuesta (200 OK):**
```json
{
    "message": "Cliente eliminado exitosamente"
}
```

---

## 5. Ventas

### Listar Ventas
**Path:** `GET /ventas/`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Respuesta (200 OK):**
```json
{
    "count": 1,
    "results": [
        {
            "id": "uuid-venta-1",
            "folio": "V-0001",
            "cliente_id": "uuid-cliente-1",
            "inventario_id": "uuid-moto-1",
            "fecha": "2025-11-24T12:00:00Z",
            "metodo_pago": "efectivo",
            "precio_total": "180000.00",
            "estado": "completada",
            "created_at": "2025-11-24T12:00:00Z",
            "updated_at": "2025-11-24T12:00:00Z"
        }
    ]
}
```

### Nueva Venta
**Path:** `POST /ventas/`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Body:**
```json
{
    "cliente_id": "uuid-cliente-1",
    "inventario_id": "uuid-moto-1",
    "fecha": "2025-11-25T14:30:00Z",
    "metodo_pago": "transferencia",
    "precio_total": "185000.00",
    "estado": "completada" // Opcional, default: completada
}
```

**Respuesta (200 OK):**
```json
{
    "id": "uuid-generado",
    "folio": "V-0002",
    "fecha": "2025-11-25T14:30:00Z",
    "metodo_pago": "transferencia",
    "precio_total": "185000.00",
    "estado": "completada",
    "created_at": "2025-11-25T14:30:00Z",
    "updated_at": "2025-11-25T14:30:00Z",
    "motocicleta": "{{marca}} {{modelo}}",
    "cliente": "{{nombres}} {{apellidos}}"
}
```

### Detalle de Venta
**Path:** `GET /ventas/<id>/`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Respuesta (200 OK):**
```json
{
    "id": "uuid-venta-1",
    "folio": "V-0001",
    "fecha": "2025-11-24T12:00:00Z",
    "metodo_pago": "efectivo",
    "precio_total": "180000.00",
    "estado": "completada",
    "created_at": "2025-11-24T12:00:00Z",
    "updated_at": "2025-11-24T12:00:00Z",
    "cliente": {
        "id": "uuid-cliente-1",
        "nombres": "Juan",
        "apellidos": "Pérez",
        "telefono": "5551234567",
        "email": "juan@example.com",
        "rfc": "XAXX010101000"
    },
    "inventario": {
        "id": "uuid-moto-1",
        "color": "azul",
        "serie": "SERIE12345",
        "motor": "MOTOR12345",
        "vin": "VIN12345678901234",
        "estado": "vendida"
    },
    "modelo": {
        "id": "uuid-modelo-1",
        "nombre": "MT-07",
        "marca": "Yamaha",
        "anio": "2024",
        "cilindrada": "689cc",
        "precio": "180000.00"
    }
}
```

---

## 6. Servicios

### Obtener todos los servicios
**Path:** `GET /servicios/`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Respuesta (200 OK):**
```json
{
    "results": [
        {
            "id": "019b20d8-b3e0-7164-a85e-feafcf4ceb32",
            "cliente_id": "019afc22-8ad7-7363-ae18-21a05fa9c1aa",
            "inventario_id": "019b20c6-cfcd-7012-9caa-c5583f6265a1",
            "venta_id": "019b20c7-ac92-738b-8aa3-53c7ccd9afe9",
            "tipo_servicio": "primer-servicio",
            "fecha_programada": "2025-12-15T00:00:00.000000Z",
            "fecha_realizada": null,
            "estado": "programado",
            "notas": "Primer servicio de mantenimiento preventivo",
            "costo": "1500.00",
            "created_at": "2025-12-15T07:10:31.000000Z",
            "updated_at": "2025-12-15T07:10:31.000000Z"
        }
    ],
    "count": 1,
    "current_page": 1,
    "total_pages": 1
}
```
### Obtener un servicio en especifico
**Path:** `GET /servicios/{id}`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Respuesta (200 OK):**
```json
{
    "results": {
        "id": "019b20d8-b3e0-7164-a85e-feafcf4ceb32",
        "cliente_id": "019afc22-8ad7-7363-ae18-21a05fa9c1aa",
        "inventario_id": "019b20c6-cfcd-7012-9caa-c5583f6265a1",
        "venta_id": "019b20c7-ac92-738b-8aa3-53c7ccd9afe9",
        "tipo_servicio": "primer-servicio",
        "fecha_programada": "2025-12-15T00:00:00.000000Z",
        "fecha_realizada": null,
        "estado": "programado",
        "notas": "Primer servicio de mantenimiento preventivo",
        "costo": "1500.00",
        "created_at": "2025-12-15T07:10:31.000000Z",
        "updated_at": "2025-12-15T07:10:31.000000Z"
    }
}
```
### Obtener todos los servicios de un cliente
**Path:** `GET /servicios/cliente_id/{cliente_id}`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Respuesta (200 OK):**
```json
{
    "results": [
        {
            "id": "019b20d8-b3e0-7164-a85e-feafcf4ceb32",
            "cliente_id": "019afc22-8ad7-7363-ae18-21a05fa9c1aa",
            "inventario_id": "019b20c6-cfcd-7012-9caa-c5583f6265a1",
            "venta_id": "019b20c7-ac92-738b-8aa3-53c7ccd9afe9",
            "tipo_servicio": "primer-servicio",
            "fecha_programada": "2025-12-15T00:00:00.000000Z",
            "fecha_realizada": null,
            "estado": "programado",
            "notas": "Primer servicio de mantenimiento preventivo",
            "costo": "1500.00",
            "created_at": "2025-12-15T07:10:31.000000Z",
            "updated_at": "2025-12-15T07:10:31.000000Z"
        }
    ],
    "count": 1,
    "current_page": 1,
    "total_pages": 1
}
```

### Crear Servicio
**Path:** `POST /servicios/`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Body:**
```json
{
    "cliente_id": "uuid-cliente-1",
    "inventario_id": "uuid-moto-1",
    "venta_id": "uuid-venta-1", // Opcional
    "tipo_servicio": "primer-servicio",
    "fecha_programada": "2025-12-15",
    "notas": "Primer servicio de mantenimiento preventivo", // Opcional
    "costo": "1500.00" // Opcional
}
```

**Respuesta (200 OK):**
```json
{
    "cliente_id": "019afc22-8ad7-7363-ae18-21a05fa9c1aa",
    "inventario_id": "019b20c6-cfcd-7012-9caa-c5583f6265a1",
    "venta_id": "019b20c7-ac92-738b-8aa3-53c7ccd9afe9",
    "tipo_servicio": "primer-servicio",
    "fecha_programada": "2025-12-15T00:00:00.000000Z",
    "notas": "Primer servicio de mantenimiento preventivo",
    "costo": "1500.00",
    "id": "019b20d8-b3e0-7164-a85e-feafcf4ceb32",
    "updated_at": "2025-12-15T07:10:31.000000Z",
    "created_at": "2025-12-15T07:10:31.000000Z"
}
```

### Marcar Servicio como Completado
**Path:** `PATCH /servicios/<id>/completar/`
**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer <access_token>`

**Body:**
```json
{
    "fecha_realizada": "2025-12-15",
    "notas": "Servicio completado exitosamente" // Opcional
}
```

**Respuesta (200 OK):**
```json
{
    "message": "Servicio marcado como completado exitosamente"
}
```

