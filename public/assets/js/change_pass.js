async function changePassword() {
    const csrfTokenc = document.head.querySelector('meta[name="csrf-token"]').content;

    const email = document.getElementById('email').value;
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;

    try {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Please enter a valid email address.');
            return;
        }

        const response = await fetch('http://127.0.0.1:8000/api/auth/change-pass', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN':csrfTokenc
            },
            body: JSON.stringify({
                email,
                currentPassword,
                newPassword
            }),
        });

        if (response.ok) {
            const result = await response.json();
            console.log(result.message);

            // Redirect to another page after changing password
            window.location.href = '/';
        } else {
            const errorData = await response.json();
            console.error(errorData.error);
            alert('Failed to change password. Please check your email and current password, then try again.');
        }
    } catch (error) {
        console.error('An error occurred:', error);
        alert('An error occurred. Please try again later.');
    }
}
