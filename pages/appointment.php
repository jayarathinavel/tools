<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    $pageTitle = "Contact Us";
    require_once $rootPath . '/pages/includes/main-pages/header.php';
?>
<link rel="stylesheet" 
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <?php
                if (isset($_POST["appointment_date"])) {
                    $rootPath = $_SERVER['DOCUMENT_ROOT'];
                    require_once $rootPath . '/handlers/appointment/process_booking.php';
                    if ($appointmentDone) {
                        echo '<div class="alert alert-success mb-3" role="alert">' . $successMessage . '</div>';
                    } else {
                        echo '<div class="alert alert-danger mb-3" role="alert">' . $failureMessage . '</div>';
                    }
                }
            ?>
            <h2>Book an Appointment</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="appointment_date">Select Date:</label>
                    <input type="hidden" id="appointment_date" name="appointment_date">
                    <div id="datepickerContainer"></div>
                </div>
                <div class="form-group form-group-session">
                    <label>Session:</label><br>
                    <input type="radio" name="session" value="Morning" required> Morning
                    <input type="radio" name="session" value="Afternoon" required> Afternoon
                    <input type="radio" name="session" value="Evening" required> Evening
                </div>
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="company_details">Company Details:</label>
                    <textarea class="form-control" id="company_details" name="company_details" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="/resources/appointment-booking.js"></script>

<?php
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
