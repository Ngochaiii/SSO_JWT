const csrfTokenr = document.head.querySelector('meta[name="csrf-token"]').content;
document.addEventListener('DOMContentLoaded', function () {
    const resgisterForm = document.getElementById('resgisterForm');

    if (resgisterForm) {
        resgisterForm.addEventListener('submit', function (event) {
            event.preventDefault();

            // Lấy giá trị từ form
            const name = document.getElementById('name_signUp').value;
            const email = document.getElementById('email_signUp').value;
            const password = document.getElementById('password_signUp').value;
            if (name.trim() === '') {
                alert('Name is required.');
                return;
            }
            var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
            let check_email =email.match(validRegex);
            if (!check_email) {
                alert('Invalid email address.');
                return;
            }

            if (password.trim() === '' || password.length < 5) {
                alert('Password must be at least 6 characters.');
                return;
            }
            // Gửi yêu cầu đăng ký tài khoản
            registerUser(name, email, password);
        });
    }
});

function registerUser(name, email, password) {
    const apiUrl = 'http://127.0.0.1:8000/api/auth/register';
    fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfTokenr,
            },
            body: JSON.stringify({
                name: name,
                email: email,
                password: password,
            }),
        })
        .then(response => response.json())
        .then(data => {
            // Handle the JSON response here

            // Hiển thị thông báo
            alert("Bạn tạo tài khoản thành công mời chuyển qua phần đăng nhập"); // Chuyển hướng đến trang dashboard
            // You can display a success message or perform other actions based on the response
        })
        .catch(error => {
            console.error('Error:', error);
        });
}
