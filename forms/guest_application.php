<?php
/* Guest Application Form
   This file loads inside modal from forms.php
*/
?>

<style>

h2{
margin-top:0;
color:#1d3d6f;
font-size:22px;
}

.guideline{
font-size:14px;
margin-bottom:15px;
color:#444;
}

.guideline a{
color:#1d3d6f;
font-weight:bold;
text-decoration:none;
}

.guideline a:hover{
text-decoration:underline;
}

.form-grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:15px;
margin-top:10px;
}

.form-group{
display:flex;
flex-direction:column;
}

.form-group label{
font-size:14px;
margin-bottom:5px;
}

.form-group input,
.form-group select,
.form-group textarea{
padding:10px;
border:1px solid #ccc;
border-radius:6px;
font-size:14px;
}

.full-width{
grid-column:span 2;
}

textarea{
resize:none;
}

.agreement{
margin-top:15px;
font-size:14px;
}

.submit-btn{
margin-top:20px;
width:100%;
padding:12px;
background:#1d3d6f;
color:white;
border:none;
border-radius:8px;
font-size:15px;
cursor:pointer;
}

.submit-btn:hover{
background:#162e54;
}

</style>


<h2>Guest Accommodation Request</h2>

<p class="guideline">
Please read the hostel guidelines before submitting your request.
<a href="../uploads/guidelines/guest_guidelines.pdf" target="_blank">
Download Guidelines
</a>
</p>

<form method="POST" action="../guest_module/save_guest_request.php" enctype="multipart/form-data" target=_top>

<div class="form-grid">

<div class="form-group">
<label>Guest Student ID</label>
<input type="text" name="guest_student_id" required>
</div>

<div class="form-group">
<label>Guest Full Name</label>
<input type="text" name="guest_name" required>
</div>

<div class="form-group">
<label>Gender</label>
<select name="gender" required>
<option value="">Select</option>
<option value="Male">Male</option>
<option value="Female">Female</option>
<option value="Other">Other</option>
</select>
</div>

<div class="form-group">
<label>Phone Number</label>
<input type="text" name="phone" required>
</div>

<div class="form-group">
<label>Email Address</label>
<input type="email" name="email" required>
</div>

<div class="form-group">
<label>Requested Hostel</label>
<select name="hostel_id" required>
<option value="">Select Hostel</option>
<option value="1">Ganga Hostel</option>
<option value="2">Yamuna Hostel</option>
<option value="3">Narmada Hostel</option>
<option value="4">Godavari Hostel</option>
<option value="5">Kaveri Hostel</option>
</select>
</div>

<div class="form-group">
<label>Requested Room</label>
<input type="text" name="room_number" placeholder="Example: G101" required>
</div>

<div class="form-group">
<label>Stay From</label>
<input type="date" name="stay_from" required>
</div>

<div class="form-group">
<label>Stay To</label>
<input type="date" name="stay_to" required>
</div>

<div class="form-group full-width">
<label>Upload ID Proof</label>
<input type="file" name="id_proof" required>
</div>

<div class="form-group full-width">
<label>Request Message (Optional)</label>
<textarea name="request_message" rows="3"></textarea>
</div>

</div>

<div class="agreement">
<label>
<input type="checkbox" required>
I confirm that I have read the hostel guidelines and agree to follow all hostel rules and regulations.
</label>
</div>
<label style="display:flex;align-items:center;gap:8px;margin-top:10px;">
<input type="checkbox" name="email_updates" value="1">
Send me email updates about the progress of this request
</label>

<button type="submit" class="submit-btn">
Submit Guest Request
</button>

</form>