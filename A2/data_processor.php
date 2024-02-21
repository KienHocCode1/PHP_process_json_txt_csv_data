<?php
/**
 * I, Trung Kien Bui, 000356049, certify that this material is my original work. No other person's
 * work has been used without  acknowledgment and I have not made my work available to anyone else.
 */

/**
 * @author Trung Kien Bui <trung-kien.bui@mohawkcollege.ca>
 * @package COMP 10260 Assignment2
 * 
 * @version 202335.00
 */

/**
 * Function: readPokemonData
 * Description: Reads data from the "pokemon.txt" file, sanitizes it, and returns an array of Pokémon information.
 * 
 * @return array An array of Pokémon data, including their name and image URL.
 */
function readPokemonData() {
    $data = [];
    $lines = file("pokemon.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    for ($i = 0; $i < count($lines); $i += 2) {
        // call sanitize function
        $name = sanitizeInput($lines[$i]);
        $image = sanitizeInput($lines[$i + 1]);

        //key => value
        if ($name !== false && $image !== false) {
            $data[] = ['name' => $name, 'image' => $image];
        }
    }
    return $data;
}

/**
 * Function: readMoviesData
 * Description: Reads data from the "movies.json" file, sanitizes it, and returns an array of movie information.
 * 
 * @return array An array of movie data, including their name and release year.
 */
function readMoviesData() {
    $jsonData = file_get_contents("movies.json");
    //The json_decode() function returns an object by default. The json_decode() function has a second parameter, 
    //and when set to true, JSON objects are decoded into associative arrays.
    // https://www.w3schools.com/php/php_json.asp
    $data = json_decode($jsonData, true);

    if (is_array($data)) {
        $sanitizedData = [];

        foreach ($data as $item) {
            // is array, is not null?
            if (is_array($item) && isset($item['name']) && isset($item['year'])) {
                $sanitizedName = sanitizeInput($item['name']);
                // key=> value
                $sanitizedItem = ['name' => $sanitizedName, 'year' => $item['year']];
                $sanitizedData[] = $sanitizedItem;
            }
        }

        return $sanitizedData;
    }

    return [];
}

/**
 * Function: sanitizeInput
 * Description: Sanitizes input by applying the FILTER_SANITIZE_STRING filter.
 * 
 * @param string $input The input to be sanitized.
 * @return string Sanitized input.
 */
function sanitizeInput($input) {
    $sanitizedInput = filter_var($input, FILTER_SANITIZE_STRING);
    return $sanitizedInput;
}

$choice = filter_input(INPUT_GET, 'choice', FILTER_SANITIZE_STRING);
$sort = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING);

if (empty($choice) || ($choice != "pokemon" && $choice != "movies")) {
    // Handle invalid choice parameter by returning a 400 Bad Request response with an error message.
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Invalid choice parameter']);
    exit;
}

$data = [];
// call function and assign it to $data
if ($choice == "pokemon") {
    $data = readPokemonData();
} elseif ($choice == "movies") {
    $data = readMoviesData();
}

if ($sort == 'a') {
    // Sort the data in ascending order (A-Z) based on the name.
    usort($data, function($a, $b) {
        return strnatcasecmp($a['name'], $b['name']);
    });
} elseif ($sort == 'd') {
    // Sort the data in descending order (Z-A) based on the name.
    usort($data, function($a, $b) {
        return strnatcasecmp($b['name'], $a['name']);
    });
}

// Set the response header as JSON and send the sorted data as a JSON response.
header('Content-Type: application/json');
echo json_encode($data);

?>
