<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];
$pageTitle = "Change Theme";
require_once $rootPath . '/config/config.php';
isLoggedIn();
require_once $rootPath . '/pages/includes/admin-pages/header.php';
?>
<h4 class="text-center"> Help </h4>
<ul>
    <li>
        <p><strong>Contact Form Submissions</strong>:<br />
            This link takes you to a page where you can view submissions from the contact form. You can see
            the messages and information sent by users through the contact form. </p>
    </li>
    <li>
        <p><strong>Change Theme</strong>:<br />
            The &quot;Change Theme&quot; link allows you to customize the visual appearance of your Web application. You
            can select from different themes to change the overall look and feel. Choose a theme that
            suits your preferences or enhances user experience.</p>
    </li>
    <li>
        <p><strong>Reset Password</strong>:<br />
            This link leads to a page where you can reset your admin password. If you&#39;ve forgotten your password or
            need to update it for security reasons, use this option. Follow the on-screen instructions to reset your
            password securely.</p>
    </li>
    <li>
        <p><strong>Logout</strong>:<br />
            Clicking the &quot;Logout&quot; link will log you out of your admin session. It&#39;s important to log out
            when you&#39;re done using the admin dashboard, especially if you&#39;re using a shared computer or public
            device, to ensure the security of your account.</p>
    </li>
</ul>

<?php
require_once $rootPath . '/pages/includes/admin-pages/footer.php';
?>