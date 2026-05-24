<?php
require 'vendor/autoload.php';
try {
 $client = new MongoDB\Client("mongodb://localhost:27017");
 $db = $client->sigae_mobile;
} catch (Exception $e) {
 die("Error: " . $e->getMessage());
}
?>