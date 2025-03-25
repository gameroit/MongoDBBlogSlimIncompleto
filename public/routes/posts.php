<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use MongoDB\BSON\ObjectId;

// Endpoint para crear un nuevo post
$app->post($prefix . '/posts', function (RequestInterface $request, ResponseInterface $response) use ($posts) {

    // Validate the request body
    /**********
     * YOUR CODE HERE
     * Check if the body, permalink, author, title and tags are set in the request body
     * If not, return a 400 response with a message 'Invalid input'
     * **********/

    // Insert the new post into the database
    /**********
     * YOUR CODE HERE
     * Insert the new post into the database
     * Data is obtained from the request body
     * **********/

    // Return the response with the ID of the new post
    /**********
     * YOUR CODE HERE
     * Return the response with the ID of the new post
     * An status of 201 should be set
     * The response should have a message 'Post created'
     * **********/
});

// Endpoint para obtener un post por ID
$app->get($prefix . '/posts/{id}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($posts) {

    // Get the ID from the URL
    /**********
     * YOUR CODE HERE
     * Get the ID from the URL
     * **********/

    // Validate the ID
    /**********
     * YOUR CODE HERE
     * Validate the ID
     * If the ID is invalid, return a 400 response with a message 'Invalid product ID'
     * **********/

    // Find the post in the database
    /**********
     * YOUR CODE HERE
     * Find the post in the database
     * If the post is not found, return a 404 response with a message 'Post not found'
     * **********/

    // Return the response with the post
    /**********
     * YOUR CODE HERE
     * Return the response with the post
     * The response should have a status of 200
     * Data is returned in a field named 'data'
     * **********/
});

// Endpoint para obtener posts por etiqueta, autor de post o autor de comentario
// Se usa un parámetro adicional 'skip' para paginación. De forma predeterminada, se establece en 0
// Los posts se devuelven en orden descendente por ID
$app->get($prefix . '/posts', function (RequestInterface $request, ResponseInterface $response) use ($posts) {
    // Get the query parameters
    /**********
     * YOUR CODE HERE
     * Get the query parameters from the request
     * **********/

    // Set the filter and skip values
    /**********
     * YOUR CODE HERE
     * Set the filter and skip values
     * The filter should be an empty array
     * The skip value should be 0 if not set in the query parameters
     * **********/

    // Get the posts from the database. A limit of 5 is set
    /**********
     * YOUR CODE HERE
     * Get the posts from the database with the filter and skip values
     * The posts should be sorted in descending order by ID
     * **********/

    // Get total number of posts
    /**********
     * YOUR CODE HERE
     * Get the total number of posts
     * **********/

    // Return the response with the posts
    /**********
     * YOUR CODE HERE
     * Return the response with the posts
     * The response should have a status of 200
     * Data is returned in a field named 'data'
     * The total number of posts is returned in a field named 'total'
     * **********/
});

// Endpoint para añadir un comentario a un post
$app->post($prefix . '/posts/{id}/comments', function (RequestInterface $request, ResponseInterface $response, array $args) use ($posts) {
    // Get the ID from the URL and the request body
    /**********
     * YOUR CODE HERE
     * Get the ID from the URL
     * Get the request body
     * **********/

    // Validate the ID
    /**********
     * YOUR CODE HERE
     * Validate the ID
     * If the ID is invalid, return a 400 response with a message 'Invalid product ID'
     * **********/

    // Validate the request body
    /**********
     * YOUR CODE HERE
     * Check if the body, email and author are set in the request body
     * If not, return a 400 response with a message 'Invalid input'
     * **********/

    // Find the post in the database
    /**********
     * YOUR CODE HERE
     * Find the post in the database
     * If the post is not found, return a 404 response with a message 'Post not found'
     * **********/

    // Add the comment to the post
    /**********
     * YOUR CODE HERE
     * Add the comment to the post
     * **********/

    // Check if the comment was added
    /**********
     * YOUR CODE HERE
     * Check if the comment was added
     * If not, return a 500 response with a message 'Failed to add comment'
     * **********/

    // Return the response
    /**********
     * YOUR CODE HERE
     * Return the response
     * The response should have a status of 201
     * The response should have a message 'Comment added'
     * **********/
});

// Endpoint para obtener los autores de comentarios
$app->get($prefix . '/comments/authors', function (RequestInterface $request, ResponseInterface $response) use ($posts) {
    // Get the authors of the comments
    /**********
     * YOUR CODE HERE
     * Get the authors of the comments
     * **********/

    // Return the response with the authors
    /**********
     * YOUR CODE HERE
     * Return the response with the authors
     * The response should have a status of 200
     * Data is returned in a field named 'data'
     * **********/
});
