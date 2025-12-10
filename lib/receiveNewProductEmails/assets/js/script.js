document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('akamodeNewsletterForm');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Collect Data
            const name = document.getElementById('akamode-name').value;
            const email = document.getElementById('akamode-email').value;

            if (name && email) {
                // Logic to send data to WordPress/Laravel API goes here
                console.log("Newsletter Submission:", { name, email });
                
                alert("ثبت نام شما با موفقیت انجام شد.");
                
                // Reset form after success
                form.reset();
            }
        });
    }
});