<?php
include 'database/db_connect.php';

/* ================= APPLICATION WINDOW ================= */
$result = $conn->query("SELECT start_date, end_date FROM application_settings WHERE id = 1");
$settings = $result->fetch_assoc();

$today = date('Y-m-d');
$editable = ($today >= $settings['start_date'] && $today <= $settings['end_date']);

/* ================= STUDENT IDENTIFIER ================= */
$student_email = 'student@example.com';

/* ================= LOAD APPLICATION ================= */
$stmt = $conn->prepare("SELECT * FROM hostel_applications WHERE student_email = ?");
$stmt->bind_param("s", $student_email);
$stmt->execute();
$application = $stmt->get_result()->fetch_assoc();

$distance_km = $application['distance_km'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Hostel Application</title>
 <link rel="stylesheet" href="apply.css" />


</head>
<body>

<div class="container">

<h1>Hostel Application</h1>
<p>Application Window:
  <strong><?= $settings['start_date'] ?></strong> –
  <strong><?= $settings['end_date'] ?></strong>
</p>

<?php if ($editable): ?>
  <p class="status-open">✅ Application window is OPEN</p>
<?php else: ?>
  <p class="status-closed">🔒 Application window is CLOSED</p>
<?php endif; ?>

<!-- Steps -->
<div class="steps">
  <div class="step active" onclick="showStep(1)">Personal Info</div>
  <div class="step" onclick="showStep(2)">Priority Data</div>
  <div class="step" onclick="showStep(3)">Documents</div>
</div>

<form method="POST" action="save_application.php" enctype="multipart/form-data">

<!-- ================= STEP 1 ================= -->
<div class="section active" id="step1">
  <div class="card">
    <h3>Personal Information</h3>
    <div class="form-grid">

      <div><label>Full Name</label><input type="text" name="full_name" value="<?= $application['full_name'] ?? '' ?>" <?= !$editable?'disabled':'' ?>></div>
      <div><label>Register Number</label><input type="text" name="register_number" value="<?= $application['register_number'] ?? '' ?>" <?= !$editable?'disabled':'' ?>></div>

      <div><label>Personal Email</label><input type="email" name="personal_email" value="<?= $application['personal_email'] ?? '' ?>" <?= !$editable?'disabled':'' ?>></div>
      <div><label>Phone</label><input type="text" name="phone" value="<?= $application['phone'] ?? '' ?>" <?= !$editable?'disabled':'' ?>></div>

      <div><label>Gender</label>
        <select name="gender" <?= !$editable?'disabled':'' ?>>
          <option value="">Select</option>
          <option value="Male" <?= ($application['gender']??'')=='Male'?'selected':'' ?>>Male</option>
          <option value="Female" <?= ($application['gender']??'')=='Female'?'selected':'' ?>>Female</option>
          <option value="Other" <?= ($application['gender']??'')=='Other'?'selected':'' ?>>Other</option>
        </select>
      </div>

      <div><label>Department</label><input type="text" name="department" value="<?= $application['department'] ?? '' ?>" <?= !$editable?'disabled':'' ?>></div>
      <div><label>Year / Semester</label><input type="text" name="year_semester" value="<?= $application['year_semester'] ?? '' ?>" <?= !$editable?'disabled':'' ?>></div>
      <div><label>Date of Birth</label><input type="date" name="dob" value="<?= $application['dob'] ?? '' ?>" <?= !$editable?'disabled':'' ?>></div>

      <div><label>Pin Code</label><input type="text" name="pincode" value="<?= $application['pincode'] ?? '' ?>" <?= !$editable?'disabled':'' ?>></div>
      <div><label>Distance (km)</label><input type="text" value="<?= $distance_km ?>" readonly></div>

    </div>
  </div>
</div>

<!-- ================= STEP 2 ================= -->
<div class="section" id="step2">
  <div class="priority-layout">

    <div class="card">
      <h3>Priority Data</h3>
      <label>Annual Family Income (INR)</label>
      <input type="number" name="annual_income" value="<?= $application['annual_income'] ?? '' ?>" <?= !$editable?'disabled':'' ?>>

      <br><br>

      <label>Physical Disability (PWD)</label>
      <select name="pwd_status" <?= !$editable?'disabled':'' ?>>
        <option value="">Select</option>
        <option value="Yes" <?= ($application['pwd_status']??'')=='Yes'?'selected':'' ?>>Yes</option>
        <option value="No" <?= ($application['pwd_status']??'')=='No'?'selected':'' ?>>No</option>
      </select>
    </div>

    <div class="card info-box">
      <h3>How Priority is Calculated</h3>
      <ul>
        <li>Distance from home</li>
        <li>Family income</li>
        <li>PWD weightage</li>
      </ul>
    </div>

  </div>
</div>

<!-- ================= STEP 3 ================= -->
<div class="section" id="step3">
  <div class="card">
    <h3>Documents</h3>

    <label>Income Certificate (PDF)</label>
    <input type="file" name="income_certificate" <?= !$editable?'disabled':'' ?>><br><br>

    <label>PWD Certificate (PDF)</label>
    <input type="file" name="pwd_certificate" <?= !$editable?'disabled':'' ?>><br><br>

    <label>Identity Proof (PDF)</label>
    <input type="file" name="id_proof" <?= !$editable?'disabled':'' ?>>
  </div>
</div>

<div class="actions">
  <button type="button" onclick="prevStep()">Back</button>
  <?php if ($editable): ?>
    <button type="submit">Save Application</button>
  <?php else: ?>
    <button disabled>Editing Disabled</button>
  <?php endif; ?>
</div>

</form>

</div>

<script>
let currentStep = 1;

function showStep(step) {
  currentStep = step;
  document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));

  document.getElementById('step' + step).classList.add('active');
  document.querySelectorAll('.step')[step - 1].classList.add('active');
}

function prevStep() {
  if (currentStep > 1) showStep(currentStep - 1);
}
</script>

</body>
</html>
