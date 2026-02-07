<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>SysAccess - Dashboard</title>
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
  <nav>
    <a href="index.php">Dashboard</a>
    <a href="employees.php">Empleados</a>
  </nav>

  <h2>Registro de Acceso</h2>

  <div class="card">
    <div class="row">
      <div>
        <label>Código empleado</label>
        <input id="code" placeholder="Ej: E001">
      </div>

      <div>
        <label>Acción</label>
        <select id="action">
          <option value="ARRIVAL">Llegada</option>
          <option value="BREAK_START">Salida al break</option>
          <option value="BREAK_END">Llegada de break</option>
          <option value="EXIT">Salida</option>
        </select>
      </div>

      <div style="min-width:220px">
        <label>Nota (opcional)</label>
        <input id="note" placeholder="Ej: tarde por tráfico">
      </div>

      <div>
        <button id="btnMark">Marcar</button>
      </div>
    </div>
    <small id="msg"></small>
  </div>

  <div class="card">
    <div class="row">
      <div>
        <label>Fecha</label>
        <input id="date" type="date" value="<?php echo date('Y-m-d'); ?>">
      </div>
      <div>
        <label>Buscar</label>
        <input id="search" placeholder="código, nombre, depto, acción, nota">
      </div>
      <div>
        <button id="btnReload">Cargar</button>
      </div>
    </div>
  </div>

  <h3>Registros del día</h3>
  <table>
    <thead>
      <tr>
        <th>Fecha/Hora</th>
        <th>Código</th>
        <th>Empleado</th>
        <th>Departamento</th>
        <th>Acción</th>
        <th>Nota</th>
      </tr>
    </thead>
    <tbody id="tbody"></tbody>
  </table>

  <script src="assets/app.js"></script>
  <script>
    const $ = (id) => document.getElementById(id);

    async function loadLogs() {
      const date = $("date").value;
      const q = $("search").value.trim();

      const data = await api(`api/access.php?date=${encodeURIComponent(date)}&q=${encodeURIComponent(q)}`);
      const rows = data.logs || [];

      $("tbody").innerHTML = rows.map(r => `
        <tr>
          <td>${esc(r.occurred_at)}</td>
          <td>${esc(r.code)}</td>
          <td>${esc(r.full_name)}</td>
          <td>${esc(r.department)}</td>
          <td><span class="badge">${esc(r.action_label)}</span></td>
          <td>${esc(r.note || "")}</td>
        </tr>
      `).join("");
    }

    $("btnReload").addEventListener("click", () => loadLogs().catch(showErr));
    $("search").addEventListener("keyup", () => loadLogs().catch(showErr));
    $("date").addEventListener("change", () => loadLogs().catch(showErr));

    $("btnMark").addEventListener("click", async () => {
      $("msg").textContent = "";
      try {
        const payload = {
          code: $("code").value.trim(),
          action: $("action").value,
          note: $("note").value.trim()
        };
        const res = await api("api/access.php", { method: "POST", body: JSON.stringify(payload) });
        $("msg").textContent = `OK: ${res.action_label}`;
        $("code").value = "";
        $("note").value = "";
        await loadLogs();
      } catch (e) {
        showErr(e);
      }
    });

    function showErr(e) {
      $("msg").textContent = "Error: " + (e.message || e);
    }

    loadLogs().catch(showErr);
  </script>
</body>
</html>
