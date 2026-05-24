# 🚀 SIGAE - Sistema de Gestión Académica Escolar (Backend API)

Este repositorio contiene la API REST desarrollada en **PHP** para el sistema SIGAE. Todos los endpoints manejan respuestas estandarizadas en formato JSON, control estricto de códigos de estado HTTP y soporte completo para caracteres en español (`JSON_UNESCAPED_UNICODE`).

---

## 🛠️ Sección 1: Entrega para Edgar (Diseño de Base de Datos - MongoDB)

Para que el backend funcione con las colecciones reales de MongoDB, se requiere que la base de datos implemente las siguientes estructuras de documentos:

### 1. Colección: `estudiantes`
* **Campos requeridos para Autenticación y Módulo Académico**:
  ```json
  {
    "_id": "ObjectId",
    "ci": "String (Cédula de identidad, usada como credencial)",
    "password_hash": "String (Contraseña encriptada con password_hash de PHP)",
    "nombre": "String (Nombre completo del usuario)",
    "rol": "String ('estudiante' o 'administrador')",
    "auth_token": "String o null (Token de sesión activa)",
    "reset_token": "String o null (Token temporal de recuperación)",
    "reset_token_expira": "String / Datetime (Expiración del token de recuperación)"
  }
  ```

### 2. Colección: `notificaciones` (Sprint 3)
* **Estructura para el almacenamiento de comunicados**:
  ```json
  {
    "_id": "ObjectId",
    "titulo": "String",
    "mensaje": "String",
    "destino": "String ('todos', 'estudiantes' o CI específica)",
    "remitente": "String (Ej: 'Dirección Académica')",
    "fecha_envio": "Datetime / String"
  }
  ```

### 3. Colección: `sugerencias` (Sprint 3)
* **Estructura para el buzón**:
  ```json
  {
    "_id": "ObjectId",
    "tipo": "String ('Sugerencia', 'Queja', 'Reclamo')",
    "contenido": "String",
    "usuario_ci": "String ('Anónimo' o la CI del estudiante)",
    "fecha_registro": "Datetime / String",
    "estatus": "String ('Pendiente Por Revisar')"
  }
  ```

---

## 💻 Sección 2: Entrega para Frontend (Contrato de la API)

Todas las peticiones deben enviarse mediante el método **POST** con el encabezado `Content-Type: application/json`.

### 1. Autenticación (`/auth/login.php`)
* **Request Body**:
  ```json
  {
    "ci": "admin001",
    "password": "Test@1234"
  }
  ```
* **Response (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Inicio de sesión exitoso.",
    "auth_token": "token_dinamico_generado",
    "rol": "administrador"
  }
  ```
* **Errores posibles**: `400 Bad Request` (Campos vacíos), `401 Unauthorized` (Credenciales incorrectas).

### 2. Módulo Académico (`/academico/obtener_notas.php`)
* **Request Body**:
  ```json
  {
    "auth_token": "token_valido_prueba"
  }
  ```
* **Response (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Boletín académico obtenido con éxito.",
    "data": {
      "periodo": "2026-I",
      "estudiante": "Estudiante de Prueba",
      "cedula": "12345678",
      "materias": [
        { "codigo": "MAT-101", "nombre": "Matemáticas I", "nota": 18, "estado": "Aprobado" }
      ],
      "promedio": 16.5
    }
  }
  ```

### 3. Módulo de Comunicación (`/comunicacion/enviar_sugerencia.php`)
* **Request Body** (Sugerencia Anónima: omitir `usuario_ci`):
  ```json
  {
    "tipo": "Sugerencia",
    "contenido": "Habilitar más computadoras en el laboratorio.",
    "usuario_ci": "12345678"
  }
  ```
* **Response (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Su sugerencia ha sido recibida de forma exitosa.",
    "sugerencia": {
      "tipo": "Sugerencia",
      "contenido": "Habilitar más computadoras en el laboratorio.",
      "usuario_ci": "12345678",
      "fecha_registro": "2026-05-24 15:54:24",
      "estatus": "Pendiente Por Revisar"
    }
  }
  ```

---
*
