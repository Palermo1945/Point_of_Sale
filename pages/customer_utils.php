<?php
function normalizeCustomerName($name)
{
    $name = trim(preg_replace('/\s+/', ' ', (string) $name));
    return function_exists('mb_strtolower') ? mb_strtolower($name, 'UTF-8') : strtolower($name);
}

function ensureCustomerRecord(PDO $db, $name, array $details = array())
{
    $normalizedName = normalizeCustomerName($name);
    if ($normalizedName === '') {
        throw new InvalidArgumentException('Customer name is required.');
    }

    $columns = array();
    foreach ($db->query('SHOW COLUMNS FROM customer') as $column) {
        $columns[$column['Field']] = true;
    }
    if (!isset($columns['customer_name'])) {
        throw new RuntimeException('Customer table is missing customer_name.');
    }

    $nameParts = preg_split('/\s+/', $normalizedName, -1, PREG_SPLIT_NO_EMPTY);
    $defaults = array(
        'customer_name' => $normalizedName,
        'first_name' => isset($nameParts[0]) ? $nameParts[0] : '',
        'middle_name' => count($nameParts) > 2 ? implode(' ', array_slice($nameParts, 1, -1)) : '',
        'last_name' => count($nameParts) > 1 ? $nameParts[count($nameParts) - 1] : '',
        'address' => '',
        'contact' => '',
        'membership_number' => ''
    );
    $provided = array();
    foreach ($details as $field => $value) {
        if (isset($columns[$field]) && trim((string) $value) !== '') {
            $provided[$field] = $value;
        }
    }

    $find = $db->prepare('SELECT customer_id FROM customer WHERE LOWER(TRIM(customer_name)) = :name ORDER BY customer_id ASC LIMIT 1');
    $find->execute(array(':name' => $normalizedName));
    $customerId = $find->fetchColumn();

    if ($customerId !== false) {
        $nameFields = array_intersect_key($defaults, array_flip(array('customer_name', 'first_name', 'middle_name', 'last_name')));
        $nameFields = array_intersect_key($nameFields, $columns);
        $updates = array_merge($nameFields, $provided);
        unset($updates['customer_id']);
        if ($updates) {
            $set = array();
            $params = array();
            foreach ($updates as $field => $value) {
                if (isset($columns[$field])) {
                    $set[] = '`' . str_replace('`', '``', $field) . '` = :' . $field;
                    $params[':' . $field] = $value;
                }
            }
            if ($set) {
                $params[':customer_id'] = $customerId;
                $update = $db->prepare('UPDATE customer SET ' . implode(', ', $set) . ' WHERE customer_id = :customer_id');
                $update->execute($params);
            }
        }
        return $normalizedName;
    }

    $data = array_merge($defaults, $provided);
    $insertFields = array();
    $placeholders = array();
    $params = array();
    foreach ($data as $field => $value) {
        if (isset($columns[$field])) {
            $insertFields[] = '`' . str_replace('`', '``', $field) . '`';
            $placeholders[] = ':' . $field;
            $params[':' . $field] = $value;
        }
    }

    $insert = $db->prepare('INSERT INTO customer (' . implode(', ', $insertFields) . ') VALUES (' . implode(', ', $placeholders) . ')');
    $insert->execute($params);
    return $normalizedName;
}
