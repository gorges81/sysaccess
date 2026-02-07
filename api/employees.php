<?php
require_once __DIR__ . "/../config/db.php";

$method = $_SERVER["REQUEST_METHOD"];
$conn = db();

if ($method === "GET") {
  $q = trim($_GET["q"] ?? "");

  $sql = "
    SELECT e.id, e.code, e.full_name, e.is_active,
           d.id AS department_id, d.name AS department
    FROM employees e
    JOIN departments d ON d.id = e.department_id
    WHERE (e.code LIKE CONCAT('%', ?, '%') OR e.full_name LIKE CONCAT('%', ?, '%'))
    ORDER BY e.id DESC
    LIMIT 200
  ";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $q, $q);
  $stmt->execute();
  $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

  json_out(["ok" => true, "employees" => $rows]);
}

if ($method === "POST") {
  $payload = json_decode(file_get_contents("php://input"), true) ?? [];

  $code = trim($payload["code"] ?? "");
  $full_name = trim($payload["full_name"] ?? "");
  $department_id = (int)($payload["department_id"] ?? 0);

  if ($code === "" || $full_name === "" || $department_id <= 0) {
    json_out(["ok" => false, "error" => "Faltan datos"], 400);
  }

  $stmt = $conn->prepare("INSERT INTO employees (code, full_name, department_id) VALUES (?,?,?)");
  $stmt->bind_param("ssi", $code, $full_name, $department_id);
  $stmt->execute();

  json_out(["ok" => true, "id" => $conn->insert_id]);
}

if ($method === "PUT") {
  $payload = json_decode(file_get_contents("php://input"), true) ?? [];

  $id = (int)($payload["id"] ?? 0);
  $code = trim($payload["code"] ?? "");
  $full_name = trim($payload["full_name"] ?? "");
  $department_id = (int)($payload["department_id"] ?? 0);
  $is_active = isset($payload["is_active"]) ? (int)!!$payload["is_active"] : 1;

  if ($id <= 0 || $code === "" || $full_name === "" || $department_id <= 0) {
    json_out(["ok" => false, "error" => "Datos inválidos"], 400);
  }

  $stmt = $conn->prepare("
    UPDATE employees
    SET code=?, full_name=?, department_id=?, is_active=?
    WHERE id=?
  ");
  $stmt->bind_param("ssiii", $code, $full_name, $department_id, $is_active, $id);
  $stmt->execute();

  json_out(["ok" => true]);
}

if ($method === "DELETE") {
  $payload = json_decode(file_get_contents("php://input"), true) ?? [];
  $id = (int)($payload["id"] ?? 0);
  if ($id <= 0) json_out(["ok" => false, "error" => "ID inválido"], 400);

  $stmt = $conn->prepare("DELETE FROM employees WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();

  json_out(["ok" => true]);
}

json_out(["ok" => false, "error" => "Método no permitido"], 405);
