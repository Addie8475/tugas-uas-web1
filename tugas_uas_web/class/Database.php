<?php

require_once __DIR__ . "/../config.php";

class Database {
    public $conn;

    public function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }

        // Supaya karakter seperti tanda kutip tidak bikin error
        $this->conn->set_charset("utf8mb4");
    }

    // ============================================================
    // SELECT BY ID
    // ============================================================
    public function getById($table, $id) {
        $id = intval($id); // Anti SQL injection
        $result = $this->conn->query("SELECT * FROM $table WHERE id = $id");
        return $result ? $result->fetch_assoc() : null;
    }

    // ============================================================
    // SELECT ALL
    // ============================================================
    public function getAll($table) {
        $data = [];
        $result = $this->conn->query("SELECT * FROM $table");

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // ============================================================
    // GET PAGED (LIMIT + OFFSET)
    // ============================================================
    public function getPaged($table, $limit, $offset = 0, $orderBy = 'id DESC') {
        $limit = intval($limit);
        $offset = intval($offset);
        $orderBy = $this->conn->real_escape_string($orderBy);

        $data = [];
        $sql = "SELECT * FROM $table ORDER BY $orderBy LIMIT $limit OFFSET $offset";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // ============================================================
    // COUNT ROWS
    // ============================================================
    public function count($table) {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM $table");
        if ($result) {
            $row = $result->fetch_assoc();
            return intval($row['total']);
        }
        return 0;
    }

    // ============================================================
    // INSERT DATA
    // ============================================================
    public function insert($table, $data) {
        // Escape semua input agar aman
        foreach ($data as $key => $value) {
            $data[$key] = $this->conn->real_escape_string($value);
        }

        $fields = implode(",", array_keys($data));
        $values = "'" . implode("','", array_values($data)) . "'";

        $sql = "INSERT INTO $table ($fields) VALUES ($values)";
        return $this->conn->query($sql);
    }

    // ============================================================
    // UPDATE DATA
    // ============================================================
    public function update($table, $data, $id) {
        $id = intval($id);

        $set = [];
        foreach ($data as $key => $value) {
            $value = $this->conn->real_escape_string($value);
            $set[] = "$key = '$value'";
        }

        $setQuery = implode(", ", $set);
        $sql = "UPDATE $table SET $setQuery WHERE id = $id";

        return $this->conn->query($sql);
    }

    // ============================================================
    // DELETE DATA
    // ============================================================
    public function delete($table, $id) {
        $id = intval($id);
        return $this->conn->query("DELETE FROM $table WHERE id = $id");
    }

    // ============================================================
    // RAW QUERY
    // ============================================================
    public function query($sql) {
        return $this->conn->query($sql);
    }
}

