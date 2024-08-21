<?php
include 'db_connect.php';

$notification = ""; // Biến để lưu thông báo
$notification_type = ""; // Biến để lưu loại thông báo (thành công, lỗi)

// Xử lý khi biểu mẫu được gửi để thêm tác giả
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['author_name'])) {
    $author_name = mysqli_real_escape_string($conn, $_POST['author_name']);

    // Kiểm tra trùng lặp tên tác giả
    $check_sql = "SELECT id FROM authors WHERE author_name = ?";
    $stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt, "s", $author_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $notification = "Tác giả với tên này đã tồn tại.";
        $notification_type = 'error'; // Loại thông báo lỗi
    } else {
        // Thêm tác giả vào cơ sở dữ liệu
        $sql = "INSERT INTO authors (author_name, book_numbers) VALUES (?, 0)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $author_name);
        mysqli_stmt_execute($stmt);

        if (mysqli_affected_rows($conn) > 0) {
            $notification = "Tác giả đã được thêm thành công.";
            $notification_type = 'success'; // Loại thông báo thành công
        } else {
            $notification = "Lỗi: " . mysqli_error($conn);
            $notification_type = 'error'; // Loại thông báo lỗi
        }
    }

    mysqli_stmt_close($stmt);
}

// Xử lý khi xóa tác giả
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_author_id'])) {
    $author_id = (int)$_POST['delete_author_id'];

    // Xóa tác giả khỏi cơ sở dữ liệu
    $delete_sql = "DELETE FROM authors WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, "i", $author_id);
    mysqli_stmt_execute($stmt);

    if (mysqli_affected_rows($conn) > 0) {
        $notification = "Tác giả đã được xóa thành công.";
        $notification_type = 'success'; // Loại thông báo thành công
    } else {
        $notification = "Lỗi: " . mysqli_error($conn);
        $notification_type = 'error'; // Loại thông báo lỗi
    }

    mysqli_stmt_close($stmt);
}

// Lấy danh sách tác giả
$sql = "SELECT * FROM authors ORDER BY author_name ASC";
$result = mysqli_query($conn, $sql);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Tác Giả</title>
    <style>
        form {
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        form label {
            display: block;
            margin: 10px 0 5px;
        }

        form input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        form button {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
        }

        form button:hover {
            background-color: #555;
        }

        table {
            width: 60%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        header nav ul {
            list-style: none;
            padding: 0;
        }

        header nav ul li {
            display: inline;
            margin-right: 10px;
        }

        header nav ul li a {
            color: #fff;
            text-decoration: none;
        }

        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        h2 {
            text-align: center;
        }

        .notification {
            max-width: 600px;
            margin: 10px auto;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
        }

        .notification.success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }

        .notification.error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }

        .delete-button {
            background-color: #d9534f;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }

        .delete-button:hover {
            background-color: #c9302c;
        }
    </style>
    <script>
        function confirmDelete(authorId) {
            const confirmAction = confirm("Bạn có chắc chắn muốn xóa tác giả này?");
            if (confirmAction) {
                document.getElementById('delete_author_id').value = authorId;
                document.getElementById('delete_form').submit();
            }
        }
    </script>
</head>

<body>
    <header>
        <h1>Quản Lý Thư Viện</h1>
        <nav>
            <ul>
                <li><a href="index.php">Danh Sách Sách</a></li>
                <li><a href="add_book.php">Thêm Sách</a></li>
                <li><a href="add_author.php">Thêm Tác Giả</a></li>
                <li><a href="add_category.php">Thêm Thể Loại</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Thêm Tác Giả</h2>
        <?php if ($notification): ?>
            <div class="notification <?php echo $notification_type; ?>">
                <p><?php echo $notification; ?></p>
            </div>
        <?php endif; ?>

        <form method="post">
            <label for="author_name">Tên Tác Giả:</label>
            <input type="text" id="author_name" name="author_name" required>
            <button type="submit">Thêm Tác Giả</button>
        </form>

        <h2>Danh Sách Tác Giả</h2>
        <table>
            <tr>
                <th>Tên Tác Giả</th>
                <th>Xóa</th>
            </tr>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["author_name"]) . "</td>";
                echo "<td>";
                echo "<button class='delete-button' onclick='confirmDelete(" . $row["id"] . ")'>Xóa</button>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>

        <form id="delete_form" method="post">
            <input type="hidden" name="delete_author_id" id="delete_author_id">
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Thư viện</p>
    </footer>

    
</body>

</html>
