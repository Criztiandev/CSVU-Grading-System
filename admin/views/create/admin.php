<?php
session_start();
// kung walang session mag reredirect sa login //

require("../../../configuration/config.php");
require('../../../auth/controller/auth.controller.php');
require('../../../utils/mailer.php');

if (!AuthController::isAuthenticated()) {
    header("Location: ../../../public/login.php");
    exit();
}

// pag meron session mag rerender yung dashboard//
require_once("../../../components/header.php");

// Error and success handlers
$hasError = false;
$hasSuccess = false;
$message = "";

// Create new admin
if (isset($_POST['create-admin'])) {
    $firstName = $dbCon->real_escape_string($_POST['firstName']);
    $middleName = $dbCon->real_escape_string($_POST['middleName']);
    $lastName = $dbCon->real_escape_string($_POST['lastName']);
    $gender = $dbCon->real_escape_string($_POST['gender']);
    $contact = str_replace("-", "", $dbCon->real_escape_string($_POST['contact']));
    $birthday = $dbCon->real_escape_string($_POST['birthday']);
    $email = filter_var($dbCon->real_escape_string($_POST['email']), FILTER_VALIDATE_EMAIL);

    if (!$email) {
        $hasError = true;
        $hasSuccess = false;
        $message = "Please enter a valid email address";
    } else if (!str_ends_with($email, "@cvsu.edu.ph")) {
        $hasError = true;
        $hasSuccess = false;
        $message = "Please use your <strong>@cvsu.edu.ph</strong> email address.";
    } else if (!str_starts_with($contact, "09") || strlen($contact) != 11) {
        $hasError = true;
        $hasSuccess = false;
        $message = "Please enter a valid contact number. It should start with <strong>09</strong> and has <strong>11 digits</strong>.";
    } else if ($dbCon->query("SELECT * FROM userdetails WHERE email = '$email'")->num_rows > 0) {
        $hasError = true;
        $hasSuccess = false;
        $message = "A user with that email address already exists!";
    } else {
        // Auto generate password using uuid to prevent collision and with at least 8 characters    
        // $password = substr(md5(uniqid()), 0, 8);

        // randomly insert at least 1-3 special character to the password
        // $specialChars = ['!', '@', '#', '$', '&', '_', '?'];
        // $specialCharCount = rand(1, 3);
        // for ($i = 0; $i < $specialCharCount; $i++) {
        //     $password = substr_replace($password, $specialChars[rand(0, count($specialChars) - 1)], rand(0, strlen($password) - 1), 0);
        // }

        // Default password
        $password = constant("USER_DEFAULT_PASSWORD");

        $insertAdminQuery = "INSERT INTO userdetails(firstName, middleName, lastName, gender, contact,  birthday, email, password, roles) VALUES(
            '$firstName',
            '$middleName',
            '$lastName',
            '$gender',
            '$contact',
            '$birthday',
            '$email',
            '" . crypt($password, '$6$Crypt$') . "',
            'admin'
        )";

        $insertAdminResult = $dbCon->query($insertAdminQuery);

        if ($insertAdminResult) {
            // get the email template
            $template = getNewAccountMailTemplate(
                $email,
                "$firstName $middleName $lastName",
                $password,
                "Welcome to CvSU Grading System",
                constant('APP_URL'),
                "We've sent you this email to notify you that we have created your account and you may login using this email address and this generated password. Under no circumstances are you to share this password to anyone. You may change your password once you've logged in.",
                date('Y')
            );

            // send the email
            sendMail($email, 'CvSU Grading System', $template);

            $hasError = false;
            $hasSuccess = true;
            $message = "Admin successfully created! The password has been sent to its email address.";

            // unset entered values
            unset($firstName);
            unset($middleName);
            unset($lastName);
            unset($gender);
            unset($contact);
            unset($birthday);
            unset($email);
            unset($password);
        } else {
            $hasError = true;
            $hasSuccess = false;
            $message = "Something went wrong. Please try again later.";
        }
    }
}
?>

<main class="w-screen h-screen overflow-scroll flex">
    <?php require_once("../../layout/sidebar.php")  ?>
    <section class="w-full px-4">
        <?php require_once("../../layout/topbar.php") ?>

        <div class="flex flex-col gap-4 justify-center items-center md:w-[700px] mx-auto">
            <div class="flex justify-center items-center flex-col w-full gap-4">
                <h2 class="text-[38px] font-bold mb-8">Create Admin</h2>
                <form class="flex flex-col gap-4  px-[32px]  w-full mb-auto" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">

                    <?php if ($hasError) { ?>
                        <div role="alert" class="alert alert-error mb-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><?= $message ?></span>
                        </div>
                    <?php } ?>

                    <?php if ($hasSuccess) { ?>
                        <div role="alert" class="alert alert-success mb-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><?= $message ?></span>
                        </div>
                    <?php } ?>

                    <!-- Name -->
                    <div class="grid md:grid-cols-3 gap-4">
                        <label class="flex flex-col gap-2">
                            <span class="font-bold text-[18px]">First Name</span>
                            <input class="input input-bordered" name="firstName" placeholder="Enter First name" value="<?= $firstName ?? "" ?>" required />
                        </label>

                        <label class="flex flex-col gap-2">
                            <span class="font-bold text-[18px]">Middle Name</span>
                            <input class="input input-bordered" name="middleName" placeholder="Enter Middle name" value="<?= $middleName ?? "" ?>" />
                        </label>
                        <label class="flex flex-col gap-2">
                            <span class="font-bold text-[18px]">Last Name</span>
                            <input class="input input-bordered" name="lastName" required placeholder="Enter Last name" value="<?= $lastName ?? "" ?>" required />
                        </label>
                    </div>

                    <!-- Details -->
                    <div class="grid md:grid-cols-3 gap-4">
                        <label class="flex flex-col gap-2">
                            <span class="font-bold text-[18px]">Sex</span>
                            <select class="select select-bordered" name="gender" required>
                                <option value="" selected disabled>Select Sex</option>
                                <option value="male" <?php if (isset($gender) && strtolower($gender) == 'male') { ?> selected <?php } ?>>Male</option>
                                <option value="female" <?php if (isset($gender) && strtolower($gender) == 'female') { ?> selected <?php } ?>>Female</option>
                            </select>
                        </label>

                        <label class="flex flex-col gap-2" x-data>
                            <span class="font-bold text-[18px]">Contact</span>
                            <input x-mask="9999-999-9999" @input="enforcePrefix" type="tel" class="input input-bordered" name="contact" placeholder="0912-345-6789" value="<?= $contact ?? "" ?>" required />
                        </label>

                        <label class="flex flex-col gap-2">
                            <span class="font-bold text-[18px]">Birthdate</span>
                            <input class="input input-bordered" type="date" name="birthday" value="2000-01-01" required />
                        </label>
                    </div>



                    <!-- Account -->
                    <label class="flex flex-col gap-2">
                        <span class="font-bold text-[18px]">Email</span>
                        <input type="email" placeholder="Enter email" class="input input-bordered w-full" type="email" name="email" value="<?= $email ?? "" ?>" required />
                    </label>

                    <!-- Actions -->
                    <div class="grid grid-cols-2 gap-4">
                        <a href="../manage-admin.php" class="btn btn-error text-lg text-semibold text-white">Cancel</a>
                        <button class="btn btn-success text-lg text-semibold text-white" name="create-admin">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

</main>

<script>
    function enforcePrefix(e) {
        let currentValue = e.target.value;

        if (!currentValue.startsWith("09")) {
            e.target.value = "09" + currentValue.substring(2);
        }

        console.log("HELO")
    }
</script>