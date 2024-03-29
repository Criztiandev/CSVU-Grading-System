<?php
// session_start();
// kung walang session mag reredirect sa login //

require("../../../configuration/config.php");
require '../../../auth/controller/auth.controller.php';

if (!AuthController::isAuthenticated()) {
    header("Location: ../../../public/login");
    exit();
}

// pag meron session mag rerender yung dashboard//
require_once("../../../components/header.php");
?>


<main class="h-[95%] overflow-x-hidden flex">
    <?php require_once("../../layout/sidebar.php")  ?>
    <section class="border w-full px-4">
        <?php require_once("../../layout/topbar.php") ?>
        <div class="px-4 flex justify-between flex-col gap-4">

            <!-- Table Header -->
            <div class="flex justify-between items-center">
                <!-- Table Header -->
                <div class="flex justify-between items-center">
                    <h1 class="text-[32px] font-bold">Activities</h1>
                </div>

                <div class="flex gap-4">
                    <label class="flex flex-col gap-2">
                        <select class="select select-bordered">
                            <option value="">Select Term</option>
                            <option value="female">Prelim</option>
                            <option value="male">Midterm</option>
                            <option value="male">Finals</option>
                        </select>
                    </label>
                    <a href="../dashboard.php" class="btn">Back</a>
                </div>

            </div>

            <!-- Table Content -->
            <div class="overflow-x-hidden border border-gray-300 rounded-md" style="height: calc(100vh - 250px)">
                <table class="table table-md table-pin-rows table-pin-cols ">
                    <thead>
                        <tr>
                            <th></th>
                            <td>Name</td>
                            <td>Term</td>
                            <td>Subject</td>
                            <td>Score</td>
                            <td>Status</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>20</th>
                            <td>Activity 1</td>
                            <td>Prelim</td>
                            <td>DSA-101</td>
                            <td>100</td>
                            <td>
                                <div class="badge p-4 text-base bg-green-400 font-semibold">
                                    Passed
                                </div>
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-between items-center">
                <button class="btn text-[24px]">
                    <i class='bx bx-chevron-left'></i>
                </button>
                <button class="btn">Page 22</button>
                <button class="btn text-[24px]">
                    <i class='bx bxs-chevron-right'></i>
                </button>
            </div>
        </div>
    </section>
</main>