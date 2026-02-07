<?php
require_once __DIR__ . "/../config/db.php";

$method = $_SERVER["REQUEST_METHOD"];
$conn = db();

function action_label(string $a): string {
  return match ($a) {
    "ARRIVAL" => "Llegada",
    "BREAK_START" => "Salida al break",
    "BREAK_END" => "Llegada de break",
    "EXIT" => "Salida",
    default => $a
  };
}

if ($method === "GET") {
  $date = $_GET["date"] ?? date("Y-m-d"); // YYYY-MM-DD
  $sql = "
    SELECT l.id, l.action, l.occurred_at, l.note,
           e.code, e.full_name, d.name AS department
    FROM access_logs l
    JOIN employees e ON e.id = l.employee_id
    JOIN departments d ON d.id = e.department_id
    WHERE DATE(l.occurred_at) = ?
    ORDER BY l.occurred_at DESC
    LIMIT 300
  ";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $date);
  $stmt->execute();
  $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

  foreach ($rows as &$r) $r["action_label"] = action_label($r["action"]);

  json_out(["ok" => true, "date" => $date, "logs" => $rows]);
}

if ($method === "POST") {
  $payload = json_decode(file_get_contents("php://input"), true) ?? [];

  $code = trim($payload["code"] ?? "");
  $action = trim($payload["action"] ?? "");
  $note = trim($payload["note"] ?? "");

  $allowed = ["ARRIVAL","BREAK_START","BREAK_END","EXIT"];
  if ($code === "" || !in_array($action, $allowed, true)) {
    json_out(["ok" => false, "error" => "Código o acción inválida"], 400);
  }

  // Buscar empleado activo por código
  $stmt = $conn->prepare("SELECT id, is_active FROM employees WHERE code=? LIMIT 1");
  $stmt->bind_param("s", $code);
  $stmt->execute();
  $emp = $stmt->get_result()->fetch_assoc();

  if (!$emp) json_out(["ok" => false, "error" => "Empleado no existe"], 404);
  if ((int)$emp["is_active"] !== 1) json_out(["ok" => false, "error" => "Empleado inactivo"], 400);

  $employee_id = (int)$emp["id"];

  $stmt = $conn->prepare("INSERT INTO access_logs (employee_id, action, note) VALUES (?,?,?)");
  $stmt->bind_param("iss", $employee_id, $action, $note);
  $stmt->execute();

  json_out(["ok" => true, "id" => $conn->insert_id, "action_label" => action_label($action)]);
}

json_out(["ok" => false, "error" => "Método no permitido"], 405);
