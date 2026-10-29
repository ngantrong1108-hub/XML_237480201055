<?php
$xmlFile = 'contacts.xml';

// Tạo file contacts.xml mẫu nếu chưa tồn tại
if (!file_exists($xmlFile)) {
    $defaultXml = '<?xml version="1.0" encoding="UTF-8"?>
<directory>
    <contact id="1">
        <name>Nguyễn Văn A</name>
        <phone>0901234567</phone>
        <email>nva@gmail.com</email>
        <group>Công việc</group>
    </contact>
    <contact id="2">
        <name>Trần Thị B</name>
        <phone>0987654321</phone>
        <email>ttb@gmail.com</email>
        <group>Gia đình</group>
    </contact>
</directory>';
    file_put_contents($xmlFile, $defaultXml);
}

$dom = new DOMDocument();
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->load($xmlFile);

$message = "";

// Xử lý khi người dùng gửi form thêm liên hệ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $group = trim($_POST['group']);

    if (!empty($name) && !empty($phone) && !empty($email) && !empty($group)) {
        $directory = $dom->getElementsByTagName('directory')->item(0);

        // Tìm ID tiếp theo tự động
        $contacts = $dom->getElementsByTagName('contact');
        $newId = 1;
        foreach ($contacts as $contact) {
            $id = (int)$contact->getAttribute('id');
            if ($id >= $newId) {
                $newId = $id + 1;
            }
        }

        // Tạo phần tử contact mới
        $newContact = $dom->createElement('contact');
        $newContact->setAttribute('id', $newId);

        $newContact->appendChild($dom->createElement('name', htmlspecialchars($name)));
        $newContact->appendChild($dom->createElement('phone', htmlspecialchars($phone)));
        $newContact->appendChild($dom->createElement('email', htmlspecialchars($email)));
        $newContact->appendChild($dom->createElement('group', htmlspecialchars($group)));

        $directory->appendChild($newContact);
        $dom->save($xmlFile);

        $message = "Thêm liên hệ thành công!";
    }
}

// Xử lý khi người dùng yêu cầu tải file XML
if (isset($_GET['action']) && $_GET['action'] === 'download') {
    header('Content-Description: File Transfer');
    header('Content-Type: text/xml; charset=utf-8');
    header('Content-Disposition: attachment; filename="contacts.xml"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($xmlFile));
    readfile($xmlFile);
    exit;
}

// Lấy danh sách nhóm từ XML để lọc
$contacts = $dom->getElementsByTagName('contact');
$groups = [];
$contactData = [];

foreach ($contacts as $contact) {
    $g = $contact->getElementsByTagName('group')->item(0)->textContent;
    $groups[$g] = true;

    $contactData[] = [
        'id' => $contact->getAttribute('id'),
        'name' => $contact->getElementsByTagName('name')->item(0)->textContent,
        'phone' => $contact->getElementsByTagName('phone')->item(0)->textContent,
        'email' => $contact->getElementsByTagName('email')->item(0)->textContent,
        'group' => $g
    ];
}
$uniqueGroups = array_keys($groups);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Ứng dụng Danh bạ XML (PHP Backend)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f4f4f9;
            max-width: 650px;
        }

        .section {
            background: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-group label {
            display: inline-block;
            width: 100px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            padding: 6px 10px;
            font-size: 0.95em;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 220px;
        }

        button {
            padding: 8px 15px;
            font-size: 0.95em;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-add {
            background: #2ecc71;
            color: white;
            margin-top: 5px;
        }

        .btn-download {
            background: #e67e22;
            color: white;
            float: right;
            text-decoration: none;
            display: inline-block;
            padding: 8px 15px;
            font-size: 0.95em;
            border-radius: 4px;
            font-weight: bold;
        }

        .card {
            background: white;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .name {
            font-weight: bold;
            color: #2c3e50;
            font-size: 1.1em;
            margin-bottom: 5px;
        }

        .badge {
            background: #3498db;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8em;
        }

        .empty {
            color: #7f8c8d;
            font-style: italic;
        }

        .alert {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>

    <h2>Danh bạ Cá nhân XML (PHP)</h2>

    <?php if (!empty($message)): ?>
        <div class="alert"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- Form thêm liên hệ mới -->
    <div class="section">
        <h3>➕ Thêm liên hệ mới</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label>Họ và Tên:</label>
                <input type="text" name="name" required placeholder="Nhập tên...">
            </div>
            <div class="form-group">
                <label>Số điện thoại:</label>
                <input type="tel" name="phone" required placeholder="Nhập SĐT...">
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required placeholder="Nhập email...">
            </div>
            <div class="form-group">
                <label>Nhóm:</label>
                <input type="text" name="group" required placeholder="Công việc, Gia đình...">
            </div>
            <button type="submit" class="btn-add">Thêm vào Danh bạ</button>
        </form>
    </div>

    <!-- Thanh lọc & Nút Download -->
    <div class="section clearfix">
        <label for="group-select"><strong>Lọc nhóm: </strong></label>
        <select id="group-select" onchange="filterContacts()">
            <option value="ALL">Tất cả nhóm</option>
            <?php foreach ($uniqueGroups as $g): ?>
                <option value="<?php echo htmlspecialchars($g); ?>"><?php echo htmlspecialchars($g); ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Nút tải file XML về máy -->
        <a href="?action=download" class="btn-download">📥 Tải file contacts.xml</a>
    </div>

    <!-- Danh sách danh bạ -->
    <div id="contact-list"></div>

    <script>
        // Dữ liệu danh bạ được truyền từ PHP sang JavaScript
        const contactsData = <?php echo json_encode($contactData, JSON_UNESCAPED_UNICODE); ?>;

        function filterContacts() {
            const selectedGroup = document.getElementById("group-select").value;
            let htmlOutput = "";

            contactsData.forEach(contact => {
                if (selectedGroup === "ALL" || contact.group === selectedGroup) {
                    htmlOutput += `
                        <div class="card" data-group="${contact.group}">
                            <div class="name">${contact.name} <span class="badge">${contact.group}</span></div>
                            <div>📞 SĐT: ${contact.phone}</div>
                            <div>✉️ Email: ${contact.email}</div>
                        </div>
                    `;
                }
            });

            document.getElementById("contact-list").innerHTML =
                htmlOutput || `<p class="empty">Không có liên hệ nào trong nhóm này.</p>`;
        }

        // Khởi tạo hiển thị ban đầu
        filterContacts();
    </script>
</body>

</html>