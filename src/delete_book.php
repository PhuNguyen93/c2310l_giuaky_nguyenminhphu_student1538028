<?php
include 'db_connect.php';

// Lấy ID sách từ tham số URL
if (isset($_GET['id'])) {
    $book_id = (int)$_GET['id'];

    // Xóa sách từ cơ sở dữ liệu
    $sql = "DELETE FROM books WHERE id = $book_id";

    if (mysqli_query($conn, $sql)) {
        echo "Sách đã được xóa thành công. <a href='index.php'>Quay lại danh sách sách</a>";
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    echo "ID sách không được xác định.";
}
?>

<?php
include 'db_connect.php';

// Xử lý khi biểu mẫu được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $author_name = mysqli_real_escape_string($conn, $_POST['author_name']);

    // Kiểm tra trùng lặp tên tác giả
    $check_sql = "SELECT id FROM authors WHERE author_name = '$author_name'";
    $result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($result) > 0) {
        echo "Tác giả với tên này đã tồn tại. Vui lòng kiểm tra lại. <a href='add_author.php'>Quay lại</a>";
    } else {
        // Thêm tác giả vào cơ sở dữ liệu
        $sql = "INSERT INTO authors (author_name, book_numbers) VALUES ('$author_name', 0)";

        if (mysqli_query($conn, $sql)) {
            echo "Tác giả đã được thêm thành công. <a href='index.php'>Quay lại danh sách tác giả</a>";
        } else {
            echo "Lỗi: " . mysqli_error($conn);
        }
    }

    mysqli_close($conn);
}
?>

