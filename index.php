<?php
// Połączenie z bazą
$mysqli = mysqli_connect('todo.mysql.database.azure.com', 'Test1234', 'Test1234', 'todo');

if (!$mysqli) {
    die("Błąd połączenia: " . mysqli_connect_error());
}
// Dodawanie zadania
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['taskname'])) {
    $taskname = mysqli_real_escape_string($mysqli, $_POST['taskname']);
    $description = mysqli_real_escape_string($mysqli, $_POST['description']);
    $assigned_to = mysqli_real_escape_string($mysqli, $_POST['assigned_to']);
    $sql = "INSERT INTO task (taskname, description, assigned_to) VALUES ('$taskname', '$description', '$assigned_to')";
    mysqli_query($mysqli, $sql);
    header('Location: index.php');
    exit();
}

// Usuwanie zadania
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($mysqli, "DELETE FROM task WHERE id = $id");
    header('Location: index.php');
    exit();
}

// Zmiana statusu wykonania
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    mysqli_query($mysqli, "UPDATE task SET done = NOT done WHERE id = $id");
    header('Location: index.php');
    exit();
}

// Pobieranie zadań
$result = mysqli_query($mysqli, "SELECT * FROM task ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>TODO App Azure</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; }
        form input, form button { padding: 10px; margin: 5px 0; }
        ul { list-style: none; padding: 0; }
        li {
            background: #fff; margin: 5px 0; padding: 10px;
            border-left: 4px solid #0078d7;
        }
        li.done {
            text-decoration: line-through;
            opacity: 0.6;
            border-left-color: green;
        }
        a.delete { color: red; text-decoration: none; float: right; }
        a.toggle { color: green; margin-left: 10px; text-decoration: none; }
    </style>
</head>
<body>
    <h1>Moja lista zadań</h1>

    <form method="POST">
        <input type="text" name="taskname" placeholder="Nazwa zadania" required><br>
        <input type="text" name="description" placeholder="Opis zadania"><br>
        <input type="text" name="assigned_to" placeholder="Imię osoby odpowiedzialnej"><br>
        <button type="submit">Dodaj zadanie</button>
    </form>

    <ul>
        <?php
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $doneClass = $row['done'] ? 'done' : '';
                echo "<li class='$doneClass'>
                        <strong>" . htmlspecialchars($row['taskname']) . "</strong>: " .
                        htmlspecialchars($row['description']) .
                        "<br>👤 " . htmlspecialchars($row['assigned_to']) .
                        " <a class='toggle' href='?toggle=" . $row['id'] . "'>" .
                        ($row['done'] ? '☑️ Wykonane' : '⬜ Zrób') . "</a>" .
                        " <a class='delete' href='?delete=" . $row['id'] . "'>🗑️</a>
                      </li>";
            }
        } else {
            echo '<li>Brak zadań do wyświetlenia.</li>';
        }
        ?>
    </ul>
</body>
</html>

