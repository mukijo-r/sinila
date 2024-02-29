<?php
include 'function.php';
//require 'config.php';

$conn = mysqli_connect("localhost:3306","root","","sdk");

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $tahunAjar = $_POST['tahunAjar'];
    $kelas = $_POST['kelas'];
    // Dapatkan kata sandi terenkripsi dari database
    $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if ($row = mysqli_fetch_assoc($result)) {
        $hashedPassword = $row['password'];

        // Periksa apakah kata sandi yang dimasukkan sesuai dengan yang terenkripsi
        if (password_verify($password, $hashedPassword)) {
            // Kata sandi cocok, beri izin login
            $_SESSION['user'] = $username; // Simpan nama user dalam sesi
            $_SESSION['log'] = 'True';
            $_SESSION['previous_user'] = $username;  
            $_SESSION['tahunAjar'] = $tahunAjar;
            $_SESSION['kelas'] = $kelas;
            header('location:index.php');
            
        } else {
            // Kata sandi tidak cocok, arahkan kembali ke halaman login
            header('location:login.php');
        }
    } else {
        // Tidak ada akun dengan username tersebut
        header('location:login.php');
    }
}
?>

<!DOCTYPE html>
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <style>
      /* Import Google font - Poppins */
      @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap");
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
      }
      a {
        text-decoration: none;
      }
      .header {
        position: fixed;
        height: 80px;
        width: 100%;
        z-index: 100;
        padding: 0 20px;
      }
      .nav {
        max-width: 1100px;
        width: 100%;
        margin: 0 auto;
      }
      .nav,
      .nav_item {
        display: flex;
        height: 100%;
        align-items: center;
        justify-content: space-between;
      }
      .nav_logo,
      .nav_link,
      .button {
        color: #fff;
      }
      .nav_logo {
        font-size: 25px;
      }
      .nav_item {
        column-gap: 25px;
      }
      .nav_link:hover {
        color: #d9d9d9;
      }
      .button {
        padding: 6px 24px;
        border: 2px solid #fff;
        background: transparent;
        border-radius: 6px;
        cursor: pointer;
      }
      .button:active {
        transform: scale(0.98);
      }

      /* Home */
      .home {
        position: relative;
        height: 100vh;
        width: 100%;
        background-image: url("assets/img/bg.jpg");
        background-size: cover;
        background-position: center;
      }
      .home::before {
        content: "";
        position: absolute;
        height: 100%;
        width: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        z-index: 100;
        opacity: 0;
        pointer-events: none;
        transition: all 0.5s ease-out;
      }
      .home.show::before {
        opacity: 1;
        pointer-events: auto;
      }
      /* From */
      .form_container {
        position: fixed;
        max-width: 320px;
        width: 100%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(1.2);
        z-index: 101;
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: rgba(0, 0, 0, 0.1);
        opacity: 0;
        pointer-events: none;
        transition: all 0.4s ease-out;
      }
      .home.show .form_container {
        opacity: 1;
        pointer-events: auto;
        transform: translate(-50%, -50%) scale(1);
      }
      .signup_form {
        display: none;
      }
      .form_container.active .signup_form {
        display: block;
      }
      .form_container.active .login_form {
        display: none;
      }
      .form_close {
        position: absolute;
        top: 10px;
        right: 20px;
        color: #0b0217;
        font-size: 22px;
        opacity: 0.7;
        cursor: pointer;
      }
      .form_container h2 {
        font-size: 22px;
        color: #0b0217;
        text-align: center;
      }
      .input_box {
        position: relative;
        margin-top: 30px;
        width: 100%;
        height: 40px;
      }
      .input_box input {
        height: 100%;
        width: 100%;
        border: none;
        outline: none;
        padding: 0 30px;
        color: #333;
        transition: all 0.2s ease;
        border-bottom: 1.5px solid #aaaaaa;
      }
      .input_box input:focus {
        border-color: #7d2ae8;
      }
      .input_box i {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-size: 20px;
        color: #707070;
      }
      .input_box i.email,
      .input_box i.password {
        left: 0;
      }
      .input_box input:focus ~ i.email,
      .input_box input:focus ~ i.password {
        color: #7d2ae8;
      }
      .input_box i.pw_hide {
        right: 0;
        font-size: 18px;
        cursor: pointer;
      }
      .option_field {
        margin-top: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .form_container a {
        color: #7d2ae8;
        font-size: 12px;
      }
      .form_container a:hover {
        text-decoration: underline;
      }
      .checkbox {
        display: flex;
        column-gap: 8px;
        white-space: nowrap;
      }
      .checkbox input {
        accent-color: #7d2ae8;
      }
      .checkbox label {
        font-size: 12px;
        cursor: pointer;
        user-select: none;
        color: #0b0217;
      }
      .form_container .button {
        background: #7d2ae8;
        margin-top: 30px;
        width: 100%;
        padding: 10px 0;
        border-radius: 10px;
      }
      .login_signup {
        font-size: 12px;
        text-align: center;
        margin-top: 15px;
      }    

    </style>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
	<link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  </head>
  <body>
    <!-- Header -->
    <header class="header">
      <br>
      <h1 style="color: #fff;">RAKUM</h1>
      <nav class="nav">
        <a href="#" class="nav_logo"></a>
        
        <ul class="nav_items">
          <li class="nav_item">
            <a href="#" class="nav_link"></a>
            <a href="#" class="nav_link"></a>
            <a href="#" class="nav_link"></a>
            <a href="#" class="nav_link"></a>
          </li>
        </ul>

        <button class="button" id="form-open">Login</button>
      </nav>
    </header>

    <!-- Home -->
    <section class="home">
      <div class="form_container">
        <i class="uil uil-times form_close"></i>
        <!-- Login From -->
        <div class="form login_form">
        <form action="" method="post">
            <h2>Login</h2>

            <div class="input_box">
              <i class="uil uil-user user"></i>
              <input type="text" name="username" placeholder="Username" required />              
            </div>
            <div class="input_box">
              <input type="password" name="password" placeholder="Password" required />
              <i class="uil uil-lock password"></i>
              <i class="uil uil-eye-slash pw_hide"></i>
            </div>

            <div class="input_box" style="width: 270px;">
              <i class="uil-calendar-alt"></i>
              <select class="form-select" name="tahunAjar" aria-label="Pilih TA" required>
              <option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pilih Tahun Ajar</option>
              <?php
                // Ambil data kelas dari tabel kelas
                $queryTA = mysqli_query($conn, "SELECT id_tahun_ajar, tahun_ajar FROM tahun_ajar");
                while ($ta = mysqli_fetch_assoc($queryTA)) {
                  echo '<option value="' . $ta['tahun_ajar'] . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $ta['tahun_ajar'] . '</option>';
                }
              ?>
              </select>              
                </div>

                <div class="input_box" style="width: 270px;">
                  <i class="uil-angle-double-down"></i>
                  <select class="form-select" name="kelas" id="kelas" aria-label="Kelas" required>
              <option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pilih Kelas</option>
              <?php
              // Ambil data kelas dari tabel kelas
              $queryKelas = mysqli_query($conn, "SELECT id_kelas, nama_kelas FROM kelas");
              while ($kelas = mysqli_fetch_assoc($queryKelas)) {
                echo '<option value="' . $kelas['id_kelas'] . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $kelas['nama_kelas'] . '</option>';
              }
              ?>
            </select>
            
           </div><br>
            <div class="option_field">
              <span class="checkbox">
                <input type="checkbox" id="check" />
                <label for="check">Remember me</label>
              </span>
            </div>

            <button class="button" name="login">Login</button>

          </form>
        </div>

      </div>
    </section>

    <script>
      const formOpenBtn = document.querySelector("#form-open"),
        home = document.querySelector(".home"),
        formContainer = document.querySelector(".form_container"),
        formCloseBtn = document.querySelector(".form_close"),
        loginBtn = document.querySelector("#login"),
        pwShowHide = document.querySelectorAll(".pw_hide");

      formOpenBtn.addEventListener("click", () => home.classList.add("show"));
      formCloseBtn.addEventListener("click", () => home.classList.remove("show"));     

      loginBtn.addEventListener("click", (e) => {
        e.preventDefault();
        formContainer.classList.remove("active");
      });

    </script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
  </body>
</html>
