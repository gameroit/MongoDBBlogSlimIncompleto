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
    $data = $request->getParsedBody();
    
    if (!isset($data['body']) || !isset($data['permalink']) || !isset($data['author']) || 
        !isset($data['title']) || !isset($data['tags'])) {
        $response = $response->withStatus(400);
        $response->getBody()->write(json_encode(['message' => 'Invalid input']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Insert the new post into the database
    /**********
     * YOUR CODE HERE
     * Insert the new post into the database
     * Data is obtained from the request body
     * **********/
    $result = $posts->insertOne([
        'body' => $data['body'],
        'permalink' => $data['permalink'],
        'author' => $data['author'],
        'title' => $data['title'],
        'tags' => $data['tags'],
        'comments' => [],
    ]);

    // Return the response with the ID of the new post
    /**********
     * YOUR CODE HERE
     * Return the response with the ID of the new post
     * An status of 201 should be set
     * The response should have a message 'Post created'
     * **********/
    $response = $response->withStatus(201);
    $response->getBody()->write(json_encode([
        'message' => 'Post created',
        'id' => (string)$result->getInsertedId()
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Endpoint para obtener un post por ID
$app->get($prefix . '/posts/{id}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($posts) {

    // Get the ID from the URL
    /**********
     * YOUR CODE HERE
     * Get the ID from the URL
     * **********/
    $id = $args['id'];

    // Validate the ID
    /**********
     * YOUR CODE HERE
     * Validate the ID
     * If the ID is invalid, return a 400 response with a message 'Invalid product ID'
     * **********/
    if (!ObjectId::isValid($id)) {
        $response = $response->withStatus(400);
        $response->getBody()->write(json_encode(['message' => 'Invalid product ID']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Find the post in the database
    /**********
     * YOUR CODE HERE
     * Find the post in the database
     * If the post is not found, return a 404 response with a message 'Post not found'
     * **********/
    $post = $posts->findOne(['_id' => new ObjectId($id)]);
    
    if (!$post) {
        $response = $response->withStatus(404);
        $response->getBody()->write(json_encode(['message' => 'Post not found']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Return the response with the post
    /**********
     * YOUR CODE HERE
     * Return the response with the post
     * The response should have a status of 200
     * Data is returned in a field named 'data'
     * **********/
    $response = $response->withStatus(200);
    $response->getBody()->write(json_encode(['data' => $post]));
    return $response->withHeader('Content-Type', 'application/json');
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
    $params = $request->getQueryParams();


    // Set the filter and skip values
    /**********
     * YOUR CODE HERE
     * Set the filter and skip values
     * The filter should be an empty array
     * The skip value should be 0 if not set in the query parameters
     * **********/
    $filter = [];
    $skip = isset($params['skip']) ? (int)$params['skip'] : 0;
    
    if (isset($params['tag'])) {
        $filter['tags'] = $params['tag'];
    }
    
    if (isset($params['author'])) {
        $filter['author'] = $params['author'];
    }
    
    if (isset($params['comment_author'])) {
        $filter['comments.author'] = $params['comment_author'];
    }

    // Get the posts from the database. A limit of 5 is set
    /**********
     * YOUR CODE HERE
     * Get the posts from the database with the filter and skip values
     * The posts should be sorted in descending order by ID
     * **********/
    $cursor = $posts->find(
        $filter,
        [
            'limit' => 5,
            'skip' => $skip,
            'sort' => ['_id' => -1]
        ]
    );
    
    $result = [];
    foreach ($cursor as $post) {
        $result[] = $post;
    }

    // Get total number of posts
    /**********
     * YOUR CODE HERE
     * Get the total number of posts
     * **********/
    $total = $posts->countDocuments($filter);

    // Return the response with the posts
    /**********
     * YOUR CODE HERE
     * Return the response with the posts
     * The response should have a status of 200
     * Data is returned in a field named 'data'
     * The total number of posts is returned in a field named 'total'
     * **********/
    $response = $response->withStatus(200);
    $response->getBody()->write(json_encode([
        'data' => $result,
        'total' => $total
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Endpoint para añadir un comentario a un post
$app->post($prefix . '/posts/{id}/comments', function (RequestInterface $request, ResponseInterface $response, array $args) use ($posts) {
    // Get the ID from the URL and the request body
    /**********
     * YOUR CODE HERE
     * Get the ID from the URL
     * Get the request body
     * **********/
    $id = $args['id'];
    $data = $request->getParsedBody();

    // Validate the ID
    /**********
     * YOUR CODE HERE
     * Validate the ID
     * If the ID is invalid, return a 400 response with a message 'Invalid product ID'
     * **********/
    if (!ObjectId::isValid($id)) {
        $response = $response->withStatus(400);
        $response->getBody()->write(json_encode(['message' => 'Invalid product ID']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Validate the request body
    /**********
     * YOUR CODE HERE
     * Check if the body, email and author are set in the request body
     * If not, return a 400 response with a message 'Invalid input'
     * **********/
    if (!isset($data['body']) || !isset($data['email']) || !isset($data['author'])) {
        $response = $response->withStatus(400);
        $response->getBody()->write(json_encode(['message' => 'Invalid input']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Find the post in the database
    /**********
     * YOUR CODE HERE
     * Find the post in the database
     * If the post is not found, return a 404 response with a message 'Post not found'
     * **********/
     $post = $posts->findOne(['_id' => new ObjectId($id)]);
    
    if (!$post) {
        $response = $response->withStatus(404);
        $response->getBody()->write(json_encode(['message' => 'Post not found']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Add the comment to the post
    /**********
     * YOUR CODE HERE
     * Add the comment to the post
     * **********/
    $comment = [
        'body' => $data['body'],
        'email' => $data['email'],
        'author' => $data['author'],
        'date' => new MongoDB\BSON\UTCDateTime()
    ];
    
    $updateResult = $posts->updateOne(
        ['_id' => new ObjectId($id)],
        ['$push' => ['comments' => $comment]]
    );

    // Check if the comment was added
    /**********
     * YOUR CODE HERE
     * Check if the comment was added
     * If not, return a 500 response with a message 'Failed to add comment'
     * **********/
    if ($updateResult->getModifiedCount() === 0) {
        $response = $response->withStatus(500);
        $response->getBody()->write(json_encode(['message' => 'Failed to add comment']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Return the response
    /**********
     * YOUR CODE HERE
     * Return the response
     * The response should have a status of 201
     * The response should have a message 'Comment added'
     * **********/
    $response = $response->withStatus(201);
    $response->getBody()->write(json_encode(['message' => 'Comment added']));
    return $response->withHeader('Content-Type', 'application/json');
});

// Endpoint para obtener los autores de comentarios
$app->get($prefix . '/comments/authors', function (RequestInterface $request, ResponseInterface $response) use ($posts) {
    // Get the authors of the comments
    /**********
     * YOUR CODE HERE
     * Get the authors of the comments
     * **********/
    $pipeline = [
        ['$unwind' => '$comments'],
        ['$group' => ['_id' => '$comments.author']],
        ['$sort' => ['_id' => 1]]
    ];
    
    $cursor = $posts->aggregate($pipeline);
    
    $authors = [];
    foreach ($cursor as $document) {
        $authors[] = $document->_id;
    }

    // Return the response with the authors
    /**********
     * YOUR CODE HERE
     * Return the response with the authors
     * The response should have a status of 200
     * Data is returned in a field named 'data'
     * **********/
    $response = $response->withStatus(200);
    $response->getBody()->write(json_encode(['data' => $authors]));
    return $response->withHeader('Content-Type', 'application/json');
});
