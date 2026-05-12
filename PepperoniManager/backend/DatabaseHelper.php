<?php

class DatabaseHelper {
    private $conn;

    public function __construct($db_name = "pepperoni_manager_db") {
        $this->conn = new mysqli("localhost", "root", "", $db_name);

        if ($this->conn->connect_error) {
            exit("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function prepareExecuteQuery($query, ...$params) {
        $stmt = $this->conn->prepare($query);

        if (!$stmt->execute()) {
            exit("Error creating table");
        }

        return $stmt;
    }

    public function executeQuery($query) {
        $result = $this->conn->query($query);

        if (!$result) {
            exit("Query error: " . $this->conn->error);
        }

        return $result;
    }

    public function selectDB($db_name) {
        if ($db_name != "") {
            $this->conn->select_db($db_name);
        }
    }

    public function escapeString($string) {
        return $this->conn->real_escape_string($string);
    }

    public function prepareQuery($query) {
        return $this->conn->prepare($query);
    }

    public function getLastInsertId() {
        return $this->conn->insert_id;
    }

}