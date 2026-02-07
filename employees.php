<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>SysAccess - Empleados</title>
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
  <nav>
    <a href="index.php">Dashboard</a>
    <a href="employees.php">Empleados</a>
  </nav>

  <h2>Empleados</h2>

  <div class="card">
    <h3>Crear / Editar</h3>
    <div class="row">
      <input type="hidden" id="id">
      <div>
        <label>Código</label>
        <input id="code" placeholder="E004">
      </div>
      <div>
        <label>Nombre</label>
        <input id="full_name" placeholder="Nombre completo">
      </div>
      <div>
        <label>Departamento</label>
        <select id="department_id">
          <option value="1">Arte</option>
          <option value="2">Diseño</option>
          <option value="3">Tecnologia</option>
        </select>
      </div>
      <div>
        <label>Activo</label>
        <select id="is_active">
          <option value="1">Sí</option>
          <option value="0">No</option>
        </select>
      </div>
      <div>
        <button id="btnSave">Guardar</button>
      </div>
      <div>
        <button id="btnClear" type="button">Limpiar</button>
      </div>
    </div>
    <small id="msg"></small>
  </div>

  <div class="card">
    <div class="row">
      <div>
        <label>Buscar</label>
        <input id="q" placeholder="código o nombre">
      </div>
      <div>
        <button id="btnSearch">Buscar</button>
      </div>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Código</th>
        <th>Nombre</th>
        <th>Departamento</th>
        <th>Activo</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody id="tbody"></tbody>
  </table>

  <script src="assets/app.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const $ = (id) => document.getElementById(id);

    function fillForm(e) {
      $("id").value = e.id;
      $("code").value = e.code;
      $("full_name").value = e.full_name;
      $("department_id").value = e.department_id;
      $("is_active").value = e.is_active;
    }

    function clearForm() {
      $("id").value = "";
      $("code").value = "";
      $("full_name").value = "";
      $("department_id").value = "1";
      $("is_active").value = "1";
      $("msg").textContent = "";
    }

    async function loadEmployees() {
      const q = $("q").value.trim();
      const data = await api(`api/employees.php?q=${encodeURIComponent(q)}`);
      const rows = data.employees || [];

      $("tbody").innerHTML = rows.map(r => `
        <tr>
          <td>${esc(r.id)}</td>
          <td>${esc(r.code)}</td>
          <td>${esc(r.full_name)}</td>
          <td>${esc(r.department)}</td>
          <td>${r.is_active == 1 ? "Sí" : "No"}</td>
          <td>
            <button data-edit="${r.id}">Editar</button>
            <button data-del="${r.id}">Eliminar</button>
          </td>
        </tr>
      `).join("");

      rows.forEach(r => {
        const btnE = document.querySelector(`button[data-edit="${r.id}"]`);
        const btnD = document.querySelector(`button[data-del="${r.id}"]`);
        btnE.addEventListener("click", () => fillForm(r));
        btnD.addEventListener("click", () => delEmp(r.id).catch(showErr));
      });
    }

    async function delEmp(id) {
      const result = await Swal.fire({
        title: "¿Eliminar empleado?",
        text: "Se eliminarán también sus registros de acceso",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
      });

      if (!result.isConfirmed) return;

      await api("api/employees.php", { method: "DELETE", body: JSON.stringify({ id }) });

      await loadEmployees();
      clearForm();

      Swal.fire("Eliminado", "El empleado fue eliminado", "success");
    }

    function showErr(e) {
      $("msg").textContent = "Error: " + (e.message || e);
    }

    $("btnSearch").addEventListener("click", () => loadEmployees().catch(showErr));
    $("btnClear").addEventListener("click", clearForm);

    $("btnSave").addEventListener("click", async () => {
      $("msg").textContent = "";
      try {
        const payload = {
          id: $("id").value ? parseInt($("id").value, 10) : 0,
          code: $("code").value.trim(),
          full_name: $("full_name").value.trim(),
          department_id: parseInt($("department_id").value, 10),
          is_active: $("is_active").value === "1"
        };

        if (!payload.code || !payload.full_name) throw new Error("Código y nombre son obligatorios");

        if (!payload.id) {
          await api("api/employees.php", { method: "POST", body: JSON.stringify(payload) });
          $("msg").textContent = "Empleado creado";
        } else {
          await api("api/employees.php", { method: "PUT", body: JSON.stringify(payload) });
          $("msg").textContent = "Empleado actualizado";
        }

        await loadEmployees();
        clearForm();
      } catch (e) {
        showErr(e);
      }
    });

    loadEmployees().catch(showErr);
  </script>
</body>
</html>
