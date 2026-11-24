function validateRegistration() {  
    var name = document.registerForm.full_name.value;  
    var username = document.registerForm.username.value;  
    var phone = document.registerForm.phone_number.value; // Added field to match DB setup 
    var password = document.registerForm.password.value;  
    var confirmPassword = document.registerForm.confirm_password.value;  
    
    // Check for empty string data fields (Week 8 Lab Standard)
    if (name == null || name == "") {  
        alert("Full Name cannot be blank.");  
        return false;  
    }
    if (username == null || username == "") {  
        alert("Username cannot be blank.");  
        return false;  
    }
    
    // --- START OF UPDATE: CONTACT PHONE VALIDATION ---
    if (phone == null || phone == "") {  
        alert("Contact phone number cannot be blank.");  
        return false;  
    }
    // Ensures a standard numerical entry length constraint between 9 and 15 digits
    var phoneCleaned = phone.replace(/[\s-]/g, "");
    if (isNaN(phoneCleaned) || phoneCleaned.length < 9 || phoneCleaned.length > 15) {
        alert("Please enter a valid numeric phone number (between 9 and 15 digits).");
        return false;
    }
    // --- END OF UPDATE ---
    
    // Check minimal string character length boundaries
    if (password.length < 6) {  
        alert("Password must be at least 6 characters long.");  
        return false;  
    }
    
    // Direct equivalence matching validation logic
    if (password !== confirmPassword) {  
        alert("Passwords must match exactly!");  
        return false;  
    }
    return true;
}