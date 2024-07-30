<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    $pageTitle = "Contact Us";
    require_once $rootPath . '/pages/includes/main-pages/header.php';
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h4 class="text-center" >Contact Us</h4>
            <?php
            $smtpStatus = false;
            try {
                $smtpStatus = checkSMTPStatus(SMTP_SERVER, 587, SMTP_USERNAME, SMTP_PASSWORD);
                $smtpStatus = true;
            }
            catch(Exception $e) {
                $smtpStatus = false;
            }
            if (isset($_POST["message"])) {
                $rootPath = $_SERVER['DOCUMENT_ROOT'];
                require_once $rootPath . '/handlers/contact-handler.php';
                if ($emailSent) {
                    echo '<div class="alert alert-success mb-3" role="alert">Email sent successfully.</div>';
                } else {
                    echo '<div class="alert alert-danger mb-3" role="alert">
                            Email could not be sent. Error: ' . $emailError . '</div>';
                }
            } elseif(!$smtpStatus) {
                echo '<div class="alert alert-danger mb-3" role="alert">
                    Sorry for the Inconvience.
                    There is an Error in the Mail Server, message could not be sent for the moment!
                </div>';
            }
            ?>
            <form method="post" action="" id="contactForm">
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    Submit
                </button>
            </form>
        </div>
    </div>
</div>
<?php
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
<script>
    $(document).ready(function() {
        $('#contactForm :input').prop('disabled', <?php echo !$smtpStatus ? 'true' : 'false' ?>);
    });
</script>
