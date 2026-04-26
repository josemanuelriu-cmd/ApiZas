# Introduction



<aside>
    <strong>Base URL</strong>: <code>http://localhost:8000</code>
</aside>


🎲 API de Gestión de Juegos de Mesa

Bienvenido a la documentación de la API. Esta API permite gestionar juegos, tipos, usuarios, sesiones de juego (**zassessions**) y partidas de juegos de mesa.


🚀 Funcionalidades principales

- Autenticación de usuarios mediante token
- Gestión completa de juegos de mesa (boardgames) y de sus tipos (types)
- Gestión completa de usuarios
- Creación y administración de sesiones de juego
- Gestión de partidas dentro de sesiones
- Unirse y salir de sesiones y partidas

🔐 Autenticación

Esta API utiliza autenticación mediante **Bearer Token**.

Para obtener tu token:

```bash
POST /api/v1/login
Content-Type: application/json

{
"email": "user@example.com",
"password": "password"
}
```

