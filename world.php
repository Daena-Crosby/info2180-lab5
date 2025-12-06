<?php
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'world';

$country = isset($_GET['country']) ? $_GET['country'] : '';

$lookup = isset($_GET['lookup']) ? $_GET['lookup'] : 'countries';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($lookup === 'cities') {
        
        $sql = "SELECT cities.name, cities.district, cities.population 
                FROM cities 
                INNER JOIN countries ON cities.country_code = countries.code 
                WHERE countries.name LIKE :country 
                ORDER BY cities.population DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute(['country' => '%' . $country . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($results) > 0) {
            echo '<table>';
            echo '<thead>';
            echo '<tr><th>Name</th><th>District</th><th>Population</th></tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($results as $row) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['district']) . '</td>';
                echo '<td>' . number_format($row['population']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p class="no-results">No cities found for the specified country.</p>';
        }
        
    } else {
        $sql = "SELECT * FROM countries WHERE name LIKE :country";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['country' => '%' . $country . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($results) > 0) {
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Country Name</th>';
            echo '<th>Continent</th>';
            echo '<th>Independence Year</th>';
            echo '<th>Head of State</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($results as $row) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['continent']) . '</td>';
                echo '<td>' . ($row['independence_year'] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($row['head_of_state'] ?? 'N/A') . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p class="no-results">No countries found matching your search.</p>';
        }
    }
    
} catch(PDOException $e) {
    echo '<p class="error">Database error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>