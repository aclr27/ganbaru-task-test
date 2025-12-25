<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Gestor de tareas</title>
  <link rel="stylesheet" href="assets/styles.css" />
</head>
<body>
  <main class="container">
    <h1>Gestor de tareas</h1>

    <section class="card">
      <div class="form-row">
        <input id="taskName" type="text" placeholder="Nueva tarea..." maxlength="255" />
        <label class="chk"><input type="checkbox" value="PHP" /> PHP</label>
        <label class="chk"><input type="checkbox" value="Javascript" /> Javascript</label>
        <label class="chk"><input type="checkbox" value="CSS" /> CSS</label>
        <button id="addBtn">Añadir</button>
      </div>
      <p id="msg" class="msg"></p>
    </section>

    <section class="card">
      <table class="table">
        <thead>
          <tr>
            <th>Tarea</th>
            <th>Categorías</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody id="tasksBody"></tbody>
      </table>
    </section>
  </main>

  <script src="assets/app.js"></script>
</body>
</html>
