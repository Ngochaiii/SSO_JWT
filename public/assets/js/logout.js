// logout.js

const logout = async () => {
    try {
        const response = await fetch('http://your-app-url/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                // Add any additional headers if needed
            },
            credentials: 'same-origin', // Include credentials (cookies)
        });

        if (response.ok) {
            // Redirect or perform any other action after successful logout
            window.location.href = '/';
        } else {
            // Handle logout failure
            console.error('Logout failed');
        }
    } catch (error) {
        console.error('Error during logout:', error);
    }
};

// Attach the logout function to a button click event or any other trigger
document.getElementById('logoutButton').addEventListener('click', logout);
