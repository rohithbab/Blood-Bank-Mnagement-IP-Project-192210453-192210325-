const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

registerBtn.addEventListener('click', () =>{
    container.classList.add("active")
});
loginBtn.addEventListener('click', () =>{
    container.classList.remove("active")
});
document.getElementById('signInBtn').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the form from submitting in the traditional way
    window.location.href = "user_dashboard.html"; // Replace with your dashboard page
});
