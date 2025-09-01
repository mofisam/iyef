<?php
require_once 'db.php';

/**
 * Execute a prepared SQL statement with parameters
 * 
 * @param string $sql The SQL query with placeholders
 * @param array $params The parameters to bind
 * @return mysqli_stmt|false The prepared statement or false on failure
 */
function executeQuery($sql, $params = []) {
    global $conn;
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        trigger_error('Error in prepare: ' . $conn->error, E_USER_ERROR);
        return false;
    }
    
    if (!empty($params)) {
        $types = '';
        $values = [];
        
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b'; // blob
            }
            $values[] = $param;
        }
        
        $stmt->bind_param($types, ...$values);
    }
    
    $stmt->execute();
    return $stmt;
}

/**
 * Fetch a single row from database
 * 
 * @param string $sql The SQL query
 * @param array $params The parameters to bind
 * @return array|false Associative array or false if no results
 */
function fetchSingle($query, $params = []) {
    global $conn;
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $types = str_repeat('s', count($params)); // Default to string type
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}
/**
 * Fetch all rows from database
 * 
 * @param string $sql The SQL query
 * @param array $params The parameters to bind
 * @return array Array of associative arrays
 */

function fetchAll($query, $params = []) {
    global $conn;
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $types = str_repeat('s', count($params)); // Default to string type
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}


/**
 * Insert a record into database
 * 
 * @param string $table The table name
 * @param array $data Associative array of column => value
 * @return int|false The inserted ID or false on failure
 */
function insertRecord($table, $data) {
    global $conn;
    
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    $values = array_values($data);
    
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = executeQuery($sql, $values);
    
    return $stmt ? $conn->insert_id : false;
}

/**
 * Update a record in database
 * 
 * @param string $table The table name
 * @param array $data Associative array of column => value
 * @param string $condition The WHERE condition
 * @param array $conditionParams Parameters for WHERE condition
 * @return int|false Number of affected rows or false on failure
 */
function updateRecord($table, $data, $condition, $conditionParams = []) {
    $setParts = [];
    $values = [];
    
    foreach ($data as $column => $value) {
        $setParts[] = "$column = ?";
        $values[] = $value;
    }
    
    $setClause = implode(', ', $setParts);
    $values = array_merge($values, $conditionParams);
    
    $sql = "UPDATE $table SET $setClause WHERE $condition";
    $stmt = executeQuery($sql, $values);
    
    return $stmt ? $stmt->affected_rows : false;
}

/**
 * Delete a record from database
 * 
 * @param string $table The table name
 * @param string $condition The WHERE condition
 * @param array $conditionParams Parameters for WHERE condition
 * @return int|false Number of affected rows or false on failure
 */
function deleteRecord($table, $condition, $conditionParams = []) {
    $sql = "DELETE FROM $table WHERE $condition";
    $stmt = executeQuery($sql, $conditionParams);
    
    return $stmt ? $stmt->affected_rows : false;
}

/**
 * Check if a record exists in database
 * 
 * @param string $table The table name
 * @param string $condition The WHERE condition
 * @param array $conditionParams Parameters for WHERE condition
 * @return bool True if record exists, false otherwise
 */
function recordExists($table, $condition, $conditionParams = []) {
    $sql = "SELECT COUNT(*) as count FROM $table WHERE $condition";
    $result = fetchSingle($sql, $conditionParams);
    
    return $result && $result['count'] > 0;
}