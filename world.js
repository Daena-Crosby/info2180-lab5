document.addEventListener('DOMContentLoaded', function() {
    
    const countryInput = document.getElementById('country');
    const lookupButton = document.getElementById('lookup');
    const lookupCitiesButton = document.getElementById('lookup-cities');
    const resultDiv = document.getElementById('result');
    
    function fetchData(country, lookupType) {
        let url = 'world.php?country=' + encodeURIComponent(country);
        
        if (lookupType === 'cities') {
            url += '&lookup=cities';
        }
        
        resultDiv.innerHTML = '<p class="loading">Loading...</p>';
        
        fetch(url)
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Server responded with status: ' + response.status);
                }
                return response.text();
            })
            .then(function(data) {
                resultDiv.innerHTML = data;
            })
            .catch(function(error) {
                console.error('Fetch error:', error);
                resultDiv.innerHTML = '<p class="error">' +
                    '<strong>Error:</strong> ' + error.message + '<br><br>' +
                    '<strong>Troubleshooting:</strong><br>' +
                    '1. Make sure you are running this through a web server (XAMPP, WAMP, etc.)<br>' +
                    '2. Do NOT open index.html directly in browser<br>' +
                    '3. Access via: http://localhost/your-folder/index.html<br>' +
                    '4. Ensure PHP and MySQL are running' +
                    '</p>';
            });
    }
    
    function fetchDataXHR(country, lookupType) {
        let url = 'world.php?country=' + encodeURIComponent(country);
        
        if (lookupType === 'cities') {
            url += '&lookup=cities';
        }
        
        resultDiv.innerHTML = '<p class="loading">Loading...</p>';
        
        const xhr = new XMLHttpRequest();
        
        xhr.open('GET', url, true);
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    resultDiv.innerHTML = xhr.responseText;
                } else if (xhr.status === 0) {
                    resultDiv.innerHTML = '<p class="error">' +
                        '<strong>Network Error (Status 0)</strong><br><br>' +
                        'This usually means:<br>' +
                        '1. You opened index.html directly (file:// protocol)<br>' +
                        '2. You need to use a web server<br><br>' +
                        '<strong>Solution:</strong> Run through XAMPP/WAMP and access via http://localhost/' +
                        '</p>';
                } else {
                    resultDiv.innerHTML = '<p class="error">Error: Server responded with status ' + xhr.status + '</p>';
                }
            }
        };
        
        xhr.onerror = function() {
            resultDiv.innerHTML = '<p class="error">' +
                '<strong>Network Error</strong><br><br>' +
                'Please ensure:<br>' +
                '1. You are using a web server (XAMPP, WAMP, MAMP)<br>' +
                '2. Access the page via http://localhost/...<br>' +
                '3. PHP and MySQL services are running' +
                '</p>';
        };
        
        xhr.send();
    }
    
    lookupButton.addEventListener('click', function() {
        const country = countryInput.value.trim();
        fetchData(country, 'countries');
    });
    
    if (lookupCitiesButton) {
        lookupCitiesButton.addEventListener('click', function() {
            const country = countryInput.value.trim();
            fetchData(country, 'cities');
        });
    }
    
    countryInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            const country = countryInput.value.trim();
            fetchData(country, 'countries');
        }
    });
    
});