# Blog MongoDB con Slim Framework (Incompleto)

Este es un proyecto de ejemplo de un blog utilizando MongoDB como base de datos y Slim Framework para la API REST.

## Instalación

1. Clonar el repositorio
2. Desplegar el entorno de desarrollo:
```bash
docker compose up -d
```
3. Instalar dependencias:
```bash
docker compose exec php composer install
```
4. Configurar la conexión a MongoDB en `public/connection.php`

## Estado del proyecto

Este proyecto está intencionalmente incompleto y contiene bloques de código que deben ser implementados. Los archivos que contienen código por completar son:

### `public/connection.php`
- Configuración de la conexión a MongoDB
- Selección de la base de datos 'blog' y la colección 'posts'

### `public/routes/posts.php`
Contiene los siguientes endpoints que necesitan implementación:

1. `POST /api/posts`
   - Validación del cuerpo de la petición
   - Inserción del nuevo post
   - Respuesta con el ID del post creado

2. `GET /api/posts/{id}`
   - Obtención y validación del ID
   - Búsqueda del post en la base de datos
   - Respuesta con los datos del post

3. `GET /api/posts`
   - Obtención de parámetros de consulta
   - Configuración de filtros y paginación
   - Obtención de posts y conteo total

4. `POST /api/posts/{id}/comments`
   - Validación de ID y cuerpo de la petición
   - Añadir comentario al post
   - Verificación y respuesta

5. `GET /api/comments/authors`
   - Obtención de autores de comentarios
   - Respuesta con la lista de autores

Busca las secciones marcadas con `YOUR CODE HERE` en los archivos mencionados y sigue las instrucciones en los comentarios para completar la implementación.
