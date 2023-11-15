const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;
document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            event.preventDefault();

            // Lấy giá trị từ form
            const email = document.getElementById('email_signIn').value;
            const password = document.getElementById('password_signIn').value;

            // Gửi yêu cầu đăng nhập
            loginUser(email, password);
        });
    }
});

function loginUser(email, password) {
    const apiUrl = 'http://127.0.0.1:8000/api/auth/login';
    fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                email: email,
                password: password,
            }),
        })
        .then(response => response.json())
        .then(data => {

            // Kiểm tra xem có token hay không
            if (data.token) {
                // Lưu token vào session hoặc cookie
                // sessionStorage.setItem('jwt_token', data.token);
                const token = data.token;
                // Thời gian sống của cookie, đặt theo đơn vị giây (ở đây là 7 ngày)
                const expirationDays = 7;
                const expirationTime = expirationDays * 24 * 60 * 60 * 1000;

                // Lấy thời gian hiện tại và thêm vào thời gian sống của cookie
                const expirationDate = new Date(Date.now() + expirationTime);
                document.cookie = `jwt_token=${token}; expires=${expirationDate.toUTCString()}; path=/`;

                // Chuyển hướng hoặc thực hiện các thao tác khác sau khi đăng nhập thành công
                window.location.href = '/home'; // Chuyển hướng đến trang dashboard
            } else {
                // Xử lý khi đăng nhập không thành công
                console.error('Authentication failed');
            }
        })
        .catch(error => console.error('Error:', error));
}


