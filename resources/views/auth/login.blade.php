
<!DOCTYPE html>
<!-- Coding By CodingNepal - www.codingnepalweb.com -->
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>YPH</title>
  <link rel="stylesheet" href="{{ URL::asset('assets/auth/css/style.css') }}">
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/auth/img/thumb.jpg') }}">
</head>
<body>
  <div class="wrapper">
    <form id="loginform">
      <!-- <h2>Login</h2> -->
       @csrf
        <div class="input-field">
        <input type="email" name="email" id="email" required>
        <label>Enter your email</label>
      </div>
      <div class="input-field">
        <input type="password" id="password" name="password" required>
        <label>Enter your password</label>
      </div>
      <button id="loginsubmit">Log In</button>
    </form>
  </div>
<script src="{{ URL::asset('assets/master/lib/jquery/jquery.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function () {
    $('#loginsubmit').on('click', function (e) {
      e.preventDefault();

      let email = $('#email').val();
      let password = $('#password').val();

      $.ajax({
        url: '/login',
        method: 'POST',
        data: {
          _token: '{{ csrf_token() }}',
          email: email,
          password: password
        },
        success: function (res) {
          Swal.fire({
            icon: 'success',
            title: 'Login berhasil!',
            showConfirmButton: false,
            timer: 1500
          }).then(() => {
            window.location.href = res.redirect; // pakai URL dari response
          });
        },
        error: function (xhr) {
          let message = 'Terjadi kesalahan.';

          if (xhr.status === 422) {
            message = Object.values(xhr.responseJSON.errors).join('<br>');
          } else if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
          }

          Swal.fire({
            icon: 'error',
            title: 'Login gagal!',
            html: message,
          });
        }
      });


    });
  });
</script>

</body>
</html>