<?php
declare(strict_types=1);

require_once __DIR__ . '/../db/db.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = db();

    if ($method === 'GET') {
        $sql = "
            SELECT
                t.id,
                t.name,
                t.created_at,
                COALESCE(GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', '), '') AS categories
            FROM tasks t
            LEFT JOIN task_categories tc ON tc.task_id = t.id
            LEFT JOIN categories c ON c.id = tc.category_id
            GROUP BY t.id
            ORDER BY t.id DESC
        ";
        $tasks = $pdo->query($sql)->fetchAll();

        echo json_encode(['ok' => true, 'tasks' => $tasks]);
        exit;
    }

    if ($method === 'POST') {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        $name = isset($data['name']) ? trim((string)$data['name']) : '';
        $categories = isset($data['categories']) && is_array($data['categories'])
            ? $data['categories']
            : [];

        if ($name === '') {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'El nombre de la tarea no puede estar vacío.']);
            exit;
        }

        // Insert tarea
        $stmt = $pdo->prepare("INSERT INTO tasks (name) VALUES (:name)");
        $stmt->execute([':name' => $name]);
        $taskId = (int)$pdo->lastInsertId();

        // Insert relación con categorías (si hay)
        if (!empty($categories)) {
            // Solo permitimos categorías válidas (por nombre)
            $in = implode(',', array_fill(0, count($categories), '?'));
            $catStmt = $pdo->prepare("SELECT id, name FROM categories WHERE name IN ($in)");
            $catStmt->execute($categories);
            $catRows = $catStmt->fetchAll();

            $insertRel = $pdo->prepare("INSERT IGNORE INTO task_categories (task_id, category_id) VALUES (?, ?)");

            foreach ($catRows as $cat) {
                $insertRel->execute([$taskId, (int)$cat['id']]);
            }
        }

        echo json_encode(['ok' => true, 'task_id' => $taskId]);
        exit;
    }

    if ($method === 'DELETE') {
        // DELETE /api/tasks.php?id=123
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'ID inválido.']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'La tarea no existe.']);
            exit;
        }

        echo json_encode(['ok' => true]);
        exit;
    }

    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error interno', 'detail' => $e->getMessage()]);
}
?>