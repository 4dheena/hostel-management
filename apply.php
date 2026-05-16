<?php
require_once 'database/db_connect.php';

date_default_timezone_set('Asia/Kolkata');

/* ================= APPLICATION WINDOW ================= */

$settingsQuery = $conn->query("
SELECT start_date, end_date, edit_start, edit_end
FROM application_settings
WHERE id = 1
");

$settings = $settingsQuery->fetch_assoc();

$now = date('Y-m-d H:i:s');

$appWindowOpen = (
    $now >= $settings['start_date'] &&
    $now <= $settings['end_date']
);

$editWindowOpen = (
    !empty($settings['edit_start']) &&
    !empty($settings['edit_end']) &&
    $now >= $settings['edit_start'] &&
    $now <= $settings['edit_end']
);


/* ================= IDENTIFY APPLICATION ================= */

/* Student enters email in form → used to reload saved application */

$student_email = $_POST['personal_email'] ?? '';

/* ================= LOAD EXISTING APPLICATION ================= */

$application = null;

if (!empty($student_email)) {

    $stmt = $conn->prepare("
        SELECT *
        FROM hostel_applications
        WHERE personal_email = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $student_email);
    $stmt->execute();

    $application = $stmt->get_result()->fetch_assoc();
}


/* ================= SUBMISSION STATUS ================= */

$submitted = !empty($application['submitted_at']);

$editable = (
    ($appWindowOpen && !$submitted) ||
    ($editWindowOpen && $submitted)
);


/* ================= PASSWORD CHECK ================= */

$password_set = false;

if (!empty($student_email)) {

    $pwdStmt = $conn->prepare("
        SELECT password_hash
        FROM hostel_applications
        WHERE personal_email = ?
    ");

    $pwdStmt->bind_param("s", $student_email);
    $pwdStmt->execute();

    $pwdData = $pwdStmt->get_result()->fetch_assoc();

    if (!empty($pwdData['password_hash'])) {
        $password_set = true;
    }
}


/* ================= DISTANCE ================= */

$distance_km = $application['distance_km'] ?? '';

?>
<!DOCTYPE html> 
<html lang="en"> 
<head> 
<meta charset="UTF-8"> 
<title>Hostel Application</title> 
<link rel="stylesheet" href="assets/css/apply.css"> 
</head> 
<body> 

<div class="container"> 

<h1>Hostel Application</h1> 
<p>Application Window: 
<strong><?= $settings['start_date'] ?></strong> – 
<strong><?= $settings['end_date'] ?></strong> 
</p> 

<?php if ($appWindowOpen): ?>
  <p class="status-open">✅ Application window is OPEN</p>
<?php else: ?>
  <p class="status-closed">🔒 Application window is CLOSED</p>
<?php endif; ?>

<?php if ($editWindowOpen): ?>
  <p class="status-info">✏️ Edit window is OPEN for submitted applications</p>
<?php endif; ?>

<?php if ($submitted && !$editable): ?>
  <p class="status-info">
    📌 Your application has been submitted and cannot be edited at this time.
  </p>
<?php endif; ?>


<div class="steps"> 
  <div class="step active" onclick="showStep(1)">Personal Info</div> 
  <div class="step" onclick="showStep(2)">Priority Data</div> 
  <div class="step" onclick="showStep(3)">Documents</div> 
  <div class="step" onclick="showStep(4)">Account Security</div> 
</div> 

<form method="POST" action="save_application.php" enctype="multipart/form-data" onsubmit="return validatePassword();"
onkeydown="return event.key !=='Enter';"> 

<!-- ================= STEP 1 ================= --> 
<div class="section active" id="step1"> 
  <div class="card"> 
    <h3>Personal Information</h3> 
    <div class="form-grid"> 

      <div> 
        <label>Full Name</label> 
        <input type="text" required name="full_name" value="<?= htmlspecialchars($application['full_name'] ?? '') ?>" <?= !$editable ? 'readonly' : '' ?>> 
      </div>

      <div> 
        <label>Register Number</label> 
        <input type="text" required name="register_number" value="<?= htmlspecialchars($application['register_number'] ?? '') ?>" <?= !$editable ? 'readonly' : '' ?>> 
      </div> 

      <div> 
        <label>Email</label> 
        <input type="email" required name="personal_email" value="<?= htmlspecialchars($application['personal_email'] ?? '') ?>" <?= !$editable ? 'readonly' : '' ?>> 
      </div> 

      <div> 
        <label>Phone</label> 
        <input type="text" required name="phone" value="<?= htmlspecialchars($application['phone'] ?? '') ?>" <?= !$editable ? 'readonly' : '' ?>> 
      </div> 

      <div> 
        <label>Gender</label> 
        <select name="gender" <?= !$editable ? 'disabled' : '' ?>> 
          <option value="">Select</option> 
          <option value="Male" <?= ($application['gender'] ?? '')==='Male'?'selected':'' ?>>Male</option> 
          <option value="Female" <?= ($application['gender'] ?? '')==='Female'?'selected':'' ?>>Female</option> 
          <option value="Other" <?= ($application['gender'] ?? '')==='Other'?'selected':'' ?>>Other</option> 
        </select> 
      </div> 

      <div> 
        <label>Department</label> 
        <input type="text" required name="department" value="<?= htmlspecialchars($application['department'] ?? '') ?>" <?= !$editable ? 'readonly' : '' ?>> 
      </div> 

      <div> 
        <label>Semester</label> 
        <input type="text" required name="year_semester" value="<?= htmlspecialchars($application['year_semester'] ?? '') ?>" <?= !$editable ? 'readonly' : '' ?>> 
      </div> 

      <div> 
        <label>Date of Birth</label> 
        <input type="date" required name="dob" value="<?= $application['dob'] ?? '' ?>" <?= !$editable ? 'readonly' : '' ?>> 
      </div> 

      <div> 
        <label>Pin Code</label> 
       <input type="text" required name="pincode" maxlength="6" oninput="fetchDistance()" >
      </div> 

      <div> 
        <label>Distance (km)</label> 
        <input type="text" id="distance_display" value="<?= htmlspecialchars($distance_km) ?>" readonly>
        <input type="hidden" name="distance_km" id="distance_km" value="<?= htmlspecialchars($distance_km) ?>">
      </div> 

    </div> 
  </div> 
</div> 

<!-- ================= STEP 2 ================= --> 
<div class="section" id="step2"> 
  <div class="priority-layout"> 

    <div class="card"> 
      <h3>Priority Data</h3> 

      <label>Annual Family Income (INR)</label> 
      <input type="number" required name="annual_income" value="<?= htmlspecialchars($application['annual_income'] ?? '') ?>" <?= !$editable ? 'readonly' : '' ?>> 

      <br><br> 

      <label>Physical Disability (PWD)</label> 
      <select name="pwd_status" id="pwd_status" onchange="toggleDisability()" <?= !$editable ? 'disabled' : '' ?>> 
        <option value="">Select</option> 
        <option value="No" <?= ($application['pwd_status'] ?? '')==='No'?'selected':'' ?>>No</option> 
        <option value="Yes" <?= ($application['pwd_status'] ?? '')==='Yes'?'selected':'' ?>>Yes</option>  
      </select> 

      <div id="disabilityBox"> 
        <label>Disability Percentage (%)</label> 
        <input type="number" name="disability_percentage" min="1" max="100" placeholder="Enter percentage" value="<?= htmlspecialchars($application['disability_percentage'] ?? '') ?>" <?= !$editable ? 'readonly' : '' ?>> 
      </div> 
    </div> 

    <div class="card info-box"> 
      <h3>How Priority is Calculated</h3> 
      <ul> 
        <li>Distance from home</li> 
        <li>Family income</li> 
        <li>PWD & disability percentage</li> 
      </ul> 
    </div> 

  </div> 
</div> 

<!-- ================= STEP 3 ================= --> 
<div class="section" id="step3"> 
  <div class="card"> 
    <h3>Documents</h3> 
    <p id=docinfo>Note: Documents may be submitted at the time of application or during admission. Any discrepancy between the submitted documents and the information provided in the application may result in appropriate action as per institutional rules.</p>

    <label>Income Certificate (PDF) </label> 
    <input type="file" name="income_certificate" <?= !$editable?'disabled':'' ?>><br><br> 

    <label>PWD Certificate (if applicable)</label> 
    <input type="file" name="pwd_certificate" <?= !$editable?'disabled':'' ?>><br><br> 

    <label>Identity Proof (PDF)</label> 
    <input type="file" name="id_proof" <?= !$editable?'disabled':'' ?>> 
  </div> 
</div> 

<!-- ================= STEP 4 ================= --> 
<div class="section" id="step4"> 
  <div class="card security-card"> 
    <h3>🔐 Account Security</h3> 

    <?php if ($password_set): ?> 
      <div class="password-set"> 
        ✅ Password already set for this account. 
        <p class="password-note">Contact admin to reset if required.</p> 
      </div> 
    <?php else: ?> 
      <div class="form-grid"> 
        <div> 
          <label>New Password</label> 
          <input type="password" required name="new_password" <?= !$editable?'disabled':'' ?>> 
        </div> 

        <div> 
          <label>Confirm Password</label> 
          <input type="password" required name="confirm_password" <?= !$editable?'disabled':'' ?>> 
        </div> 
      </div> 

      <p class="password-hint">
        Password must contain atleast 8 characters , including uppercase ,
        lowercase ,number and special characters.
      </p> 
    <?php endif; ?> 
  </div> 

  <!-- ACTION BUTTONS MOVED HERE -->
  <div class="actions"> 
    <button type="button" onclick="prevStep()">Back</button> 

    <?php if ($editable): ?>

      <button type="submit"
        name="action"
        value="<?= $submitted ? 'update' : 'submit' ?>">
      Submit
      </button>

    <?php elseif ($submitted): ?>

      <button disabled>✅ Application Submitted</button>

    <?php else: ?>

      <button disabled>🔒 Editing Disabled</button>

    <?php endif; ?>
  </div>

</div>
 

</form> 
</div> 

<script> 
let currentStep = 1; 

function showStep(step){ 
  if(step<1 || step>4) return; 
  currentStep = step; 
  document.querySelectorAll('.section').forEach(s=>s.classList.remove('active')); 
  document.querySelectorAll('.step').forEach(s=>s.classList.remove('active')); 
  document.getElementById('step'+step).classList.add('active'); 
  document.querySelectorAll('.step')[step-1].classList.add('active'); 
} 

function prevStep(){ 
  showStep(currentStep-1); 
} 

function toggleDisability(){ 
  const pwd = document.getElementById('pwd_status').value; 
  const box = document.getElementById('disabilityBox'); 
  box.style.display = (pwd === 'Yes') ? 'block' : 'none'; 
} 

document.addEventListener("DOMContentLoaded", toggleDisability); 
</script> 

<script> 
function validatePassword() { 
  const pwd = document.querySelector('input[name="new_password"]'); 
  const confirm = document.querySelector('input[name="confirm_password"]'); 

  if (!pwd || pwd.value === '') return true; 

  const password = pwd.value; 

  if ( 
    password.length < 8 || 
    !/[A-Z]/.test(password) || 
    !/[a-z]/.test(password) || 
    !/[0-9]/.test(password) || 
    !/[\W_]/.test(password) 
  ) { 
    alert( 
      "Password must be at least 8 characters and include:\n" + 
      "• Uppercase letter\n" + 
      "• Lowercase letter\n" + 
      "• Number\n" + 
      "• Special character" 
    ); 
    return false; 
  } 

  if (password !== confirm.value) { 
    alert("Passwords do not match."); 
    return false; 
  } 

  return true; 
} 
</script> 

<script>
function fetchDistance() {
  const pin = document.querySelector('input[name="pincode"]').value;

  // Only run when pincode is complete
  if (pin.length !== 6) {
    document.getElementById('distance_display').value = '';
    document.getElementById('distance_km').value = '';
    return;
  }

  fetch("get_distance.php?pincode=" + pin)
    .then(res => res.text())
    .then(distance => {
      if (distance) {
        document.getElementById('distance_display').value = distance + " km";
        document.getElementById('distance_km').value = distance;
      }
    })
    .catch(() => {
      document.getElementById('distance_display').value = 'Error';
    });
}
</script>


</body> 
</html>