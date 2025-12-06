<?php
$host = 'localhost';
$username = 'lab5_user';
$password = '';
$dbname = 'world';

// Get the country parameter from GET request
$country = isset($_GET['country']) ? $_GET['country'] : '';

// Get the lookup type (countries or cities)
$lookup = isset($_GET['lookup']) ? $_GET['lookup'] : 'countries';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($lookup === 'cities') {
        // Lookup cities for a country
        // First, we need to get the country code from the country name
        // Then get cities matching that country code
        $sql = "SELECT c.name, c.district, c.population 
                FROM cities c 
                INNER JOIN countries co ON c.country_code = co.code 
                WHERE co.name LIKE :country 
                ORDER BY c.population DESC";
        
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
        // Default: Lookup countries
        $sql = "SELECT * FROM countries WHERE name LIKE :country";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['country' => '%' . $country . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($results) > 0) {
            echo '<ul>';
            foreach ($results as $row) {
                echo '<li>';
                echo '<strong>' . htmlspecialchars($row['name']) . '</strong>';
                echo ' (' . htmlspecialchars($row['code']) . ')';
                echo '<br>';
                echo 'Continent: ' . htmlspecialchars($row['continent']);
                echo ' | Region: ' . htmlspecialchars($row['region']);
                echo '<br>';
                echo 'Population: ' . number_format($row['population']);
                echo ' | Life Expectancy: ' . ($row['life_expectancy'] ?? 'N/A');
                echo '<br>';
                echo 'Head of State: ' . htmlspecialchars($row['head_of_state'] ?? 'N/A');
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="no-results">No countries found matching your search.</p>';
        }
    }
    
} catch(PDOException $e) {
    echo '<p class="error">Database error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>