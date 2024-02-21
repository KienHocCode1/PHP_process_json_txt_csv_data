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
 * Function: sanitizeInput
 * Description: Sanitizes the input by applying the FILTER_SANITIZE_STRING filter.
 * 
 * @param string $input The input to be sanitized.
 * @return string Sanitized input.
 */
function sanitizeInput($input) {
    $sanitizedInput = filter_var($input, FILTER_SANITIZE_STRING);
    return $sanitizedInput;
}

/**
 * Function: customSort
 * Description: Custom sorting function used for sorting an associative array by the specified column.
 * 
 * @param array $a First data element to compare.
 * @param array $b Second data element to compare.
 * @return int Comparison result for sorting.
 */
function customSort($a, $b) {
    global $sortValue;
    return strnatcasecmp($a[$sortValue], $b[$sortValue]);
}

if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] === UPLOAD_ERR_OK) {
    // Retrieve the temporary file path of the uploaded CSV file.
    $uploadedFile = $_FILES['csvFile']['tmp_name'];

    // Get the column to sort by from the POST request.
    $sortColumn = isset($_POST['sortColumn']) ? intval($_POST['sortColumn']) - 1 : 0;

    $csvData = array(); // Initialize an array to store CSV data.

    // Open and read the uploaded CSV file.
    if (($handle = fopen($uploadedFile, "r")) !== FALSE) {
        $headersLine = fgets($handle); // Read the header line with column names.

        // Split header line into an array of column names.
        $headers = preg_split('/,/', $headersLine);

        $numColumns = count($headers);
        $sortValue = $headers[$sortColumn];

        // Read and process each row of the CSV file.
        while (($row = fgetcsv($handle)) !== FALSE) {
            $rowData = array();

            // Create an associative array with column names as keys and sanitized row data as values.
            for ($i = 0; $i < $numColumns; $i++) {
                $rowData[$headers[$i]] = sanitizeInput($row[$i]);
            }

            $csvData[] = $rowData; // Add the row data to the CSV data array.
        }

        fclose($handle);

        // Sort the CSV data using the custom sorting function.
        usort($csvData, 'customSort');
    }

    // Set the response header as JSON and send the sorted CSV data as a JSON response.
    header('Content-Type: application/json');
    echo json_encode($csvData);
} else {
    // Handle the case of an invalid or missing CSV file by returning a 400 Bad Request response with an error message.
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Invalid or missing CSV file']);
}
/**
 * https://www.php.net/manual/en/language.variables.scope.php ----global
 */
?>
