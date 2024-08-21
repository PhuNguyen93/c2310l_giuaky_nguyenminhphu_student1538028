<?php
include 'db_connect.php';

$notification = "";

// Xử lý yêu cầu xóa sách
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_book_id'])) {
    $delete_book_id = mysqli_real_escape_string($conn, $_POST['delete_book_id']);
    
    // Xóa sách khỏi cơ sở dữ liệu
    $sql_delete = "DELETE FROM books WHERE id = '$delete_book_id'";
    
    if (mysqli_query($conn, $sql_delete)) {
        $notification = "Sách đã được xóa thành công";
    } else {
        $notification = "lỗi";
    }
}

// Xử lý tìm kiếm
$search_term = '';
$where_clause = '';  

if ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['search'])) {
    $search_term = mysqli_real_escape_string($conn, $_POST['search']);
    $where_clause = "WHERE books.title LIKE '%$search_term%' 
                     OR authors.author_name LIKE '%$search_term%'";
}

// Xử lý sắp xếp
$order_by = 'title'; // Mặc định sắp xếp theo tiêu đề
$order_type = 'ASC'; // Mặc định sắp xếp tăng dần

if (isset($_GET['sort_by'])) {
    $order_by = $_GET['sort_by'];
}

if (isset($_GET['order_type']) && in_array($_GET['order_type'], ['ASC', 'DESC'])) {
    $order_type = $_GET['order_type'];
}

// Thiết lập phân trang
$records_per_page = 5; // Số bản ghi mỗi trang
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

// Lấy tổng số bản ghi
$sql_count = "SELECT COUNT(*) as total FROM books 
              INNER JOIN authors ON books.author_id = authors.id 
              INNER JOIN categories ON books.category_id = categories.id 
              $where_clause";
$result_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_records = $row_count['total'];
$total_pages = ceil($total_records / $records_per_page);

// Câu lệnh SQL lấy danh sách sách với phân trang
$sql = "SELECT books.*, authors.author_name, categories.category_name 
        FROM books 
        INNER JOIN authors ON books.author_id = authors.id 
        INNER JOIN categories ON books.category_id = categories.id 
        $where_clause
        ORDER BY $order_by $order_type
        LIMIT $offset, $records_per_page";

$result = mysqli_query($conn, $sql);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Sách</title>
    <style>
        table {
            width: 80%;
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

        form {
            text-align: center;
            margin-bottom: 20px;
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
        .edit-button {
            background-color: #f0ad4e;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }

        .edit-button:hover {
            background-color: #ec971f;
        }
        .pagination {
            text-align: center;
            margin: 20px 0;
        }
        .pagination a {
            margin: 0 5px;
            padding: 5px 10px;
            text-decoration: none;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .pagination a.active {
            background-color: #333;
            color: #fff;
            border-color: #333;
        }
    </style>
    <script>
        function confirmDelete(bookId) {
            const confirmAction = confirm("Bạn có chắc chắn muốn xóa sách này?");
            if (confirmAction) {
                document.getElementById('delete_book_id').value = bookId;
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
        <h2>Danh Sách</h2>

        <form method="post">
            <input type="text" name="search" placeholder="Tìm kiếm theo tiêu đề sách hoặc tác giả" value="<?php echo htmlspecialchars($search_term); ?>">
            <input type="submit" value="Tìm kiếm">
        </form>

        <hr>

        <table>
            <tr>
                <th><a href="?sort_by=title&order_type=<?php echo ($order_by == 'title' && $order_type == 'ASC') ? 'DESC' : 'ASC'; ?>">Tên sách</a></th>
                <th><a href="?sort_by=author_name&order_type=<?php echo ($order_by == 'author_name' && $order_type == 'ASC') ? 'DESC' : 'ASC'; ?>">Tác giả</a></th>
                <th><a href="?sort_by=category_name&order_type=<?php echo ($order_by == 'category_name' && $order_type == 'ASC') ? 'DESC' : 'ASC'; ?>">Thể loại</a></th>
                <th><a href="?sort_by=publisher&order_type=<?php echo ($order_by == 'publisher' && $order_type == 'ASC') ? 'DESC' : 'ASC'; ?>">Nhà xuất bản</a></th>
                <th><a href="?sort_by=publish_year&order_type=<?php echo ($order_by == 'publish_year' && $order_type == 'ASC') ? 'DESC' : 'ASC'; ?>">Năm xuất bản</a></th>
                <th>Số lượng</th>
                <th>Sửa</th>
                <th>Xóa</th>
            </tr>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["title"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["author_name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["category_name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["publisher"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["publish_year"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["quantity"]) . "</td>";
                echo "<td><button class='edit-button' onclick=\"window.location.href='edit_book.php?id=" . $row["id"] . "'\">Sửa</button></td>";
                echo "<td><button class='delete-button' onclick='confirmDelete(" . $row["id"] . ")'>Xóa</button></td>";
                echo "</tr>";
            }
            ?>
        </table>

        <form id="delete_form" method="post">
            <input type="hidden" name="delete_book_id" id="delete_book_id">
        </form>

        <div class="pagination">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = ($i == $current_page) ? 'active' : '';
                echo "<a href='?page=$i&sort_by=$order_by&order_type=$order_type' class='$active'>$i</a>";
            }
            ?>
        </div>

    </main>

    <footer>
        <p>&copy; 2024 Thư viện</p>
    </footer>
</body>

</html>
