document.addEventListener('DOMContentLoaded', () => {
    
    
    const currentUrl = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-links a');
    
    navLinks.forEach(link => {
        const hrefAttribute = link.getAttribute('href');
        if (hrefAttribute && currentUrl.endsWith(hrefAttribute)) {
            document.querySelectorAll('.nav-links a').forEach(el => el.classList.remove('active'));
            link.classList.add('active');
        }
    });



    
    const dynamicForms = document.querySelectorAll('form:not(#appointmentForm)');
    dynamicForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#ef4444';
                } else {
                    field.style.borderColor = '#cbd5e1';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill out all required validation fields before proceeding.');
            }
        });
    });

});

