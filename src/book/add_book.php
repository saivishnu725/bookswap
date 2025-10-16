<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
        exit();
    }
    ?>
    <h1>Add a New Book</h1>
    <form action="../server/add_book_process.php" method="POST" enctype="multipart/form-data">

    <label for="title">Title:</label>
    <input type="text" id="title" name="title" value="Sample Book Title" required><br>

    <label for="author">Author:</label>
    <input type="text" id="author" name="author" value="John Doe" required><br>

    <label for="description">Give a detailed description of the book</label>
    <textarea id="description" name="description" rows="4" cols="50"
        required>This is a sample description of the book.</textarea><br>

    <label for="condition">Condition:</label>
    <select id="condition" name="condition" required>
        <option value="NEW" selected>New</option>
        <option value="GOOD">Good</option>
        <option value="FAIR">Fair</option>
        <option value="POOR">Poor</option>
    </select><br>

    <label for="year_of_purchase">Year of Purchase:</label>
    <input type="number" id="year_of_purchase" name="year_of_purchase" min="1900" max="2025" value="2022" required><br>

    <label for="cost_at_purchase">Cost at Purchase:</label>
    <input type="number" step="5" id="cost_at_purchase" name="cost_at_purchase" value="1200" required><br>

    <label for="current_selling_price">Current Selling Price:</label>
    <input type="number" step="5" id="current_selling_price" name="current_selling_price" value="1000" required><br>

    <label for="negotiable">Negotiable:</label>
    <select id="negotiable" name="negotiable" required>
        <option value="YES" selected>Yes</option>
        <option value="NO">No</option>
    </select><br>

    <!-- <label for="image">Upload Image:</label>
    <input type="file" id="image" name="image" accept="image/*"><br> -->

    <input type="submit" value="Add Book">

</body>

</html>