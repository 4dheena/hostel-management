<?php
require_once 'config.php';

$contacts = [];
$db_error = null;

try {
  $conn = get_db_conn();
  $sql = "SELECT role, name, phone FROM contacts ORDER BY id";
  $result = $conn->query($sql);
  while ($row = $result->fetch_assoc()) {
    $contacts[] = $row;
  }
  $conn->close();
} catch (Exception $e) {
  $db_error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ARUVI Hostel - Contact</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f8fafc; margin: 0; }
    nav { background: #fff; display: flex; justify-content: space-between; padding: 20px 50px; box-shadow: 0 1px 6px rgba(0,0,0,0.05); }
    .logo { font-weight: bold; color: #0b3b5a; font-size: 24px; }
    a { text-decoration: none; color: #333; margin-left: 20px; }
    a:hover { color: #0b3b5a; }
    .container { max-width: 900px; margin: 60px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    h2 { color: #0b3b5a; }
    ul { list-style: none; padding: 0; }
    li { margin-bottom: 12px; }
    .phone { color: #ef476f; margin-left: 8px; }
  </style>
</head>
<body>
  <nav>
    <div class="logo">ARUVI</div>
    <div>
      <a href="index.php">Home</a>
      <a href="contact.php" style="color:#0b3b5a;font-weight:bold;">Contact</a>
    </div>
  </nav>

  <div class="container">
    <h2>Hostel Management</h2>
    <p>ARUVI Hostel is managed by experienced and caring staff who ensure the well-being, safety, and discipline of all residents.</p>

    <?php if ($db_error): ?>
      <p style="color:red;">⚠️ Database not ready yet. Once your friend adds the contacts table, this page will show the data automatically.</p>
    <?php elseif (empty($contacts)): ?>
      <p>No contact details available yet.</p>
    <?php else: ?>
      <ul>
        <?php foreach ($contacts as $c): ?>
          <li><strong><?= htmlspecialchars($c['role']) ?>:</strong>
              <?= htmlspecialchars($c['name']) ?>
              <span class="phone">📞 <?= htmlspecialchars($c['phone']) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</body>
</html>
