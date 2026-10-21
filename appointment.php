<?php


require_once "backend/config/session.php";

$avatar_image = "img 1.jpg";

$dashboards = [
    "patient" => "patient/dashboard.php",
    "doctor"  => "doctor/dashboard.php",
    "admin"   => "admin/dashboard.php"
];

$user_type = strtolower((string) ($_SESSION["user_type"] ?? ""));

$profile_link = $dashboards[$user_type] ?? "login.php";




if (
    !isset($_SESSION["user_type"]) ||
    $_SESSION["user_type"] !== "patient" ||
    !isset($_SESSION["patient_id"])
) {
    header("Location: login.php?redirect=appointment.php");
    exit;
}
?>




<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>fctCare - Book Appointment</title>

    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpeg" href="logo.jpeg">

    
<style>

    .form {
        margin-bottom: 20px;
        text-align: left;
    }

    /* Labels - Black */
    .form label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #000000;
    }

    /* Input / Select / Textarea */
    .form_control {
        width: 100%;
        padding: 10px;
        border: 1px solid #00a896;
        border-radius: 5px;
        color: #000000;
        box-sizing: border-box;
        font-size: 14px;
        height: 42px;
        background: #ffffff;
    }

    .form textarea.form_control {
        height: auto;
    }

    /* Dropdown text */
    .form_control option {
        color: #000000;
        background: #ffffff;
    }

    /* Dropdown selected value */
    select.form_control {
        color: #000000;
        background-color: #ffffff;
    }

    /* Phone Number */
    .phone_num {
        display: flex;
        width: 100%;
        border: 1px solid #00a896;
        border-radius: 5px;
        overflow: hidden;
        height: 42px;
        box-sizing: border-box;
        background: #ffffff;
    }

    .country_code {
        padding: 0 15px;
        background: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        border-right: 1px solid #00a896;
        color: #000000;
        font-size: 14px;
        white-space: nowrap;
        height: 100%;
        box-sizing: border-box;
    }

    .profile-btn {
        display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white !important;
            
    
            background: rgba(5, 123, 115, 0.55);
            padding: 5px 16px 5px 5px;
            border-radius: 50px;
    }

    .profile-btn img {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        object-fit: cover;
    }   

    .phone_control {
        flex: 1;
        padding: 10px;
        border: none;
        outline: none;
        color: #000000;
        font-size: 14px;
        background: transparent;
        width: 100%;
        height: 100%;
        box-sizing: border-box;
    }

    .phone_num:focus-within {
        border-color: #00a896;
        box-shadow: 0 0 0 3px rgba(0, 168, 150, 0.1);
    }

    .form_control:focus {
        outline: none;
        border-color: #00a896;
        box-shadow: 0 0 0 3px rgba(0, 168, 150, 0.1);
    }

    /* Button */
    .btn {
        width: 100%;
        margin-top: 16px;
        padding: 12px;
        background: #00a896;
        color: #ffffff;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
        font-size: 16px;
    }

    /* Total Payment */
    .total-pay-box {
        background-color: #f0fdfa;
        font-weight: 700;
        color: #000000;
        border: 2px solid #0d9488;
    }

    /* ---------- Booking success popup ---------- */
.booking-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(0, 0, 0, 0.45);
}

.booking-overlay.show { display: flex; }

.booking-modal {
    width: 100%;
    max-width: 480px;
    max-height: 90vh;
    overflow-y: auto;
    background: #ffffff;
    border-radius: 14px;
    border-top: 5px solid #00a896;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
    padding: 28px 26px 24px;
    text-align: left;
    animation: popIn .25s ease both;
}

@keyframes popIn {
    from { opacity: 0; transform: translateY(12px) scale(.97); }
    to   { opacity: 1; transform: none; }
}

.booking-modal .tick {
    width: 54px;
    height: 54px;
    margin: 0 auto 12px;
    border-radius: 50%;
    background: #f0fdfa;
    border: 2px solid #00a896;
    color: #00a896;
    display: grid;
    place-items: center;
    font-size: 28px;
    font-weight: 700;
}

.booking-modal h3 {
    margin: 0 0 4px;
    text-align: center;
    color: #000000;
}

.booking-modal .sub {
    margin: 0 0 18px;
    text-align: center;
    font-size: 14px;
    color: #555555;
}

.booking-row {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    padding: 9px 0;
    border-bottom: 1px solid #eeeeee;
    font-size: 14px;
}

.booking-row span   { color: #555555; flex: none; }
.booking-row strong { color: #000000; text-align: right; word-break: break-word; }

.booking-row.total {
    margin-top: 8px;
    padding: 12px 14px;
    background: #f0fdfa;
    border: 2px solid #0d9488;
    border-radius: 8px;
}

.booking-close {
    width: 100%;
    margin-top: 18px;
    padding: 12px;
    background: #00a896;
    color: #ffffff;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

</style>


</head>


<body>

    <!-- =========================
         NAVBAR
    ========================== -->

    <nav class="navbar">
        <a href="index.html" class="brand">
            <img src="logo.jpeg" alt="fctCare Logo" class="logo">
            fct<span>Care</span>
        </a>
        <button type="button" class="hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false" aria-controls="navLinks">
            <span></span><span></span><span></span>
        </button>
        <ul class="nav-links" id="navLinks">
            <li><a href="index.html" class="active">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="doctors.php">Doctors</a></li>
            <li><a href="hospitals.php">Hospitals</a></li>
            <li><a href="appointment.php">Appointment</a></li>
            <li><a href="contact.php">Contact</a></li>
             <?php
                if (session_status() === PHP_SESSION_NONE) {
    session_start();
                }
            ?>
                <li>
                    <?php if (isset($_SESSION["user_type"])): ?>

                        <a href="<?= htmlspecialchars($profile_link, ENT_QUOTES, 'UTF-8') ?>"
                        class="profile-btn">

                            <img src="<?= htmlspecialchars($avatar_image, ENT_QUOTES, 'UTF-8') ?>"
                                alt="Profile">

                            Profile

                        </a>

                    <?php else: ?>

                        <a href="login.php" class="login-btn">🔒 Login</a>

                    <?php endif; ?>
                </li>



        </ul>

    </nav>


    <!-- =========================
         APPOINTMENT FORM
    ========================== -->

    <main
        class="container"
        style="max-width: 600px; margin: 0 auto; padding: 20px;"
    >

        <h2>
            Schedule an E-Channeling Session
        </h2>

        <p style="margin-bottom: 32px;">
            Fill in the validation fields to confirm your consultation schedule.
        </p>


        <form
            id="appointmentForm"
            action="#"
            method="POST"
        >


            <!-- PATIENT NAME -->

            <div class="form">

                <label for="patientName">
                    Patient Full Name
                </label>

                <input
                    type="text"
                    id="patientName"
                    class="form_control"
                    placeholder="John Doe"
                    required
                >

            </div>


            <!-- NIC -->

            <div class="form">

                <label for="patientNIC">
                    NIC Number
                </label>

                <input
                    type="text"
                    id="patientNIC"
                    class="form_control"
                    placeholder="*************"
                    required
                >

            </div>


            <!-- PHONE -->

            <div class="form">

                <label for="patientPhone">
                    Mobile Number
                </label>

                <div class="phone_num">

                    <span class="country_code">
                        +94
                    </span>

                    <input
                        type="tel"
                        id="patientPhone"
                        class="phone_control"
                        placeholder="123456789"
                        maxlength="9"
                        required
                    >

                </div>

            </div>


            <!-- EMAIL -->

            <div class="form">

                <label for="patientEmail">
                    Email Address
                </label>

                <input
                    type="email"
                    id="patientEmail"
                    class="form_control"
                    placeholder="name@example.com"
                    required
                >

            </div>


            <!-- DOCTOR -->

            <div class="form">

                <label for="selectDoctor">
                    Select Specialist
                </label>

                <select
                    id="selectDoctor"
                    class="form_control"
                    required
                >

                    <option value="">
                        -- Choose a Doctor --
                    </option>

                </select>

            </div>


            <!-- HOSPITAL -->

            <div class="form">

                <label for="selectHospital">
                    Select Hospital
                </label>

                <select
                    id="selectHospital"
                    class="form_control"
                    required
                    disabled
                >

                    <option value="">
                        -- Choose a Doctor First --
                    </option>

                </select>

            </div>


            <!-- DATE -->

            <div class="form">

                <label for="appointmentDate">
                    Preferred Date
                </label>

                <input
                    type="date"
                    id="appointmentDate"
                    class="form_control"
                    required
                    disabled
                >

            </div>


            <!-- TIME SLOT -->

            <div class="form">

                <label for="selectTimeSlot">
                    Available Time Slot
                </label>

                <select
                    id="selectTimeSlot"
                    class="form_control"
                    required
                    disabled
                >

                    <option value="">
                        -- Choose a Date First --
                    </option>

                </select>

            </div>


            <!-- TOTAL PAYMENT -->

            <div class="form">

                <label for="totalPayment">
                    Total Pay (Rs.)
                </label>

                <input
                    type="text"
                    id="totalPayment"
                    class="form_control total-pay-box"
                    value="0.00"
                    readonly
                >

            </div>


            <!-- REASON -->

            <div class="form">

                <label>
                    Reason for Visit
                </label>

                <textarea
                    id="reason"
                    class="form_control"
                    rows="4"
                    placeholder="Briefly describe your medical request..."
                ></textarea>

            </div>


            <!-- SUBMIT -->

            <button
                type="submit"
                class="btn"
            >
                Confirm Booking Request
            </button>

        </form>

    </main>


    <!-- =========================
         FOOTER
    ========================== -->

    <footer class="footer">
        <div class="footer-top">

            <div class="footer-brand">
                <a href="index.php" class="footer-logo">
                    <img src="logo.jpeg" alt="fctCare Logo">
                    <span>fctCare</span>
                </a>
                <p>Sri Lanka's e-channeling framework. Book appointments with top doctors and leading hospitals from the comfort of your home.</p>
            </div>

            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="doctors.php">Doctors</a></li>
                    <li><a href="hospitals.php">Hospitals</a></li>
                    <li><a href="appointment.php">Appointment</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>

            <div class="footer-col footer-contact">
                <div class="contact-item">
                    <h4>Phone Number</h4>
                    <a href="tel:+94112345678">+94 11 234 5678</a>
                </div>
                <div class="contact-item">
                    <h4>Email Address</h4>
                    <a href="mailto:info@fctcare.com">info@fctcare.com</a>
                </div>
                <div class="contact-item">
                    <h4>eChannelling Hotline Operating Hours</h4>
                    <span>7:00 AM – 9:00 PM</span>
                </div>
            </div>

            <div class="footer-col footer-contact">
                <div class="contact-item">
                    <h4>Address</h4>
                    <address style="font-style: normal;">
                        eChannelling PLC, No: 108,<br>
                        W A D Ramanayake Mawatha,<br>
                        Colombo 2, Sri Lanka
                    </address>
                </div>
            </div>

        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 fctCare. Book. Channel. Care.</p>
            <p class="footer_text">FCT • KELANIYA</p>
            <a href="#" class="back-to-top" aria-label="Back to top">↑ Back to top</a>
        </div>
    </footer>


    <!-- =========================
            BOOKING SUCCESS POPUP
        ========================== -->
        <div class="booking-overlay" id="bookingModal" role="dialog" aria-modal="true" aria-labelledby="bookingTitle">
            <div class="booking-modal">
                <div class="tick">✓</div>
                <h3 id="bookingTitle">Appointment Booked Successfully!</h3>
                <p class="sub">Here are your appointment details.</p>

                <div id="bookingDetails"></div>

                <button type="button" class="booking-close" id="bookingClose">Close</button>
            </div>
        </div>
    <script src="script.js"></script>


    <script>

        document.addEventListener("DOMContentLoaded", () => {


            // =========================================
            // GET ELEMENTS
            // =========================================

            const appointmentDate =
                document.getElementById("appointmentDate");

            const doctorSelect =
                document.getElementById("selectDoctor");

            const hospitalSelect =
                document.getElementById("selectHospital");

            const slotSelect =
                document.getElementById("selectTimeSlot");

            const totalPaymentInput =
                document.getElementById("totalPayment");

            const form =
                document.getElementById("appointmentForm");


            // =========================================
            // CHECK REQUIRED ELEMENTS
            // =========================================

            if (
                !appointmentDate ||
                !doctorSelect ||
                !hospitalSelect ||
                !slotSelect ||
                !totalPaymentInput ||
                !form
            ) {

                console.error(
                    "Appointment form elements are missing."
                );

                return;
            }


            // =========================================
            // VARIABLES
            // =========================================

            let doctorFee = 0;
            let hospitalFee = 0;


            // =========================================
            // SET MINIMUM DATE
            // =========================================

            const today =
                new Date();

            const localToday =
                new Date(
                    today.getTime() -
                    today.getTimezoneOffset() * 60000
                )
                .toISOString()
                .split("T")[0];

            appointmentDate.min =
                localToday;


            // =========================================
            // CALCULATE TOTAL PAYMENT
            // =========================================

            function calculateTotalPay() {

                const total =
                    doctorFee + hospitalFee;

                totalPaymentInput.value =
                    total.toLocaleString("en-US", {

                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2

                    });

            }


            // =========================================
            // RESET HOSPITALS
            // =========================================

            function resetHospitals() {

                hospitalSelect.innerHTML =
                    '<option value="">-- Choose a Doctor First --</option>';

                hospitalSelect.disabled =
                    true;

            }


            // =========================================
            // RESET DATE
            // =========================================

            function resetDate() {

                appointmentDate.value = "";

                appointmentDate.disabled =
                    true;

            }


            // =========================================
            // RESET TIME SLOTS
            // =========================================

            function resetTimeSlots() {

                slotSelect.innerHTML =
                    '<option value="">-- Choose a Date First --</option>';

                slotSelect.disabled =
                    true;

            }


            // =========================================
            // GET DAY NAME FROM DATE
            // =========================================

            function getDayOfWeek(dateValue) {

                const date =
                    new Date(
                        dateValue + "T00:00:00"
                    );

                const days = [

                    "Sunday",
                    "Monday",
                    "Tuesday",
                    "Wednesday",
                    "Thursday",
                    "Friday",
                    "Saturday"

                ];

                return days[
                    date.getDay()
                ];

            }


            // =========================================
            // FORMAT TIME
            // =========================================

            function formatTime(time) {

                if (!time) {
                    return "-";
                }

                const parts =
                    time.split(":");

                if (parts.length < 2) {
                    return time;
                }

                let hour =
                    parseInt(
                        parts[0],
                        10
                    );

                const minute =
                    parts[1];

                const period =
                    hour >= 12
                        ? "PM"
                        : "AM";


                if (hour === 0) {

                    hour = 12;

                }

                else if (hour > 12) {

                    hour -= 12;

                }


                return `${hour}:${minute} ${period}`;

            }

            // =========================================
            // BOOKING SUCCESS POPUP
            // =========================================

            const bookingModal = document.getElementById("bookingModal");
            const bookingDetails = document.getElementById("bookingDetails");

            function money(value) {
                return "Rs. " + Number(value || 0).toLocaleString("en-US", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function showBookingPopup(d) {

                const rows = [
                    ["Appointment ID", "#" + d.id],
                    ["Patient Name", d.name],
                    ["NIC Number", d.nic],
                    ["Mobile", d.phone],
                    ["Email", d.email],
                    ["Doctor", d.doctor],
                    ["Hospital", d.hospital],
                    ["Date", d.date + " (" + d.day + ")"],
                    ["Time Slot", d.slot],
                    ["Doctor Fee", money(d.doctorFee)],
                    ["Hospital Fee", money(d.hospitalFee)]
                ];

                if (d.reason) {
                    rows.push(["Reason", d.reason]);
                }

                bookingDetails.innerHTML = "";

                rows.forEach(function (r) {
                    const row = document.createElement("div");
                    row.className = "booking-row";

                    const label = document.createElement("span");
                    label.textContent = r[0];

                    const value = document.createElement("strong");
                    value.textContent = r[1];

                    row.appendChild(label);
                    row.appendChild(value);
                    bookingDetails.appendChild(row);
                });

                const total = document.createElement("div");
                total.className = "booking-row total";
                total.innerHTML = "<span>Total Payment</span><strong></strong>";
                total.querySelector("strong").textContent = money(d.total);
                bookingDetails.appendChild(total);

                bookingModal.classList.add("show");
            }

            function closeBookingPopup() {
                bookingModal.classList.remove("show");
            }

            document.getElementById("bookingClose").addEventListener("click", closeBookingPopup);

            bookingModal.addEventListener("click", function (e) {
                if (e.target === bookingModal) closeBookingPopup();
            });

            document.addEventListener("keydown", function (e) {
                if (e.key === "Escape") closeBookingPopup();
            });


            // =========================================
            // LOAD DOCTORS
            // =========================================

            async function loadDoctors() {

                try {

                    const response =
                        await fetch(
                            "backend/doctors.php"
                        );


                    if (!response.ok) {

                        throw new Error(
                            "HTTP Error: " +
                            response.status
                        );

                    }


                    const result =
                        await response.json();


                    console.log(
                        "Doctors:",
                        result
                    );


                    if (!result.success) {

                        doctorSelect.innerHTML =
                            '<option value="">-- Unable to Load Doctors --</option>';

                        console.error(
                            result.message
                        );

                        return;

                    }


                    doctorSelect.innerHTML =
                        '<option value="">-- Choose a Doctor --</option>';


                    if (
                        !Array.isArray(result.data) ||
                        result.data.length === 0
                    ) {

                        doctorSelect.innerHTML =
                            '<option value="">-- No Doctors Available --</option>';

                        return;

                    }


                    result.data.forEach(
                        doctor => {

                            const option =
                                document.createElement(
                                    "option"
                                );


                            option.value =
                                doctor.id;


                            option.textContent =
                                `${doctor.name} (${doctor.specialty}) - Rs. ${Number(
                                    doctor.fee
                                ).toLocaleString(
                                    "en-US",
                                    {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }
                                )}`;


                            option.dataset.fee =
                                doctor.fee;


                            option.dataset.code =
                                doctor.doctor_code;


                            doctorSelect.appendChild(
                                option
                            );

                        }
                    );


                    // =========================================
                    // DOCTOR SELECTED FROM doctors.html
                    // Example:
                    // appointment.html?doc=DR001
                    // =========================================

                    const params =
                        new URLSearchParams(
                            window.location.search
                        );


                    const doctorCode =
                        params.get("doc");


                    if (doctorCode) {

                        const matchingOption =
                            Array.from(
                                doctorSelect.options
                            ).find(
                                option =>
                                    option.dataset.code ===
                                    doctorCode
                            );


                        if (matchingOption) {

                            doctorSelect.value =
                                matchingOption.value;


                            doctorSelect.dispatchEvent(
                                new Event("change")
                            );

                        }

                    }

                }

                catch (error) {

                    console.error(
                        "Doctor loading error:",
                        error
                    );


                    doctorSelect.innerHTML =
                        '<option value="">-- Server Error --</option>';


                    alert(
                        "Unable to connect to the server."
                    );

                }

            }


            // =========================================
            // LOAD HOSPITALS FOR THE SELECTED DOCTOR
            // (runs on doctor change + refreshes automatically,
            //  so newly added hospitals show up without reload)
            // =========================================

            async function loadHospitals(silent = false) {

                const doctorId = doctorSelect.value;

                if (!doctorId) {
                    return;
                }

                const previous = silent ? hospitalSelect.value : "";

                if (!silent) {
                    hospitalSelect.innerHTML =
                        '<option value="">-- Loading Hospitals... --</option>';
                    hospitalSelect.disabled = true;
                    hospitalSelect.dataset.sig = "";
                }

                try {

                    const response = await fetch(
                        `backend/doctor_hospitals.php?doctor_id=${encodeURIComponent(doctorId)}` +
                        `&_=${Date.now()}`,
                        { cache: "no-store" }
                    );

                    if (!response.ok) {
                        throw new Error("HTTP Error: " + response.status);
                    }

                    const result = await response.json();

                    // doctor changed while loading → ignore this result
                    if (doctorSelect.value !== doctorId) {
                        return;
                    }

                    if (!result.success) {
                        if (!silent) {
                            hospitalSelect.innerHTML =
                                '<option value="">-- No Hospitals Found --</option>';
                        }
                        return;
                    }

                    const list = Array.isArray(result.data) ? result.data : [];

                    // nothing changed since last load → don't touch the dropdown
                    const sig = list.map(h => h.id + "|" + h.fee).join(",") || "empty";

                    if (silent && hospitalSelect.dataset.sig === sig) {
                        return;
                    }

                    hospitalSelect.dataset.sig = sig;

                    if (list.length === 0) {
                        hospitalSelect.innerHTML =
                            '<option value="">-- No Hospitals Assigned --</option>';
                        hospitalSelect.disabled = true;

                        if (previous) {
                            hospitalFee = 0;
                            calculateTotalPay();
                            resetDate();
                            resetTimeSlots();
                        }
                        return;
                    }

                    hospitalSelect.innerHTML =
                        '<option value="">-- Choose a Hospital --</option>';

                    list.forEach(hospital => {

                        const option = document.createElement("option");

                        option.value = hospital.id;

                        option.textContent =
                            `${hospital.name} (${hospital.district}) - Rs. ${Number(
                                hospital.fee
                            ).toLocaleString("en-US", {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            })}`;

                        option.dataset.fee       = hospital.fee;
                        option.dataset.telephone = hospital.telephone || "";

                        hospitalSelect.appendChild(option);
                    });

                    hospitalSelect.disabled = false;

                    // keep the patient's selected hospital if it still exists
                    if (previous) {

                        if (list.some(h => String(h.id) === String(previous))) {

                            hospitalSelect.value = previous;

                        } else {

                            // selected hospital was removed → reset the steps below it
                            hospitalFee = 0;
                            calculateTotalPay();
                            resetDate();
                            resetTimeSlots();
                        }
                    }

                } catch (error) {

                    console.error("Hospital loading error:", error);

                    if (!silent) {
                        hospitalSelect.innerHTML =
                            '<option value="">-- Server Error --</option>';
                        alert("Unable to load hospitals.");
                    }
                }
            }


            // =========================================
            // DOCTOR CHANGE
            // =========================================

            doctorSelect.addEventListener("change", () => {

                resetDate();
                resetTimeSlots();

                doctorFee   = 0;
                hospitalFee = 0;
                calculateTotalPay();

                const doctorId = doctorSelect.value;

                if (!doctorId) {
                    resetHospitals();
                    hospitalSelect.dataset.sig = "";
                    return;
                }

                const selectedDoctor =
                    doctorSelect.options[doctorSelect.selectedIndex];

                doctorFee = Number(selectedDoctor.dataset.fee) || 0;
                calculateTotalPay();

                loadHospitals();
            });


            // auto refresh hospitals: every 15 seconds, and when the patient comes back to the tab
            setInterval(() => {
                if (!document.hidden) {
                    loadHospitals(true);
                }
            }, 15000);

            window.addEventListener("focus", () => loadHospitals(true));

            document.addEventListener("visibilitychange", () => {
                if (!document.hidden) {
                    loadHospitals(true);
                }
            });


            // =========================================
            // HOSPITAL CHANGE
            // ENABLE DATE
            // =========================================

            hospitalSelect.addEventListener(
                "change",
                () => {


                    resetTimeSlots();


                    hospitalFee =
                        0;


                    const hospitalId =
                        hospitalSelect.value;


                    if (!hospitalId) {

                        resetDate();
                        calculateTotalPay();

                        return;

                    }


                    const selectedHospital =
                        hospitalSelect.options[
                            hospitalSelect.selectedIndex
                        ];


                    hospitalFee =
                        Number(
                            selectedHospital.dataset.fee
                        ) || 0;


                    calculateTotalPay();


                    // Enable date

                    appointmentDate.disabled =
                        false;


                    appointmentDate.focus();

                }
            );


                        // =========================================
            // LOAD AVAILABLE TIME SLOTS
            // (runs on date change + refreshes automatically,
            //  so newly added schedules show up without reload)
            // =========================================

            async function loadTimeSlots(silent = false) {

                const dateValue  = appointmentDate.value;
                const doctorId   = doctorSelect.value;
                const hospitalId = hospitalSelect.value;

                if (!dateValue || !doctorId || !hospitalId) {
                    return;
                }

                const dayOfWeek = getDayOfWeek(dateValue);
                const previous  = slotSelect.value;
                const key       = doctorId + "|" + hospitalId + "|" + dateValue;

                if (!silent) {
                    slotSelect.innerHTML =
                        '<option value="">-- Loading Time Slots... --</option>';
                    slotSelect.disabled = true;
                    slotSelect.dataset.sig = "";
                }

                try {

                    const response = await fetch(
                        `backend/schedules.php?doctor_id=${encodeURIComponent(doctorId)}` +
                        `&hospital_id=${encodeURIComponent(hospitalId)}` +
                        `&day_of_week=${encodeURIComponent(dayOfWeek)}` +
                        `&_=${Date.now()}`,
                        { cache: "no-store" }
                    );

                    if (!response.ok) {
                        throw new Error("HTTP Error: " + response.status);
                    }

                    const result = await response.json();

                    // user changed doctor / hospital / date while loading → ignore this result
                    const currentKey =
                        doctorSelect.value + "|" + hospitalSelect.value + "|" + appointmentDate.value;

                    if (currentKey !== key) {
                        return;
                    }

                    if (!result.success) {
                        if (!silent) {
                            slotSelect.innerHTML =
                                '<option value="">-- Unable to Load Time Slots --</option>';
                        }
                        return;
                    }

                    const list = Array.isArray(result.data) ? result.data : [];

                    // nothing changed since last load → don't touch the dropdown
                    const sig = list.map(s => s.id + s.start_time + s.end_time).join(",") || "empty";

                    if (silent && slotSelect.dataset.sig === sig) {
                        return;
                    }

                    slotSelect.dataset.sig = sig;

                    if (list.length === 0) {
                        slotSelect.innerHTML =
                            `<option value="">-- No Time Slots Available for ${dayOfWeek} --</option>`;
                        slotSelect.disabled = true;
                        return;
                    }

                    slotSelect.innerHTML =
                        `<option value="">-- Choose a Time Slot (${dayOfWeek}) --</option>`;

                    list.forEach(schedule => {

                        const option = document.createElement("option");

                        option.value = schedule.id;

                        option.textContent =
                            `${formatTime(schedule.start_time)} - ${formatTime(schedule.end_time)}`;

                        option.dataset.startTime = schedule.start_time;
                        option.dataset.endTime   = schedule.end_time;
                        option.dataset.day       = schedule.day_of_week;

                        slotSelect.appendChild(option);
                    });

                    slotSelect.disabled = false;

                    // keep the patient's selected slot if it still exists
                    if (previous && list.some(s => String(s.id) === String(previous))) {
                        slotSelect.value = previous;
                    }

                } catch (error) {

                    console.error("Schedule loading error:", error);

                    if (!silent) {
                        slotSelect.innerHTML =
                            '<option value="">-- Server Error --</option>';
                        alert("Unable to load time slots.");
                    }
                }
            }


            // date change → load slots
            appointmentDate.addEventListener("change", () => {
                resetTimeSlots();
                loadTimeSlots();
            });


            // auto refresh: every 15 seconds, and when the patient comes back to the tab
            setInterval(() => {
                if (!document.hidden) {
                    loadTimeSlots(true);
                }
            }, 15000);

            window.addEventListener("focus", () => loadTimeSlots(true));

            document.addEventListener("visibilitychange", () => {
                if (!document.hidden) {
                    loadTimeSlots(true);
                }
            });


            // =========================================
            // FORM SUBMIT
            // =========================================

            form.addEventListener(
                "submit",
                async function (e) {

                    e.preventDefault();


                    // =========================================
                    // GET FORM VALUES
                    // =========================================

                    const name =
                        document
                            .getElementById("patientName")
                            .value
                            .trim();


                    const nic =
                        document
                            .getElementById("patientNIC")
                            .value
                            .trim();


                    const phone =
                        document
                            .getElementById("patientPhone")
                            .value
                            .trim();


                    const email =
                        document
                            .getElementById("patientEmail")
                            .value
                            .trim();


                    const date =
                        appointmentDate.value;


                    const doctorId =
                        doctorSelect.value;


                    const hospitalId =
                        hospitalSelect.value;


                    const slotId =
                        slotSelect.value;


                    // =========================================
                    // PATIENT NAME VALIDATION
                    // =========================================

                    if (
                        !/^[A-Za-z\s.]+$/.test(name)
                    ) {

                        alert(
                            "Please enter a valid patient name."
                        );

                        return;

                    }


                    // =========================================
                    // NIC VALIDATION
                    // =========================================

                    if (
                        !/^[0-9]{9}[vVxX]$/.test(nic) &&
                        !/^[0-9]{12}$/.test(nic)
                    ) {

                        alert(
                            "Please enter a valid NIC number."
                        );

                        return;

                    }


                    // =========================================
                    // PHONE VALIDATION
                    // =========================================

                    if (
                        !/^[0-9]{9}$/.test(phone)
                    ) {

                        alert(
                            "Mobile number must contain 9 digits after +94."
                        );

                        return;

                    }


                    // =========================================
                    // EMAIL VALIDATION
                    // =========================================

                    if (
                        !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
                    ) {

                        alert(
                            "Please enter a valid email address."
                        );

                        return;

                    }


                    // =========================================
                    // DOCTOR VALIDATION
                    // =========================================

                    if (!doctorId) {

                        alert(
                            "Please select a doctor."
                        );

                        return;

                    }


                    // =========================================
                    // HOSPITAL VALIDATION
                    // =========================================

                    if (!hospitalId) {

                        alert(
                            "Please select a hospital."
                        );

                        return;

                    }


                    // =========================================
                    // DATE VALIDATION
                    // =========================================

                    if (!date) {

                        alert(
                            "Please select an appointment date."
                        );

                        return;

                    }


                    // =========================================
                    // TIME SLOT VALIDATION
                    // =========================================

                    if (!slotId) {

                        alert(
                            "Please select a time slot."
                        );

                        return;

                    }


                    // =========================================
                    // CREATE FORM DATA
                    // =========================================

                    const formData =
                        new FormData();


                    formData.append(
                        "patient_name",
                        name
                    );


                    formData.append(
                        "patient_nic",
                        nic
                    );


                    formData.append(
                        "patient_phone",
                        "+94" + phone
                    );


                    formData.append(
                        "patient_email",
                        email
                    );


                    formData.append(
                        "doctor_id",
                        doctorId
                    );


                    formData.append(
                        "hospital_id",
                        hospitalId
                    );


                    formData.append(
                        "schedule_id",
                        slotId
                    );


                    formData.append(
                        "appointment_date",
                        date
                    );


                    formData.append(
                        "reason",
                        document
                            .getElementById("reason")
                            .value
                            .trim()
                    );


                    // =========================================
                    // SEND TO PHP
                    // =========================================

                    try {

                        const response =
                            await fetch(
                                "backend/book_appointment.php",
                                {
                                    method: "POST",
                                    body: formData
                                }
                            );


                        if (!response.ok) {

                            throw new Error(
                                "HTTP Error: " +
                                response.status
                            );

                        }


                        const result =
                            await response.json();


                        console.log(
                            "Booking result:",
                            result
                        );


                        // =========================================
                        // BOOKING SUCCESS
                        // =========================================

                        if (result.success) {
                            showBookingPopup({
                                id:          result.appointment_id,
                                name:        name,
                                nic:         nic,
                                phone:       "+94" + phone,
                                email:       email,
                                doctor:      doctorSelect.options[doctorSelect.selectedIndex].textContent.split(" - Rs.")[0],
                                hospital:    hospitalSelect.options[hospitalSelect.selectedIndex].textContent.split(" - Rs.")[0],
                                date:        date,
                                day:         getDayOfWeek(date),
                                slot:        slotSelect.options[slotSelect.selectedIndex].textContent,
                                doctorFee:   doctorFee,
                                hospitalFee: hospitalFee,
                                total:       result.total_payment,
                                reason:      document.getElementById("reason").value.trim()
                            });
                            


                            // Reset form

                            form.reset();


                            // Reset dropdowns

                            doctorSelect.innerHTML =
                                '<option value="">-- Choose a Doctor --</option>';


                            hospitalSelect.innerHTML =
                                '<option value="">-- Choose a Doctor First --</option>';


                            slotSelect.innerHTML =
                                '<option value="">-- Choose a Date First --</option>';


                            hospitalSelect.disabled =
                                true;


                            appointmentDate.disabled =
                                true;


                            slotSelect.disabled =
                                true;


                            // Reset fees

                            doctorFee =
                                0;

                            hospitalFee =
                                0;


                            calculateTotalPay();


                            // Reload doctors

                            loadDoctors();

                        }


                        // =========================================
                        // BOOKING FAILED
                        // =========================================

                        else {

                            alert(
                                "Appointment failed!\n\n" +

                                (
                                    result.message ||
                                    "Unknown error occurred."
                                )
                            );

                        }

                    }

                    catch (error) {

                        console.error(
                            "Booking error:",
                            error
                        );


                        alert(
                            "Unable to connect to the server."
                        );

                    }

                }
            );


            // =========================================
            // START APPLICATION
            // =========================================

            resetHospitals();

            resetDate();

            resetTimeSlots();

            calculateTotalPay();

            loadDoctors();

        });

        
// Mobile hamburger menu toggle
        (function () {
            const btn = document.getElementById("hamburger");
            const menu = document.getElementById("navLinks");

            btn.addEventListener("click", function () {
                const isOpen = menu.classList.toggle("show");
                btn.classList.toggle("open", isOpen);
                btn.setAttribute("aria-expanded", isOpen ? "true" : "false");
            });

            // Close the menu after tapping a link
            menu.querySelectorAll("a").forEach(function (link) {
                link.addEventListener("click", function () {
                    menu.classList.remove("show");
                    btn.classList.remove("open");
                    btn.setAttribute("aria-expanded", "false");
                });
            });
        })();

    </script>

</body>

</html>

