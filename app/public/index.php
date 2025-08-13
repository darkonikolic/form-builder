<?php
/**
 * Form Builder Application
 * Entry point
 */

// Basic error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simple response to show the application is working
echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "    <title>Form Builder</title>";
echo "    <style>";
echo "        body { font-family: Arial, sans-serif; margin: 40px; }";
echo "        .container { max-width: 800px; margin: 0 auto; }";
echo "        .status { background: #e8f5e8; padding: 20px; border-radius: 5px; }";
echo "    </style>";
echo "</head>";
echo "<body>";
echo "    <div class='container'>";
echo "        <h1>🚀 Form Builder Application</h1>";
echo "        <div class='status'>";
echo "            <h2>✅ Status: Running Successfully!</h2>";
echo "            <p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "            <p><strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "            <p><strong>Database:</strong> PostgreSQL (configured)</p>";
echo "            <p><strong>Frontend:</strong> Node.js (configured)</p>";
echo "        </div>";
echo "        <h3>Next Steps:</h3>";
echo "        <ul>";
echo "            <li>Create database migrations</li>";
echo "            <li>Build your form builder logic</li>";
echo "            <li>Add frontend components</li>";
echo "        </ul>";
echo "    </div>";
echo "</body>";
echo "</html>";
?>
