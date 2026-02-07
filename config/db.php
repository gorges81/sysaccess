 <?php
// config/db.php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function db(): mysqli {
  $host = "127.0.0.1";
  $user = "root";
  $pass = "";
  $name = "sysaccess";

  $conn = new mysqli($host, $user, $pass, $name);
  $conn->set_charset("utf8mb4");
  return $conn;
}

function json_out($data, int $code = 200): void {
  http_response_code($code);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}
