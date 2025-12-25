const API_URL = "api/tasks.php";

const taskName = document.getElementById("taskName");
const addBtn = document.getElementById("addBtn");
const tasksBody = document.getElementById("tasksBody");
const msg = document.getElementById("msg");

function setMsg(text, type = "info") {
  msg.textContent = text;
  msg.className = `msg ${type}`;
}

function getSelectedCategories() {
  const checks = document.querySelectorAll('input[type="checkbox"]:checked');
  return Array.from(checks).map(c => c.value);
}

function clearForm() {
  taskName.value = "";
  document.querySelectorAll('input[type="checkbox"]').forEach(c => (c.checked = false));
}

function renderTasks(tasks) {
  tasksBody.innerHTML = "";
  for (const t of tasks) {
    const tr = document.createElement("tr");

    const tdName = document.createElement("td");
    tdName.textContent = t.name;

    const tdCats = document.createElement("td");
    tdCats.innerHTML = (t.categories || "")
      .split(",")
      .map(s => s.trim())
      .filter(Boolean)
      .map(cat => `<span class="badge">${cat}</span>`)
      .join(" ");

    const tdActions = document.createElement("td");
    const btn = document.createElement("button");
    btn.className = "danger";
    btn.textContent = "Borrar";
    btn.addEventListener("click", () => deleteTask(t.id));
    tdActions.appendChild(btn);

    tr.appendChild(tdName);
    tr.appendChild(tdCats);
    tr.appendChild(tdActions);

    tasksBody.appendChild(tr);
  }
}

async function loadTasks() {
  setMsg("");
  const res = await fetch(API_URL);
  const data = await res.json();
  if (!data.ok) {
    setMsg("No se pudieron cargar las tareas.", "error");
    return;
  }
  renderTasks(data.tasks);
}

async function addTask() {
  const name = taskName.value.trim();
  const categories = getSelectedCategories();

  if (!name) {
    setMsg("Escribe un nombre de tarea.", "error");
    return;
  }

  setMsg("Creando tarea...", "info");

  const res = await fetch(API_URL, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ name, categories })
  });

  const data = await res.json();

  if (!res.ok || !data.ok) {
    setMsg(data.error || "Error al crear la tarea.", "error");
    return;
  }

  clearForm();
  setMsg("Tarea creada ✅", "ok");
  await loadTasks(); // sin refrescar página
}

async function deleteTask(id) {
  setMsg("Borrando tarea...", "info");

  const res = await fetch(`${API_URL}?id=${encodeURIComponent(id)}`, {
    method: "DELETE"
  });

  const data = await res.json();

  if (!res.ok || !data.ok) {
    setMsg(data.error || "Error al borrar.", "error");
    return;
  }

  setMsg("Tarea borrada ✅", "ok");
  await loadTasks(); // sin refrescar página
}

addBtn.addEventListener("click", addTask);
taskName.addEventListener("keydown", (e) => {
  if (e.key === "Enter") addTask();
});

loadTasks();
