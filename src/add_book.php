<?php
include 'db_connect.php';

$message = ''; // Khởi tạo biến thông báo

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author_id = (int)$_POST['author_id'];
    $category_id = (int)$_POST['category_id'];
    $publisher = mysqli_real_escape_string($conn, $_POST['publisher']);
    $publish_year = (int)$_POST['publish_year'];
    $quantity = (int)$_POST['quantity'];

    // Kiểm tra xem sách đã tồn tại chưa
    $check_sql = "SELECT * FROM books WHERE title = '$title' AND author_id = $author_id";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        $message = "Sách đã tồn tại.";
        $message_type = 'error'; // Thiết lập loại thông báo lỗi
    } else {
        // Thêm sách mới
        $sql = "INSERT INTO books (title, author_id, category_id, publisher, publish_year, quantity) 
                VALUES ('$title', $author_id, $category_id, '$publisher', $publish_year, $quantity)";

        if (mysqli_query($conn, $sql)) {
            $message = "Thêm sách thành công.";
            $message_type = 'success'; // Thiết lập loại thông báo thành công
        } else {
            $message = "Lỗi: " . mysqli_error($conn);
            $message_type = 'error'; // Thiết lập loại thông báo lỗi
        }
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sách</title>
    <style>
        form {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        form label {
            display: block;
            margin: 10px 0 5px;
        }

        form input,
        form select {
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

        header,
        footer {
            text-align: center;
            padding: 10px;
        }

        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px;
            position: relative;
            width: 100%;
            bottom: 0;
        }

        h2 {
            text-align: center;
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
        <h2>Thêm Sách</h2>

        <?php if ($message): ?>
            <div class="notification <?php echo $message_type; ?>">
                <p><?php echo $message; ?></p>
                <?php if ($message_type === 'success'): ?>
                    <a href="index.php">Quay lại danh sách sách</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label for="title">Tiêu Đề Sách:</label>
            <input type="text" id="title" name="title" required>

            <label for="author_id">Tác Giả:</label>
            <select id="author_id" name="author_id" required>
                <?php
                include 'db_connect.php';
                $result = mysqli_query($conn, "SELECT id, author_name FROM authors");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['id'] . "'>" . $row['author_name'] . "</option>";
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
                    echo "<option value='" . $row['id'] . "'>" . $row['category_name'] . "</option>";
                }
                mysqli_close($conn);
                ?>
            </select>

            <label for="publisher">Nhà Xuất Bản:</label>
            <input type="text" id="publisher" name="publisher" required>

            <label for="publish_year">Năm Xuất Bản:</label>
            <input type="number" id="publish_year" name="publish_year" min="1000" max="<?php echo date('Y'); ?>" required>

            <label for="quantity">Số Lượng:</label>
            <input type="number" id="quantity" name="quantity" min="1" required>

            <button type="submit">Thêm Sách</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Thư viện</p>
    </footer>
</body>

</html>
