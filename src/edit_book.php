<?php
include 'db_connect.php';

$notification = ""; // Biến để lưu thông báo
$notification_type = ""; // Biến để lưu loại thông báo (thành công, lỗi)

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = (int)$_POST['id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author_id = (int)$_POST['author_id'];
    $category_id = (int)$_POST['category_id'];
    $publisher = mysqli_real_escape_string($conn, $_POST['publisher']);
    $publish_year = (int)$_POST['publish_year'];
    $quantity = (int)$_POST['quantity'];

    // Cập nhật thông tin sách
    $sql = "UPDATE books SET 
            title = '$title', 
            author_id = $author_id, 
            category_id = $category_id, 
            publisher = '$publisher', 
            publish_year = $publish_year, 
            quantity = $quantity 
            WHERE id = $book_id";

    if (mysqli_query($conn, $sql)) {
        $notification = "Sách đã được cập nhật thành công.";
        $notification_type = 'success'; // Loại thông báo thành công
    } else {
        $notification = "Lỗi: " . mysqli_error($conn);
        $notification_type = 'error'; // Loại thông báo lỗi
    }
}

// Lấy thông tin sách để điền vào biểu mẫu
if (isset($_GET['id'])) {
    $book_id = (int)$_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM books WHERE id = $book_id");
    $book = mysqli_fetch_assoc($result);

    if (!$book) {
        die("Sách không tồn tại.");
    }
} else if (!isset($_POST['id'])) {
    die("ID sách không được xác định.");
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Sách</title>
    <link rel="stylesheet" href="other/css/edit_book.css">
    <style>
        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
            margin-bottom: 20px;
        }

        header h1 {
            margin: 0;
        }

        nav ul {
            list-style: none;
            padding: 0;
        }

        nav ul li {
            display: inline;
            margin-right: 10px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }

        main {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        select {
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button[type="submit"] {
            background-color: #333;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button[type="submit"]:hover {
            background-color: #555;
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

        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
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
        <h2>Sửa Sách</h2>
        <?php if ($notification): ?>
            <div class="notification <?php echo $notification_type; ?>">
                <p><?php echo $notification; ?></p>
            </div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="id" value="<?php echo $book['id']; ?>">

            <label for="title">Tiêu Đề Sách:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>

            <label for="author_id">Tác Giả:</label>
            <select id="author_id" name="author_id" required>
                <?php
                include 'db_connect.php';
                $result = mysqli_query($conn, "SELECT id, author_name FROM authors");
                while ($row = mysqli_fetch_assoc($result)) {
                    $selected = ($row['id'] == $book['author_id']) ? 'selected' : '';
                    echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['author_name']) . "</option>";
                }
                mysqli_close($conn);
                ?>
            </select>

            <label for="category_id">Thể Loại:</label>
            <select id="category_id" name="category_id" required>
                <?php
                include 'db_connect.php';
                $result = mysqli_query($conn, "SELECT id, category_name FROM categories");
                while ($row = mysqli_fetch_assoc($result)) {
                    $selected = ($row['id'] == $book['category_id']) ? 'selected' : '';
                    echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['category_name']) . "</option>";
                }
                mysqli_close($conn);
                ?>
            </select>

            <label for="publisher">Nhà Xuất Bản:</label>
            <input type="text" id="publisher" name="publisher" value="<?php echo htmlspecialchars($book['publisher']); ?>" required>

            <label for="publish_year">Năm Xuất Bản:</label>
            <input type="number" id="publish_year" name="publish_year" value="<?php echo $book['publish_year']; ?>" min="1000" max="<?php echo date('Y'); ?>" required>

            <label for="quantity">Số Lượng:</label>
            <input type="number" id="quantity" name="quantity" value="<?php echo $book['quantity']; ?>" min="1" required>

            <button type="submit">Cập Nhật Sách</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Thư viện</p>
    </footer>
</body>
</html>
