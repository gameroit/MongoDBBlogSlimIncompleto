<?php
// Set the connection to the MongoDB database
/**********
 * YOUR CODE HERE
 * Create a new MongoDB connection
 * *********
 */

require '../vendor/autoload.php';
$mongo = new MongoDB\Client("mongodb://localhost:27017");      // Change this value to set the connection to the MongoDB database

// Set the database and collection
/**********
 * YOUR CODE HERE
 * Select the 'blog' database and the 'posts' collection
 * *********
 */
$database = $mongo->blog;   // Change this value to set the database
$posts = $database->posts;      // Change this value to set the collection
